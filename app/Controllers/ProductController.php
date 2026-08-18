<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
use App\Core\Session;
use App\Models\Category;
use App\Models\Product;

class ProductController extends Controller
{
    public function show(string $slug): void
    {
        $model   = new Product();
        $product = $model->bySlug($slug);

        if ($product === null) {
            $this->notFound('That product is no longer listed. Browse the catalog or send us a quote request and we will source it.');
        }

        $productId = (int) $product['id'];

        // Count a view once per session per product.
        $seen = Session::get('_viewed_products', []);
        if (!in_array($productId, $seen, true)) {
            $model->incrementViews($productId);
            $seen[] = $productId;
            Session::set('_viewed_products', $seen);
        }

        $pillar = null;
        if (!empty($product['category_parent_id'])) {
            $pillar = (new Category())->find((int) $product['category_parent_id']);
        } elseif (!empty($product['category_id'])) {
            $pillar = (new Category())->find((int) $product['category_id']);
        }

        $images = $model->images($productId);

        // Lead with the product, then the category — that is the phrase people
        // actually search ("anatomic snaffle bridle" before "bridles").
        $title = $product['meta_title']
            ?: ($product['name'] . (!empty($product['category_name']) ? ' — ' . $product['category_name'] : ''));

        $description = $product['meta_desc']
            ?: excerpt($product['short_desc'] ?: $product['description'], 155);

        $trail = ['Home' => url('/')];
        $trail['Catalog'] = url('/shop');
        if ($pillar !== null) {
            $trail[$pillar['name']] = url('/shop/' . $pillar['slug']);
        }
        if (!empty($product['category_slug']) && ($pillar['slug'] ?? null) !== $product['category_slug']) {
            $trail[$product['category_name']] = url('/shop/' . $product['category_slug']);
        }
        $trail[$product['name']] = null;

        $seo = Seo::make()
            ->title($title)
            ->description($description)
            ->type('product')
            ->canonical(url('/product/' . $product['slug']))
            ->image(isset($images[0]['path']) ? image($images[0]['path']) : null, $product['name'])
            ->schema(Schema::product($product, $images))
            ->schema(Schema::breadcrumbs($trail));

        $this->view('site.product', [
            'seo'       => $seo,
            'bodyClass' => 'page-product',
            'product'   => $product,
            'images'    => $images,
            'variants'  => $model->variants($productId),
            'related'   => $model->related($productId, $product['category_id'] ? (int) $product['category_id'] : null, 4),
            'pillar'    => $pillar,
        ]);
    }
}
