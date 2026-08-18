<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Service;
use RuntimeException;

class ServiceController extends Controller
{
    public function index(): void
    {
        $this->view('admin.services.index', [
            'pageTitle' => 'Services',
            'services'  => (new Service())->allWithCounts(),
        ], 'layouts.admin');
    }

    public function edit(string $id): void
    {
        $service = (new Service())->find((int) $id);

        if ($service === null) {
            Session::flash('error', 'That service no longer exists.');
            $this->redirect('/admin/services');
        }

        $this->view('admin.services.form', [
            'pageTitle' => 'Edit: ' . $service['name'],
            'service'   => $service,
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function update(string $id): void
    {
        $serviceId = (int) $id;
        $model     = new Service();
        $existing  = $model->find($serviceId);

        if ($existing === null) {
            Session::flash('error', 'That service no longer exists.');
            $this->redirect('/admin/services');
        }

        $validator = new Validator($_POST);
        $validator->require('name', 'Service name')->max('name', 150, 'Service name')
            ->max('tagline', 255, 'Tagline')
            ->max('meta_title', 190, 'Meta title')
            ->max('meta_desc', 300, 'Meta description');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/admin/services/' . $serviceId . '/edit');
        }

        $duration = trim((string) ($_POST['duration_minutes'] ?? ''));
        $price    = trim((string) ($_POST['price_from'] ?? ''));

        $image = $existing['image'];
        if (Uploader::present($_FILES['image'] ?? null)) {
            try {
                $uploader = new Uploader(config('uploads'));
                $image    = $uploader->store($_FILES['image'], 'services');
                $uploader->delete($existing['image']);
            } catch (RuntimeException $e) {
                Session::flash('error', 'Image not saved — ' . $e->getMessage());
            }
        }

        $model->updateById($serviceId, [
            'name'             => $validator->value('name'),
            'tagline'          => $validator->value('tagline') ?: null,
            'description'      => $validator->value('description') ?: null,
            'what_to_expect'   => $validator->value('what_to_expect') ?: null,
            'meta_title'       => $validator->value('meta_title') ?: null,
            'meta_desc'        => $validator->value('meta_desc') ?: null,
            'duration_minutes' => $duration !== '' && is_numeric($duration) ? (int) $duration : null,
            'price_from'       => is_numeric($price) ? round((float) $price, 2) : null,
            'travel_available' => isset($_POST['travel_available']) ? 1 : 0,
            'image'            => $image,
            'is_active'        => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'       => (int) ($_POST['sort_order'] ?? 0),
        ]);

        Session::clearOld();
        Session::flash('success', 'Service saved.');
        $this->redirect('/admin/services/' . $serviceId . '/edit');
    }
}
