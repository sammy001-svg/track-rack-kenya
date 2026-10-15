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
        // Every level, not just the sections: the navigation lists the
        // sub-categories under each one. Built from a single query and nested
        // here, because walking it level by level was a query per section and
        // there are now seventy-odd categories.
        $where = $activeOnly ? 'WHERE c.`is_active` = 1' : '';

        $rows = $this->db()->all(
            "SELECT c.*, (
                 SELECT COUNT(*) FROM `products` p
                  WHERE p.`is_active` = 1 AND p.`category_id` = c.`id`
             ) AS own_count
             FROM `categories` c
             {$where}
             ORDER BY c.`sort_order` ASC, c.`name` ASC"
        );

        $byParent = [];

        foreach ($rows as $row) {
            $byParent[(int) ($row['parent_id'] ?? 0)][] = $row;
        }

        // A category's count includes everything filed beneath it, so a
        // section whose products all live one level down does not read empty.
        $build = static function (int $parentId) use (&$build, $byParent): array {
            $out = [];

            foreach ($byParent[$parentId] ?? [] as $row) {
                $row['children']      = $build((int) $row['id']);
                $row['product_count'] = (int) $row['own_count']
                    + array_sum(array_column($row['children'], 'product_count'));
                $out[] = $row;
            }

            return $out;
        };

        return $build(0);
    }

    public function childrenWithCounts(int $parentId, bool $activeOnly = true): array
    {
        $where = $activeOnly ? 'AND c.`is_active` = 1' : '';

        // The count takes in sub-categories too, so a section whose products
        // all live one level further down does not read as empty.
        return $this->db()->all(
            "SELECT c.*, (
                 SELECT COUNT(*) FROM `products` p
                  WHERE p.is_active = 1
                    AND (p.category_id = c.id
                         OR p.category_id IN (SELECT g.id FROM `categories` g WHERE g.parent_id = c.id))
             ) AS product_count
             FROM `categories` c
             WHERE c.`parent_id` = :pid {$where}
             ORDER BY c.`sort_order` ASC, c.`name` ASC",
            ['pid' => $parentId]
        );
    }

    /** Flat list for <select> menus, children prefixed for readability. */
    /** Every category in tree order, with its depth, for admin selects. */
    public function flatList(): array
    {
        $flat = [];

        foreach ($this->pillars(false) as $pillar) {
            $flat[] = ['id' => (int) $pillar['id'], 'name' => $pillar['name'], 'depth' => 0];

            foreach ($this->children((int) $pillar['id'], false) as $child) {
                $flat[] = ['id' => (int) $child['id'], 'name' => $child['name'], 'depth' => 1];

                // Sections can have sub-categories, and a product is usually
                // filed on one of those, so they have to be selectable.
                foreach ($this->children((int) $child['id'], false) as $grandchild) {
                    $flat[] = ['id' => (int) $grandchild['id'], 'name' => $grandchild['name'], 'depth' => 2];
                }
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
    /**
     * A category and everything beneath it, however deep.
     *
     * Sections can have sub-categories, so one hop down is not enough: a
     * product filed under Hoof Care > Farriery has to appear on the Hoof Care
     * page and on the Yard page as well.
     */
    public function descendantIds(int $categoryId): array
    {
        $ids   = [$categoryId];
        $front = [$categoryId];

        // Bounded in case a parent is ever pointed at its own descendant.
        for ($depth = 0; $depth < 5 && $front !== []; $depth++) {
            $rows = $this->db()->all(
                'SELECT `id` FROM `categories` WHERE `parent_id` IN (' . implode(',', array_map('intval', $front)) . ')'
            );

            $front = [];
            foreach ($rows as $row) {
                $id = (int) $row['id'];
                if (!in_array($id, $ids, true)) {
                    $ids[]   = $id;
                    $front[] = $id;
                }
            }
        }

        return $ids;
    }

    /**
     * The ids above a category, nearest first. Its length is the category's
     * depth: empty for a department, one for a section, two for a
     * sub-category.
     *
     * @return array<int, int>
     */
    public function ancestorIds(int $categoryId): array
    {
        $ids     = [];
        $current = $this->find($categoryId);

        for ($depth = 0; $depth < 5 && $current !== null && $current['parent_id'] !== null; $depth++) {
            $ids[]   = (int) $current['parent_id'];
            $current = $this->find((int) $current['parent_id']);
        }

        return $ids;
    }

    /** The top-level department a category sits under — itself if it is one. */
    public function departmentOf(array $category): array
    {
        $current = $category;

        for ($depth = 0; $depth < 5 && $current['parent_id'] !== null; $depth++) {
            $parent = $this->find((int) $current['parent_id']);

            if ($parent === null) {
                break;
            }

            $current = $parent;
        }

        return $current;
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
