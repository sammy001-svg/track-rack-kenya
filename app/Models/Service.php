<?php
namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    protected string $table = 'services';

    public function active(): array
    {
        return $this->db()->all(
            'SELECT * FROM `services` WHERE `is_active` = 1 ORDER BY `sort_order` ASC, `name` ASC'
        );
    }

    public function bySlug(string $slug): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `services` WHERE `slug` = :slug AND `is_active` = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }

    public function allWithCounts(): array
    {
        return $this->db()->all(
            'SELECT s.*, COUNT(b.id) AS booking_count
             FROM `services` s
             LEFT JOIN `bookings` b ON b.service_id = s.id
             GROUP BY s.id
             ORDER BY s.`sort_order` ASC, s.`name` ASC'
        );
    }
}
