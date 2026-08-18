<?php
namespace App\Models;

use App\Core\Model;

class Customer extends Model
{
    protected string $table = 'customers';

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM `customers` WHERE `email` = :email';
        $params = ['email' => $email];

        if ($ignoreId !== null) {
            $sql .= ' AND `id` <> :id';
            $params['id'] = $ignoreId;
        }

        return (int) $this->db()->value($sql, $params) > 0;
    }

    public function register(array $data): int
    {
        return $this->create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'] ?? null,
            'location'      => $data['location'] ?? null,
            'discipline'    => $data['discipline'] ?? null,
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'is_active'     => 1,
        ]);
    }

    // ---- Password reset -------------------------------------------------

    /** Issue a single-use reset token valid for one hour. */
    public function issueResetToken(int $customerId): string
    {
        $token = bin2hex(random_bytes(32));

        $this->updateById($customerId, [
            'reset_token'      => hash('sha256', $token),
            'reset_expires_at' => date('Y-m-d H:i:s', time() + 3600),
        ]);

        return $token;
    }

    public function findByResetToken(string $token): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `customers`
             WHERE `reset_token` = :token AND `reset_expires_at` > NOW() AND `is_active` = 1
             LIMIT 1',
            ['token' => hash('sha256', $token)]
        );
    }

    public function completeReset(int $customerId, string $password): void
    {
        $this->updateById($customerId, [
            'password_hash'    => password_hash($password, PASSWORD_DEFAULT),
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);
    }

    // ---- Horses ---------------------------------------------------------

    public function horses(int $customerId): array
    {
        return $this->db()->all(
            'SELECT * FROM `customer_horses` WHERE `customer_id` = :id ORDER BY `name` ASC',
            ['id' => $customerId]
        );
    }

    public function findHorse(int $horseId, int $customerId): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `customer_horses` WHERE `id` = :id AND `customer_id` = :cid LIMIT 1',
            ['id' => $horseId, 'cid' => $customerId]
        );
    }

    public function saveHorse(int $customerId, array $data, ?int $horseId = null): int
    {
        $row = [
            'name'             => $data['name'],
            'height_hh'        => $data['height_hh'] !== '' ? (float) $data['height_hh'] : null,
            'breed'            => $data['breed'] ?: null,
            'discipline'       => $data['discipline'] ?: null,
            'saddle_seat_size' => $data['saddle_seat_size'] ?: null,
            'gullet_width'     => $data['gullet_width'] ?: null,
            'rug_size'         => $data['rug_size'] ?: null,
            'girth_size'       => $data['girth_size'] ?: null,
            'bit_size'         => $data['bit_size'] ?: null,
            'notes'            => $data['notes'] ?: null,
        ];

        if ($horseId !== null && $this->findHorse($horseId, $customerId) !== null) {
            $this->db()->update('customer_horses', $row, '`id` = :id AND `customer_id` = :cid', [
                'id' => $horseId, 'cid' => $customerId,
            ]);
            return $horseId;
        }

        $row['customer_id'] = $customerId;
        return $this->db()->insert('customer_horses', $row);
    }

    public function deleteHorse(int $horseId, int $customerId): void
    {
        $this->db()->delete('customer_horses', '`id` = :id AND `customer_id` = :cid', [
            'id' => $horseId, 'cid' => $customerId,
        ]);
    }

    // ---- Activity -------------------------------------------------------

    /**
     * Attach this account to any past quotes, bookings and repairs that used
     * the same email address, so history appears the moment they register.
     */
    public function claimHistory(int $customerId, string $email): void
    {
        foreach (['quotes', 'bookings', 'repair_requests'] as $table) {
            $this->db()->run(
                "UPDATE `{$table}` SET `customer_id` = :cid WHERE `email` = :email AND `customer_id` IS NULL",
                ['cid' => $customerId, 'email' => $email]
            );
        }
    }

    public function activityCounts(int $customerId): array
    {
        $count = fn (string $table): int => (int) $this->db()->value(
            "SELECT COUNT(*) FROM `{$table}` WHERE `customer_id` = :id",
            ['id' => $customerId]
        );

        return [
            'quotes'   => $count('quotes'),
            'orders'   => $count('orders'),
            'bookings' => $count('bookings'),
            'repairs'  => $count('repair_requests'),
            'horses'   => $count('customer_horses'),
        ];
    }

    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(`name` LIKE :q1 OR `email` LIKE :q2 OR `phone` LIKE :q3)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `customers` WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT c.`id`,c.`name`,c.`email`,c.`phone`,c.`location`,c.`is_active`,c.`last_login_at`,c.`created_at`,
                    (SELECT COUNT(*) FROM `orders` o WHERE o.customer_id = c.id) AS order_count,
                    (SELECT COUNT(*) FROM `quotes` q WHERE q.customer_id = c.id) AS quote_count
             FROM `customers` c
             WHERE {$whereSql}
             ORDER BY c.`created_at` DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }
}
