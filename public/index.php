<?php
/**
 * Front controller. Every request that is not a real file on disk is
 * routed through here by public/.htaccess.
 */

require dirname(__DIR__) . '/app/bootstrap.php';

use App\Core\Auth;
use App\Core\Csrf;
use App\Core\CustomerAuth;
use App\Core\Router;

// Reject any POST that does not carry a valid CSRF token.
//
// The M-Pesa callback is the one exemption: Safaricom posts to it server to
// server and has no session or token. It is protected instead by requiring a
// CheckoutRequestID that we generated and are still waiting on.
if (CURRENT_PATH !== '/checkout/mpesa/callback') {
    Csrf::verify();
}

// Security headers.
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: strict-origin-when-cross-origin');
header('X-XSS-Protection: 0');

$router = new Router();

$auth     = [[Auth::class, 'requireLogin']];
$admin    = [[Auth::class, 'requireAdmin']];
$customer = [[CustomerAuth::class, 'requireLogin']];

// =====================================================================
//  Public site
// =====================================================================
$router->get('/',                    'App\Controllers\HomeController@index');
$router->get('/shop',                'App\Controllers\ShopController@index');
$router->get('/shop/{slug}',         'App\Controllers\ShopController@category');
$router->get('/product/{slug}',      'App\Controllers\ProductController@show');
$router->get('/heritage',            'App\Controllers\PageController@heritage');
$router->get('/search',              'App\Controllers\ShopController@index');

// Quote list + request
$router->post('/quote/add',          'App\Controllers\QuoteController@add');
$router->get('/quote',               'App\Controllers\QuoteController@index');
$router->post('/quote/update',       'App\Controllers\QuoteController@update');
$router->post('/quote/remove',       'App\Controllers\QuoteController@remove');
$router->post('/quote/clear',        'App\Controllers\QuoteController@clear');
$router->get('/request-a-quote',     'App\Controllers\QuoteController@form');
$router->post('/request-a-quote',    'App\Controllers\QuoteController@submit');
$router->get('/quote/sent/{reference}', 'App\Controllers\QuoteController@confirmation');

// Contact
$router->get('/contact',             'App\Controllers\ContactController@index');
$router->post('/contact',            'App\Controllers\ContactController@submit');

// CMS pages (how-to-order, quote-process, privacy-policy, terms-of-service)
$router->get('/page/{slug}',         'App\Controllers\PageController@show');

// Discoverability
$router->get('/sitemap.xml',         'App\Controllers\SeoController@sitemap');
$router->get('/robots.txt',          'App\Controllers\SeoController@robots');

// ---------------------------------------------------------------------
//  Services — saddle fitting & workshop repairs
// ---------------------------------------------------------------------
$router->get('/services',                    'App\Controllers\ServiceController@index');
$router->get('/services/saddle-fitting',     'App\Controllers\ServiceController@fitting');
$router->post('/services/saddle-fitting',    'App\Controllers\ServiceController@submitFitting');
$router->get('/services/booked/{reference}', 'App\Controllers\ServiceController@bookingConfirmation');
$router->get('/services/repairs',            'App\Controllers\ServiceController@repairs');
$router->post('/services/repairs',           'App\Controllers\ServiceController@submitRepair');
$router->get('/services/repair-sent/{reference}', 'App\Controllers\ServiceController@repairConfirmation');

// ---------------------------------------------------------------------
//  Customer accounts
// ---------------------------------------------------------------------
$router->get('/account/login',        'App\Controllers\AccountController@showLogin');
$router->post('/account/login',       'App\Controllers\AccountController@login');
$router->get('/account/register',     'App\Controllers\AccountController@showRegister');
$router->post('/account/register',    'App\Controllers\AccountController@register');
$router->post('/account/logout',      'App\Controllers\AccountController@logout');
$router->get('/account/forgot',       'App\Controllers\AccountController@showForgot');
$router->post('/account/forgot',      'App\Controllers\AccountController@sendReset');
$router->get('/account/reset/{token}',  'App\Controllers\AccountController@showReset');
$router->post('/account/reset/{token}', 'App\Controllers\AccountController@completeReset');

$router->get('/account',                  'App\Controllers\AccountController@dashboard',  $customer);
$router->get('/account/orders',           'App\Controllers\AccountController@orders',     $customer);
$router->get('/account/orders/{reference}', 'App\Controllers\AccountController@orderDetail', $customer);
$router->get('/account/quotes',           'App\Controllers\AccountController@quotes',     $customer);
$router->get('/account/activity',         'App\Controllers\AccountController@activity',   $customer);
$router->get('/account/profile',          'App\Controllers\AccountController@profile',    $customer);
$router->post('/account/profile',         'App\Controllers\AccountController@updateProfile', $customer);
$router->get('/account/horses',           'App\Controllers\AccountController@horses',     $customer);
$router->post('/account/horses',          'App\Controllers\AccountController@saveHorse',  $customer);
$router->post('/account/horses/delete',   'App\Controllers\AccountController@deleteHorse', $customer);

// ---------------------------------------------------------------------
//  Checkout & payment
// ---------------------------------------------------------------------
$router->get('/checkout',                    'App\Controllers\CheckoutController@index');
$router->post('/checkout',                   'App\Controllers\CheckoutController@place');
$router->get('/checkout/pay/{reference}',    'App\Controllers\CheckoutController@pay');
$router->post('/checkout/mpesa',             'App\Controllers\CheckoutController@mpesa');
$router->post('/checkout/mpesa/callback',    'App\Controllers\CheckoutController@mpesaCallback');
$router->get('/checkout/status/{reference}', 'App\Controllers\CheckoutController@status');
$router->get('/checkout/done/{reference}',   'App\Controllers\CheckoutController@done');

// =====================================================================
//  Admin
// =====================================================================
$router->get('/admin/login',         'App\Controllers\Admin\AuthController@showLogin');
$router->post('/admin/login',        'App\Controllers\Admin\AuthController@login');
$router->post('/admin/logout',       'App\Controllers\Admin\AuthController@logout');

$router->get('/admin',               'App\Controllers\Admin\DashboardController@index', $auth);

// Products
$router->get('/admin/products',              'App\Controllers\Admin\ProductController@index',  $auth);
$router->get('/admin/products/create',       'App\Controllers\Admin\ProductController@create', $auth);
$router->post('/admin/products/store',       'App\Controllers\Admin\ProductController@store',  $auth);
$router->get('/admin/products/{id}/edit',    'App\Controllers\Admin\ProductController@edit',   $auth);
$router->post('/admin/products/{id}/update', 'App\Controllers\Admin\ProductController@update', $auth);
$router->post('/admin/products/{id}/delete', 'App\Controllers\Admin\ProductController@destroy', $auth);
$router->post('/admin/products/{id}/images', 'App\Controllers\Admin\ProductController@uploadImage', $auth);
$router->post('/admin/images/{id}/delete',   'App\Controllers\Admin\ProductController@deleteImage', $auth);
$router->post('/admin/images/{id}/primary',  'App\Controllers\Admin\ProductController@makePrimary', $auth);

// Categories
$router->get('/admin/categories',              'App\Controllers\Admin\CategoryController@index',  $auth);
$router->get('/admin/categories/create',       'App\Controllers\Admin\CategoryController@create', $auth);
$router->post('/admin/categories/store',       'App\Controllers\Admin\CategoryController@store',  $auth);
$router->get('/admin/categories/{id}/edit',    'App\Controllers\Admin\CategoryController@edit',   $auth);
$router->post('/admin/categories/{id}/update', 'App\Controllers\Admin\CategoryController@update', $auth);
$router->post('/admin/categories/{id}/delete', 'App\Controllers\Admin\CategoryController@destroy', $auth);

// Brands
$router->get('/admin/brands',              'App\Controllers\Admin\BrandController@index',  $auth);
$router->get('/admin/brands/create',       'App\Controllers\Admin\BrandController@create', $auth);
$router->post('/admin/brands/store',       'App\Controllers\Admin\BrandController@store',  $auth);
$router->get('/admin/brands/{id}/edit',    'App\Controllers\Admin\BrandController@edit',   $auth);
$router->post('/admin/brands/{id}/update', 'App\Controllers\Admin\BrandController@update', $auth);
$router->post('/admin/brands/{id}/delete', 'App\Controllers\Admin\BrandController@destroy', $auth);

// Quotes
$router->get('/admin/quotes',              'App\Controllers\Admin\QuoteController@index',  $auth);
$router->get('/admin/quotes/{id}',         'App\Controllers\Admin\QuoteController@show',   $auth);
$router->post('/admin/quotes/{id}/update', 'App\Controllers\Admin\QuoteController@update', $auth);
$router->post('/admin/quotes/{id}/delete', 'App\Controllers\Admin\QuoteController@destroy', $auth);
$router->get('/admin/quotes/{id}/print',   'App\Controllers\Admin\QuoteController@printable', $auth);

// Messages
$router->get('/admin/messages',            'App\Controllers\Admin\MessageController@index', $auth);
$router->get('/admin/messages/{id}',       'App\Controllers\Admin\MessageController@show',  $auth);
$router->post('/admin/messages/{id}/delete', 'App\Controllers\Admin\MessageController@destroy', $auth);

// Pages
$router->get('/admin/pages',               'App\Controllers\Admin\PageController@index',  $auth);
$router->get('/admin/pages/{id}/edit',     'App\Controllers\Admin\PageController@edit',   $auth);
$router->post('/admin/pages/{id}/update',  'App\Controllers\Admin\PageController@update', $auth);

// Saddle fittings
$router->get('/admin/bookings',              'App\Controllers\Admin\BookingController@index',  $auth);
$router->get('/admin/bookings/{id}',         'App\Controllers\Admin\BookingController@show',   $auth);
$router->post('/admin/bookings/{id}/update', 'App\Controllers\Admin\BookingController@update', $auth);
$router->post('/admin/bookings/{id}/delete', 'App\Controllers\Admin\BookingController@destroy', $auth);

// Workshop repairs
$router->get('/admin/repairs',               'App\Controllers\Admin\RepairController@index',  $auth);
$router->get('/admin/repairs/{id}',          'App\Controllers\Admin\RepairController@show',   $auth);
$router->post('/admin/repairs/{id}/update',  'App\Controllers\Admin\RepairController@update', $auth);
$router->post('/admin/repairs/{id}/photos',  'App\Controllers\Admin\RepairController@uploadPhoto', $auth);
$router->post('/admin/repairs/{id}/delete',  'App\Controllers\Admin\RepairController@destroy', $auth);
$router->post('/admin/repair-photos/{id}/delete', 'App\Controllers\Admin\RepairController@deletePhoto', $auth);

// Orders & payments
$router->get('/admin/orders',                'App\Controllers\Admin\OrderController@index',  $auth);
$router->get('/admin/orders/{id}',           'App\Controllers\Admin\OrderController@show',   $auth);
$router->post('/admin/orders/{id}/update',   'App\Controllers\Admin\OrderController@update', $auth);
$router->post('/admin/orders/{id}/payment',  'App\Controllers\Admin\OrderController@recordPayment', $auth);
$router->post('/admin/orders/{id}/delete',   'App\Controllers\Admin\OrderController@destroy', $auth);
$router->get('/admin/orders/{id}/print',     'App\Controllers\Admin\OrderController@printable', $auth);

// Customers
$router->get('/admin/customers',              'App\Controllers\Admin\CustomerController@index',  $auth);
$router->get('/admin/customers/{id}',         'App\Controllers\Admin\CustomerController@show',   $auth);
$router->post('/admin/customers/{id}/update', 'App\Controllers\Admin\CustomerController@update', $auth);
$router->post('/admin/customers/{id}/delete', 'App\Controllers\Admin\CustomerController@destroy', $admin);

// Services
$router->get('/admin/services',              'App\Controllers\Admin\ServiceController@index',  $auth);
$router->get('/admin/services/{id}/edit',    'App\Controllers\Admin\ServiceController@edit',   $auth);
$router->post('/admin/services/{id}/update', 'App\Controllers\Admin\ServiceController@update', $auth);

// Import & export
$router->get('/admin/import',                'App\Controllers\Admin\ImportController@index',    $auth);
$router->get('/admin/import/template',       'App\Controllers\Admin\ImportController@template', $auth);
$router->post('/admin/import/products',      'App\Controllers\Admin\ImportController@importProducts', $auth);
$router->get('/admin/export/products',       'App\Controllers\Admin\ImportController@exportProducts', $auth);
$router->get('/admin/export/quotes',         'App\Controllers\Admin\ImportController@exportQuotes',   $auth);
$router->get('/admin/export/orders',         'App\Controllers\Admin\ImportController@exportOrders',   $auth);

// Settings + users (admin role only)
$router->get('/admin/settings',            'App\Controllers\Admin\SettingController@index',  $admin);
$router->post('/admin/settings',           'App\Controllers\Admin\SettingController@update', $admin);
$router->get('/admin/users',               'App\Controllers\Admin\UserController@index',   $admin);
$router->post('/admin/users/store',        'App\Controllers\Admin\UserController@store',   $admin);
$router->post('/admin/users/{id}/update',  'App\Controllers\Admin\UserController@update',  $admin);
$router->post('/admin/users/{id}/delete',  'App\Controllers\Admin\UserController@destroy', $admin);
$router->get('/admin/account',             'App\Controllers\Admin\UserController@account', $auth);
$router->post('/admin/account',            'App\Controllers\Admin\UserController@updateAccount', $auth);

// =====================================================================
$router->notFound(static function (): void {
    (new App\Controllers\PageController())->missing();
});

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', CURRENT_PATH);
