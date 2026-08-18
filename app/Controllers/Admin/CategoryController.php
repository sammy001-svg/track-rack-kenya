<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Category;
use RuntimeException;

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->view('admin.categories.index', [
            'pageTitle'  => 'Categories',
            'categories' => (new Category())->allWithCounts(),
        ], 'layouts.admin');
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(string $id): void
    {
        $category = (new Category())->find((int) $id);

        if ($category === null) {
            Session::flash('error', 'That category no longer exists.');
            $this->redirect('/admin/categories');
        }

        $this->form($category);
    }

    private function form(?array $category): void
    {
        $this->view('admin.categories.form', [
            'pageTitle' => $category === null ? 'New category' : 'Edit category',
            'category'  => $category,
            'pillars'   => (new Category())->pillars(false),
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function store(): void
    {
        $data = $this->validate(null);

        if ($data === null) {
            $this->redirect('/admin/categories/create');
        }

        $model = new Category();
        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name']);

        $id = $model->create($data);

        Session::clearOld();
        Session::flash('success', 'Category created.');
        $this->redirect('/admin/categories/' . $id . '/edit');
    }

    public function update(string $id): void
    {
        $categoryId = (int) $id;
        $model      = new Category();
        $existing   = $model->find($categoryId);

        if ($existing === null) {
            Session::flash('error', 'That category no longer exists.');
            $this->redirect('/admin/categories');
        }

        $data = $this->validate($categoryId);

        if ($data === null) {
            $this->redirect('/admin/categories/' . $categoryId . '/edit');
        }

        // A category may not become its own parent.
        if ($data['parent_id'] === $categoryId) {
            $data['parent_id'] = null;
        }

        // A pillar with children may not be demoted into a child itself.
        if ($data['parent_id'] !== null && $model->children($categoryId, false) !== []) {
            Session::flash('error', 'This category has sub-categories, so it must stay a top-level section.');
            $data['parent_id'] = null;
        }

        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name'], $categoryId);

        if (!empty($existing['image']) && empty($data['image'])) {
            $data['image'] = $existing['image']; // keep the current image
        }

        $model->updateById($categoryId, $data);

        Session::clearOld();
        Session::flash('success', 'Category updated.');
        $this->redirect('/admin/categories/' . $categoryId . '/edit');
    }

    public function destroy(string $id): void
    {
        $categoryId = (int) $id;
        $model      = new Category();
        $category   = $model->find($categoryId);

        if ($category === null) {
            Session::flash('error', 'That category no longer exists.');
            $this->redirect('/admin/categories');
        }

        $children = $model->children($categoryId, false);

        if ($children !== []) {
            Session::flash('error', 'Move or delete the ' . count($children) . ' sub-categories first.');
            $this->redirect('/admin/categories');
        }

        (new Uploader(config('uploads')))->delete($category['image']);
        $model->deleteById($categoryId);

        Session::flash('success', 'Category deleted. Its products are now uncategorised.');
        $this->redirect('/admin/categories');
    }

    private function validate(?int $ignoreId): ?array
    {
        $validator = new Validator($_POST);
        $validator->require('name', 'Category name')->max('name', 150, 'Category name')
            ->max('tagline', 255, 'Tagline')
            ->max('meta_title', 190, 'Meta title')
            ->max('meta_desc', 300, 'Meta description');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            return null;
        }

        $parentId = (int) ($_POST['parent_id'] ?? 0);

        $image = '';
        if (Uploader::present($_FILES['image'] ?? null)) {
            try {
                $image = (new Uploader(config('uploads')))->store($_FILES['image'], 'categories');
            } catch (RuntimeException $e) {
                Session::flash('error', 'Image not saved — ' . $e->getMessage());
            }
        }

        return [
            'parent_id'   => $parentId > 0 ? $parentId : null,
            'name'        => $validator->value('name'),
            'tagline'     => $validator->value('tagline') ?: null,
            'description' => $validator->value('description') ?: null,
            'meta_title'  => $validator->value('meta_title') ?: null,
            'meta_desc'   => $validator->value('meta_desc') ?: null,
            'image'       => $image ?: null,
            'sort_order'  => (int) ($_POST['sort_order'] ?? 0),
            'is_active'   => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
