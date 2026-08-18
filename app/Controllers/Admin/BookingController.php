<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Models\Booking;

class BookingController extends Controller
{
    public function index(): void
    {
        $model = new Booking();

        $filters = [
            'status' => array_key_exists((string) ($_GET['status'] ?? ''), Booking::STATUSES) ? $_GET['status'] : null,
            'q'      => trim((string) ($_GET['q'] ?? '')),
        ];

        $result = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.bookings.index', [
            'pageTitle'    => 'Saddle fittings',
            'bookings'     => $result['items'],
            'total'        => $result['total'],
            'pages'        => $result['pages'],
            'page'         => $result['page'],
            'filters'      => $filters,
            'statusCounts' => $model->countByStatus(),
            'upcoming'     => $model->upcoming(5),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $booking = (new Booking())->withService((int) $id);

        if ($booking === null) {
            Session::flash('error', 'That booking no longer exists.');
            $this->redirect('/admin/bookings');
        }

        $this->view('admin.bookings.show', [
            'pageTitle' => 'Fitting ' . $booking['reference'],
            'booking'   => $booking,
        ], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $bookingId = (int) $id;
        $model     = new Booking();
        $booking   = $model->find($bookingId);

        if ($booking === null) {
            Session::flash('error', 'That booking no longer exists.');
            $this->redirect('/admin/bookings');
        }

        $status = (string) ($_POST['status'] ?? 'new');
        $status = array_key_exists($status, Booking::STATUSES) ? $status : 'new';

        $scheduledAt = trim((string) ($_POST['scheduled_at'] ?? ''));
        $scheduled   = null;

        if ($scheduledAt !== '') {
            $parsed = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i', $scheduledAt)
                ?: \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $scheduledAt);

            if ($parsed !== false) {
                $scheduled = $parsed->format('Y-m-d H:i:s');
            }
        }

        $fee = trim((string) ($_POST['fee'] ?? ''));

        $model->updateById($bookingId, [
            'status'       => $status,
            'scheduled_at' => $scheduled,
            'fee'          => is_numeric($fee) ? round((float) $fee, 2) : null,
            'admin_notes'  => trim((string) ($_POST['admin_notes'] ?? '')) ?: null,
        ]);

        // Tell the customer when we confirm or schedule, not on every save.
        $announce = in_array($status, ['confirmed', 'scheduled'], true)
            && $booking['status'] !== $status
            && isset($_POST['notify']);

        if ($announce) {
            $when = $scheduled !== null
                ? pretty_date($scheduled, true)
                : ($booking['preferred_date'] ? pretty_date($booking['preferred_date']) : 'a date we will confirm');

            Mailer::send(
                $booking['email'],
                'Your saddle fitting — ' . $booking['reference'],
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Your fitting is confirmed</h2>'
                    . '<p>Hello ' . e(explode(' ', $booking['name'])[0]) . ',</p>'
                    . '<p>We have your saddle fitting booked for <strong>' . e($when) . '</strong>'
                    . ((int) $booking['at_yard'] === 1 && $booking['location']
                        ? ' at ' . e($booking['location'])
                        : ' at the shop, ' . e(setting('contact_address')))
                    . '.</p>'
                    . '<p>Reference <strong>' . e($booking['reference']) . '</strong>.</p>'
                    . '<p style="color:#6B655C;font-size:13px;">If you need to change the time, reply to this email or call '
                    . e(setting('contact_phone')) . '.</p>'
            );

            Session::flash('success', 'Booking updated and the customer has been emailed.');
        } else {
            Session::flash('success', 'Booking updated.');
        }

        $this->redirect('/admin/bookings/' . $bookingId);
    }

    public function destroy(string $id): void
    {
        (new Booking())->deleteById((int) $id);

        Session::flash('success', 'Booking deleted.');
        $this->redirect('/admin/bookings');
    }
}
