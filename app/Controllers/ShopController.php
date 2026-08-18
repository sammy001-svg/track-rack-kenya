<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ShopController extends Controller
{
    /** The full catalog, with the filter bar applied. */
    public function index(): void
    {
        $this->renderCatalog(null);
    }

    /** A pillar (rider/horse/stable) or one of its child categories. */
    public function category(string $slug): void
    {
        $category = (new Category())->bySlug($slug);

        if ($category === null) {
            $this->notFound('That category is no longer part of our catalog.');
        }

        $this->renderCatalog($category);
    }

    private function renderCatalog(?array $category): void
    {
        $categoryModel = new Category();
        $productModel  = new Product();

        $page    = max(1, (int) ($_GET['page'] ?? 1));
        $perPage = (int) config('per_page.shop', 12);

        $filters = [
            'q'        => trim((string) ($_GET['q'] ?? '')),
            'brand_id' => (int) ($_GET['brand'] ?? 0) ?: null,
            'stock'    => in_array($_GET['stock'] ?? '', ['in_stock', 'low_stock', 'on_order', 'out_of_stock'], true)
                ? $_GET['stock'] : null,
            'sort'     => in_array($_GET['sort'] ?? '', ['name_asc', 'name_desc', 'newest', 'popular'], true)
                ? $_GET['sort'] : '',
        ];

        // A sub-category filter chosen from the dropdown overrides the URL segment.
        $subCategoryId = (int) ($_GET['category'] ?? 0);

        if ($subCategoryId > 0) {
            $filters['category_ids'] = [$subCategoryId];
        } elseif ($category !== null) {
            $filters['category_ids'] = $categoryModel->descendantIds((int) $category['id']);
        }

        $result = $productModel->catalog($filters, $page, $perPage);

        // Which sub-categories to offer in the dropdown.
        $pillar = null;
        if ($category !== null) {
            $pillar = $category['parent_id'] === null
                ? $category
                : $categoryModel->find((int) $category['parent_id']);
        }

        $subCategories = $pillar !== null
            ? $categoryModel->childrenWithCounts((int) $pillar['id'])
            : [];

        $heading = $category['name'] ?? 'The Catalog';
        $tagline = $category['tagline'] ?? 'Rider, Horse and Stable - the complete Tack Rack range.';

        $this->view('site.shop', [
            'seo'           => $this->buildSeo($category, $pillar, $filters, $subCategoryId, $result),
            'bodyClass'     => 'page-shop',
            'heading'       => $heading,
            'tagline'       => $tagline,
            'category'      => $category,
            'pillar'        => $pillar,
            'pillars'       => $categoryModel->pillars(),
            'subCategories' => $subCategories,
            'brands'        => (new Brand())->active(),
            'products'      => $result['items'],
            'total'         => $result['total'],
            'pages'         => $result['pages'],
            'page'          => $result['page'],
            'filters'       => $filters,
            'activeSubId'   => $subCategoryId,
        ]);
    }

    /**
     * Search-engine handling for a catalog page.
     *
     * Filter combinations (search terms, brand, availability, sort) generate an
     * effectively unlimited number of near-identical URLs, so those are marked
     * noindex and pointed back at the clean category page. Genuine pagination
     * stays indexable with a self-referencing canonical.
     */
    private function buildSeo(?array $category, ?array $pillar, array $filters, int $subCategoryId, array $result): Seo
    {
        $cleanPath = $category !== null ? '/shop/' . $category['slug'] : '/shop';

        $isFiltered = ($filters['q'] ?? '') !== ''
            || !empty($filters['brand_id'])
            || !empty($filters['stock'])
            || ($filters['sort'] ?? '') !== ''
            || $subCategoryId > 0;

        $page = (int) $result['page'];

        $title = $category['meta_title']
            ?? ($category !== null ? $category['name'] : 'Shop Equestrian Supplies Online');

        $description = $category['meta_desc']
            ?? ($category['description']
                ?? 'Browse the full Tack Rack catalog — saddlery, rider apparel and yard essentials for every discipline ridden in Kenya.');

        // Page 2 onwards gets its own title so results are not duplicates.
        if (!$isFiltered && $page > 1) {
            $title .= ' — Page ' . $page;
        }

        $seo = Seo::make()->title($title)->description($description);

        if ($isFiltered) {
            $seo->noindex()->canonical(url($cleanPath));
        } else {
            $seo->canonical($page > 1 ? url($cleanPath) . '?page=' . $page : url($cleanPath));

            $trail = ['Home' => url('/'), 'Catalog' => url('/shop')];

            if ($category !== null) {
                if ($pillar !== null && $pillar['id'] !== $category['id']) {
                    $trail[$pillar['name']] = url('/shop/' . $pillar['slug']);
                }
                $trail[$category['name']] = null;
            }

            $seo->schema(Schema::breadcrumbs($trail))
                ->schema(Schema::collection($category ?? ['name' => 'The Catalog'], $result['items']));
        }

        if ($category !== null && !empty($category['image'])) {
            $seo->image(image($category['image']), $category['name']);
        } elseif ($pillar !== null && in_array($pillar['slug'], ['rider', 'horse', 'stable'], true)) {
            $seo->image(asset('/assets/img/pillar-' . $pillar['slug'] . '.jpg'), $pillar['name']);
        }

        return $seo;
    }
}
