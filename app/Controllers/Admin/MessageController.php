<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Models\Message;

class MessageController extends Controller
{
    public function index(): void
    {
        $model = new Message();

        $filters = [
            'is_read' => isset($_GET['read']) && $_GET['read'] !== '' ? (int) $_GET['read'] : '',
            'q'       => trim((string) ($_GET['q'] ?? '')),
        ];

        $result = $model->paginate($filters, max(1, (int) ($_GET['page'] ?? 1)), (int) config('per_page.admin', 20));

        $this->view('admin.messages.index', [
            'pageTitle'   => 'Messages',
            'messages'    => $result['items'],
            'total'       => $result['total'],
            'pages'       => $result['pages'],
            'page'        => $result['page'],
            'filters'     => $filters,
            'unreadCount' => $model->unreadCount(),
        ], 'layouts.admin');
    }

    public function show(string $id): void
    {
        $model   = new Message();
        $message = $model->find((int) $id);

        if ($message === null) {
            Session::flash('error', 'That message no longer exists.');
            $this->redirect('/admin/messages');
        }

        if ((int) $message['is_read'] === 0) {
            $model->markRead((int) $message['id']);
            $message['is_read'] = 1;
        }

        $this->view('admin.messages.show', [
            'pageTitle' => 'Message from ' . $message['name'],
            'message'   => $message,
        ], 'layouts.admin');
    }

    public function destroy(string $id): void
    {
        (new Message())->deleteById((int) $id);

        Session::flash('success', 'Message deleted.');
        $this->redirect('/admin/messages');
    }
}
