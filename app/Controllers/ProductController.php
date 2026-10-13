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
    /**
     * Where a product went when the October 2026 review renamed it.
     *
     * Brand names came out of the titles, two whips became one product, and a
     * few lines were dropped — all of which changes the URL. Anyone holding
     * the old one, including Google, is sent to the right place with a 301
     * rather than a 404. A value beginning with /shop is a category: the
     * product itself is gone, so the nearest shelf is the honest answer.
     */
    private const MOVED = [
        // Renamed
        'thorowgood-leather-saddle'                    => 'thorowgood-saddle',
        'fleece-lined-headcollar'                      => 'lunging-cavesson',
        'leather-girth-buckle-guards'                  => 'rubber-guards',
        'arma-anatomic-brushing-boots'                 => 'anatomic-brushing-boots',
        'arma-brushing-boots-navy'                     => 'brushing-boots-navy',
        'arma-overreach-boots-red'                     => 'overreach-boots-red',
        'back-to-basics-muscle-up'                     => 'muscle-up',
        'coligone-coliflex-gastro-joint-care'          => 'coliflex-gastro-joint-care',
        'endeavon-flex-o-joint'                        => 'flex-o-joint',
        'equimins-devils-claw-root-herb'               => 'devils-claw-root-herb',
        'equine-america-buteless'                      => 'buteless',
        'equine-america-cortaflex-ha'                  => 'cortaflex-ha',
        'equine-america-glucosamine-hcl-12-000'        => 'glucosamine-hcl-12-000',
        'equimins-arnica-witch-hazel'                  => 'arnica-witch-hazel',
        'equimins-devils-claw-gel'                     => 'devils-claw-gel',
        'equimins-leg-gel'                             => 'leg-gel',
        'equimins-msm-cream'                           => 'msm-cream',
        'moores-herbal-cooling-gel'                    => 'herbal-cooling-gel',
        'moores-dubbin'                                => 'dubbin',
        'moores-glycerine-leather-saddle-soap'         => 'glycerine-leather-saddle-soap',
        'moores-leather-dressing'                      => 'leather-dressing',
        'moores-leather-soap'                          => 'leather-soap',
        'calro-organic-neem-oil-shampoo-conditioner'   => 'organic-neem-oil-shampoo-conditioner',

        // Merged into one schooling whip
        'braided-schooling-whip'                       => 'schooling-whip',
        'dressage-schooling-whip'                      => 'schooling-whip',

        // Withdrawn — sent to the shelf they were on
        'black-leather-jump-saddle'                    => '/shop/saddles',
        'trail-hoof-boot'                              => '/shop/brushing-overreach-boots',
        'weatherbeeta-fleece-bandages'                 => '/shop/bandages-wraps',
    ];

    public function show(string $slug): void
    {
        $model   = new Product();
        $product = $model->bySlug($slug);

        if ($product === null) {
            $this->moved($slug, $model);

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
     * Send a renamed product's old URL to its new one, permanently.
     *
     * Only redirects when the destination is really there, so a stale entry
     * here falls through to the 404 rather than bouncing to another one.
     */
    private function moved(string $slug, Product $model): void
    {
        $to = self::MOVED[$slug] ?? null;

        if ($to === null) {
            return;
        }

        if ($to[0] === '/') {
            header('Location: ' . url($to), true, 301);
            exit;
        }

        if ($model->bySlug($to) !== null) {
            header('Location: ' . url('/product/' . $to), true, 301);
            exit;
        }
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
