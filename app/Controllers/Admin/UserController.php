<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $this->view('admin.users', [
            'pageTitle' => 'Staff accounts',
            'users'     => (new User())->allOrdered(),
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function store(): void
    {
        $model     = new User();
        $validator = new Validator($_POST);

        $validator->require('name', 'Name')->max('name', 120, 'Name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('password', 'Password')->min('password', 10, 'Password')
            ->matches('password_confirm', 'password', 'Passwords')
            ->in('role', ['admin', 'manager'], 'Role');

        if ($validator->passes() && $model->emailExists($validator->value('email'))) {
            $validator->addManualError('email', 'That email address is already registered.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'The account was not created.');
            $this->redirect('/admin/users');
        }

        $model->createUser(
            $validator->value('name'),
            $validator->value('email'),
            (string) $_POST['password'],
            $validator->value('role')
        );

        Session::clearOld();
        Session::flash('success', 'Staff account created.');
        $this->redirect('/admin/users');
    }

    public function update(string $id): void
    {
        $userId = (int) $id;
        $model  = new User();
        $user   = $model->find($userId);

        if ($user === null) {
            Session::flash('error', 'That account no longer exists.');
            $this->redirect('/admin/users');
        }

        $role     = in_array($_POST['role'] ?? '', ['admin', 'manager'], true) ? $_POST['role'] : $user['role'];
        $isActive = isset($_POST['is_active']) ? 1 : 0;

        // Never allow the last active administrator to be demoted or disabled.
        $wasActiveAdmin = $user['role'] === 'admin' && (int) $user['is_active'] === 1;
        $stillAdmin     = $role === 'admin' && $isActive === 1;

        if ($wasActiveAdmin && !$stillAdmin && $model->activeAdminCount() <= 1) {
            Session::flash('error', 'This is the last active administrator — the role and status were left unchanged.');
            $role     = 'admin';
            $isActive = 1;
        }

        $data = ['role' => $role, 'is_active' => $isActive];

        $name = trim((string) ($_POST['name'] ?? ''));
        if ($name !== '') {
            $data['name'] = mb_substr($name, 0, 120);
        }

        $password = (string) ($_POST['password'] ?? '');
        if ($password !== '') {
            if (mb_strlen($password) < 10) {
                Session::flash('error', 'The password was not changed — it must be at least 10 characters.');
            } else {
                $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
            }
        }

        $model->updateById($userId, $data);

        Session::flash('success', 'Account updated.');
        $this->redirect('/admin/users');
    }

    public function destroy(string $id): void
    {
        $userId = (int) $id;
        $model  = new User();
        $user   = $model->find($userId);

        if ($user === null) {
            Session::flash('error', 'That account no longer exists.');
            $this->redirect('/admin/users');
        }

        if ($userId === Auth::id()) {
            Session::flash('error', 'You cannot delete the account you are signed in with.');
            $this->redirect('/admin/users');
        }

        if ($user['role'] === 'admin' && (int) $user['is_active'] === 1 && $model->activeAdminCount() <= 1) {
            Session::flash('error', 'That is the last active administrator and cannot be deleted.');
            $this->redirect('/admin/users');
        }

        $model->deleteById($userId);

        Session::flash('success', 'Account deleted.');
        $this->redirect('/admin/users');
    }

    // ---- The signed-in user's own account -------------------------------

    public function account(): void
    {
        $this->view('admin.account', [
            'pageTitle' => 'My account',
            'user'      => Auth::user(),
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function updateAccount(): void
    {
        $userId = Auth::id();
        $model  = new User();

        $validator = new Validator($_POST);
        $validator->require('name', 'Your name')->max('name', 120, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address');

        if ($validator->passes() && $model->emailExists($validator->value('email'), $userId)) {
            $validator->addManualError('email', 'Another account already uses that email address.');
        }

        $password = (string) ($_POST['password'] ?? '');

        if ($password !== '') {
            $validator->min('password', 10, 'New password')
                ->matches('password_confirm', 'password', 'Passwords');

            $current = (string) ($_POST['current_password'] ?? '');
            $stored  = $model->find($userId);

            if ($stored === null || !password_verify($current, $stored['password_hash'])) {
                $validator->addManualError('current_password', 'Your current password is not correct.');
            }
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Nothing was saved — please check the highlighted fields.');
            $this->redirect('/admin/account');
        }

        $data = [
            'name'  => $validator->value('name'),
            'email' => $validator->value('email'),
        ];

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->updateById($userId, $data);

        Session::clearOld();
        Session::flash('success', 'Your account has been updated.');
        $this->redirect('/admin/account');
    }
}
