<?php
namespace App\Controllers;

use App\Core\Controller;
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

        $this->view('site.product', [
            'pageTitle' => $product['meta_title'] ?: $product['name'],
            'metaDesc'  => $product['meta_desc'] ?: excerpt($product['short_desc'] ?: $product['description'], 155),
            'bodyClass' => 'page-product',
            'product'   => $product,
            'images'    => $model->images($productId),
            'variants'  => $model->variants($productId),
            'related'   => $model->related($productId, $product['category_id'] ? (int) $product['category_id'] : null, 4),
            'pillar'    => $pillar,
        ]);
    }
}
