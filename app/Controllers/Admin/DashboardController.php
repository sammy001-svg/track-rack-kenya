<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Models\Category;
use App\Models\Message;
use App\Models\Product;
use App\Models\Quote;

class DashboardController extends Controller
{
    public function index(): void
    {
        $db      = Database::instance();
        $quotes  = new Quote();
        $counts  = $quotes->countByStatus();
        $series  = $quotes->dailyCounts(14);
        $peak    = max(1, max(array_column($series, 'total')));

        $this->view('admin.dashboard', [
            'pageTitle' => 'Dashboard',
            'stats' => [
                'quotes_total'   => array_sum($counts),
                'quotes_new'     => $counts['new'],
                'quotes_open'    => $counts['new'] + $counts['in_review'] + $counts['quoted'],
                'quotes_won'     => $counts['won'],
                'products_total' => (int) $db->value('SELECT COUNT(*) FROM products'),
                'products_live'  => (int) $db->value('SELECT COUNT(*) FROM products WHERE is_active = 1'),
                'messages_new'   => (new Message())->unreadCount(),
                'categories'     => (int) $db->value('SELECT COUNT(*) FROM categories'),
            ],
            'statusCounts'  => $counts,
            'series'        => $series,
            'peak'          => $peak,
            'recentQuotes'  => $quotes->recent(6),
            'topRequested'  => $quotes->topRequestedProducts(6),
            'mostViewed'    => $db->all(
                'SELECT id, name, slug, views FROM products WHERE is_active = 1 AND views > 0
                 ORDER BY views DESC LIMIT 6'
            ),
            'emptyCategories' => $db->all(
                'SELECT c.name, c.slug FROM categories c
                 LEFT JOIN products p ON p.category_id = c.id AND p.is_active = 1
                 WHERE c.parent_id IS NOT NULL
                 GROUP BY c.id HAVING COUNT(p.id) = 0
                 ORDER BY c.name ASC'
            ),
            'missingImages' => (int) $db->value(
                'SELECT COUNT(*) FROM products p
                 WHERE p.is_active = 1
                   AND NOT EXISTS (SELECT 1 FROM product_images pi WHERE pi.product_id = p.id)'
            ),
        ], 'layouts.admin');
    }
}
