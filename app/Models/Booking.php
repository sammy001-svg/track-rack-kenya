<?php
namespace App\Models;

use App\Core\Model;

class Booking extends Model
{
    protected string $table = 'bookings';

    public const STATUSES = [
        'new'       => 'New',
        'confirmed' => 'Confirmed',
        'scheduled' => 'Scheduled',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    public const SLOTS = [
        'morning'   => 'Morning (8:30am – 12:30pm)',
        'afternoon' => 'Afternoon (1:00pm – 5:00pm)',
        'flexible'  => 'Flexible — whatever suits you',
    ];

    public function createBooking(array $data): array
    {
        $reference = $this->generateReference();

        $id = $this->create([
            'reference'      => $reference,
            'service_id'     => $data['service_id'] ?? null,
            'customer_id'    => $data['customer_id'] ?? null,
            'name'           => $data['name'],
            'email'          => $data['email'],
            'phone'          => $data['phone'],
            'location'       => $data['location'] ?? null,
            'at_yard'        => !empty($data['at_yard']) ? 1 : 0,
            'horse_name'     => $data['horse_name'] ?? null,
            'horse_details'  => $data['horse_details'] ?? null,
            'discipline'     => $data['discipline'] ?? null,
            'saddle_details' => $data['saddle_details'] ?? null,
            'preferred_date' => $data['preferred_date'] ?? null,
            'preferred_slot' => $data['preferred_slot'] ?? 'flexible',
            'alternate_date' => $data['alternate_date'] ?? null,
            'notes'          => $data['notes'] ?? null,
            'status'         => 'new',
            'ip_address'     => $data['ip_address'] ?? null,
        ]);

        return ['id' => $id, 'reference' => $reference];
    }

    private function generateReference(): string
    {
        do {
            $reference = 'FIT-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            $exists    = (int) $this->db()->value(
                'SELECT COUNT(*) FROM `bookings` WHERE `reference` = :ref',
                ['ref' => $reference]
            );
        } while ($exists > 0);

        return $reference;
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'b.`status` = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(b.`reference` LIKE :q1 OR b.`name` LIKE :q2 OR b.`email` LIKE :q3 OR b.`horse_name` LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `bookings` b WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT b.*, s.name AS service_name
             FROM `bookings` b
             LEFT JOIN `services` s ON s.id = b.service_id
             WHERE {$whereSql}
             ORDER BY
               FIELD(b.`status`,'new','confirmed','scheduled','completed','cancelled'),
               COALESCE(b.`scheduled_at`, b.`preferred_date`) ASC,
               b.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function withService(int $id): ?array
    {
        return $this->db()->one(
            'SELECT b.*, s.name AS service_name, s.slug AS service_slug
             FROM `bookings` b
             LEFT JOIN `services` s ON s.id = b.service_id
             WHERE b.`id` = :id LIMIT 1',
            ['id' => $id]
        );
    }

    public function countByStatus(): array
    {
        $rows   = $this->db()->all('SELECT `status`, COUNT(*) AS total FROM `bookings` GROUP BY `status`');
        $counts = array_fill_keys(array_keys(self::STATUSES), 0);

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    /** Bookings that are confirmed or scheduled and still ahead of us. */
    public function upcoming(int $limit = 6): array
    {
        $limit = max(1, min(50, $limit));

        return $this->db()->all(
            "SELECT b.*, s.name AS service_name
             FROM `bookings` b
             LEFT JOIN `services` s ON s.id = b.service_id
             WHERE b.`status` IN ('confirmed','scheduled')
               AND COALESCE(DATE(b.`scheduled_at`), b.`preferred_date`) >= CURDATE()
             ORDER BY COALESCE(b.`scheduled_at`, b.`preferred_date`) ASC
             LIMIT {$limit}"
        );
    }

    public function forCustomer(int $customerId): array
    {
        return $this->db()->all(
            'SELECT b.*, s.name AS service_name
             FROM `bookings` b
             LEFT JOIN `services` s ON s.id = b.service_id
             WHERE b.`customer_id` = :id
             ORDER BY b.`created_at` DESC',
            ['id' => $customerId]
        );
    }
}
