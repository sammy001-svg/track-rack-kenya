<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Page;

class PageController extends Controller
{
    /** The Heritage page gets a bespoke editorial layout. */
    public function heritage(): void
    {
        $page = (new Page())->bySlug('heritage');

        if ($page === null) {
            $this->notFound();
        }

        $this->view('site.heritage', [
            'pageTitle' => $page['title'],
            'metaDesc'  => $page['meta_desc'],
            'bodyClass' => 'page-heritage',
            'page'      => $page,
            'pillars'   => (new Category())->pillars(),
        ]);
    }

    /** Generic CMS page: how-to-order, quote-process, privacy-policy, terms. */
    public function show(string $slug): void
    {
        if ($slug === 'heritage') {
            $this->heritage();
            return;
        }

        $page = (new Page())->bySlug($slug);

        if ($page === null) {
            $this->notFound();
        }

        $this->view('site.page', [
            'pageTitle' => $page['title'],
            'metaDesc'  => $page['meta_desc'],
            'bodyClass' => 'page-cms',
            'page'      => $page,
        ]);
    }

    public function missing(): void
    {
        $this->notFound();
    }
}
