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

/** A stored upload path, or a category-appropriate placeholder. */
function image(?string $path, string $fallback = 'product'): string
{
    if ($path !== null && $path !== '') {
        return rtrim(BASE_URL, '/') . '/' . ltrim($path, '/');
    }
    return asset('/assets/img/placeholder-' . $fallback . '.svg');
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

/** Truncate on a word boundary. */
function excerpt(?string $text, int $length = 140): string
{
    $text = trim(strip_tags((string) $text));

    if (mb_strlen($text) <= $length) {
        return $text;
    }

    $cut   = mb_substr($text, 0, $length);
    $space = mb_strrpos($cut, ' ');

    return rtrim($space === false ? $cut : mb_substr($cut, 0, $space), " ,.;:") . '&hellip;';
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
