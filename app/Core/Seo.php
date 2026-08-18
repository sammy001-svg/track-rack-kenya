<?php
namespace App\Core;

/**
 * Builds the contents of <head> for a page: title, description, canonical,
 * robots directives, Open Graph and Twitter cards.
 *
 * Controllers describe the page; this decides how it is presented to search
 * engines. Keeping it in one place means length limits, fallbacks and the
 * title template are applied consistently rather than per-view.
 */
class Seo
{
    /** Google truncates titles around 60 characters and descriptions around 160. */
    public const TITLE_MAX = 60;
    public const DESC_MAX  = 158;

    private string $title = '';
    private string $description = '';
    private ?string $canonical = null;
    private ?string $image = null;
    private ?string $imageAlt = null;
    private string $type = 'website';
    private bool $index = true;
    private bool $follow = true;
    private array $schema = [];
    /** @var array<string,string> */
    private array $extraMeta = [];

    public static function make(): self
    {
        return new self();
    }

    // ---- Fluent setters -------------------------------------------------

    public function title(?string $title): self
    {
        $this->title = trim((string) $title);
        return $this;
    }

    public function description(?string $description): self
    {
        $this->description = trim(strip_tags((string) $description));
        return $this;
    }

    public function canonical(?string $url): self
    {
        $this->canonical = $url;
        return $this;
    }

    public function image(?string $url, ?string $alt = null): self
    {
        if ($url !== null && $url !== '') {
            $this->image    = $url;
            $this->imageAlt = $alt;
        }
        return $this;
    }

    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /** Keep the page out of the index; links are still followed by default. */
    public function noindex(bool $follow = true): self
    {
        $this->index  = false;
        $this->follow = $follow;
        return $this;
    }

    public function schema(array $node): self
    {
        if ($node !== []) {
            $this->schema[] = $node;
        }
        return $this;
    }

    public function meta(string $name, string $content): self
    {
        $this->extraMeta[$name] = $content;
        return $this;
    }

    // ---- Resolved values ------------------------------------------------

    /** "Page title | Tack Rack Kenya", trimmed to a length Google will show. */
    public function fullTitle(): string
    {
        $suffix = trim((string) setting('seo_title_suffix', 'Tack Rack Kenya'));
        $title  = $this->title !== '' ? $this->title : (string) setting('seo_home_title', 'Equestrian Supplies');

        if ($suffix === '' || $title === $suffix) {
            return $this->clamp($title, self::TITLE_MAX);
        }

        $combined = $title . ' | ' . $suffix;

        // Only drop the suffix if the page's own title still needs the room.
        if (mb_strlen($combined) > self::TITLE_MAX && mb_strlen($title) > self::TITLE_MAX - 8) {
            return $this->clamp($title, self::TITLE_MAX);
        }

        return $combined;
    }

    public function resolvedDescription(): string
    {
        $description = $this->description !== ''
            ? $this->description
            : (string) setting('seo_default_desc', setting('site_intro', ''));

        return $this->clamp(trim($description), self::DESC_MAX);
    }

    public function resolvedImage(): string
    {
        if ($this->image !== null) {
            return $this->image;
        }

        $configured = (string) setting('seo_share_image', '');

        return $configured !== ''
            ? image($configured)
            : asset('/assets/img/og-default.jpg');
    }

    public function resolvedCanonical(): string
    {
        return $this->canonical ?? url(CURRENT_PATH);
    }

    public function robots(): string
    {
        return ($this->index ? 'index' : 'noindex') . ', ' . ($this->follow ? 'follow' : 'nofollow');
    }

    // ---- Rendering ------------------------------------------------------

    /** The complete set of SEO tags, ready to drop into <head>. */
    public function render(): string
    {
        $title       = $this->fullTitle();
        $description = $this->resolvedDescription();
        $canonical   = $this->resolvedCanonical();
        $imageUrl    = $this->resolvedImage();
        $siteName    = (string) setting('site_name', 'Tack Rack');

        $out = [];
        $out[] = '<title>' . e($title) . '</title>';
        $out[] = '<meta name="description" content="' . e($description) . '">';
        $out[] = '<meta name="robots" content="' . e($this->robots()) . '">';
        $out[] = '<link rel="canonical" href="' . e($canonical) . '">';

        foreach ($this->extraMeta as $name => $content) {
            $out[] = '<meta name="' . e($name) . '" content="' . e($content) . '">';
        }

        // Open Graph
        $out[] = '';
        $out[] = '<meta property="og:type" content="' . e($this->type) . '">';
        $out[] = '<meta property="og:site_name" content="' . e($siteName) . '">';
        $out[] = '<meta property="og:title" content="' . e($title) . '">';
        $out[] = '<meta property="og:description" content="' . e($description) . '">';
        $out[] = '<meta property="og:url" content="' . e($canonical) . '">';
        $out[] = '<meta property="og:locale" content="en_KE">';
        $out[] = '<meta property="og:image" content="' . e($imageUrl) . '">';
        $out[] = '<meta property="og:image:width" content="1200">';
        $out[] = '<meta property="og:image:height" content="630">';

        if ($this->imageAlt !== null && $this->imageAlt !== '') {
            $out[] = '<meta property="og:image:alt" content="' . e($this->imageAlt) . '">';
        }

        // Twitter / X
        $out[] = '';
        $out[] = '<meta name="twitter:card" content="summary_large_image">';
        $out[] = '<meta name="twitter:title" content="' . e($title) . '">';
        $out[] = '<meta name="twitter:description" content="' . e($description) . '">';
        $out[] = '<meta name="twitter:image" content="' . e($imageUrl) . '">';

        $handle = trim((string) setting('seo_twitter_handle', ''));
        if ($handle !== '') {
            $handle = '@' . ltrim($handle, '@');
            $out[] = '<meta name="twitter:site" content="' . e($handle) . '">';
        }

        // Structured data — one graph, so the entities can reference each other.
        $graph = array_merge([Schema::organisation(), Schema::website()], $this->schema);

        $json = json_encode(
            ['@context' => 'https://schema.org', '@graph' => array_values(array_filter($graph))],
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
        );

        $out[] = '';
        $out[] = '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>';

        return implode("\n", $out);
    }

    /** Trim to a length, on a word boundary, without a dangling ellipsis. */
    private function clamp(string $text, int $limit): string
    {
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        if (mb_strlen($text) <= $limit) {
            return $text;
        }

        $cut   = mb_substr($text, 0, $limit);
        $space = mb_strrpos($cut, ' ');

        return rtrim($space === false ? $cut : mb_substr($cut, 0, $space), " ,.;:—-");
    }
}
