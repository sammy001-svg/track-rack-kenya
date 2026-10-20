<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Database;
use App\Core\ImageProcessor;
use App\Core\Session;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;

class ImportController extends Controller
{
    /** The columns a CSV may contain, in the order the template writes them. */
    private const COLUMNS = [
        'sku', 'name', 'category', 'brand', 'short_desc', 'description',
        'specifications', 'sizing_guide', 'price', 'price_visible', 'buyable',
        'stock_status', 'stock_qty', 'is_featured', 'is_new', 'is_active', 'sort_order',
    ];

    public function index(): void
    {
        $this->view('admin.import', [
            'pageTitle'  => 'Import & export',
            'categories' => (new Category())->flatList(),
            'brands'     => (new Brand())->allWithCounts(),
            'imageNote'  => ImageProcessor::statusNote(),
            'report'     => Session::pull('_import_report'),
        ], 'layouts.admin');
    }

    /**
     * POST /admin/import/catalogue — apply database/catalog-import.sql.
     *
     * The host has no command line and the file is 185 KB of SQL, which makes
     * phpMyAdmin the only other way in. This runs it against whatever database
     * the site is connected to, so bringing the catalogue across is a button
     * rather than an export, an upload and a prayer.
     *
     * It replaces the catalogue: categories, brands, products, their images
     * and their sizes. It does not touch users, settings, pages, orders,
     * quotes, bookings or repairs, and an order line keeps its own product
     * name and SKU, so history still reads correctly afterwards.
     *
     * A CSV of what is there now is written to storage/backups first, so the
     * work being replaced is recoverable even if nobody took a database dump.
     */
    public function applyCatalogue(): void
    {
        $file = dirname(__DIR__, 3) . '/database/catalog-import.sql';

        if (!is_file($file) || filesize($file) < 1024) {
            Session::flash('error', 'database/catalog-import.sql is missing from this deployment. Pull the latest code first.');
            $this->redirect('/admin/import');
        }

        if (trim((string) ($_POST['confirm'] ?? '')) !== 'REPLACE') {
            Session::flash('error', 'Type REPLACE to confirm. Nothing has been changed.');
            $this->redirect('/admin/import');
        }

        $db  = Database::instance();
        $pdo = $db->pdo();

        $before = [
            'categories' => (int) $db->value('SELECT COUNT(*) FROM categories'),
            'products'   => (int) $db->value('SELECT COUNT(*) FROM products'),
        ];

        $backup = $this->backupCatalogue();

        try {
            /*
             * The file is several hundred statements in one string, and the
             * connection the rest of the site uses will not run more than one
             * at a time. This opens its own, with multi-statements allowed,
             * and steps through every result set so a failure half way down is
             * raised rather than swallowed.
             */
            $cfg = config('db');

            $runner = new \PDO(
                sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']),
                $cfg['username'],
                $cfg['password'],
                [
                    \PDO::ATTR_ERRMODE              => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
                ]
            );

            $statement = $runner->query(file_get_contents($file));

            if ($statement !== false) {
                while ($statement->nextRowset()) {
                    // nothing to read; we only want the errors
                }
            }
        } catch (\PDOException $e) {
            Session::flash('error', 'The catalogue was not applied: ' . $e->getMessage()
                . ' Nothing else has been changed. Your backup is at ' . $backup . '.');
            $this->redirect('/admin/import');
        }

        $after = [
            'categories' => (int) $db->value('SELECT COUNT(*) FROM categories'),
            'products'   => (int) $db->value('SELECT COUNT(*) FROM products'),
            'images'     => (int) $db->value('SELECT COUNT(*) FROM product_images'),
            'variants'   => (int) $db->value('SELECT COUNT(*) FROM product_variants'),
        ];

        Session::flash('success', sprintf(
            'Catalogue applied: %d categories and %d products, with %d photographs and %d sizes and colours. '
            . 'It replaced %d categories and %d products; a copy of those is in %s.',
            $after['categories'], $after['products'], $after['images'], $after['variants'],
            $before['categories'], $before['products'], $backup
        ));

        $this->redirect('/admin/import');
    }

    /**
     * POST /admin/import/pending — apply database/apply-pending.sql.
     *
     * Wording, not stock: the page and service copy that the review minutes
     * corrected, and the settings behind it. Each statement in that file
     * matches the exact sentence it replaces, so copy edited here since is
     * left alone and running it twice changes nothing the second time.
     */
    public function applyPending(): void
    {
        $file = dirname(__DIR__, 3) . '/database/apply-pending.sql';

        if (!is_file($file) || filesize($file) < 512) {
            Session::flash('error', 'database/apply-pending.sql is missing from this deployment. Pull the latest code first.');
            $this->redirect('/admin/import');
        }

        $db = Database::instance();

        $before = $db->value("SELECT `value` FROM `settings` WHERE `key_name` = 'fitting_fee_note'");

        try {
            $cfg = config('db');

            $runner = new \PDO(
                sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s',
                    $cfg['host'], $cfg['port'], $cfg['database'], $cfg['charset']),
                $cfg['username'],
                $cfg['password'],
                [
                    \PDO::ATTR_ERRMODE               => \PDO::ERRMODE_EXCEPTION,
                    \PDO::MYSQL_ATTR_MULTI_STATEMENTS => true,
                ]
            );

            $statement = $runner->query(file_get_contents($file));

            if ($statement !== false) {
                while ($statement->nextRowset()) {
                    // stepping through so a failure half way down is raised
                }
            }
        } catch (\PDOException $e) {
            Session::flash('error', 'The copy changes were not applied: ' . $e->getMessage());
            $this->redirect('/admin/import');
        }

        $after = $db->value("SELECT `value` FROM `settings` WHERE `key_name` = 'fitting_fee_note'");

        Session::flash('success', $before === $after
            ? 'Copy changes applied. Everything in that file was already in place, so nothing changed.'
            : 'Copy changes applied. The catalogue spelling and the saddle fitting wording are now correct.');

        $this->redirect('/admin/import');
    }

    /** Write the catalogue as it stands to a CSV, and return the path. */
    private function backupCatalogue(): string
    {
        $dir = dirname(__DIR__, 3) . '/storage/backups';

        if (!is_dir($dir) && !@mkdir($dir, 0775, true) && !is_dir($dir)) {
            return '(could not be written)';
        }

        $name = 'catalogue-before-' . date('Y-m-d-His') . '.csv';
        $out  = fopen($dir . '/' . $name, 'w');

        if ($out === false) {
            return '(could not be written)';
        }

        fputcsv($out, ['sku', 'name', 'slug', 'category', 'brand', 'short_desc', 'description', 'stock_status', 'image']);

        $rows = Database::instance()->all(
            'SELECT p.sku, p.name, p.slug, c.name AS category, b.name AS brand,
                    p.short_desc, p.description, p.stock_status,
                    (SELECT i.path FROM product_images i WHERE i.product_id = p.id ORDER BY i.sort_order LIMIT 1) AS image
               FROM products p
               LEFT JOIN categories c ON c.id = p.category_id
               LEFT JOIN brands b ON b.id = p.brand_id
              ORDER BY p.name'
        );

        foreach ($rows as $row) {
            fputcsv($out, array_values($row));
        }

        fclose($out);

        return 'storage/backups/' . $name;
    }

    /** GET /admin/import/template — a CSV with the headers and one example row. */
    public function template(): void
    {
        $this->streamCsv('tackrack-product-template.csv', static function ($out): void {
            fputcsv($out, self::COLUMNS);
            fputcsv($out, [
                'TR-EX-001',
                'Example Snaffle Bridle',
                'Bridles, Bits & Reins',
                'Heritage Saddlery',
                'One-line description shown on the product card.',
                "Full description.\nUse \\n for line breaks, or wrap the cell in quotes.",
                "Leather: vegetable-tanned\nFittings: stainless steel",
                'Measure from the corner of the mouth over the poll.',
                '12500',
                '1',
                '1',
                'in_stock',
                '4',
                '0',
                '1',
                '1',
                '0',
            ]);
        });
    }

    /** GET /admin/export/products — the live catalog as CSV. */
    public function exportProducts(): void
    {
        $rows = Database::instance()->all(
            'SELECT p.*, c.name AS category_name, b.name AS brand_name
             FROM `products` p
             LEFT JOIN `categories` c ON c.id = p.category_id
             LEFT JOIN `brands` b ON b.id = p.brand_id
             ORDER BY p.id ASC'
        );

        $this->streamCsv('tackrack-products-' . date('Y-m-d') . '.csv', static function ($out) use ($rows): void {
            fputcsv($out, self::COLUMNS);

            foreach ($rows as $row) {
                fputcsv($out, [
                    $row['sku'],
                    $row['name'],
                    $row['category_name'],
                    $row['brand_name'],
                    $row['short_desc'],
                    $row['description'],
                    $row['specifications'],
                    $row['sizing_guide'],
                    $row['price'],
                    $row['price_visible'],
                    $row['buyable'] ?? 0,
                    $row['stock_status'],
                    $row['stock_qty'] ?? '',
                    $row['is_featured'],
                    $row['is_new'],
                    $row['is_active'],
                    $row['sort_order'],
                ]);
            }
        });
    }

    /** GET /admin/export/quotes */
    public function exportQuotes(): void
    {
        $rows = Database::instance()->all(
            'SELECT q.`reference`, q.`customer_name`, q.`email`, q.`phone`, q.`location`,
                    q.`discipline`, q.`status`, q.`quoted_total`, q.`created_at`,
                    GROUP_CONCAT(CONCAT(qi.quantity, " x ", qi.product_name) SEPARATOR " | ") AS items
             FROM `quotes` q
             LEFT JOIN `quote_items` qi ON qi.quote_id = q.id
             GROUP BY q.id
             ORDER BY q.`created_at` DESC'
        );

        $this->streamCsv('tackrack-quotes-' . date('Y-m-d') . '.csv', static function ($out) use ($rows): void {
            fputcsv($out, ['Reference', 'Customer', 'Email', 'Phone', 'Location', 'Discipline', 'Status', 'Quoted total', 'Received', 'Items']);

            foreach ($rows as $row) {
                fputcsv($out, array_values($row));
            }
        });
    }

    /** GET /admin/export/orders */
    public function exportOrders(): void
    {
        $rows = Database::instance()->all(
            'SELECT o.`reference`, o.`customer_name`, o.`email`, o.`phone`, o.`delivery_method`,
                    o.`delivery_town`, o.`subtotal`, o.`delivery_cost`, o.`total`, o.`amount_paid`,
                    o.`status`, o.`payment_status`, o.`created_at`
             FROM `orders` o ORDER BY o.`created_at` DESC'
        );

        $this->streamCsv('tackrack-orders-' . date('Y-m-d') . '.csv', static function ($out) use ($rows): void {
            fputcsv($out, ['Reference', 'Customer', 'Email', 'Phone', 'Delivery', 'Town', 'Subtotal', 'Delivery cost', 'Total', 'Paid', 'Status', 'Payment', 'Placed']);

            foreach ($rows as $row) {
                fputcsv($out, array_values($row));
            }
        });
    }

    /** POST /admin/import/products */
    public function importProducts(): void
    {
        if (!isset($_FILES['csv']) || $_FILES['csv']['error'] !== UPLOAD_ERR_OK) {
            Session::flash('error', 'Choose a CSV file to import.');
            $this->redirect('/admin/import');
        }

        $handle = @fopen($_FILES['csv']['tmp_name'], 'r');

        if ($handle === false) {
            Session::flash('error', 'That file could not be read.');
            $this->redirect('/admin/import');
        }

        $dryRun = isset($_POST['dry_run']);

        // Skip a UTF-8 BOM if Excel added one.
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);

        if ($header === false) {
            fclose($handle);
            Session::flash('error', 'That file appears to be empty.');
            $this->redirect('/admin/import');
        }

        $map = [];
        foreach ($header as $index => $name) {
            $key = strtolower(trim((string) $name));
            if (in_array($key, self::COLUMNS, true)) {
                $map[$key] = $index;
            }
        }

        if (!isset($map['name'])) {
            fclose($handle);
            Session::flash('error', 'The CSV must have a "name" column. Download the template for the expected format.');
            $this->redirect('/admin/import');
        }

        $report = $this->processRows($handle, $map, $dryRun);
        fclose($handle);

        Session::set('_import_report', $report);

        $verb = $dryRun ? 'would be' : 'were';
        Session::flash(
            $report['errors'] === [] ? 'success' : 'error',
            sprintf(
                '%d product(s) %s created, %d %s updated, %d skipped.%s',
                $report['created'], $verb, $report['updated'], $verb, $report['skipped'],
                $dryRun ? ' Nothing was saved — this was a dry run.' : ''
            )
        );

        $this->redirect('/admin/import');
    }

    // =================================================================

    private function processRows($handle, array $map, bool $dryRun): array
    {
        $productModel  = new Product();
        $categoryModel = new Category();
        $brandModel    = new Brand();

        // Resolve names to ids once, case-insensitively.
        $categories = [];
        foreach ($categoryModel->allWithCounts() as $row) {
            $categories[mb_strtolower($row['name'])] = (int) $row['id'];
        }

        $brands = [];
        foreach ($brandModel->allWithCounts() as $row) {
            $brands[mb_strtolower($row['name'])] = (int) $row['id'];
        }

        $report = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => [], 'rows' => []];
        $line   = 1;

        while (($row = fgetcsv($handle)) !== false) {
            $line++;

            if ($row === [null] || implode('', array_map('strval', $row)) === '') {
                continue; // blank line
            }

            $get = static function (string $key, string $default = '') use ($row, $map): string {
                return isset($map[$key]) && isset($row[$map[$key]]) ? trim((string) $row[$map[$key]]) : $default;
            };

            $name = $get('name');

            if ($name === '') {
                $report['skipped']++;
                $report['errors'][] = "Line {$line}: no product name — skipped.";
                continue;
            }

            $categoryName = $get('category');
            $categoryId   = $categoryName === '' ? null : ($categories[mb_strtolower($categoryName)] ?? null);

            if ($categoryName !== '' && $categoryId === null) {
                $report['errors'][] = "Line {$line}: category \"{$categoryName}\" not found — left uncategorised.";
            }

            $brandName = $get('brand');
            $brandId   = $brandName === '' ? null : ($brands[mb_strtolower($brandName)] ?? null);

            if ($brandName !== '' && $brandId === null) {
                $report['errors'][] = "Line {$line}: brand \"{$brandName}\" not found — left blank.";
            }

            $price  = $get('price');
            $stock  = $get('stock_status', 'in_stock');
            $stockQ = $get('stock_qty');

            if (!in_array($stock, ['in_stock', 'low_stock', 'on_order', 'out_of_stock'], true)) {
                $stock = 'in_stock';
            }

            $data = [
                'category_id'    => $categoryId,
                'brand_id'       => $brandId,
                'name'           => mb_substr($name, 0, 200),
                'sku'            => $get('sku') ?: null,
                'short_desc'     => mb_substr($get('short_desc'), 0, 500) ?: null,
                'description'    => $get('description') ?: null,
                'specifications' => $get('specifications') ?: null,
                'sizing_guide'   => $get('sizing_guide') ?: null,
                'price'          => is_numeric($price) ? round((float) $price, 2) : null,
                'price_visible'  => $this->flag($get('price_visible')),
                'buyable'        => $this->flag($get('buyable')),
                'stock_status'   => $stock,
                'stock_qty'      => $stockQ !== '' && is_numeric($stockQ) ? (int) $stockQ : null,
                'is_featured'    => $this->flag($get('is_featured')),
                'is_new'         => $this->flag($get('is_new')),
                'is_active'      => $this->flag($get('is_active', '1'), 1),
                'sort_order'     => (int) $get('sort_order', '0'),
            ];

            // Match on SKU where present, otherwise on the exact name.
            $existing = null;
            $sku      = $get('sku');

            if ($sku !== '') {
                $existing = $productModel->findBy('sku', $sku);
            }

            if ($existing === null) {
                $existing = $productModel->findBy('name', $name);
            }

            if ($existing !== null) {
                if (!$dryRun) {
                    $data['slug'] = $productModel->uniqueSlug($existing['slug'] ?: $name, (int) $existing['id']);
                    $productModel->updateById((int) $existing['id'], $data);
                }
                $report['updated']++;
                $report['rows'][] = ['line' => $line, 'name' => $name, 'action' => 'update'];
            } else {
                if (!$dryRun) {
                    $data['slug'] = $productModel->uniqueSlug($name);
                    $productModel->create($data);
                }
                $report['created']++;
                $report['rows'][] = ['line' => $line, 'name' => $name, 'action' => 'create'];
            }
        }

        // Keep the report a sensible size for the session.
        $report['rows']   = array_slice($report['rows'], 0, 200);
        $report['errors'] = array_slice($report['errors'], 0, 60);
        $report['dry_run'] = $dryRun;

        return $report;
    }

    private function flag(string $value, int $default = 0): int
    {
        $value = mb_strtolower(trim($value));

        if ($value === '') {
            return $default;
        }

        return in_array($value, ['1', 'y', 'yes', 'true', 'on'], true) ? 1 : 0;
    }

    private function streamCsv(string $filename, callable $writer): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');

        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM so Excel reads UTF-8 correctly

        $writer($out);

        fclose($out);
        exit;
    }
}
