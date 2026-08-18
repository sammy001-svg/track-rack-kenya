<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\CustomerAuth;
use App\Core\Mailer;
use App\Core\Schema;
use App\Core\Seo;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\RepairRequest;
use App\Models\Service;
use RuntimeException;

class ServiceController extends Controller
{
    private const DISCIPLINES = [
        'Racing', 'Polo', 'Showjumping', 'Dressage', 'Eventing',
        'Hacking', 'Safari riding', 'Pony Club', 'Other',
    ];

    /** GET /services */
    public function index(): void
    {
        $services = (new Service())->active();

        $seo = Seo::make()
            ->title('Saddle Fitting & Tack Repairs')
            ->description('Saddle fitting by the only Society of Master Saddlers qualified fitter in East Africa, plus saddle and tack repairs in our own Nairobi workshop.')
            ->canonical(url('/services'))
            ->image(asset('/assets/img/service-fitting.jpg'), 'Saddle fitting at Tack Rack')
            ->schema(Schema::breadcrumbs(['Home' => url('/'), 'Services' => null]));

        foreach ($services as $service) {
            $seo->schema(Schema::service($service));
        }

        $this->view('site.services', [
            'seo'       => $seo,
            'bodyClass' => 'page-services',
            'services'  => $services,
        ]);
    }

    // =================================================================
    //  Saddle fitting
    // =================================================================

    /** GET /services/saddle-fitting */
    public function fitting(): void
    {
        $service  = (new Service())->bySlug('saddle-fitting');
        $customer = CustomerAuth::user();

        $seo = Seo::make()
            ->title($service['meta_title'] ?? 'Saddle Fitting in Nairobi')
            ->description($service['meta_desc'] ?? $service['tagline'] ?? 'Book a saddle fitting with Tack Rack, Nairobi.')
            ->canonical(url('/services/saddle-fitting'))
            ->image(asset('/assets/img/service-fitting.jpg'), 'Saddle fitting on the horse')
            ->schema(Schema::breadcrumbs([
                'Home' => url('/'), 'Services' => url('/services'), 'Saddle Fitting' => null,
            ]))
            ->schema($service !== null ? Schema::service($service) : [])
            ->schema(Schema::faq([
                'Why does a saddle need professional fitting?'
                    => 'A saddle that does not fit will damage a horse\'s back long before the rider notices. Horses also change shape with work, age and condition, so a saddle that fitted last season may not fit this one.',
                'Do you travel to the yard?'
                    => 'Yes. Fittings are carried out at the shop on Ngong Road or at your own yard anywhere in Kenya. Yard visits are charged by distance and confirmed before we travel.',
                'How long does a saddle fitting take?'
                    => 'Around 90 minutes. The horse is assessed standing and in work, checking wither clearance, panel contact, balance and billet alignment.',
                'Who carries out the fitting?'
                    => 'Sharon Ashley, the only Saddle Fitter in East Africa qualified with the Society of Master Saddlers.',
            ]));

        $this->view('site.service-fitting', [
            'seo'         => $seo,
            'bodyClass'   => 'page-fitting',
            'service'     => $service,
            'disciplines' => self::DISCIPLINES,
            'slots'       => Booking::SLOTS,
            'customer'    => $customer,
            'horses'      => $customer ? (new Customer())->horses((int) $customer['id']) : [],
            'errors'      => Session::errors(),
        ]);

        Session::clearOld();
    }

    /** POST /services/saddle-fitting */
    public function submitFitting(): void
    {
        $validator = new Validator($_POST);
        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->max('location', 200, 'Location')
            ->max('horse_name', 120, 'Horse name')
            ->max('notes', 4000, 'Notes');

        $preferredDate = $this->validDate($validator->value('preferred_date', ''));
        $alternateDate = $this->validDate($validator->value('alternate_date', ''));

        if ($validator->value('preferred_date', '') !== '' && $preferredDate === null) {
            $validator->addManualError('preferred_date', 'Choose a date from today onwards.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields and try again.');
            $this->redirect('/services/saddle-fitting');
        }

        $service   = (new Service())->bySlug('saddle-fitting');
        $slot      = array_key_exists((string) ($_POST['preferred_slot'] ?? ''), Booking::SLOTS)
            ? $_POST['preferred_slot'] : 'flexible';
        $discipline = $validator->value('discipline', '');

        $result = (new Booking())->createBooking([
            'service_id'     => $service['id'] ?? null,
            'customer_id'    => CustomerAuth::id(),
            'name'           => $validator->value('name'),
            'email'          => $validator->value('email'),
            'phone'          => $validator->value('phone'),
            'location'       => $validator->value('location') ?: null,
            'at_yard'        => isset($_POST['at_yard']) ? 1 : 0,
            'horse_name'     => $validator->value('horse_name') ?: null,
            'horse_details'  => $validator->value('horse_details') ?: null,
            'discipline'     => in_array($discipline, self::DISCIPLINES, true) ? $discipline : null,
            'saddle_details' => $validator->value('saddle_details') ?: null,
            'preferred_date' => $preferredDate,
            'preferred_slot' => $slot,
            'alternate_date' => $alternateDate,
            'notes'          => $validator->value('notes') ?: null,
            'ip_address'     => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $this->notifyBooking($result['reference'], $validator, $preferredDate, $slot);

        Session::clearOld();
        Session::set('_last_booking', $result['reference']);
        $this->redirect('/services/booked/' . $result['reference']);
    }

    public function bookingConfirmation(string $reference): void
    {
        $booking = (new Booking())->findBy('reference', $reference);

        if ($booking === null) {
            $this->notFound('We could not find that booking reference.');
        }

        $this->view('site.service-booked', [
            'pageTitle' => 'Fitting Requested',
            'bodyClass' => 'page-confirmation',
            'noindex'   => true,
            'booking'   => $booking,
            'kind'      => 'fitting',
        ]);
    }

    // =================================================================
    //  Workshop repairs
    // =================================================================

    /** GET /services/repairs */
    public function repairs(): void
    {
        $service = (new Service())->bySlug('workshop-repairs');

        $seo = Seo::make()
            ->title($service['meta_title'] ?? 'Saddle & Tack Repairs, Nairobi')
            ->description($service['meta_desc'] ?? $service['tagline'] ?? 'Saddle, tree and tack repairs in our Nairobi workshop.')
            ->canonical(url('/services/repairs'))
            ->image(asset('/assets/img/service-repairs.jpg'), 'Saddlery in the Tack Rack workshop')
            ->schema(Schema::breadcrumbs([
                'Home' => url('/'), 'Services' => url('/services'), 'Workshop Repairs' => null,
            ]))
            ->schema($service !== null ? Schema::service($service) : [])
            ->schema(Schema::faq([
                'What can you repair?'
                    => 'Broken and cracked saddle trees, re-flocking, panel repair, restitching bridles, girths and stirrup leathers, replacement billets and fittings, rug repairs and re-proofing, and brass nameplate engraving.',
                'How much does a repair cost?'
                    => 'Every job is quoted individually. Send photographs of the damage and we will assess and quote before any work begins — nothing starts until you approve it.',
                'How long does a repair take?'
                    => 'Most repairs are turned around within about a week. Tree repairs take longer and we will tell you so up front.',
                'Do I need to bring the item in?'
                    => 'Send photographs first if you would rather have a figure before you travel. Otherwise drop the item at the MacNaughton Business Centre on Ngong Road.',
            ]));

        $this->view('site.service-repairs', [
            'seo'       => $seo,
            'bodyClass' => 'page-repairs',
            'service'   => $service,
            'itemTypes' => RepairRequest::ITEM_TYPES,
            'urgency'   => RepairRequest::URGENCY,
            'customer'  => CustomerAuth::user(),
            'errors'    => Session::errors(),
        ]);

        Session::clearOld();
    }

    /** POST /services/repairs */
    public function submitRepair(): void
    {
        $validator = new Validator($_POST);
        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->require('item_type', 'Item type')->in('item_type', RepairRequest::ITEM_TYPES, 'Item type')
            ->max('item_make', 150, 'Make or model')
            ->require('damage', 'Description of the damage')->min('damage', 15, 'Description of the damage')
            ->max('damage', 4000, 'Description of the damage')
            ->in('urgency', array_keys(RepairRequest::URGENCY), 'Urgency');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields and try again.');
            $this->redirect('/services/repairs');
        }

        $model  = new RepairRequest();
        $result = $model->createRequest([
            'customer_id' => CustomerAuth::id(),
            'name'        => $validator->value('name'),
            'email'       => $validator->value('email'),
            'phone'       => $validator->value('phone'),
            'location'    => $validator->value('location') ?: null,
            'item_type'   => $validator->value('item_type'),
            'item_make'   => $validator->value('item_make') ?: null,
            'damage'      => $validator->value('damage'),
            'urgency'     => $validator->value('urgency') ?: 'standard',
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $photoCount = $this->savePhotos($model, $result['id']);

        $this->notifyRepair($result['reference'], $validator, $photoCount);

        Session::clearOld();
        $this->redirect('/services/repair-sent/' . $result['reference']);
    }

    public function repairConfirmation(string $reference): void
    {
        $model  = new RepairRequest();
        $repair = $model->findBy('reference', $reference);

        if ($repair === null) {
            $this->notFound('We could not find that repair reference.');
        }

        $this->view('site.service-booked', [
            'pageTitle' => 'Repair Request Sent',
            'bodyClass' => 'page-confirmation',
            'noindex'   => true,
            'booking'   => $repair,
            'kind'      => 'repair',
            'photos'    => $model->photos((int) $repair['id']),
        ]);
    }

    // =================================================================
    //  Helpers
    // =================================================================

    /** Accept only a well-formed date that is not in the past. */
    private function validDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('Y-m-d', $value);

        if ($date === false || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date->format('Y-m-d') < date('Y-m-d') ? null : $date->format('Y-m-d');
    }

    /** Store up to six damage photographs. */
    private function savePhotos(RepairRequest $model, int $repairId): int
    {
        if (!isset($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
            return 0;
        }

        $uploader = new Uploader(config('uploads'));
        $count    = min(6, count($_FILES['photos']['name']));
        $saved    = 0;

        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $_FILES['photos']['name'][$i],
                'type'     => $_FILES['photos']['type'][$i],
                'tmp_name' => $_FILES['photos']['tmp_name'][$i],
                'error'    => $_FILES['photos']['error'][$i],
                'size'     => $_FILES['photos']['size'][$i],
            ];

            if (!Uploader::present($file)) {
                continue;
            }

            try {
                $model->addPhoto($repairId, $uploader->store($file, 'repairs'), 'customer');
                $saved++;
            } catch (RuntimeException $e) {
                Session::flash('error', $file['name'] . ': ' . $e->getMessage());
            }
        }

        return $saved;
    }

    private function notifyBooking(string $reference, Validator $v, ?string $date, string $slot): void
    {
        $staff = (string) (setting('booking_recipient', '') ?: setting('contact_email', ''));

        $detail = '<p><strong>' . e($v->value('name')) . '</strong><br>'
            . e($v->value('email')) . '<br>' . e($v->value('phone')) . '<br>'
            . e($v->value('location')) . '</p>'
            . '<p><strong>Horse:</strong> ' . e($v->value('horse_name') ?: 'not given')
            . '<br><strong>Discipline:</strong> ' . e($v->value('discipline') ?: 'not given')
            . '<br><strong>Preferred:</strong> ' . e($date ? pretty_date($date) : 'no date given')
            . ' — ' . e(Booking::SLOTS[$slot] ?? '')
            . '<br><strong>At the yard:</strong> ' . (isset($_POST['at_yard']) ? 'yes' : 'no') . '</p>'
            . ($v->value('saddle_details') ? '<p><strong>Current saddle</strong><br>' . nl2br(e($v->value('saddle_details'))) . '</p>' : '')
            . ($v->value('horse_details') ? '<p><strong>Horse details</strong><br>' . nl2br(e($v->value('horse_details'))) . '</p>' : '')
            . ($v->value('notes') ? '<p><strong>Notes</strong><br>' . nl2br(e($v->value('notes'))) . '</p>' : '');

        if ($staff !== '') {
            Mailer::send(
                $staff,
                'Saddle fitting request ' . $reference,
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Saddle fitting request</h2>'
                    . '<p style="font-family:monospace;font-size:15px;">' . e($reference) . '</p>' . $detail,
                $v->value('email')
            );
        }

        Mailer::send(
            $v->value('email'),
            'Your saddle fitting request — ' . $reference,
            '<h2 style="font-family:Georgia,serif;font-weight:normal;">Thank you, ' . e(explode(' ', $v->value('name'))[0]) . '</h2>'
                . '<p>We have your saddle fitting request and will be in touch to confirm a date and time.</p>'
                . '<p>Your reference is <strong>' . e($reference) . '</strong>.</p>'
                . '<p style="color:#6B655C;font-size:13px;">Fittings are carried out by Sharon Ashley, the only Saddle Fitter '
                . 'in East Africa qualified with the Society of Master Saddlers.</p>'
                . Mailer::button('View our services', url('/services'))
        );
    }

    private function notifyRepair(string $reference, Validator $v, int $photoCount): void
    {
        $staff = (string) (setting('repair_recipient', '') ?: setting('contact_email', ''));

        if ($staff !== '') {
            Mailer::send(
                $staff,
                'Repair request ' . $reference,
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Workshop repair request</h2>'
                    . '<p style="font-family:monospace;font-size:15px;">' . e($reference) . '</p>'
                    . '<p><strong>' . e($v->value('name')) . '</strong><br>'
                    . e($v->value('email')) . '<br>' . e($v->value('phone')) . '</p>'
                    . '<p><strong>Item:</strong> ' . e($v->value('item_type'))
                    . ($v->value('item_make') ? ' — ' . e($v->value('item_make')) : '')
                    . '<br><strong>Urgency:</strong> ' . e(RepairRequest::URGENCY[$v->value('urgency')] ?? 'Standard')
                    . '<br><strong>Photographs:</strong> ' . $photoCount . '</p>'
                    . '<p><strong>Damage</strong><br>' . nl2br(e($v->value('damage'))) . '</p>',
                $v->value('email')
            );
        }

        Mailer::send(
            $v->value('email'),
            'Your repair request — ' . $reference,
            '<h2 style="font-family:Georgia,serif;font-weight:normal;">Thank you, ' . e(explode(' ', $v->value('name'))[0]) . '</h2>'
                . '<p>Your repair request has reached our Nairobi workshop. We will assess it and come back with a quote '
                . 'before any work begins.</p>'
                . '<p>Your reference is <strong>' . e($reference) . '</strong>.</p>'
                . ($photoCount > 0
                    ? '<p style="color:#6B655C;font-size:13px;">' . $photoCount . ' photograph(s) received.</p>'
                    : '<p style="color:#6B655C;font-size:13px;">If you can, reply to this email with photographs of the damage — '
                      . 'it speeds the assessment up considerably.</p>')
        );
    }
}
