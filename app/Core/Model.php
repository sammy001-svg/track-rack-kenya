<?php
namespace App\Core;

abstract class Model
{
    protected string $table;
    protected string $primaryKey = 'id';

    protected function db(): Database
    {
        return Database::instance();
    }

    public function find(int $id): ?array
    {
        return $this->db()->one(
            "SELECT * FROM `{$this->table}` WHERE `{$this->primaryKey}` = :id LIMIT 1",
            ['id' => $id]
        );
    }

    public function findBy(string $column, $value): ?array
    {
        return $this->db()->one(
            "SELECT * FROM `{$this->table}` WHERE `{$column}` = :value LIMIT 1",
            ['value' => $value]
        );
    }

    public function all(string $orderBy = 'id ASC'): array
    {
        return $this->db()->all("SELECT * FROM `{$this->table}` ORDER BY {$orderBy}");
    }

    public function create(array $data): int
    {
        return $this->db()->insert($this->table, $data);
    }

    public function updateById(int $id, array $data): int
    {
        return $this->db()->update(
            $this->table,
            $data,
            "`{$this->primaryKey}` = :pk_id",
            ['pk_id' => $id]
        );
    }

    public function deleteById(int $id): int
    {
        return $this->db()->delete($this->table, "`{$this->primaryKey}` = :id", ['id' => $id]);
    }

    public function count(string $where = '1', array $params = []): int
    {
        return (int) $this->db()->value("SELECT COUNT(*) FROM `{$this->table}` WHERE {$where}", $params);
    }

    /**
     * Build a slug that is unique within this table.
     */
    public function uniqueSlug(string $source, ?int $ignoreId = null, string $column = 'slug'): string
    {
        $base = slugify($source);
        if ($base === '') {
            $base = 'item';
        }

        $slug    = $base;
        $counter = 2;

        while (true) {
            $sql    = "SELECT COUNT(*) FROM `{$this->table}` WHERE `{$column}` = :slug";
            $params = ['slug' => $slug];

            if ($ignoreId !== null) {
                $sql .= " AND `{$this->primaryKey}` <> :ignore";
                $params['ignore'] = $ignoreId;
            }

            if ((int) $this->db()->value($sql, $params) === 0) {
                return $slug;
            }

            $slug = $base . '-' . $counter++;
        }
    }
}
