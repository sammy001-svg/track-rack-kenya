<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Page;

class PageController extends Controller
{
    public function index(): void
    {
        $this->view('admin.pages.index', [
            'pageTitle' => 'Pages',
            'pages'     => (new Page())->allOrdered(),
        ], 'layouts.admin');
    }

    public function edit(string $id): void
    {
        $page = (new Page())->find((int) $id);

        if ($page === null) {
            Session::flash('error', 'That page no longer exists.');
            $this->redirect('/admin/pages');
        }

        $this->view('admin.pages.form', [
            'pageTitle' => 'Edit: ' . $page['title'],
            'page'      => $page,
            'errors'    => Session::errors(),
        ], 'layouts.admin');

        Session::clearOld();
    }

    public function update(string $id): void
    {
        $pageId = (int) $id;
        $model  = new Page();

        if ($model->find($pageId) === null) {
            Session::flash('error', 'That page no longer exists.');
            $this->redirect('/admin/pages');
        }

        $validator = new Validator($_POST);
        $validator->require('title', 'Page title')->max('title', 200, 'Page title')
            ->max('subtitle', 300, 'Subtitle')
            ->max('meta_desc', 300, 'Meta description')
            ->require('body', 'Page content');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields.');
            $this->redirect('/admin/pages/' . $pageId . '/edit');
        }

        $model->updateById($pageId, [
            'title'     => $validator->value('title'),
            'subtitle'  => $validator->value('subtitle') ?: null,
            'body'      => $this->sanitiseHtml((string) ($_POST['body'] ?? '')),
            'meta_desc' => $validator->value('meta_desc') ?: null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ]);

        Session::clearOld();
        Session::flash('success', 'Page saved.');
        $this->redirect('/admin/pages/' . $pageId . '/edit');
    }

    /**
     * Page bodies are rendered unescaped, so strip anything executable.
     * Only a conservative set of formatting tags survives.
     */
    private function sanitiseHtml(string $html): string
    {
        // Remove script/style/iframe blocks entirely, contents included.
        $html = preg_replace('#<(script|style|iframe|object|embed|form)\b[^>]*>.*?</\1>#is', '', $html) ?? $html;
        $html = preg_replace('#<(script|style|iframe|object|embed|form|input|button)\b[^>]*/?>#i', '', $html) ?? $html;

        $allowed = '<p><br><strong><b><em><i><u><h2><h3><h4><ul><ol><li><a><blockquote><hr><table><thead><tbody><tr><th><td><small><span><div>';
        $html    = strip_tags($html, $allowed);

        // Drop inline event handlers and javascript: URLs.
        $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;
        $html = preg_replace('/(href|src)\s*=\s*(["\']?)\s*javascript:[^"\'>\s]*/i', '$1=$2#', $html) ?? $html;

        return trim($html);
    }
}
