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
        $model = new Category();

        // Anything that may legally be a parent: departments and sections, but
        // not sub-categories (three levels is the limit), and never the
        // category itself or one of its own descendants.
        $blocked = $category !== null ? $model->descendantIds((int) $category['id']) : [];

        $parentOptions = array_values(array_filter(
            $model->flatList(),
            static fn (array $c): bool => $c['depth'] < 2 && !in_array((int) $c['id'], $blocked, true)
        ));

        $this->view('admin.categories.form', [
            'pageTitle'     => $category === null ? 'New category' : 'Edit category',
            'category'      => $category,
            'pillars'       => $model->pillars(false),
            'parentOptions' => $parentOptions,
            'errors'        => Session::errors(),
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

        // A category may not be filed under one of its own descendants — that
        // would cut the branch off the tree.
        if ($data['parent_id'] !== null
            && in_array((int) $data['parent_id'], $model->descendantIds($categoryId), true)) {
            Session::flash('error', 'A category cannot sit inside itself or one of its own sub-categories.');
            $data['parent_id'] = $existing['parent_id'] ?? null;
        }

        // Three levels is the limit: department > section > sub-category. So a
        // category can sit one level below a department, and anything that has
        // sub-categories of its own has to stay a section.
        if ($data['parent_id'] !== null) {
            $parent = $model->find((int) $data['parent_id']);

            if ($parent !== null) {
                $parentDepth = (int) count($model->ancestorIds((int) $parent['id']));
                $hasChildren = $model->children($categoryId, false) !== [];
                $wouldBe     = $parentDepth + 1;

                if ($wouldBe > 2 || ($hasChildren && $wouldBe > 1)) {
                    Session::flash('error', $hasChildren
                        ? 'This category has sub-categories of its own, so it must stay directly under a department.'
                        : 'Categories go three levels deep at most: department, section, sub-category.');
                    $data['parent_id'] = $existing['parent_id'] ?? null;
                }
            }
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
