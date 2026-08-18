<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

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

        $this->view('site.home', [
            'seo'         => $seo,
            'bodyClass'   => 'page-home',
            'transparentHeader' => true,
            'pillars'     => $categories->tree(),
            'featured'    => $products->featured(6),
            'latest'      => $products->latest(4),
            'brands'      => (new Brand())->active(),
        ]);
    }
}
