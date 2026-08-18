<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Quote;

class QuoteController extends Controller
{
    public function index(): void
    {
        $model = new Quote();

        $filters = [
            'status' => array_key_exists((string) ($_GET['status'] ?? ''), Quote::STATUSES) ? $_GET['status'] : null,
            'q'      => trim((string) ($_GET['q'] ?? '')),
        ];

        $result = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.quotes.index', [
            'pageTitle'    => 'Quote requests',
            'quotes'       => $result['items'],
            'total'        => $result['total'],
            'pages'        => $result['pages'],
            'page'         => $result['page'],
            'filters'      => $filters,
            'statusCounts' => $model->countByStatus(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $model = new Quote();
        $quote = $model->find((int) $id);

        if ($quote === null) {
            Session::flash('error', 'That quote request no longer exists.');
            $this->redirect('/admin/quotes');
        }

        $this->view('admin.quotes.show', [
            'pageTitle' => 'Quote ' . $quote['reference'],
            'quote'     => $quote,
            'items'     => $model->items((int) $quote['id']),
        ], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $quoteId = (int) $id;
        $model   = new Quote();

        if ($model->find($quoteId) === null) {
            Session::flash('error', 'That quote request no longer exists.');
            $this->redirect('/admin/quotes');
        }

        $status = (string) ($_POST['status'] ?? 'new');
        $total  = trim((string) ($_POST['quoted_total'] ?? ''));

        $model->updateById($quoteId, [
            'status'       => array_key_exists($status, Quote::STATUSES) ? $status : 'new',
            'admin_notes'  => trim((string) ($_POST['admin_notes'] ?? '')) ?: null,
            'quoted_total' => is_numeric($total) ? round((float) $total, 2) : null,
        ]);

        Session::flash('success', 'Quote updated.');
        $this->redirect('/admin/quotes/' . $quoteId);
    }

    public function destroy(string $id): void
    {
        (new Quote())->deleteById((int) $id);

        Session::flash('success', 'Quote request deleted.');
        $this->redirect('/admin/quotes');
    }

    /** A clean, printable version for sending or filing. */
    public function printable(string $id): void
    {
        $model = new Quote();
        $quote = $model->find((int) $id);

        if ($quote === null) {
            Session::flash('error', 'That quote request no longer exists.');
            $this->redirect('/admin/quotes');
        }

        $this->view('admin.quotes.print', [
            'pageTitle' => 'Quote ' . $quote['reference'],
            'quote'     => $quote,
            'items'     => $model->items((int) $quote['id']),
        ], 'layouts.blank');
    }
}
