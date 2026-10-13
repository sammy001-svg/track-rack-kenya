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
            $this->notFound('That product is no longer listed. Browse the catalogue or send us a quote request and we will source it.');
        }

        $productId = (int) $product['id'];

        // Count a view once per session per product.
        $seen = Session::get('_viewed_products', []);
        if (!in_array($productId, $seen, true)) {
            $model->incrementViews($productId);
            $seen[] = $productId;
            Session::set('_viewed_products', $seen);
        }

        // The department, and the section in between when the product sits in
        // a sub-category — department > section > sub-category.
        $categories = new Category();
        $pillar     = null;
        $section    = null;

        if (!empty($product['category_id'])) {
            $own = $categories->find((int) $product['category_id']);

            if ($own !== null) {
                $pillar = $categories->departmentOf($own);

                if ($own['parent_id'] !== null && (int) $own['parent_id'] !== (int) $pillar['id']) {
                    $section = $categories->find((int) $own['parent_id']);
                }
            }
        }

        $images = $model->images($productId);

        // Lead with the product, then the category — that is the phrase people
        // actually search ("anatomic snaffle bridle" before "bridles").
        $title = $product['meta_title']
            ?: ($product['name'] . (!empty($product['category_name']) ? ' — ' . $product['category_name'] : ''));

        $description = $product['meta_desc'] ?: $this->composeDescription($product);

        $trail = ['Home' => url('/')];
        $trail['Catalogue'] = url('/shop');
        if ($pillar !== null) {
            $trail[$pillar['name']] = url('/shop/' . $pillar['slug']);
        }
        if ($section !== null) {
            $trail[$section['name']] = url('/shop/' . $section['slug']);
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

    /**
     * Build a meta description for a product that has none of its own.
     *
     * Short product copy leaves most of the 158 characters Google will show
     * unused, so where there is room we add the maker and where to buy it —
     * both genuinely useful to someone reading a search result.
     */
    private function composeDescription(array $product): string
    {
        $base = trim(strip_tags((string) ($product['short_desc'] ?: $product['description'])));
        $base = preg_replace('/\s+/u', ' ', $base) ?? $base;

        if ($base === '') {
            $base = $product['name'] . '.';
        }

        if (mb_strlen($base) > 118) {
            return excerpt($base, 155);
        }

        $tail = [];

        if (!empty($product['brand_name'])) {
            $tail[] = 'By ' . $product['brand_name'] . '.';
        }

        $tail[] = 'Available from Tack Rack, Ngong Road, Nairobi.';

        $composed = rtrim($base, '. ') . '. ' . implode(' ', $tail);

        return mb_strlen($composed) <= 158 ? $composed : excerpt($base, 155);
    }
}
