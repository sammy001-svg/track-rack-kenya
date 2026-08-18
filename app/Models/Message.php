<?php
namespace App\Models;

use App\Core\Model;

class Message extends Model
{
    protected string $table = 'messages';

    public function paginate(array $filters = [], int $page = 1, int $perPage = 20): array
    {
        $where  = ['1'];
        $params = [];

        if (isset($filters['is_read']) && $filters['is_read'] !== '') {
            $where[] = '`is_read` = :read';
            $params['read'] = (int) $filters['is_read'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(`name` LIKE :q1 OR `email` LIKE :q2 OR `subject` LIKE :q3 OR `body` LIKE :q4)';
            $term    = '%' . $filters['q'] . '%';
            $params += ['q1' => $term, 'q2' => $term, 'q3' => $term, 'q4' => $term];
        }

        $whereSql = implode(' AND ', $where);

        $total  = (int) $this->db()->value("SELECT COUNT(*) FROM `messages` WHERE {$whereSql}", $params);
        $pages  = max(1, (int) ceil($total / $perPage));
        $page   = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $items = $this->db()->all(
            "SELECT * FROM `messages` WHERE {$whereSql} ORDER BY `created_at` DESC LIMIT {$perPage} OFFSET {$offset}",
            $params
        );

        return ['items' => $items, 'total' => $total, 'pages' => $pages, 'page' => $page];
    }

    public function unreadCount(): int
    {
        return (int) $this->db()->value('SELECT COUNT(*) FROM `messages` WHERE `is_read` = 0');
    }

    public function markRead(int $id, bool $read = true): void
    {
        $this->updateById($id, ['is_read' => $read ? 1 : 0]);
    }
}
