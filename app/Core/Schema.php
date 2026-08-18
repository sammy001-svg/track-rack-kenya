<?php
namespace App\Core;

/**
 * schema.org JSON-LD builders.
 *
 * Every node carries an @id so they can reference one another inside a single
 * @graph — that is what lets Google tie a product, its breadcrumb and the
 * business together instead of reading three unrelated blobs.
 */
class Schema
{
    /** Stable identifiers used for cross-references within the graph. */
    public static function id(string $fragment): string
    {
        return rtrim(url('/'), '/') . '/#' . $fragment;
    }

    // =================================================================
    //  The business
    // =================================================================

    public static function organisation(): array
    {
        $node = [
            '@type'       => 'SportingGoodsStore',
            '@id'         => self::id('organisation'),
            'name'        => setting('site_name', 'Tack Rack Limited'),
            'description' => setting('site_intro'),
            'url'         => url('/'),
            'image'       => asset('/assets/img/og-default.jpg'),
            'logo'        => asset('/assets/img/favicon.svg'),
            'foundingDate' => setting('founded_year', '1997'),
            'currenciesAccepted' => 'KES',
            'areaServed'  => ['@type' => 'Country', 'name' => 'Kenya'],
        ];

        if ($phone = setting('contact_phone')) {
            $node['telephone'] = $phone;
        }

        if ($email = setting('contact_email')) {
            $node['email'] = $email;
        }

        if ($priceRange = setting('seo_price_range')) {
            $node['priceRange'] = $priceRange;
        }

        $node['address'] = array_filter([
            '@type'           => 'PostalAddress',
            'streetAddress'   => setting('contact_address'),
            'addressLocality' => 'Nairobi',
            'addressRegion'   => 'Nairobi',
            'addressCountry'  => 'KE',
        ]);

        // Only publish coordinates once they have actually been entered —
        // an invented location is worse than none.
        $lat = trim((string) setting('geo_latitude', ''));
        $lng = trim((string) setting('geo_longitude', ''));

        if (is_numeric($lat) && is_numeric($lng)) {
            $node['geo'] = [
                '@type'     => 'GeoCoordinates',
                'latitude'  => (float) $lat,
                'longitude' => (float) $lng,
            ];
        }

        $hours = self::openingHours();
        if ($hours !== []) {
            $node['openingHoursSpecification'] = $hours;
        }

        $sameAs = array_values(array_filter([
            setting('social_facebook'),
            setting('social_instagram'),
            setting('social_youtube'),
        ]));

        if ($sameAs !== []) {
            $node['sameAs'] = $sameAs;
        }

        return $node;
    }

    /** Machine-readable opening hours, built from the individual settings. */
    private static function openingHours(): array
    {
        $spec = [];

        $weekOpen  = trim((string) setting('hours_weekday_open', ''));
        $weekClose = trim((string) setting('hours_weekday_close', ''));

        if (self::isTime($weekOpen) && self::isTime($weekClose)) {
            $spec[] = [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
                'opens'     => $weekOpen,
                'closes'    => $weekClose,
            ];
        }

        $satOpen  = trim((string) setting('hours_saturday_open', ''));
        $satClose = trim((string) setting('hours_saturday_close', ''));

        if (self::isTime($satOpen) && self::isTime($satClose)) {
            $spec[] = [
                '@type'     => 'OpeningHoursSpecification',
                'dayOfWeek' => ['Saturday'],
                'opens'     => $satOpen,
                'closes'    => $satClose,
            ];
        }

        return $spec;
    }

    private static function isTime(string $value): bool
    {
        return (bool) preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value);
    }

    // =================================================================
    //  The site itself — enables the sitelinks search box
    // =================================================================

    public static function website(): array
    {
        return [
            '@type'      => 'WebSite',
            '@id'        => self::id('website'),
            'url'        => url('/'),
            'name'       => setting('site_name', 'Tack Rack'),
            'publisher'  => ['@id' => self::id('organisation')],
            'inLanguage' => 'en-KE',
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => [
                    '@type'       => 'EntryPoint',
                    'urlTemplate' => url('/shop') . '?q={search_term_string}',
                ],
                'query-input' => 'required name=search_term_string',
            ],
        ];
    }

    // =================================================================
    //  Breadcrumbs
    // =================================================================

    /**
     * @param array $trail Ordered [label => url]; the last entry may have a
     *                     null url to mark the current page.
     */
    public static function breadcrumbs(array $trail): array
    {
        if ($trail === []) {
            return [];
        }

        $items = [];
        $position = 1;

        foreach ($trail as $label => $itemUrl) {
            $entry = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'name'     => $label,
            ];

            if ($itemUrl !== null && $itemUrl !== '') {
                $entry['item'] = $itemUrl;
            }

            $items[] = $entry;
        }

        return [
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    // =================================================================
    //  Product
    // =================================================================

    /**
     * @param array $product Row including category_name, brand_name, sku
     * @param array $images  Rows from Product::images()
     */
    public static function product(array $product, array $images = []): array
    {
        $node = [
            '@type'       => 'Product',
            '@id'         => url('/product/' . $product['slug']) . '#product',
            'name'        => $product['name'],
            'url'         => url('/product/' . $product['slug']),
            'description' => trim(strip_tags((string) ($product['short_desc'] ?: $product['description']))),
        ];

        $photos = [];
        foreach ($images as $img) {
            $photos[] = image($img['path']);
        }

        $node['image'] = $photos !== []
            ? $photos
            : [asset('/assets/img/og-default.jpg')];

        if (!empty($product['sku'])) {
            $node['sku'] = $product['sku'];
            $node['mpn'] = $product['sku'];
        }

        if (!empty($product['brand_name'])) {
            $node['brand'] = ['@type' => 'Brand', 'name' => $product['brand_name']];
        }

        if (!empty($product['category_name'])) {
            $node['category'] = $product['category_name'];
        }

        $availability = match ($product['stock_status'] ?? 'in_stock') {
            'out_of_stock' => 'https://schema.org/OutOfStock',
            'on_order'     => 'https://schema.org/PreOrder',
            'low_stock'    => 'https://schema.org/LimitedAvailability',
            default        => 'https://schema.org/InStock',
        };

        // Only publish an Offer with a price when the price is actually public.
        // Advertising a price Google cannot verify on the page risks a penalty.
        if ((int) ($product['price_visible'] ?? 0) === 1 && $product['price'] !== null) {
            $node['offers'] = [
                '@type'         => 'Offer',
                'url'           => url('/product/' . $product['slug']),
                'price'         => number_format((float) $product['price'], 2, '.', ''),
                'priceCurrency' => 'KES',
                'availability'  => $availability,
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller'        => ['@id' => self::id('organisation')],
            ];
        } else {
            // Quote-only: describe availability without asserting a price.
            $node['offers'] = [
                '@type'         => 'Offer',
                'url'           => url('/product/' . $product['slug']),
                'priceCurrency' => 'KES',
                'availability'  => $availability,
                'itemCondition' => 'https://schema.org/NewCondition',
                'seller'        => ['@id' => self::id('organisation')],
                'priceSpecification' => [
                    '@type'         => 'PriceSpecification',
                    'priceCurrency' => 'KES',
                    'description'   => 'Price on request',
                ],
            ];
        }

        return $node;
    }

    // =================================================================
    //  Category / collection page
    // =================================================================

    public static function collection(array $category, array $products): array
    {
        $elements = [];
        $position = 1;

        foreach ($products as $product) {
            $elements[] = [
                '@type'    => 'ListItem',
                'position' => $position++,
                'url'      => url('/product/' . $product['slug']),
                'name'     => $product['name'],
            ];
        }

        return [
            '@type'       => 'CollectionPage',
            'name'        => $category['name'] ?? 'Catalog',
            'description' => trim(strip_tags((string) ($category['description'] ?? ''))),
            'url'         => url(CURRENT_PATH),
            'isPartOf'    => ['@id' => self::id('website')],
            'mainEntity'  => [
                '@type'           => 'ItemList',
                'numberOfItems'   => count($elements),
                'itemListElement' => $elements,
            ],
        ];
    }

    // =================================================================
    //  Service
    // =================================================================

    public static function service(array $service): array
    {
        $node = [
            '@type'       => 'Service',
            'name'        => $service['name'],
            'description' => trim(strip_tags((string) $service['description'])),
            'provider'    => ['@id' => self::id('organisation')],
            'areaServed'  => ['@type' => 'Country', 'name' => 'Kenya'],
            'serviceType' => $service['name'],
        ];

        if (!empty($service['price_from'])) {
            $node['offers'] = [
                '@type'         => 'Offer',
                'priceCurrency' => 'KES',
                'price'         => number_format((float) $service['price_from'], 2, '.', ''),
                'description'   => 'From',
            ];
        }

        return $node;
    }

    // =================================================================
    //  FAQ — earns expandable answers directly in the results page
    // =================================================================

    /** @param array<string,string> $faqs question => answer */
    public static function faq(array $faqs): array
    {
        if ($faqs === []) {
            return [];
        }

        $entities = [];

        foreach ($faqs as $question => $answer) {
            $entities[] = [
                '@type'          => 'Question',
                'name'           => $question,
                'acceptedAnswer' => [
                    '@type' => 'Answer',
                    'text'  => trim(strip_tags($answer)),
                ],
            ];
        }

        return ['@type' => 'FAQPage', 'mainEntity' => $entities];
    }
}
