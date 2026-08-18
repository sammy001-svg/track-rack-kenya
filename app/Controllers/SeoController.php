<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Database;

class SeoController extends Controller
{
    /**
     * CMS pages that are deliberately noindex. Listing a noindex URL in the
     * sitemap is a contradictory signal and shows up in Search Console as
     * "Submitted URL marked noindex", so they are excluded here too.
     *
     * Keep in step with PageController::show().
     */
    private const NOINDEX_PAGES = ['privacy-policy', 'terms-of-service'];

    /** GET /sitemap.xml */
    public function sitemap(): void
    {
        $db  = Database::instance();
        $now = date('Y-m-d');

        // Only pages that are genuinely indexable belong here.
        $urls = [
            ['loc' => url('/'),                          'priority' => '1.0', 'freq' => 'weekly',  'lastmod' => $now],
            ['loc' => url('/shop'),                      'priority' => '0.9', 'freq' => 'daily',   'lastmod' => $now],
            ['loc' => url('/heritage'),                  'priority' => '0.7', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => url('/services'),                  'priority' => '0.8', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => url('/services/saddle-fitting'),   'priority' => '0.9', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => url('/services/repairs'),          'priority' => '0.9', 'freq' => 'monthly', 'lastmod' => $now],
            ['loc' => url('/contact'),                   'priority' => '0.7', 'freq' => 'yearly',  'lastmod' => $now],
        ];

        foreach ($db->all('SELECT `slug`, `updated_at` FROM `categories` WHERE `is_active` = 1 ORDER BY `sort_order`') as $row) {
            $urls[] = [
                'loc'      => url('/shop/' . $row['slug']),
                'priority' => '0.8',
                'freq'     => 'weekly',
                'lastmod'  => substr((string) $row['updated_at'], 0, 10),
            ];
        }

        foreach ($db->all('SELECT `slug`, `updated_at` FROM `products` WHERE `is_active` = 1 ORDER BY `id`') as $row) {
            $urls[] = [
                'loc'      => url('/product/' . $row['slug']),
                'priority' => '0.7',
                'freq'     => 'weekly',
                'lastmod'  => substr((string) $row['updated_at'], 0, 10),
            ];
        }

        foreach ($db->all('SELECT `slug`, `updated_at` FROM `pages` WHERE `is_active` = 1') as $row) {
            // Heritage has its own entry above; legal pages are noindex.
            if ($row['slug'] === 'heritage' || in_array($row['slug'], self::NOINDEX_PAGES, true)) {
                continue;
            }

            $urls[] = [
                'loc'      => url('/page/' . $row['slug']),
                'priority' => '0.5',
                'freq'     => 'monthly',
                'lastmod'  => substr((string) $row['updated_at'], 0, 10),
            ];
        }

        header('Content-Type: application/xml; charset=utf-8');

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $url) {
            echo "  <url>\n";
            echo '    <loc>' . e($url['loc']) . "</loc>\n";
            echo '    <lastmod>' . e($url['lastmod']) . "</lastmod>\n";
            echo '    <changefreq>' . e($url['freq']) . "</changefreq>\n";
            echo '    <priority>' . e($url['priority']) . "</priority>\n";
            echo "  </url>\n";
        }

        echo '</urlset>';
        exit;
    }

    /** GET /robots.txt */
    public function robots(): void
    {
        header('Content-Type: text/plain; charset=utf-8');

        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /account',
            'Disallow: /checkout',
            'Disallow: /quote',
            'Disallow: /request-a-quote',
            'Allow: /',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        echo implode("\n", $lines);
        exit;
    }
}
