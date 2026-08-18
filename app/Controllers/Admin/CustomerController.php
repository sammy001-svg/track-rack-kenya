<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RepairRequest;

class CustomerController extends Controller
{
    public function index(): void
    {
        $model = new Customer();

        $filters = ['q' => trim((string) ($_GET['q'] ?? ''))];
        $result  = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.customers.index', [
            'pageTitle' => 'Customers',
            'customers' => $result['items'],
            'total'     => $result['total'],
            'pages'     => $result['pages'],
            'page'      => $result['page'],
            'filters'   => $filters,
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $customerId = (int) $id;
        $model      = new Customer();
        $customer   = $model->find($customerId);

        if ($customer === null) {
            Session::flash('error', 'That customer no longer exists.');
            $this->redirect('/admin/customers');
        }

        unset($customer['password_hash'], $customer['reset_token']);

        $this->view('admin.customers.show', [
            'pageTitle' => $customer['name'],
            'customer'  => $customer,
            'counts'    => $model->activityCounts($customerId),
            'horses'    => $model->horses($customerId),
            'orders'    => (new Order())->forCustomer($customerId),
            'quotes'    => (new Quote())->forCustomer($customerId),
            'bookings'  => (new Booking())->forCustomer($customerId),
            'repairs'   => (new RepairRequest())->forCustomer($customerId),
        ], 'layouts.admin');
    }

    public function update(string $id): void
    {
        $customerId = (int) $id;
        $model      = new Customer();

        if ($model->find($customerId) === null) {
            Session::flash('error', 'That customer no longer exists.');
            $this->redirect('/admin/customers');
        }

        $model->updateById($customerId, [
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Session::flash('success', 'Customer account updated.');
        $this->redirect('/admin/customers/' . $customerId);
    }

    public function destroy(string $id): void
    {
        $customerId = (int) $id;

        // Orders, quotes, bookings and repairs are kept — their customer_id
        // is nulled by the foreign keys, so the business record survives.
        (new Customer())->deleteById($customerId);

        Session::flash('success', 'Customer account deleted. Their orders and quotes have been kept.');
        $this->redirect('/admin/customers');
    }
}
