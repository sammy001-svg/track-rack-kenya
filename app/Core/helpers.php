<?php
/**
 * Global helper functions, available in controllers and views.
 */

use App\Core\Csrf;
use App\Core\QuoteList;
use App\Core\Session;
use App\Models\Setting;

/** Escape for HTML output. Use on every piece of dynamic text. */
function e($value): string
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Read a dotted config value: config('db.host'), config('app'). */
function config(string $key, $default = null)
{
    static $config = null;

    if ($config === null) {
        $config = require CONFIG_PATH . '/config.php';
    }

    $value = $config;
    foreach (explode('.', $key) as $segment) {
        if (!is_array($value) || !array_key_exists($segment, $value)) {
            return $default;
        }
        $value = $value[$segment];
    }

    return $value;
}

/** Absolute URL for an application path, honouring a sub-directory install. */
function url(string $path = '/'): string
{
    $base = rtrim(BASE_URL, '/');
    $path = '/' . ltrim($path, '/');
    return $base . ($path === '/' ? '/' : $path);
}

/** URL for a file under public/, cache-busted by its modification time. */
function asset(string $path): string
{
    $path = '/' . ltrim($path, '/');
    $file = PUBLIC_PATH . $path;
    $url  = rtrim(BASE_URL, '/') . $path;

    return is_file($file) ? $url . '?v=' . filemtime($file) : $url;
}

/** A stored upload path, or a category-appropriate placeholder photograph. */
function image(?string $path, string $fallback = 'product'): string
{
    if ($path !== null && $path !== '') {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
    return asset('/assets/img/placeholder-' . $fallback . '.jpg');
}

/**
 * Turn a URL produced by asset() or image() back into its path on disk, so we
 * can look for sibling files. Returns null for anything outside this site.
 */
function local_path(string $url): ?string
{
    $url  = strtok($url, '?') ?: $url;
    $base = rtrim(BASE_URL, '/');

    if ($base !== '' && str_starts_with($url, $base)) {
        $url = substr($url, strlen($base));
    }

    if (!str_starts_with($url, '/') || str_contains($url, '..')) {
        return null;
    }

    return PUBLIC_PATH . str_replace('/', DIRECTORY_SEPARATOR, $url);
}

/**
 * A <picture> element that serves WebP where the browser supports it and falls
 * back to the original. Uploaded images get a .webp sibling automatically
 * (see ImageProcessor), as do the bundled site photographs.
 *
 * @param array $attrs Extra attributes for the <img>, e.g. ['loading' => 'lazy']
 */
function picture(string $src, string $alt = '', array $attrs = []): string
{
    $disk = local_path($src);
    $webp = null;

    if ($disk !== null) {
        $candidate = preg_replace('/\.[a-z0-9]+$/i', '', $disk) . '.webp';

        if ($candidate !== null && is_file($candidate) && !str_ends_with(strtolower($disk), '.webp')) {
            $webp = preg_replace('/\.[a-z0-9]+$/i', '', strtok($src, '?') ?: $src) . '.webp';
            $webp .= '?v=' . filemtime($candidate);
        }
    }

    $attributes = '';
    foreach ($attrs as $key => $value) {
        if ($value === true) {
            $attributes .= ' ' . $key;
        } elseif ($value !== false && $value !== null) {
            $attributes .= ' ' . $key . '="' . e($value) . '"';
        }
    }

    $img = '<img src="' . e($src) . '" alt="' . e($alt) . '"' . $attributes . '>';

    if ($webp === null) {
        return $img;
    }

    return '<picture><source srcset="' . e($webp) . '" type="image/webp">' . $img . '</picture>';
}

function slugify(string $text): string
{
    $text = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $text) ?: $text;
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}

/** Site setting by key, falling back to $default. */
function setting(string $key, $default = '')
{
    return Setting::get($key, $default);
}

function csrf_field(): string
{
    return Csrf::field();
}

function old(string $key, $default = '')
{
    return Session::old($key, $default);
}

/**
 * Truncate on a word boundary.
 *
 * Returns plain text, never HTML — the ellipsis is a real character rather than
 * an entity — so the result must still be passed through e() on output. That
 * keeps one rule for the whole codebase: escape everything at the point of use.
 */
function excerpt(?string $text, int $length = 140): string
{
    $text = trim(strip_tags((string) $text));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    $cut   = mb_substr($text, 0, $length);
    $space = mb_strrpos($cut, ' ');

    return rtrim($space === false ? $cut : mb_substr($cut, 0, $space), " ,.;:") . "\u{2026}";
}

function money($amount): string
{
    if ($amount === null || $amount === '') {
        return '';
    }
    return config('app.currency', 'KSh') . ' ' . number_format((float) $amount, 0);
}

/** "12 Aug 2026" / "12 Aug 2026, 14:30" */
function pretty_date(?string $datetime, bool $withTime = false): string
{
    if ($datetime === null || $datetime === '') {
        return '';
    }
    $ts = strtotime($datetime);
    return $ts === false ? '' : date($withTime ? 'j M Y, H:i' : 'j M Y', $ts);
}

/** "3 hours ago" */
function time_ago(?string $datetime): string
{
    $ts = $datetime ? strtotime($datetime) : false;
    if ($ts === false) {
        return '';
    }

    $diff = time() - $ts;

    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' d ago';

    return pretty_date($datetime);
}

function quote_count(): int
{
    return QuoteList::count();
}

/** True when $path matches the current request path. */
function is_active(string $path, bool $exact = false): bool
{
    $current = '/' . trim(CURRENT_PATH, '/');
    $path    = '/' . trim($path, '/');

    return $exact ? $current === $path : ($current === $path || str_starts_with($current, $path . '/'));
}

/** Rebuild the current query string with $changes applied. */
function query_string(array $changes = []): string
{
    $params = array_merge($_GET, $changes);
    $params = array_filter($params, static fn ($v) => $v !== '' && $v !== null);

    return $params === [] ? '' : '?' . http_build_query($params);
}

/** Human label for a product stock status. */
function stock_label(string $status): string
{
    return [
        'in_stock'     => 'In stock',
        'low_stock'    => 'Low stock',
        'on_order'     => 'Available on order',
        'out_of_stock' => 'Currently unavailable',
    ][$status] ?? 'Enquire';
}

/** WhatsApp deep link with a pre-filled message. */
function whatsapp_link(string $message = ''): string
{
    $number = preg_replace('/\D+/', '', (string) setting('whatsapp_number', ''));

    if ($number === '') {
        return '';
    }

    $url = 'https://wa.me/' . $number;
    return $message === '' ? $url : $url . '?text=' . rawurlencode($message);
}
