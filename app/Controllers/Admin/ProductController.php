<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Core\Validator;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use RuntimeException;

class ProductController extends Controller
{
    private const STOCK_STATUSES = ['in_stock', 'low_stock', 'on_order', 'out_of_stock'];

    public function index(): void
    {
        $model = new Product();

        $filters = [
            'q'           => trim((string) ($_GET['q'] ?? '')),
            'category_id' => (int) ($_GET['category'] ?? 0) ?: null,
            'is_active'   => isset($_GET['status']) && $_GET['status'] !== '' ? (int) $_GET['status'] : '',
        ];

        $result = $model->adminList($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.products.index', [
            'pageTitle'  => 'Products',
            'products'   => $result['items'],
            'total'      => $result['total'],
            'pages'      => $result['pages'],
            'page'       => $result['page'],
            'filters'    => $filters,
            'categories' => (new Category())->flatList(),
        ], 'layouts.admin');
    }

    public function create(): void
    {
        $this->form(null);
    }

    public function edit(string $id): void
    {
        $product = (new Product())->find((int) $id);

        if ($product === null) {
            Session::flash('error', 'That product no longer exists.');
            $this->redirect('/admin/products');
        }

        $this->form($product);
    }

    private function form(?array $product): void
    {
        $model = new Product();

        $this->view('admin.products.form', [
            'pageTitle'  => $product === null ? 'New product' : 'Edit product',
            'product'    => $product,
            'images'     => $product === null ? [] : $model->images((int) $product['id']),
            'variants'   => $product === null ? [] : $model->variants((int) $product['id']),
            'categories' => (new Category())->flatList(),
            'brands'     => (new Brand())->allWithCounts(),
            'errors'     => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function store(): void
    {
        $data = $this->validate(null);

        if ($data === null) {
            $this->redirect('/admin/products/create');
        }

        $model = new Product();
        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name']);

        $id = $model->create($data);

        $this->saveVariants($model, $id);
        $this->saveUploads($model, $id);

        Session::clearOld();
        Session::flash('success', 'Product created.');
        $this->redirect('/admin/products/' . $id . '/edit');
    }

    public function update(string $id): void
    {
        $productId = (int) $id;
        $model     = new Product();

        if ($model->find($productId) === null) {
            Session::flash('error', 'That product no longer exists.');
            $this->redirect('/admin/products');
        }

        $data = $this->validate($productId);

        if ($data === null) {
            $this->redirect('/admin/products/' . $productId . '/edit');
        }

        $data['slug'] = $model->uniqueSlug(trim((string) ($_POST['slug'] ?? '')) ?: $data['name'], $productId);

        $model->updateById($productId, $data);

        $this->saveVariants($model, $productId);
        $this->saveUploads($model, $productId);

        Session::clearOld();
        Session::flash('success', 'Product updated.');
        $this->redirect('/admin/products/' . $productId . '/edit');
    }

    public function destroy(string $id): void
    {
        $productId = (int) $id;
        $model     = new Product();
        $product   = $model->find($productId);

        if ($product === null) {
            Session::flash('error', 'That product no longer exists.');
            $this->redirect('/admin/products');
        }

        // Remove the image files before the rows cascade away.
        $uploader = new Uploader(config('uploads'));
        foreach ($model->images($productId) as $image) {
            $uploader->delete($image['path']);
        }

        $model->deleteById($productId);

        Session::flash('success', 'Product deleted. Any quote already referencing it keeps its record.');
        $this->redirect('/admin/products');
    }

    // ---- Images --------------------------------------------------------

    public function uploadImage(string $id): void
    {
        $productId = (int) $id;
        $model     = new Product();

        if ($model->find($productId) === null) {
            Session::flash('error', 'That product no longer exists.');
            $this->redirect('/admin/products');
        }

        $this->saveUploads($model, $productId, true);
        $this->redirect('/admin/products/' . $productId . '/edit');
    }

    public function deleteImage(string $id): void
    {
        $model = new Product();
        $image = $model->findImage((int) $id);

        if ($image === null) {
            Session::flash('error', 'That image no longer exists.');
            $this->back('/admin/products');
        }

        (new Uploader(config('uploads')))->delete($image['path']);
        $model->deleteImage((int) $id);

        Session::flash('success', 'Image removed.');
        $this->redirect('/admin/products/' . (int) $image['product_id'] . '/edit');
    }

    public function makePrimary(string $id): void
    {
        $model = new Product();
        $image = $model->findImage((int) $id);

        if ($image === null) {
            Session::flash('error', 'That image no longer exists.');
            $this->back('/admin/products');
        }

        $model->setPrimaryImage((int) $image['product_id'], (int) $id);

        Session::flash('success', 'Primary image updated.');
        $this->redirect('/admin/products/' . (int) $image['product_id'] . '/edit');
    }

    // ---- Helpers -------------------------------------------------------

    /** @return array|null Validated column data, or null when validation failed. */
    private function validate(?int $ignoreId): ?array
    {
        $validator = new Validator($_POST);
        $validator->require('name', 'Product name')->max('name', 200, 'Product name')
            ->max('sku', 80, 'SKU')
            ->max('short_desc', 500, 'Short description')
            ->max('meta_title', 190, 'Meta title')
            ->max('meta_desc', 300, 'Meta description')
            ->in('stock_status', self::STOCK_STATUSES, 'Stock status');

        $price = trim((string) ($_POST['price'] ?? ''));

        if ($price !== '') {
            $validator->numeric('price', 'Price');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            return null;
        }

        $categoryId = (int) ($_POST['category_id'] ?? 0);
        $brandId    = (int) ($_POST['brand_id'] ?? 0);

        return [
            'category_id'    => $categoryId > 0 ? $categoryId : null,
            'brand_id'       => $brandId > 0 ? $brandId : null,
            'name'           => $validator->value('name'),
            'sku'            => $validator->value('sku') ?: null,
            'short_desc'     => $validator->value('short_desc') ?: null,
            'description'    => $validator->value('description') ?: null,
            'specifications' => $validator->value('specifications') ?: null,
            'sizing_guide'   => $validator->value('sizing_guide') ?: null,
            'price'          => $price !== '' ? round((float) $price, 2) : null,
            'price_visible'  => isset($_POST['price_visible']) ? 1 : 0,
            'stock_status'   => $validator->value('stock_status') ?: 'in_stock',
            'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
            'is_new'         => isset($_POST['is_new']) ? 1 : 0,
            'is_active'      => isset($_POST['is_active']) ? 1 : 0,
            'sort_order'     => (int) ($_POST['sort_order'] ?? 0),
            'meta_title'     => $validator->value('meta_title') ?: null,
            'meta_desc'      => $validator->value('meta_desc') ?: null,
        ];
    }

    private function saveVariants(Product $model, int $productId): void
    {
        if (!isset($_POST['variant_label']) || !is_array($_POST['variant_label'])) {
            return;
        }

        $labels = $_POST['variant_label'];
        $values = $_POST['variant_value'] ?? [];
        $rows   = [];

        foreach ($labels as $index => $label) {
            $rows[] = ['label' => (string) $label, 'value' => (string) ($values[$index] ?? '')];
        }

        $model->replaceVariants($productId, $rows);
    }

    /** Handle both the single-file and multi-file image inputs. */
    private function saveUploads(Product $model, int $productId, bool $flashEmpty = false): void
    {
        if (!isset($_FILES['images'])) {
            if ($flashEmpty) {
                Session::flash('error', 'No image was selected.');
            }
            return;
        }

        $uploader = new Uploader(config('uploads'));
        $files    = $_FILES['images'];
        $count    = is_array($files['name']) ? count($files['name']) : 0;
        $saved    = 0;
        $failures = [];

        for ($i = 0; $i < $count; $i++) {
            $file = [
                'name'     => $files['name'][$i],
                'type'     => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'error'    => $files['error'][$i],
                'size'     => $files['size'][$i],
            ];

            if (!Uploader::present($file)) {
                continue;
            }

            try {
                $path = $uploader->store($file, 'products');
                $model->addImage($productId, $path, null, false);
                $saved++;
            } catch (RuntimeException $e) {
                $failures[] = $files['name'][$i] . ': ' . $e->getMessage();
            }
        }

        if ($saved > 0) {
            Session::flash('success', $saved . ' image' . ($saved === 1 ? '' : 's') . ' uploaded.');
        } elseif ($flashEmpty && $failures === []) {
            Session::flash('error', 'No image was selected.');
        }

        foreach ($failures as $failure) {
            Session::flash('error', $failure);
        }
    }
}
