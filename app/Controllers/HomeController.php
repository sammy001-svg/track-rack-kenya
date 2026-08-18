<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = new Category();
        $products   = new Product();

        $this->view('site.home', [
            'pageTitle'   => setting('site_tagline', 'Premium Equestrian Gear. Trusted Heritage.'),
            'metaDesc'    => setting('site_intro'),
            'bodyClass'   => 'page-home',
            'transparentHeader' => true,
            'pillars'     => $categories->tree(),
            'featured'    => $products->featured(6),
            'latest'      => $products->latest(4),
            'brands'      => (new Brand())->active(),
        ]);
    }
}
