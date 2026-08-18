<?php
namespace App\Models;

use App\Core\Model;

class Page extends Model
{
    protected string $table = 'pages';

    public function bySlug(string $slug): ?array
    {
        return $this->db()->one(
            'SELECT * FROM `pages` WHERE `slug` = :slug AND `is_active` = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }

    public function allOrdered(): array
    {
        return $this->db()->all('SELECT * FROM `pages` ORDER BY `title` ASC');
    }
}
