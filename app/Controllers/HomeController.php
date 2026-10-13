<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = new Category();
        $products   = new Product();

        $seo = Seo::make()
            ->title(setting('seo_home_title', 'Equestrian Supplies & Saddlery in Nairobi'))
            ->description(setting('seo_home_desc', setting('site_intro')))
            ->canonical(url('/'));

        // Each department with the sections inside it, so the home page can
        // offer a way straight to "Hoof Care" instead of only to "Yard".
        $departments = [];
        foreach ($categories->pillars() as $pillar) {
            $sections = array_values(array_filter(
                $categories->childrenWithCounts((int) $pillar['id']),
                static fn (array $c): bool => (int) $c['product_count'] > 0
            ));

            if ($sections !== []) {
                $departments[] = ['pillar' => $pillar, 'sections' => $sections];
            }
        }

        $this->view('site.home', [
            'seo'         => $seo,
            'bodyClass'   => 'page-home',
            'transparentHeader' => true,
            'departments' => $departments,
            'services'    => (new Service())->active(),
            // Three pieces to stand for the catalogue. showcase() spreads
            // its picks across categories, so they are not three girths.
            'selected'    => array_slice($products->showcase(6), 0, 6),
        ]);
    }
}
