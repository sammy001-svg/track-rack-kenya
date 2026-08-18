<?php
namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;

class AuthController extends Controller
{
    private const MAX_ATTEMPTS = 6;
    private const LOCKOUT_SECONDS = 600;

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect('/admin');
        }

        $this->view('admin.login', [
            'pageTitle' => 'Staff Login',
            'errors'    => Session::errors(),
        ], 'layouts.blank');

        Session::clearOld();
    }

    public function login(): void
    {
        if ($this->isLockedOut()) {
            Session::flash('error', 'Too many failed attempts. Please wait 10 minutes and try again.');
            $this->redirect('/admin/login');
        }

        $validator = new Validator($_POST);
        $validator->require('email', 'Email address')->email('email')
            ->require('password', 'Password');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            $this->redirect('/admin/login');
        }

        if (!Auth::attempt($validator->value('email'), (string) ($_POST['password'] ?? ''))) {
            $this->recordFailure();
            Session::flashErrors(['password' => 'Those credentials do not match our records.']);
            Session::flashInput($_POST);
            Session::flash('error', 'Login failed.');
            $this->redirect('/admin/login');
        }

        $this->clearFailures();

        $intended = Session::pull('_intended', '/admin');
        Session::flash('success', 'Welcome back.');

        // Only ever redirect to a path on this application.
        $path = parse_url((string) $intended, PHP_URL_PATH) ?: '/admin';
        $this->redirect(str_starts_with($path, '/') ? $path : '/admin');
    }

    public function logout(): void
    {
        Auth::logout();
        Session::flash('success', 'You have been signed out.');
        $this->redirect('/admin/login');
    }

    // ---- Simple session-based throttling ------------------------------

    private function isLockedOut(): bool
    {
        $attempts = Session::get('_login_attempts', 0);
        $lastFail = Session::get('_login_last_fail', 0);

        if ($attempts < self::MAX_ATTEMPTS) {
            return false;
        }

        if (time() - $lastFail > self::LOCKOUT_SECONDS) {
            $this->clearFailures();
            return false;
        }

        return true;
    }

    private function recordFailure(): void
    {
        Session::set('_login_attempts', Session::get('_login_attempts', 0) + 1);
        Session::set('_login_last_fail', time());
    }

    private function clearFailures(): void
    {
        Session::forget('_login_attempts');
        Session::forget('_login_last_fail');
    }
}
