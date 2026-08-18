<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\CustomerAuth;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RepairRequest;

class AccountController extends Controller
{
    private const DISCIPLINES = [
        'Racing', 'Polo', 'Showjumping', 'Dressage', 'Eventing',
        'Hacking', 'Safari riding', 'Pony Club', 'Other',
    ];

    // =================================================================
    //  Session
    // =================================================================

    public function showLogin(): void
    {
        if (CustomerAuth::check()) {
            $this->redirect('/account');
        }

        $this->view('site.account-login', [
            'pageTitle' => 'Sign In',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'errors'    => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function login(): void
    {
        if (CustomerAuth::isLockedOut()) {
            Session::flash('error', 'Too many attempts. Please wait fifteen minutes and try again.');
            $this->redirect('/account/login');
        }

        $validator = new Validator($_POST);
        $validator->require('email', 'Email address')->email('email')
            ->require('password', 'Password');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            $this->redirect('/account/login');
        }

        if (!CustomerAuth::attempt($validator->value('email'), (string) $_POST['password'])) {
            CustomerAuth::recordFailure();
            Session::flashErrors(['password' => 'Those details do not match an account.']);
            Session::flashInput($_POST);
            $this->redirect('/account/login');
        }

        CustomerAuth::clearFailures();
        Session::clearOld();
        Session::flash('success', 'Welcome back.');

        $intended = (string) Session::pull('_customer_intended', '/account');
        $path     = parse_url($intended, PHP_URL_PATH) ?: '/account';

        $this->redirect(str_starts_with($path, '/') ? $path : '/account');
    }

    public function logout(): void
    {
        CustomerAuth::logout();
        Session::flash('success', 'You have been signed out.');
        $this->redirect('/');
    }

    // =================================================================
    //  Registration
    // =================================================================

    public function showRegister(): void
    {
        if (CustomerAuth::check()) {
            $this->redirect('/account');
        }

        $this->view('site.account-register', [
            'pageTitle'   => 'Create an Account',
            'bodyClass'   => 'page-account',
            'noindex'     => true,
            'disciplines' => self::DISCIPLINES,
            'errors'      => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function register(): void
    {
        $model     = new Customer();
        $validator = new Validator($_POST);

        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->max('location', 150, 'Location')
            ->require('password', 'Password')->min('password', 10, 'Password')
            ->matches('password_confirm', 'password', 'Passwords');

        if ($validator->passes() && $model->emailExists($validator->value('email'))) {
            $validator->addManualError('email', 'An account already uses that email address. Try signing in instead.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/account/register');
        }

        $discipline = $validator->value('discipline', '');

        $customerId = $model->register([
            'name'       => $validator->value('name'),
            'email'      => $validator->value('email'),
            'phone'      => $validator->value('phone'),
            'location'   => $validator->value('location') ?: null,
            'discipline' => in_array($discipline, self::DISCIPLINES, true) ? $discipline : null,
            'password'   => (string) $_POST['password'],
        ]);

        // Pull in any quotes, bookings or repairs already sent from this address.
        $model->claimHistory($customerId, $validator->value('email'));

        CustomerAuth::login($customerId);

        Mailer::send(
            $validator->value('email'),
            'Your Tack Rack account',
            '<h2 style="font-family:Georgia,serif;font-weight:normal;">Welcome, ' . e(explode(' ', $validator->value('name'))[0]) . '</h2>'
                . '<p>Your account is ready. You can now track quotes, orders, fittings and repairs in one place, '
                . 'and save the sizes for each of your horses so we do not have to ask every time.</p>'
                . Mailer::button('Go to your account', url('/account'))
        );

        Session::clearOld();
        Session::flash('success', 'Your account is ready.');
        $this->redirect('/account');
    }

    // =================================================================
    //  Password reset
    // =================================================================

    public function showForgot(): void
    {
        $this->view('site.account-forgot', [
            'pageTitle' => 'Reset Your Password',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'errors'    => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function sendReset(): void
    {
        $validator = new Validator($_POST);
        $validator->require('email', 'Email address')->email('email');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            $this->redirect('/account/forgot');
        }

        $model    = new Customer();
        $customer = $model->findBy('email', $validator->value('email'));

        // Always report success — never reveal whether an address is registered.
        if ($customer !== null && (int) $customer['is_active'] === 1) {
            $token = $model->issueResetToken((int) $customer['id']);

            Mailer::send(
                $customer['email'],
                'Reset your Tack Rack password',
                '<h2 style="font-family:Georgia,serif;font-weight:normal;">Password reset</h2>'
                    . '<p>Use the button below to choose a new password. The link is valid for one hour.</p>'
                    . Mailer::button('Choose a new password', url('/account/reset/' . $token))
                    . '<p style="color:#6B655C;font-size:13px;">If you did not ask for this, you can ignore this email — '
                    . 'your password will not change.</p>'
            );
        }

        Session::clearOld();
        Session::flash('success', 'If that address has an account, a reset link is on its way.');
        $this->redirect('/account/login');
    }

    public function showReset(string $token): void
    {
        if ((new Customer())->findByResetToken($token) === null) {
            Session::flash('error', 'That reset link has expired. Please request a new one.');
            $this->redirect('/account/forgot');
        }

        $this->view('site.account-reset', [
            'pageTitle' => 'Choose a New Password',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'token'     => $token,
            'errors'    => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function completeReset(string $token): void
    {
        $model    = new Customer();
        $customer = $model->findByResetToken($token);

        if ($customer === null) {
            Session::flash('error', 'That reset link has expired. Please request a new one.');
            $this->redirect('/account/forgot');
        }

        $validator = new Validator($_POST);
        $validator->require('password', 'Password')->min('password', 10, 'Password')
            ->matches('password_confirm', 'password', 'Passwords');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            $this->redirect('/account/reset/' . $token);
        }

        $model->completeReset((int) $customer['id'], (string) $_POST['password']);
        CustomerAuth::login((int) $customer['id']);

        Session::flash('success', 'Your password has been changed.');
        $this->redirect('/account');
    }

    // =================================================================
    //  Dashboard
    // =================================================================

    public function dashboard(): void
    {
        $customer   = CustomerAuth::user();
        $customerId = (int) $customer['id'];
        $model      = new Customer();

        $this->view('site.account-dashboard', [
            'pageTitle' => 'Your Account',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'customer'  => $customer,
            'counts'    => $model->activityCounts($customerId),
            'orders'    => array_slice((new Order())->forCustomer($customerId), 0, 4),
            'quotes'    => array_slice($this->customerQuotes($customerId), 0, 4),
            'bookings'  => array_slice((new Booking())->forCustomer($customerId), 0, 3),
            'repairs'   => array_slice((new RepairRequest())->forCustomer($customerId), 0, 3),
            'horses'    => $model->horses($customerId),
        ]);
    }

    public function orders(): void
    {
        $this->view('site.account-orders', [
            'pageTitle' => 'Your Orders',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'customer'  => CustomerAuth::user(),
            'orders'    => (new Order())->forCustomer(CustomerAuth::id()),
        ]);
    }

    public function orderDetail(string $reference): void
    {
        $model = new Order();
        $order = $model->findBy('reference', $reference);

        if ($order === null || (int) $order['customer_id'] !== CustomerAuth::id()) {
            $this->notFound('We could not find that order on your account.');
        }

        $this->view('site.account-order', [
            'pageTitle' => 'Order ' . $order['reference'],
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'customer'  => CustomerAuth::user(),
            'order'     => $order,
            'items'     => $model->items((int) $order['id']),
            'payments'  => $model->payments((int) $order['id']),
        ]);
    }

    public function quotes(): void
    {
        $this->view('site.account-quotes', [
            'pageTitle' => 'Your Quotes',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'customer'  => CustomerAuth::user(),
            'quotes'    => $this->customerQuotes(CustomerAuth::id()),
        ]);
    }

    public function activity(): void
    {
        $customerId = CustomerAuth::id();

        $this->view('site.account-activity', [
            'pageTitle' => 'Fittings & Repairs',
            'bodyClass' => 'page-account',
            'noindex'   => true,
            'customer'  => CustomerAuth::user(),
            'bookings'  => (new Booking())->forCustomer($customerId),
            'repairs'   => (new RepairRequest())->forCustomer($customerId),
        ]);
    }

    // =================================================================
    //  Profile & horses
    // =================================================================

    public function profile(): void
    {
        $this->view('site.account-profile', [
            'pageTitle'   => 'Your Details',
            'bodyClass'   => 'page-account',
            'noindex'     => true,
            'customer'    => CustomerAuth::user(),
            'disciplines' => self::DISCIPLINES,
            'errors'      => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function updateProfile(): void
    {
        $customerId = CustomerAuth::id();
        $model      = new Customer();

        $validator = new Validator($_POST);
        $validator->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->require('phone', 'Phone number')->phone('phone')->max('phone', 60, 'Phone number')
            ->max('location', 150, 'Location');

        if ($validator->passes() && $model->emailExists($validator->value('email'), $customerId)) {
            $validator->addManualError('email', 'Another account already uses that address.');
        }

        $password = (string) ($_POST['password'] ?? '');

        if ($password !== '') {
            $validator->min('password', 10, 'New password')
                ->matches('password_confirm', 'password', 'Passwords');

            $stored = $model->find($customerId);

            if ($stored === null || !password_verify((string) ($_POST['current_password'] ?? ''), $stored['password_hash'])) {
                $validator->addManualError('current_password', 'Your current password is not correct.');
            }
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Nothing was saved — please check the highlighted fields.');
            $this->redirect('/account/profile');
        }

        $discipline = $validator->value('discipline', '');

        $data = [
            'name'       => $validator->value('name'),
            'email'      => $validator->value('email'),
            'phone'      => $validator->value('phone'),
            'location'   => $validator->value('location') ?: null,
            'discipline' => in_array($discipline, self::DISCIPLINES, true) ? $discipline : null,
        ];

        if ($password !== '') {
            $data['password_hash'] = password_hash($password, PASSWORD_DEFAULT);
        }

        $model->updateById($customerId, $data);

        Session::clearOld();
        Session::flash('success', 'Your details have been updated.');
        $this->redirect('/account/profile');
    }

    public function horses(): void
    {
        $model      = new Customer();
        $customerId = CustomerAuth::id();
        $editId     = (int) ($_GET['edit'] ?? 0);

        $this->view('site.account-horses', [
            'pageTitle'   => 'Your Horses',
            'bodyClass'   => 'page-account',
            'noindex'     => true,
            'customer'    => CustomerAuth::user(),
            'horses'      => $model->horses($customerId),
            'editing'     => $editId > 0 ? $model->findHorse($editId, $customerId) : null,
            'disciplines' => self::DISCIPLINES,
            'errors'      => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function saveHorse(): void
    {
        $customerId = CustomerAuth::id();

        $validator = new Validator($_POST);
        $validator->require('name', 'Horse name')->max('name', 120, 'Horse name')
            ->max('breed', 120, 'Breed')
            ->max('notes', 2000, 'Notes');

        $height = $validator->value('height_hh', '');
        if ($height !== '' && (!is_numeric($height) || $height < 5 || $height > 20)) {
            $validator->addManualError('height_hh', 'Height should be in hands, for example 16.2.');
        }

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/account/horses');
        }

        $horseId = (int) ($_POST['horse_id'] ?? 0);

        (new Customer())->saveHorse($customerId, [
            'name'             => $validator->value('name'),
            'height_hh'        => $height,
            'breed'            => $validator->value('breed', ''),
            'discipline'       => $validator->value('discipline', ''),
            'saddle_seat_size' => $validator->value('saddle_seat_size', ''),
            'gullet_width'     => $validator->value('gullet_width', ''),
            'rug_size'         => $validator->value('rug_size', ''),
            'girth_size'       => $validator->value('girth_size', ''),
            'bit_size'         => $validator->value('bit_size', ''),
            'notes'            => $validator->value('notes', ''),
        ], $horseId > 0 ? $horseId : null);

        Session::clearOld();
        Session::flash('success', $horseId > 0 ? 'Horse updated.' : 'Horse saved. We will use these sizes on your next quote.');
        $this->redirect('/account/horses');
    }

    public function deleteHorse(): void
    {
        $horseId = (int) ($_POST['horse_id'] ?? 0);

        if ($horseId > 0) {
            (new Customer())->deleteHorse($horseId, CustomerAuth::id());
            Session::flash('success', 'Horse removed.');
        }

        $this->redirect('/account/horses');
    }

    // =================================================================

    private function customerQuotes(int $customerId): array
    {
        return (new Quote())->forCustomer($customerId);
    }
}
