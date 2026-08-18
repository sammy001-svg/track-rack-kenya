<?php
namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected string $table = 'categories';

    /** The three pillars: Rider, Horse, Stable. */
    public function pillars(bool $activeOnly = true): array
    {
        $where = $activeOnly ? 'AND `is_active` = 1' : '';

        return $this->db()->all(
            "SELECT * FROM `categories` WHERE `parent_id` IS NULL {$where}
             ORDER BY `sort_order` ASC, `name` ASC"
        );
    }

    public function children(int $parentId, bool $activeOnly = true): array
    {
        $where = $activeOnly ? 'AND `is_active` = 1' : '';

        return $this->db()->all(
            "SELECT * FROM `categories` WHERE `parent_id` = :pid {$where}
             ORDER BY `sort_order` ASC, `name` ASC",
            ['pid' => $parentId]
        );
    }

    /** Full pillar -> children tree, with a product count on each child. */
    public function tree(bool $activeOnly = true): array
    {
        $tree = [];

        foreach ($this->pillars($activeOnly) as $pillar) {
            $pillar['children'] = $this->childrenWithCounts((int) $pillar['id'], $activeOnly);
            $pillar['product_count'] = array_sum(array_column($pillar['children'], 'product_count'));
            $tree[] = $pillar;
        }

        return $tree;
    }

    public function childrenWithCounts(int $parentId, bool $activeOnly = true): array
    {
        $where = $activeOnly ? 'AND c.`is_active` = 1' : '';

        return $this->db()->all(
            "SELECT c.*, COUNT(p.id) AS product_count
             FROM `categories` c
             LEFT JOIN `products` p ON p.category_id = c.id AND p.is_active = 1
             WHERE c.`parent_id` = :pid {$where}
             GROUP BY c.id
             ORDER BY c.`sort_order` ASC, c.`name` ASC",
            ['pid' => $parentId]
        );
    }

    /** Flat list for <select> menus, children prefixed for readability. */
    public function flatList(): array
    {
        $flat = [];

        foreach ($this->pillars(false) as $pillar) {
            $flat[] = ['id' => (int) $pillar['id'], 'name' => $pillar['name'], 'depth' => 0];

            foreach ($this->children((int) $pillar['id'], false) as $child) {
                $flat[] = ['id' => (int) $child['id'], 'name' => $child['name'], 'depth' => 1];
            }
        }

        return $flat;
    }

    public function bySlug(string $slug): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `categories` WHERE `slug` = :slug AND `is_active` = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }

    /** The category itself plus, for a pillar, all of its children. */
    public function descendantIds(int $categoryId): array
    {
        $ids  = [$categoryId];
        $rows = $this->db()->all(
            'SELECT `id` FROM `categories` WHERE `parent_id` = :pid',
            ['pid' => $categoryId]
        );

        foreach ($rows as $row) {
            $ids[] = (int) $row['id'];
        }

        return $ids;
    }

    public function parentOf(?int $parentId): ?array
    {
        return $parentId === null ? null : $this->find($parentId);
    }

    /** Every category with its product count, for the admin index. */
    public function allWithCounts(): array
    {
        return $this->db()->all(
            'SELECT c.*, parent.name AS parent_name, COUNT(p.id) AS product_count
             FROM `categories` c
             LEFT JOIN `categories` parent ON parent.id = c.parent_id
             LEFT JOIN `products` p ON p.category_id = c.id
             GROUP BY c.id
             ORDER BY COALESCE(parent.sort_order, c.sort_order) ASC,
                      c.parent_id IS NOT NULL ASC,
                      c.sort_order ASC, c.name ASC'
        );
    }
}
