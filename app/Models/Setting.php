<?php
namespace App\Models;

use App\Core\Database;
use App\Core\Model;

class Setting extends Model
{
    protected string $table = 'settings';
    protected string $primaryKey = 'key_name';

    private static ?array $cache = null;

    /** Load every setting once per request. */
    private static function load(): array
    {
        if (self::$cache === null) {
            self::$cache = [];
            $rows = Database::instance()->all('SELECT `key_name`, `value` FROM `settings`');
            foreach ($rows as $row) {
                self::$cache[$row['key_name']] = $row['value'];
            }
        }
        return self::$cache;
    }

    public static function get(string $key, $default = '')
    {
        $all = self::load();
        return ($all[$key] ?? '') !== '' ? $all[$key] : $default;
    }

    public static function put(string $key, ?string $value): void
    {
        Database::instance()->run(
            'INSERT INTO `settings` (`key_name`, `value`) VALUES (:k, :v)
             ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)',
            ['k' => $key, 'v' => $value]
        );

        if (self::$cache !== null) {
            self::$cache[$key] = $value;
        }
    }

    /** All settings grouped for the admin settings screen. */
    public function grouped(): array
    {
        $rows = $this->db()->all(
            'SELECT * FROM `settings` ORDER BY `group_name` ASC, `sort_order` ASC, `key_name` ASC'
        );

        $grouped = [];
        foreach ($rows as $row) {
            $grouped[$row['group_name']][] = $row;
        }

        return $grouped;
    }
}
