<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Brand;
use RuntimeException;

class BrandController extends Controller
{
    public function index(): void
    {
        $this->view('admin.brands.index', [
            'pageTitle' => 'Brands',
            'brands'    => (new Brand())->allWithCounts(),
        ], 'layouts.admin');
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(string $id): void
    {
        $brand = (new Brand())->find((int) $id);

        if ($brand === null) {
            Session::flash('error', 'That brand no longer exists.');
            $this->redirect('/admin/brands');
        }

        $this->form($brand);
    }

    private function form(?array $brand): void
    {
        $this->view('admin.brands.form', [
            'pageTitle' => $brand === null ? 'New brand' : 'Edit brand',
            'brand'     => $brand,
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function store(): void
    {
        $data = $this->validate();

        if ($data === null) {
            $this->redirect('/admin/brands/create');
        }

        $model = new Brand();
        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name']);

        $id = $model->create($data);

        Session::clearOld();
        Session::flash('success', 'Brand created.');
        $this->redirect('/admin/brands/' . $id . '/edit');
    }

    public function update(string $id): void
    {
        $brandId  = (int) $id;
        $model    = new Brand();
        $existing = $model->find($brandId);

        if ($existing === null) {
            Session::flash('error', 'That brand no longer exists.');
            $this->redirect('/admin/brands');
        }

        $data = $this->validate();

        if ($data === null) {
            $this->redirect('/admin/brands/' . $brandId . '/edit');
        }

        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name'], $brandId);

        if (empty($data['logo'])) {
            $data['logo'] = $existing['logo'];
        } else {
            (new Uploader(config('uploads')))->delete($existing['logo']);
        }

        $model->updateById($brandId, $data);

        Session::clearOld();
        Session::flash('success', 'Brand updated.');
        $this->redirect('/admin/brands/' . $brandId . '/edit');
    }

    public function destroy(string $id): void
    {
        $brandId = (int) $id;
        $model   = new Brand();
        $brand   = $model->find($brandId);

        if ($brand === null) {
            Session::flash('error', 'That brand no longer exists.');
            $this->redirect('/admin/brands');
        }

        (new Uploader(config('uploads')))->delete($brand['logo']);
        $model->deleteById($brandId);

        Session::flash('success', 'Brand deleted. Its products remain, without a brand.');
        $this->redirect('/admin/brands');
    }

    private function validate(): ?array
    {
        $validator = new Validator($_POST);
        $validator->require('name', 'Brand name')->max('name', 150, 'Brand name');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            return null;
        }

        $logo = '';
        if (Uploader::present($_FILES['logo'] ?? null)) {
            try {
                $logo = (new Uploader(config('uploads')))->store($_FILES['logo'], 'brands');
            } catch (RuntimeException $e) {
                Session::flash('error', 'Logo not saved — ' . $e->getMessage());
            }
        }

        return [
            'name'        => $validator->value('name'),
            'description' => $validator->value('description') ?: null,
            'logo'        => $logo ?: null,
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
