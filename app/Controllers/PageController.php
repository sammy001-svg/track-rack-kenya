<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Schema;
use App\Core\Seo;
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

        $seo = Seo::make()
            ->title($page['meta_title'] ?: $page['title'])
            ->description($page['meta_desc'] ?: $page['subtitle'])
            ->type('article')
            ->canonical(url('/heritage'))
            ->image(asset('/assets/img/heritage.jpg'), 'The Tack Rack workshop bench')
            ->schema(Schema::breadcrumbs(['Home' => url('/'), 'Our Heritage' => null]))
            ->schema([
                '@type'         => 'AboutPage',
                'name'          => $page['title'],
                'description'   => trim(strip_tags((string) $page['meta_desc'])),
                'url'           => url('/heritage'),
                'mainEntity'    => ['@id' => Schema::id('organisation')],
                'isPartOf'      => ['@id' => Schema::id('website')],
            ]);

        $this->view('site.heritage', [
            'seo'       => $seo,
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

        $seo = Seo::make()
            ->title($page['meta_title'] ?: $page['title'])
            ->description($page['meta_desc'] ?: $page['subtitle'])
            ->canonical(url('/page/' . $page['slug']))
            ->schema(Schema::breadcrumbs(['Home' => url('/'), $page['title'] => null]));

        // Legal pages carry no search value and only dilute the crawl budget.
        if (in_array($slug, ['privacy-policy', 'terms-of-service'], true)) {
            $seo->noindex();
        }

        // The two customer-care pages answer real questions, so mark them up
        // as such — these are the ones that can win an expandable result.
        $faqs = $this->faqsFor($slug);

        if ($faqs !== []) {
            $seo->schema(Schema::faq($faqs));
        }

        $this->view('site.page', [
            'seo'       => $seo,
            'bodyClass' => 'page-cms',
            'page'      => $page,
        ]);
    }

    /** @return array<string,string> */
    private function faqsFor(string $slug): array
    {
        return match ($slug) {
            'quote-process' => [
                'Why do you quote instead of showing prices?'
                    => 'Equestrian equipment is not a fixed-price commodity. A saddle depends on the horse, a rug on the measurement, and imported stock moves with freight and duty. Quoting lets us give an accurate figure rather than a placeholder.',
                'How long does a quote take?'
                    => 'Most quotes are returned within one working day. Items needing a fitting or a workshop assessment take longer, and we say so when we acknowledge the request.',
                'What does a quote include?'
                    => 'Current price per item in Kenyan Shillings, availability or lead time, sizing and fitting notes where relevant, delivery cost to your location, and how long the pricing is valid.',
                'Am I committed to buying?'
                    => 'No. A quote request is an enquiry, not an order. Nothing is reserved and nothing is charged until you confirm.',
            ],
            'how-to-order' => [
                'How do I order from Tack Rack?'
                    => 'Browse the catalog, add what you need to a quote list, and send the request with your contact details. You receive a reference number immediately and a full quote usually within one working day.',
                'Do you deliver across Kenya?'
                    => 'Yes. We deliver across Nairobi and dispatch countrywide by courier. Delivery cost is confirmed in your quote before you commit.',
                'Can I buy a saddle online?'
                    => 'Saddles are never sold blind. Send a quote request and we will arrange a fitting, either at the shop on Ngong Road or at your yard.',
            ],
            default => [],
        };
    }

    public function missing(): void
    {
        $this->notFound();
    }
}
