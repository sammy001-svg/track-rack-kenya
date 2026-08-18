<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\QuoteList;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Product;
use App\Models\Quote;

class QuoteController extends Controller
{
    private const DISCIPLINES = [
        'Racing', 'Polo', 'Showjumping', 'Dressage', 'Eventing',
        'Hacking', 'Safari riding', 'Pony Club', 'Other',
    ];

    /** POST /quote/add - from a product card or the detail page. */
    public function add(): void
    {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));
        $variant   = trim((string) ($_POST['variant'] ?? ''));

        $product = $productId > 0 ? (new Product())->find($productId) : null;

        if ($product === null || (int) $product['is_active'] !== 1) {
            if ($this->wantsJson()) {
                $this->json(['ok' => false, 'error' => 'That item is no longer available.'], 404);
            }
            Session::flash('error', 'That item is no longer available.');
            $this->back('/shop');
        }

        $added = QuoteList::add($productId, $quantity, $variant);

        if (!$added) {
            $message = 'Your quote list is full. Send this request first, then start another.';

            if ($this->wantsJson()) {
                $this->json(['ok' => false, 'error' => $message, 'count' => QuoteList::count()], 422);
            }

            Session::flash('error', $message);
            $this->back('/shop');
        }

        if ($this->wantsJson()) {
            $this->json([
                'ok'      => true,
                'count'   => QuoteList::count(),
                'message' => $product['name'] . ' added to your quote list.',
            ]);
        }

        Session::flash('success', $product['name'] . ' was added to your quote list.');
        $this->back('/quote');
    }

    /** GET /quote - the quote list itself. */
    public function index(): void
    {
        $split = QuoteList::split();

        $this->view('site.quote-list', [
            'pageTitle'   => 'Your Quote List',
            'metaDesc'    => 'Review the items on your Tack Rack quote list before sending your request.',
            // Contents depend on the visitor's session — nothing to index.
            'noindex'     => true,
            'bodyClass'   => 'page-quote-list',
            'items'       => array_merge($split['buyable'], $split['quote']),
            'buyable'     => $split['buyable'],
            'quoteOnly'   => $split['quote'],
            'subtotal'    => $split['subtotal'],
        ]);
    }

    public function update(): void
    {
        $key      = (string) ($_POST['key'] ?? '');
        $quantity = (int) ($_POST['quantity'] ?? 1);

        QuoteList::update($key, $quantity);

        if ($this->wantsJson()) {
            $this->json(['ok' => true, 'count' => QuoteList::count()]);
        }

        $this->redirect('/quote');
    }

    public function remove(): void
    {
        QuoteList::remove((string) ($_POST['key'] ?? ''));

        if ($this->wantsJson()) {
            $this->json(['ok' => true, 'count' => QuoteList::count()]);
        }

        Session::flash('success', 'Item removed from your quote list.');
        $this->redirect('/quote');
    }

    public function clear(): void
    {
        QuoteList::clear();
        Session::flash('success', 'Your quote list has been cleared.');
        $this->redirect('/shop');
    }

    /** GET /request-a-quote - the contact details form. */
    public function form(): void
    {
        $items = QuoteList::detailed();

        $this->view('site.quote-form', [
            'pageTitle'   => 'Request a Quote',
            'metaDesc'    => 'Send your selected items to Tack Rack and receive a full quote, usually within one working day.',
            'noindex'     => true,
            'bodyClass'   => 'page-quote-form',
            'items'       => $items,
            'disciplines' => self::DISCIPLINES,
            'errors'      => Session::errors(),
        ]);

        Session::clearOld();
    }

    /** POST /request-a-quote */
    public function submit(): void
    {
        $items = QuoteList::detailed();

        $validator = new Validator($_POST);
        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->max('location', 150, 'Location')
            ->max('notes', 4000, 'Notes');

        if ($items === []) {
            $validator->addManualError('items', 'Your quote list is empty. Add at least one item before sending.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields and try again.');
            $this->redirect('/request-a-quote');
        }

        $discipline = $validator->value('discipline', '');

        $quoteModel = new Quote();
        $result     = $quoteModel->createWithItems([
            'name'       => $validator->value('name'),
            'email'      => $validator->value('email'),
            'phone'      => $validator->value('phone'),
            'location'   => $validator->value('location') ?: null,
            'discipline' => in_array($discipline, self::DISCIPLINES, true) ? $discipline : null,
            'notes'      => $validator->value('notes') ?: null,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ], $items);

        $this->notifyStaff($result['reference'], $validator, $items);

        QuoteList::clear();
        Session::clearOld();

        $this->redirect('/quote/sent/' . $result['reference']);
    }

    public function confirmation(string $reference): void
    {
        $quote = (new Quote())->findBy('reference', $reference);

        if ($quote === null) {
            $this->notFound('We could not find that quote reference.');
        }

        $this->view('site.quote-sent', [
            'pageTitle' => 'Quote Request Sent',
            'metaDesc'  => 'Your quote request has been received by Tack Rack.',
            'bodyClass' => 'page-quote-sent',
            'quote'     => $quote,
            'items'     => (new Quote())->items((int) $quote['id']),
            'noindex'   => true,
        ]);
    }

    private function notifyStaff(string $reference, Validator $validator, array $items): void
    {
        $recipient = (string) setting('quote_recipient', setting('contact_email', ''));

        if ($recipient === '') {
            return;
        }

        $rows = '';
        foreach ($items as $item) {
            $rows .= '<tr>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee">' . e($item['product']['name'])
                . ($item['variant'] !== '' ? '<br><small style="color:#777">' . e($item['variant']) . '</small>' : '')
                . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee">' . e($item['product']['sku'] ?? '') . '</td>'
                . '<td style="padding:8px 12px;border-bottom:1px solid #eee;text-align:right">' . (int) $item['quantity'] . '</td>'
                . '</tr>';
        }

        $body = '<div style="font:14px/1.6 Helvetica,Arial,sans-serif;color:#222">'
            . '<h2 style="font-weight:600">New quote request &mdash; ' . e($reference) . '</h2>'
            . '<p><strong>' . e($validator->value('name')) . '</strong><br>'
            . e($validator->value('email')) . '<br>'
            . e($validator->value('phone')) . '<br>'
            . e($validator->value('location')) . '</p>'
            . ($validator->value('discipline') ? '<p>Discipline: ' . e($validator->value('discipline')) . '</p>' : '')
            . ($validator->value('notes') ? '<p><strong>Notes</strong><br>' . nl2br(e($validator->value('notes'))) . '</p>' : '')
            . '<table style="border-collapse:collapse;width:100%;margin-top:16px">'
            . '<thead><tr>'
            . '<th style="text-align:left;padding:8px 12px;border-bottom:2px solid #222">Item</th>'
            . '<th style="text-align:left;padding:8px 12px;border-bottom:2px solid #222">SKU</th>'
            . '<th style="text-align:right;padding:8px 12px;border-bottom:2px solid #222">Qty</th>'
            . '</tr></thead><tbody>' . $rows . '</tbody></table>'
            . '</div>';

        Mailer::send($recipient, 'New quote request ' . $reference, $body, $validator->value('email'));
    }
}
