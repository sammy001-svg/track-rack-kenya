<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
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
    /**
     * Where the catalogue's old sections went when it was regrouped to follow
     * the shop's own departments. Anyone holding a link to one of these — a
     * bookmark, a WhatsApp message, a search result — lands on the section
     * that replaced it instead of a dead end.
     */
    private const RETIRED_CATEGORIES = [
        'stable'                   => 'yard',
        'footwear'                 => 'clothing',
        'breeches-tights'          => 'clothing',
        'gloves-accessories'       => 'clothing',
        'riding-jackets-vests'     => 'safety-equipment',
        'helmets-head-protection'  => 'safety-equipment',
        'saddles-accessories'      => 'saddles',
        'bridles-bits-reins'       => 'bridles-reins',
        'saddle-pads-blankets'     => 'numnahs-saddlepads',
        'halters-lead-ropes'       => 'headcollars-lead-ropes',
        'boots-bandages'           => 'horse-boots',
        'horse-health-supplements' => 'feed-supplements',
        'grooming-kits-supplies'   => 'grooming-equipment',
        'stable-equipment'         => 'yard-equipment',
        'leather-care-maintenance' => 'leather-care',
        'first-aid-skin-care'      => 'first-aid',
        'fly-control'              => 'fly-repellent',
        // Renamed in the October 2026 regrouping.
        'digestive'                => 'gut-health',
        'farriery'                 => 'shoes-nails',
        'shampoos-conditioners'    => 'shampoo',
        'mane-tail-care'           => 'detanglers',
        'skin-sun-care'            => 'skin-care',
        'hoof-coat-supplements'    => 'hoof-supplements',
        // Regrouped from the client's sizes & colours sheet.
        'bandages-wraps'           => 'first-aid',
        'brushing-overreach-boots' => 'horse-boots',
        'numnahs-squares'          => 'shaped-numnahs',
    ];

    public function category(string $slug): void
    {
        $category = (new Category())->bySlug($slug);

        if ($category === null) {
            // Only redirect if the replacement is actually there; otherwise
            // fall through to the 404 rather than bouncing to another one.
            $moved = self::RETIRED_CATEGORIES[$slug] ?? null;

            if ($moved !== null && (new Category())->bySlug($moved) !== null) {
                $this->redirect('/shop/' . $moved);
            }

            $this->notFound('That category is no longer part of our catalogue.');
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

        // The department this page sits under, however deep the category is.
        $pillar = $category !== null ? $categoryModel->departmentOf($category) : null;

        // The dropdown offers what is one level below where you are: the
        // sections of a department, or the sub-categories of a section.
        $subCategories = $category !== null
            ? $categoryModel->childrenWithCounts((int) $category['id'])
            : [];

        // Whatever those options belong to, so the "All …" entry names it.
        $subCategoryOf = $subCategories !== [] ? $category : null;

        if ($subCategories === [] && $pillar !== null) {
            $subCategories = $categoryModel->childrenWithCounts((int) $pillar['id']);
            $subCategoryOf = $pillar;
        }

        $heading = $category['name'] ?? 'The Catalogue';
        $tagline = $category['tagline'] ?? 'Rider, Horse and Yard — the complete Tack Rack range.';

        // The catalogue and the three department pages are browsed by section
        // rather than as one long list: a filter is something you reach for,
        // not the only way to find a girth. Inside a single section, or once a
        // filter is applied, the flat grid is the right answer.
        // A page is a leaf when nothing sits beneath it — not when it happens
        // to have a parent, now that sections can have sub-categories of
        // their own.
        $isLeaf  = $category !== null && $categoryModel->children((int) $category['id']) === [];
        $groups  = (!$this->isFiltered($filters, $subCategoryId) && !$isLeaf)
            ? $this->groupByCategory($category, $categoryModel, $productModel)
            : null;

        // The section between the department and a sub-category, for the trail.
        $parentCategory = null;
        if ($category !== null && $category['parent_id'] !== null
            && (int) $category['parent_id'] !== (int) ($pillar['id'] ?? 0)) {
            $parentCategory = $categoryModel->find((int) $category['parent_id']);
        }

        $this->view('site.shop', [
            'groups'        => $groups,
            'parentCategory' => $parentCategory,
            'subCategoryOf' => $subCategoryOf,
            'seo'           => $this->buildSeo($category, $pillar, $filters, $subCategoryId, $result),
            'bodyClass'     => 'page-shop',
            'heading'       => $heading,
            'tagline'       => $tagline,
            'category'      => $category,
            'pillar'        => $pillar,
            'pillars'       => $categoryModel->pillars(),
            'subCategories' => $subCategories,
            'products'      => $result['items'],
            'total'         => $result['total'],
            'pages'         => $result['pages'],
            'page'          => $result['page'],
            'filters'       => $filters,
            'activeSubId'   => $subCategoryId,
        ]);
    }

    /** Has the visitor narrowed the catalogue in any way? */
    private function isFiltered(array $filters, int $subCategoryId): bool
    {
        return ($filters['q'] ?? '') !== ''
            || !empty($filters['stock'])
            || ($filters['sort'] ?? '') !== ''
            || $subCategoryId > 0;
    }

    /**
     * The catalogue arranged the way the shop is: departments, then the
     * sections inside them, each showing a few products and a link to the rest.
     *
     * @return array<int, array{department: ?array, sections: array}>
     */
    private function groupByCategory(?array $category, Category $categories, Product $products): array
    {
        // A department page shows only its own sections, and more of each,
        // because it has the room.
        $departments = $category !== null ? [$category] : $categories->pillars();
        $perSection  = $category !== null ? 8 : 4;

        // The catalogue's front page introduces each department rather than
        // reproducing it: twenty-two shelves of products in one scroll is not
        // a page anyone reads to the end. A department page lists all of its
        // own, and the filter bar still reaches every section from anywhere.
        $perDepartment = $category !== null ? PHP_INT_MAX : 5;

        $groups = [];

        foreach ($departments as $department) {
            $sections = [];

            $candidates = $categories->childrenWithCounts((int) $department['id']);

            if ($perDepartment !== PHP_INT_MAX) {
                usort($candidates, static fn (array $a, array $b): int
                    => (int) $b['product_count'] <=> (int) $a['product_count']);
            }

            foreach ($candidates as $section) {
                if ((int) $section['product_count'] === 0) {
                    continue;
                }

                if (count($sections) >= $perDepartment) {
                    break;
                }

                $result = $products->catalog(
                    ['category_ids' => $categories->descendantIds((int) $section['id'])],
                    1,
                    $perSection
                );

                if ($result['items'] === []) {
                    continue;
                }

                $sections[] = [
                    'category' => $section,
                    'products' => $result['items'],
                    'total'    => (int) $result['total'],
                    'url'      => url('/shop/' . $section['slug']),
                ];
            }

            // Anything filed directly against the section itself rather than
            // one of its sub-categories, so nothing is hidden by the grouping.
            if ($sections !== []) {
                $own = $products->catalog(
                    ['category_ids' => [(int) $department['id']]],
                    1,
                    $perSection
                );

                if ($own['items'] !== []) {
                    $sections[] = [
                        'category' => ['name' => 'Also in ' . $department['name'], 'slug' => $department['slug'], 'tagline' => null],
                        'products' => $own['items'],
                        'total'    => (int) $own['total'],
                        // The one URL that shows this category's own products
                        // without those of its sub-categories.
                        'url'      => url('/shop/' . $department['slug']) . '?category=' . (int) $department['id'],
                    ];
                }
            }

            if ($sections !== []) {
                $groups[] = [
                    // On a department page the heading above is already the
                    // department, so it is not repeated over the sections.
                    'department' => $category !== null ? null : $department,
                    'sections'   => $sections,
                ];
            }
        }

        return $groups;
    }

    /**
     * Search-engine handling for a catalog page.
     *
     * Filter combinations (search terms, availability, sort) generate an
     * effectively unlimited number of near-identical URLs, so those are marked
     * noindex and pointed back at the clean category page. Genuine pagination
     * stays indexable with a self-referencing canonical.
     */
    private function buildSeo(?array $category, ?array $pillar, array $filters, int $subCategoryId, array $result): Seo
    {
        $cleanPath = $category !== null ? '/shop/' . $category['slug'] : '/shop';

        $isFiltered = $this->isFiltered($filters, $subCategoryId);

        $page = (int) $result['page'];

        $title = $category['meta_title']
            ?? ($category !== null ? $category['name'] : 'Shop Equestrian Supplies Online');

        $description = $category['meta_desc']
            ?? ($category['description']
                ?? 'Browse the full Tack Rack catalogue — saddlery, rider apparel and yard essentials for every discipline ridden in Kenya.');

        // Page 2 onwards gets its own title so results are not duplicates.
        if (!$isFiltered && $page > 1) {
            $title .= ' — Page ' . $page;
        }

        $seo = Seo::make()->title($title)->description($description);

        if ($isFiltered) {
            $seo->noindex()->canonical(url($cleanPath));
        } else {
            $seo->canonical($page > 1 ? url($cleanPath) . '?page=' . $page : url($cleanPath));

            $trail = ['Home' => url('/'), 'Catalogue' => url('/shop')];

            if ($category !== null) {
                if ($pillar !== null && $pillar['id'] !== $category['id']) {
                    $trail[$pillar['name']] = url('/shop/' . $pillar['slug']);
                }
                $trail[$category['name']] = null;
            }

            $seo->schema(Schema::breadcrumbs($trail))
                ->schema(Schema::collection($category ?? ['name' => 'The Catalogue'], $result['items']));
        }

        if ($category !== null && !empty($category['image'])) {
            $seo->image(image($category['image']), $category['name']);
        } elseif ($pillar !== null && pillar_art($pillar['slug']) !== 'product') {
            $seo->image(asset('/assets/img/pillar-' . pillar_art($pillar['slug']) . '.jpg'), $pillar['name']);
        }

        return $seo;
    }
}
