<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Uploader;
use App\Models\Setting;
use RuntimeException;

class SettingController extends Controller
{
    public function index(): void
    {
        $this->view('admin.settings', [
            'pageTitle' => 'Site settings',
            'groups'    => (new Setting())->grouped(),
        ], 'layouts.admin');
    }

    public function update(): void
    {
        $model    = new Setting();
        $known    = $model->grouped();
        $uploader = new Uploader(config('uploads'));

        foreach ($known as $rows) {
            foreach ($rows as $row) {
                $key = $row['key_name'];

                if ($row['input_type'] === 'image') {
                    $file = $_FILES['settings']['name'][$key] ?? null;

                    if ($file !== null && $file !== '') {
                        $upload = [
                            'name'     => $_FILES['settings']['name'][$key],
                            'type'     => $_FILES['settings']['type'][$key],
                            'tmp_name' => $_FILES['settings']['tmp_name'][$key],
                            'error'    => $_FILES['settings']['error'][$key],
                            'size'     => $_FILES['settings']['size'][$key],
                        ];

                        try {
                            Setting::put($key, $uploader->store($upload, 'site'));
                        } catch (RuntimeException $e) {
                            Session::flash('error', $row['label'] . ' — ' . $e->getMessage());
                        }
                    }

                    continue;
                }

                if (!isset($_POST['settings'][$key])) {
                    continue;
                }

                $value = trim((string) $_POST['settings'][$key]);

                if ($row['input_type'] === 'email' && $value !== '' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    Session::flash('error', $row['label'] . ' is not a valid email address and was not saved.');
                    continue;
                }

                if ($row['input_type'] === 'url' && $value !== '' && !filter_var($value, FILTER_VALIDATE_URL)) {
                    Session::flash('error', $row['label'] . ' is not a valid URL and was not saved.');
                    continue;
                }

                Setting::put($key, $value);
            }
        }

        Session::flash('success', 'Settings saved.');
        $this->redirect('/admin/settings');
    }
}
