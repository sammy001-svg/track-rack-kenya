<?php
namespace App\Models;

use App\Core\Model;

class Product extends Model
{
    protected string $table = 'products';

    private const SELECT_CARD = "
        p.*,
        c.name AS category_name,
        c.slug AS category_slug,
        b.name AS brand_name,
        (SELECT pi.path FROM product_images pi
          WHERE pi.product_id = p.id
          ORDER BY pi.is_primary DESC, pi.sort_order ASC, pi.id ASC
          LIMIT 1) AS primary_image,
        -- Top-level section (rider/horse/stable) this product sits under, so a
        -- card with no photograph can fall back to the right section image.
        (SELECT COALESCE(parent.slug, self.slug)
           FROM categories self
           LEFT JOIN categories parent ON parent.id = self.parent_id
          WHERE self.id = p.category_id) AS pillar_slug";

    /**
     * Filtered, paginated catalog query.
     *
     * @param array $filters category|pillar|brand|q|stock|sort|featured
     * @return array{items:array, total:int, pages:int, page:int}
     */
    public function catalog(array $filters = [], int $page = 1, int $perPage = 12): array
    {
        $where  = ['p.is_active = 1'];
        $params = [];

        if (!empty($filters['category_ids'])) {
            $ids = array_map('intval', (array) $filters['category_ids']);
            $placeholders = [];
            foreach ($ids as $i => $id) {
                $key = 'cat' . $i;
                $placeholders[] = ':' . $key;
                $params[$key]   = $id;
            }
            $where[] = 'p.category_id IN (' . implode(',', $placeholders) . ')';
        }

        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = (int) $filters['brand_id'];
        }

        if (!empty($filters['q'])) {
            // Native prepares bind each named placeholder once, so repeat the
            // term under distinct names rather than reusing :q.
            $where[] = '(p.name LIKE :q1 OR p.short_desc LIKE :q2 OR p.sku LIKE :q3 OR b.name LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        if (!empty($filters['stock'])) {
            $where[] = 'p.stock_status = :stock';
            $params['stock'] = $filters['stock'];
        }

        if (!empty($filters['featured'])) {
            $where[] = 'p.is_featured = 1';
        }

        if (!empty($filters['new'])) {
            $where[] = 'p.is_new = 1';
        }

        $whereSql = implode(' AND ', $where);

        $orderBy = match ($filters['sort'] ?? '') {
            'name_asc'   => 'p.name ASC',
            'name_desc'  => 'p.name DESC',
            'newest'     => 'p.created_at DESC, p.id DESC',
            'popular'    => 'p.views DESC, p.name ASC',
            default      => 'p.is_featured DESC, p.sort_order ASC, p.name ASC',
        };

        $total = (int) $this->db()->value(
            "SELECT COUNT(*) FROM products p
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE {$whereSql}",
            $params
        );

        $pages   = max(1, (int) ceil($total / $perPage));
        $page    = max(1, min($page, $pages));
        $offset  = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE {$whereSql}
             ORDER BY {$orderBy}
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function featured(int $limit = 4): array
    {
        $limit = max(1, min(24, $limit));

        return $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 AND p.is_featured = 1
             ORDER BY p.sort_order ASC, p.id DESC
             LIMIT {$limit}"
        );
    }

    public function latest(int $limit = 4): array
    {
        $limit = max(1, min(24, $limit));

        return $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1
             ORDER BY p.created_at DESC, p.id DESC
             LIMIT {$limit}"
        );
    }

    public function bySlug(string $slug): ?array
    {
        return $this->db()->one(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    c.parent_id AS category_parent_id,
                    b.name AS brand_name, b.slug AS brand_slug, b.description AS brand_description
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.slug = :slug AND p.is_active = 1
             LIMIT 1",
            ['slug' => $slug]
        );
    }

    public function images(int $productId): array
    {
        return $this->db()->all(
            'SELECT * FROM `product_images` WHERE `product_id` = :id
             ORDER BY `is_primary` DESC, `sort_order` ASC, `id` ASC',
            ['id' => $productId]
        );
    }

    public function variants(int $productId): array
    {
        return $this->db()->all(
            'SELECT * FROM `product_variants` WHERE `product_id` = :id
             ORDER BY `sort_order` ASC, `id` ASC',
            ['id' => $productId]
        );
    }

    /** Other items from the same category. */
    public function related(int $productId, ?int $categoryId, int $limit = 4): array
    {
        if ($categoryId === null) {
            return [];
        }

        $limit = max(1, min(12, $limit));

        return $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.is_active = 1 AND p.category_id = :cid AND p.id <> :pid
             ORDER BY p.is_featured DESC, RAND()
             LIMIT {$limit}",
            ['cid' => $categoryId, 'pid' => $productId]
        );
    }

    /** Active products keyed by id - used to hydrate the quote list. */
    public function findManyActive(array $ids): array
    {
        $ids = array_values(array_filter(array_map('intval', $ids)));

        if ($ids === []) {
            return [];
        }

        $placeholders = [];
        $params       = [];
        foreach ($ids as $i => $id) {
            $placeholders[] = ':id' . $i;
            $params['id' . $i] = $id;
        }

        $rows = $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.id IN (" . implode(',', $placeholders) . ") AND p.is_active = 1",
            $params
        );

        $keyed = [];
        foreach ($rows as $row) {
            $keyed[(int) $row['id']] = $row;
        }

        return $keyed;
    }

    public function incrementViews(int $productId): void
    {
        $this->db()->run('UPDATE `products` SET `views` = `views` + 1 WHERE `id` = :id', ['id' => $productId]);
    }

    // ---- Admin ---------------------------------------------------------

    public function adminList(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1'];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(p.name LIKE :q1 OR p.sku LIKE :q2)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term];
        }

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :cid';
            $params['cid'] = (int) $filters['category_id'];
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '') {
            $where[] = 'p.is_active = :active';
            $params['active'] = (int) $filters['is_active'];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM products p WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT " . self::SELECT_CARD . "
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE {$whereSql}
             ORDER BY p.updated_at DESC, p.id DESC
             LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function addImage(int $productId, string $path, ?string $alt = null, bool $isPrimary = false): int
    {
        if ($isPrimary) {
            $this->db()->run('UPDATE `product_images` SET `is_primary` = 0 WHERE `product_id` = :id', ['id' => $productId]);
        }

        $hasImages = (int) $this->db()->value(
            'SELECT COUNT(*) FROM `product_images` WHERE `product_id` = :id',
            ['id' => $productId]
        );

        return $this->db()->insert('product_images', [
            'product_id' => $productId,
            'path'       => $path,
            'alt'        => $alt,
            'is_primary' => ($isPrimary || $hasImages === 0) ? 1 : 0,
            'sort_order' => $hasImages,
        ]);
    }

    public function findImage(int $imageId): ?array
    {
        return $this->db()->one('SELECT * FROM `product_images` WHERE `id` = :id', ['id' => $imageId]);
    }

    public function deleteImage(int $imageId): void
    {
        $image = $this->findImage($imageId);

        if ($image === null) {
            return;
        }

        $this->db()->delete('product_images', '`id` = :id', ['id' => $imageId]);

        // Promote another image if the primary one was removed.
        if ((int) $image['is_primary'] === 1) {
            $next = $this->db()->one(
                'SELECT `id` FROM `product_images` WHERE `product_id` = :pid ORDER BY `sort_order` ASC, `id` ASC LIMIT 1',
                ['pid' => (int) $image['product_id']]
            );

            if ($next !== null) {
                $this->db()->update('product_images', ['is_primary' => 1], '`id` = :id', ['id' => (int) $next['id']]);
            }
        }
    }

    public function setPrimaryImage(int $productId, int $imageId): void
    {
        $this->db()->run('UPDATE `product_images` SET `is_primary` = 0 WHERE `product_id` = :pid', ['pid' => $productId]);
        $this->db()->run(
            'UPDATE `product_images` SET `is_primary` = 1 WHERE `id` = :id AND `product_id` = :pid',
            ['id' => $imageId, 'pid' => $productId]
        );
    }

    public function replaceVariants(int $productId, array $variants): void
    {
        $this->db()->delete('product_variants', '`product_id` = :pid', ['pid' => $productId]);

        $order = 0;
        foreach ($variants as $variant) {
            $label = trim((string) ($variant['label'] ?? ''));
            $value = trim((string) ($variant['value'] ?? ''));

            if ($label === '' || $value === '') {
                continue;
            }

            $this->db()->insert('product_variants', [
                'product_id' => $productId,
                'label'      => mb_substr($label, 0, 80),
                'value'      => mb_substr($value, 0, 120),
                'sort_order' => $order++,
            ]);
        }
    }
}
