<?php
namespace App\Models;

use App\Core\Model;

class Order extends Model
{
    protected string $table = 'orders';

    public const STATUSES = [
        'pending'    => 'Awaiting payment',
        'confirmed'  => 'Confirmed',
        'processing' => 'Being prepared',
        'dispatched' => 'Dispatched',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
    ];

    public const PAYMENT_STATUSES = [
        'unpaid'   => 'Unpaid',
        'partial'  => 'Part paid',
        'paid'     => 'Paid',
        'refunded' => 'Refunded',
    ];

    public const DELIVERY_METHODS = [
        'collect' => 'Collect from the shop, Ngong Road',
        'nairobi' => 'Delivery within Nairobi',
        'courier' => 'Countrywide courier',
    ];

    /**
     * Create an order and its lines in one transaction.
     *
     * @param array $customer  name,email,phone,delivery_*,notes,customer_id,ip_address
     * @param array $items     Rows from QuoteList::split()['buyable']
     * @return array{id:int, reference:string, total:float}
     */
    public function createWithItems(array $customer, array $items, float $deliveryCost): array
    {
        $db = $this->db();
        $db->beginTransaction();

        try {
            $reference = $this->generateReference();

            $subtotal = 0.0;
            foreach ($items as $item) {
                $subtotal += (float) $item['product']['price'] * $item['quantity'];
            }

            $total = round($subtotal + $deliveryCost, 2);

            $orderId = $db->insert('orders', [
                'reference'        => $reference,
                'customer_id'      => $customer['customer_id'] ?? null,
                'customer_name'    => $customer['name'],
                'email'            => $customer['email'],
                'phone'            => $customer['phone'],
                'delivery_method'  => $customer['delivery_method'],
                'delivery_address' => $customer['delivery_address'] ?? null,
                'delivery_town'    => $customer['delivery_town'] ?? null,
                'subtotal'         => round($subtotal, 2),
                'delivery_cost'    => round($deliveryCost, 2),
                'total'            => $total,
                'status'           => 'pending',
                'payment_status'   => 'unpaid',
                'notes'            => $customer['notes'] ?? null,
                'ip_address'       => $customer['ip_address'] ?? null,
            ]);

            foreach ($items as $item) {
                $product   = $item['product'];
                $unitPrice = (float) $product['price'];

                $db->insert('order_items', [
                    'order_id'     => $orderId,
                    'product_id'   => (int) $product['id'],
                    'product_name' => $product['name'],
                    'product_sku'  => $product['sku'] ?? null,
                    'variant'      => $item['variant'] !== '' ? $item['variant'] : null,
                    'quantity'     => (int) $item['quantity'],
                    'unit_price'   => $unitPrice,
                    'line_total'   => round($unitPrice * $item['quantity'], 2),
                ]);
            }

            $db->commit();

            return ['id' => $orderId, 'reference' => $reference, 'total' => $total];
        } catch (\Throwable $e) {
            $db->rollBack();
            throw $e;
        }
    }

    private function generateReference(): string
    {
        $prefix = preg_replace('/[^A-Z0-9]/', '', strtoupper((string) setting('quote_prefix', 'TR'))) ?: 'TR';

        do {
            $reference = sprintf('%s-O%s-%s', $prefix, date('ymd'), strtoupper(bin2hex(random_bytes(2))));
            $exists    = (int) $this->db()->value(
                'SELECT COUNT(*) FROM `orders` WHERE `reference` = :ref',
                ['ref' => $reference]
            );
        } while ($exists > 0);

        return $reference;
    }

    public function items(int $orderId): array
    {
        return $this->db()->all(
            'SELECT oi.*, p.slug AS product_slug
             FROM `order_items` oi
             LEFT JOIN `products` p ON p.id = oi.product_id
             WHERE oi.`order_id` = :id ORDER BY oi.`id` ASC',
            ['id' => $orderId]
        );
    }

    public function payments(int $orderId): array
    {
        return $this->db()->all(
            'SELECT * FROM `payments` WHERE `order_id` = :id ORDER BY `id` DESC',
            ['id' => $orderId]
        );
    }

    /**
     * Recalculate what has been paid and move the order along accordingly.
     * Called after every successful payment callback.
     */
    public function recalculatePayment(int $orderId): void
    {
        $order = $this->find($orderId);

        if ($order === null) {
            return;
        }

        $paid = (float) $this->db()->value(
            "SELECT COALESCE(SUM(`amount`),0) FROM `payments` WHERE `order_id` = :id AND `status` = 'success'",
            ['id' => $orderId]
        );

        $total = (float) $order['total'];

        if ($paid <= 0) {
            $paymentStatus = 'unpaid';
        } elseif ($paid + 0.01 >= $total) {
            $paymentStatus = 'paid';
        } else {
            $paymentStatus = 'partial';
        }

        $update = [
            'amount_paid'    => round($paid, 2),
            'payment_status' => $paymentStatus,
        ];

        // Only advance a pending order; never walk back a dispatched one.
        if ($paymentStatus === 'paid' && $order['status'] === 'pending') {
            $update['status'] = 'confirmed';
        }

        $this->updateById($orderId, $update);

        if ($paymentStatus === 'paid' && $order['payment_status'] !== 'paid') {
            $this->decrementStock($orderId);
        }
    }

    /** Reduce tracked stock once an order is fully paid. */
    private function decrementStock(int $orderId): void
    {
        $rows = $this->db()->all(
            'SELECT `product_id`, `quantity` FROM `order_items` WHERE `order_id` = :id AND `product_id` IS NOT NULL',
            ['id' => $orderId]
        );

        foreach ($rows as $row) {
            $this->db()->run(
                'UPDATE `products`
                 SET `stock_qty` = GREATEST(0, COALESCE(`stock_qty`,0) - :qty)
                 WHERE `id` = :pid AND `stock_qty` IS NOT NULL',
                ['qty' => (int) $row['quantity'], 'pid' => (int) $row['product_id']]
            );
        }

        // Anything that has run out is marked unavailable rather than sold blind.
        $this->db()->run(
            "UPDATE `products` SET `stock_status` = 'out_of_stock'
             WHERE `stock_qty` IS NOT NULL AND `stock_qty` <= 0 AND `stock_status` <> 'out_of_stock'"
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

        if (!empty($filters['payment_status'])) {
            $where[] = '`payment_status` = :pstatus';
            $params['pstatus'] = $filters['payment_status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(`reference` LIKE :q1 OR `customer_name` LIKE :q2 OR `email` LIKE :q3 OR `phone` LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `orders` WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT o.*, (SELECT COUNT(*) FROM `order_items` oi WHERE oi.order_id = o.id) AS item_count
             FROM `orders` o WHERE {$whereSql}
             ORDER BY o.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function forCustomer(int $customerId): array
    {
        return $this->db()->all(
            'SELECT o.*, (SELECT COUNT(*) FROM `order_items` oi WHERE oi.order_id = o.id) AS item_count
             FROM `orders` o WHERE o.`customer_id` = :id ORDER BY o.`created_at` DESC',
            ['id' => $customerId]
        );
    }

    public function countByStatus(): array
    {
        $rows   = $this->db()->all('SELECT `status`, COUNT(*) AS total FROM `orders` GROUP BY `status`');
        $counts = array_fill_keys(array_keys(self::STATUSES), 0);

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    /** Revenue actually received, this month and all time. */
    public function revenue(): array
    {
        return [
            'month' => (float) $this->db()->value(
                "SELECT COALESCE(SUM(`amount`),0) FROM `payments`
                 WHERE `status` = 'success' AND `created_at` >= DATE_FORMAT(CURDATE(),'%Y-%m-01')"
            ),
            'total' => (float) $this->db()->value(
                "SELECT COALESCE(SUM(`amount`),0) FROM `payments` WHERE `status` = 'success'"
            ),
            'awaiting' => (float) $this->db()->value(
                "SELECT COALESCE(SUM(`total` - `amount_paid`),0) FROM `orders`
                 WHERE `status` NOT IN ('cancelled') AND `payment_status` <> 'paid'"
            ),
        ];
    }

    public function recent(int $limit = 6): array
    {
        $limit = max(1, min(50, $limit));

        return $this->db()->all(
            "SELECT o.*, (SELECT COUNT(*) FROM `order_items` oi WHERE oi.order_id = o.id) AS item_count
             FROM `orders` o ORDER BY o.`created_at` DESC LIMIT {$limit}"
        );
    }
}
