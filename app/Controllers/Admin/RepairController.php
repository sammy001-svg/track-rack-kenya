<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\RepairRequest;
use RuntimeException;

class RepairController extends Controller
{
    public function index(): void
    {
        $model = new RepairRequest();

        $filters = [
            'status' => array_key_exists((string) ($_GET['status'] ?? ''), RepairRequest::STATUSES) ? $_GET['status'] : null,
            'q'      => trim((string) ($_GET['q'] ?? '')),
        ];

        $result = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.repairs.index', [
            'pageTitle'    => 'Workshop repairs',
            'repairs'      => $result['items'],
            'total'        => $result['total'],
            'pages'        => $result['pages'],
            'page'         => $result['page'],
            'filters'      => $filters,
            'statusCounts' => $model->countByStatus(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $model  = new RepairRequest();
        $repair = $model->find((int) $id);

        if ($repair === null) {
            Session::flash('error', 'That repair request no longer exists.');
            $this->redirect('/admin/repairs');
        }

        $this->view('admin.repairs.show', [
            'pageTitle' => 'Repair ' . $repair['reference'],
            'repair'    => $repair,
            'photos'    => $model->photos((int) $repair['id']),
        ], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $repairId = (int) $id;
        $model    = new RepairRequest();
        $repair   = $model->find($repairId);

        if ($repair === null) {
            Session::flash('error', 'That repair request no longer exists.');
            $this->redirect('/admin/repairs');
        }

        $status = (string) ($_POST['status'] ?? 'new');
        $status = array_key_exists($status, RepairRequest::STATUSES) ? $status : 'new';

        $quoted = trim((string) ($_POST['quoted_amount'] ?? ''));
        $ready  = trim((string) ($_POST['estimated_ready'] ?? ''));

        $readyDate = null;
        if ($ready !== '') {
            $parsed = \DateTimeImmutable::createFromFormat('Y-m-d', $ready);
            if ($parsed !== false && $parsed->format('Y-m-d') === $ready) {
                $readyDate = $ready;
            }
        }

        $model->updateById($repairId, [
            'status'          => $status,
            'quoted_amount'   => is_numeric($quoted) ? round((float) $quoted, 2) : null,
            'estimated_ready' => $readyDate,
            'admin_notes'     => trim((string) ($_POST['admin_notes'] ?? '')) ?: null,
        ]);

        if (isset($_POST['notify']) && $repair['status'] !== $status) {
            $this->notifyStatusChange($repair, $status, $quoted, $readyDate);
            Session::flash('success', 'Repair updated and the customer has been emailed.');
        } else {
            Session::flash('success', 'Repair updated.');
        }

        $this->redirect('/admin/repairs/' . $repairId);
    }

    /** Staff can add their own assessment photographs. */
    public function uploadPhoto(string $id): void
    {
        $repairId = (int) $id;
        $model    = new RepairRequest();

        if ($model->find($repairId) === null) {
            Session::flash('error', 'That repair request no longer exists.');
            $this->redirect('/admin/repairs');
        }

        if (!isset($_FILES['photos']) || !is_array($_FILES['photos']['name'])) {
            Session::flash('error', 'No photograph was selected.');
            $this->redirect('/admin/repairs/' . $repairId);
        }

        $uploader = new Uploader(config('uploads'));
        $saved    = 0;

        for ($i = 0; $i < count($_FILES['photos']['name']); $i++) {
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
                $model->addPhoto($repairId, $uploader->store($file, 'repairs'), 'staff');
                $saved++;
            } catch (RuntimeException $e) {
                Session::flash('error', $file['name'] . ': ' . $e->getMessage());
            }
        }

        if ($saved > 0) {
            Session::flash('success', $saved . ' photograph(s) added.');
        }

        $this->redirect('/admin/repairs/' . $repairId);
    }

    public function deletePhoto(string $id): void
    {
        $model = new RepairRequest();
        $photo = $model->findPhoto((int) $id);

        if ($photo === null) {
            Session::flash('error', 'That photograph no longer exists.');
            $this->redirect('/admin/repairs');
        }

        (new Uploader(config('uploads')))->delete($photo['path']);
        $model->deletePhoto((int) $id);

        Session::flash('success', 'Photograph removed.');
        $this->redirect('/admin/repairs/' . (int) $photo['repair_id']);
    }

    public function destroy(string $id): void
    {
        $repairId = (int) $id;
        $model    = new RepairRequest();

        $uploader = new Uploader(config('uploads'));
        foreach ($model->photos($repairId) as $photo) {
            $uploader->delete($photo['path']);
        }

        $model->deleteById($repairId);

        Session::flash('success', 'Repair request deleted.');
        $this->redirect('/admin/repairs');
    }

    // =================================================================

    private function notifyStatusChange(array $repair, string $status, string $quoted, ?string $readyDate): void
    {
        $name  = e(explode(' ', $repair['name'])[0]);
        $ref   = e($repair['reference']);
        $item  = e($repair['item_type']);

        [$subject, $body] = match ($status) {
            'quoted' => [
                'Your repair quote — ' . $repair['reference'],
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Your repair has been assessed</h2>'
                    . "<p>Hello {$name},</p>"
                    . "<p>We have assessed your {$item} and can carry out the repair for "
                    . '<strong>' . (is_numeric($quoted) ? e(money($quoted)) : 'the amount quoted below') . '</strong>.</p>'
                    . ($repair['admin_notes'] ? '<p>' . nl2br(e($repair['admin_notes'])) . '</p>' : '')
                    . '<p>Reply to this email to approve and we will start work. Reference <strong>' . $ref . '</strong>.</p>',
            ],
            'in_progress' => [
                'Your repair is under way — ' . $repair['reference'],
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">In the workshop</h2>'
                    . "<p>Hello {$name},</p>"
                    . "<p>Work has started on your {$item}."
                    . ($readyDate ? ' We expect it to be ready around <strong>' . e(pretty_date($readyDate)) . '</strong>.' : '')
                    . '</p><p>Reference <strong>' . $ref . '</strong>.</p>',
            ],
            'ready' => [
                'Your repair is ready — ' . $repair['reference'],
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Ready for collection</h2>'
                    . "<p>Hello {$name},</p>"
                    . "<p>Your {$item} is finished and ready to collect from "
                    . e(setting('contact_address')) . '.</p>'
                    . '<p>We are open ' . e(setting('contact_hours')) . '.</p>'
                    . '<p>Reference <strong>' . $ref . '</strong>.</p>',
            ],
            default => [
                'Update on your repair — ' . $repair['reference'],
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Repair update</h2>'
                    . "<p>Hello {$name},</p>"
                    . "<p>Your {$item} is now marked as <strong>"
                    . e(RepairRequest::STATUSES[$status]) . '</strong>.</p>'
                    . '<p>Reference <strong>' . $ref . '</strong>.</p>',
            ],
        };

        Mailer::send($repair['email'], $subject, $body);
    }
}
