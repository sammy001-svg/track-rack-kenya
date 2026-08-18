<?php
namespace App\Models;

use App\Core\Model;

class Quote extends Model
{
    protected string $table = 'quotes';

    public const STATUSES = [
        'new'       => 'New',
        'in_review' => 'In review',
        'quoted'    => 'Quoted',
        'won'       => 'Won',
        'closed'    => 'Closed',
    ];

    /**
     * Persist a quote request and its line items in one transaction.
     *
     * @param array $customer name,email,phone,location,discipline,notes,ip_address
     * @param array $items    Rows from QuoteList::detailed()
     * @return array{id:int, reference:string}
     */
    public function createWithItems(array $customer, array $items): array
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $reference = $this->generateReference();

            $quoteId = $db->insert('quotes', [
                'reference'     => $reference,
                'customer_name' => $customer['name'],
                'email'         => $customer['email'],
                'phone'         => $customer['phone'],
                'location'      => $customer['location'] ?? null,
                'discipline'    => $customer['discipline'] ?? null,
                'notes'         => $customer['notes'] ?? null,
                'status'        => 'new',
                'ip_address'    => $customer['ip_address'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = $item['product'];

                $db->insert('quote_items', [
                    'quote_id'     => $quoteId,
                    'product_id'   => (int) $product['id'],
                    'product_name' => $product['name'],
                    'product_sku'  => $product['sku'] ?? null,
                    'variant'      => $item['variant'] !== '' ? $item['variant'] : null,
                    'quantity'     => (int) $item['quantity'],
                    'unit_price'   => ((int) ($product['price_visible'] ?? 0) === 1) ? $product['price'] : null,
                ]);
            }

            $db->commit();

            return ['id' => $quoteId, 'reference' => $reference];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /** TR-260818-4F2A */
    private function generateReference(): string
    {
        $prefix = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) setting('quote_prefix', 'TR'))) ?: 'TR';

        do {
            $reference = sprintf('%s-%s-%s', $prefix, date('ymd'), strtoupper(bin2hex(random_bytes(2))));
            $exists    = (int) $this->db()->value(
                'SELECT COUNT(*) FROM `quotes` WHERE `reference` = :ref',
                ['ref' => $reference]
            );
        } while ($exists > 0);

        return $reference;
    }

    public function items(int $quoteId): array
    {
        return $this->db()->all(
            'SELECT qi.*, p.slug AS product_slug
             FROM `quote_items` qi
             LEFT JOIN `products` p ON p.id = qi.product_id
             WHERE qi.`quote_id` = :id
             ORDER BY qi.`id` ASC',
            ['id' => $quoteId]
        );
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = '`status` = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(`reference` LIKE :q1 OR `customer_name` LIKE :q2 OR `email` LIKE :q3 OR `phone` LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `quotes` WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT q.*, (SELECT COUNT(*) FROM `quote_items` qi WHERE qi.quote_id = q.id) AS item_count
             FROM `quotes` q
             WHERE {$whereSql}
             ORDER BY q.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    /** Every quote belonging to a storefront account. */
    public function forCustomer(int $customerId): array
    {
        return $this->db()->all(
            'SELECT q.*, (SELECT COUNT(*) FROM `quote_items` qi WHERE qi.quote_id = q.id) AS item_count
             FROM `quotes` q WHERE q.`customer_id` = :id ORDER BY q.`created_at` DESC',
            ['id' => $customerId]
        );
    }

    public function countByStatus(): array
    {
        $rows   = $this->db()->all('SELECT `status`, COUNT(*) AS total FROM `quotes` GROUP BY `status`');
        $counts = array_fill_keys(array_keys(self::STATUSES), 0);

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    public function recent(int $limit = 6): array
    {
        $limit = max(1, min(50, $limit));

        return $this->db()->all(
            "SELECT q.*, (SELECT COUNT(*) FROM `quote_items` qi WHERE qi.quote_id = q.id) AS item_count
             FROM `quotes` q ORDER BY q.`created_at` DESC LIMIT {$limit}"
        );
    }

    /** Most-requested products across all quotes. */
    public function topRequestedProducts(int $limit = 8): array
    {
        $limit = max(1, min(50, $limit));

        return $this->db()->all(
            "SELECT qi.`product_name`, SUM(qi.`quantity`) AS units, COUNT(DISTINCT qi.`quote_id`) AS requests
             FROM `quote_items` qi
             GROUP BY qi.`product_name`
             ORDER BY requests DESC, units DESC
             LIMIT {$limit}"
        );
    }

    /** Quote counts for the last $days days, for the dashboard chart. */
    public function dailyCounts(int $days = 14): array
    {
        $days = max(1, min(90, $days));

        $rows = $this->db()->all(
            "SELECT DATE(`created_at`) AS day, COUNT(*) AS total
             FROM `quotes`
             WHERE `created_at` >= DATE_SUB(CURDATE(), INTERVAL {$days} DAY)
             GROUP BY DATE(`created_at`)"
        );

        $byDay = [];
        foreach ($rows as $row) {
            $byDay[$row['day']] = (int) $row['total'];
        }

        $series = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-{$i} days"));
            $series[] = ['date' => $date, 'total' => $byDay[$date] ?? 0];
        }

        return $series;
    }
}
