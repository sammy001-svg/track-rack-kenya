<?php
namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected string $table = 'users';

    public function allOrdered(): array
    {
        return $this->db()->all(
            'SELECT `id`,`name`,`email`,`role`,`is_active`,`last_login_at`,`created_at`
             FROM `users` ORDER BY `name` ASC'
        );
    }

    public function emailExists(string $email, ?int $ignoreId = null): bool
    {
        $sql    = 'SELECT COUNT(*) FROM `users` WHERE `email` = :email';
        $params = ['email' => $email];

        if ($ignoreId !== null) {
            $sql .= ' AND `id` <> :id';
            $params['id'] = $ignoreId;
        }

        return (int) $this->db()->value($sql, $params) > 0;
    }

    public function createUser(string $name, string $email, string $password, string $role = 'manager'): int
    {
        return $this->create([
            'name'          => $name,
            'email'         => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'role'          => $role,
            'is_active'     => 1,
        ]);
    }

    public function setPassword(int $id, string $password): void
    {
        $this->updateById($id, ['password_hash' => password_hash($password, PASSWORD_DEFAULT)]);
    }

    /** Number of active administrators - guards against locking everyone out. */
    public function activeAdminCount(): int
    {
        return (int) $this->db()->value(
            "SELECT COUNT(*) FROM `users` WHERE `role` = 'admin' AND `is_active` = 1"
        );
    }
}
