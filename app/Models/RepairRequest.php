<?php
namespace App\Models;

use App\Core\Model;

class RepairRequest extends Model
{
    protected string $table = 'repair_requests';

    public const STATUSES = [
        'new'         => 'New',
        'assessing'   => 'Assessing',
        'quoted'      => 'Quoted',
        'approved'    => 'Approved',
        'in_progress' => 'In the workshop',
        'ready'       => 'Ready for collection',
        'collected'   => 'Collected',
        'cancelled'   => 'Cancelled',
    ];

    public const ITEM_TYPES = [
        'Saddle', 'Saddle tree', 'Bridle', 'Girth', 'Stirrup leathers',
        'Rug', 'Numnah', 'Headcollar', 'Boots / chaps', 'Other',
    ];

    public const URGENCY = [
        'standard'    => 'Standard — no fixed deadline',
        'urgent'      => 'Urgent — needed soon',
        'competition' => 'Competition — needed for a specific date',
    ];

    public function createRequest(array $data): array
    {
        $reference = $this->generateReference();

        $id = $this->create([
            'reference'   => $reference,
            'customer_id' => $data['customer_id'] ?? null,
            'name'        => $data['name'],
            'email'       => $data['email'],
            'phone'       => $data['phone'],
            'location'    => $data['location'] ?? null,
            'item_type'   => $data['item_type'],
            'item_make'   => $data['item_make'] ?? null,
            'damage'      => $data['damage'],
            'urgency'     => $data['urgency'] ?? 'standard',
            'status'      => 'new',
            'ip_address'  => $data['ip_address'] ?? null,
        ]);

        return ['id' => $id, 'reference' => $reference];
    }

    private function generateReference(): string
    {
        do {
            $reference = 'REP-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
            $exists    = (int) $this->db()->value(
                'SELECT COUNT(*) FROM `repair_requests` WHERE `reference` = :ref',
                ['ref' => $reference]
            );
        } while ($exists > 0);

        return $reference;
    }

    public function addPhoto(int $repairId, string $path, string $uploadedBy = 'customer', ?string $caption = null): int
    {
        return $this->db()->insert('repair_photos', [
            'repair_id'   => $repairId,
            'path'        => $path,
            'caption'     => $caption,
            'uploaded_by' => $uploadedBy,
        ]);
    }

    public function photos(int $repairId): array
    {
        return $this->db()->all(
            'SELECT * FROM `repair_photos` WHERE `repair_id` = :id ORDER BY `id` ASC',
            ['id' => $repairId]
        );
    }

    public function findPhoto(int $photoId): ?array
    {
        return $this->db()->one('SELECT * FROM `repair_photos` WHERE `id` = :id', ['id' => $photoId]);
    }

    public function deletePhoto(int $photoId): void
    {
        $this->db()->delete('repair_photos', '`id` = :id', ['id' => $photoId]);
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
            $where[] = '(`reference` LIKE :q1 OR `name` LIKE :q2 OR `email` LIKE :q3 OR `item_type` LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `repair_requests` WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT r.*, (SELECT COUNT(*) FROM `repair_photos` p WHERE p.repair_id = r.id) AS photo_count
             FROM `repair_requests` r
             WHERE {$whereSql}
             ORDER BY
               FIELD(r.`status`,'new','assessing','quoted','approved','in_progress','ready','collected','cancelled'),
               r.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function countByStatus(): array
    {
        $rows   = $this->db()->all('SELECT `status`, COUNT(*) AS total FROM `repair_requests` GROUP BY `status`');
        $counts = array_fill_keys(array_keys(self::STATUSES), 0);

        foreach ($rows as $row) {
            $counts[$row['status']] = (int) $row['total'];
        }

        return $counts;
    }

    public function forCustomer(int $customerId): array
    {
        return $this->db()->all(
            'SELECT r.*, (SELECT COUNT(*) FROM `repair_photos` p WHERE p.repair_id = r.id) AS photo_count
             FROM `repair_requests` r WHERE r.`customer_id` = :id ORDER BY r.`created_at` DESC',
            ['id' => $customerId]
        );
    }

    /** Requests that are open, i.e. not collected or cancelled. */
    public function openCount(): int
    {
        return (int) $this->db()->value(
            "SELECT COUNT(*) FROM `repair_requests` WHERE `status` NOT IN ('collected','cancelled')"
        );
    }
}
