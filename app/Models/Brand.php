<?php
namespace App\Models;

use App\Core\Model;

class Brand extends Model
{
    protected string $table = 'brands';

    public function active(): array
    {
        return $this->db()->all(
            'SELECT * FROM `brands` WHERE `is_active` = 1 ORDER BY `sort_order` ASC, `name` ASC'
        );
    }

    public function allWithCounts(): array
    {
        return $this->db()->all(
            'SELECT b.*, COUNT(p.id) AS product_count
             FROM `brands` b
             LEFT JOIN `products` p ON p.brand_id = b.id
             GROUP BY b.id
             ORDER BY b.`sort_order` ASC, b.`name` ASC'
        );
    }

    public function bySlug(string $slug): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `brands` WHERE `slug` = :slug AND `is_active` = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }
}
