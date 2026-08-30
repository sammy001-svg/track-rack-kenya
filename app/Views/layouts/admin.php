<?php
use App\Core\Auth;
use App\Core\Session;
use App\Models\Booking;
use App\Models\Message;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RepairRequest;

$user        = Auth::user();
$flashes     = Session::flashes();
$newQuotes   = (new Quote())->countByStatus()['new'] ?? 0;
$newMessages = (new Message())->unreadCount();
$newBookings = (new Booking())->countByStatus()['new'] ?? 0;
$openRepairs = (new RepairRequest())->openCount();
$orderCounts = (new Order())->countByStatus();
$openOrders  = ($orderCounts['pending'] ?? 0) + ($orderCounts['confirmed'] ?? 0) + ($orderCounts['processing'] ?? 0);

$initials = '';
foreach (preg_split('/\s+/', (string) ($user['name'] ?? 'TR')) as $part) {
    if ($part !== '') {
        $initials .= mb_strtoupper(mb_substr($part, 0, 1));
    }
}
$initials = mb_substr($initials ?: 'TR', 0, 2);

/** Nav item helper. */
$navLink = static function (string $path, string $label, string $icon, int $badge = 0, bool $exact = false): void {
    $active = is_active($path, $exact) ? ' is-active' : '';
    echo '<a class="a-nav-link' . $active . '" href="' . e(url($path)) . '">' . $icon
        . '<span>' . e($label) . '</span>'
        . ($badge > 0 ? '<span class="a-nav-link__badge">' . (int) $badge . '</span>' : '')
        . '</a>';
};

$ico = [
    'dash'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="3" width="7" height="8" rx="1.6" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.6" stroke="currentColor" stroke-width="1.5"/><rect x="14" y="11" width="7" height="10" rx="1.6" stroke="currentColor" stroke-width="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.6" stroke="currentColor" stroke-width="1.5"/></svg>',
    'products' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M20.5 7.5L12 3 3.5 7.5v9L12 21l8.5-4.5v-9z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M3.5 7.5L12 12l8.5-4.5M12 12v9" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>',
    'cats'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h11" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'brands'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.4 5.1 5.6.8-4.1 3.9 1 5.6L12 15.8 7.1 18.4l1-5.6L4 8.9l5.6-.8L12 3z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/></svg>',
    'quotes'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l4 4v14H6z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M15 3v4h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'msgs'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3.5 6.5l8.5 6 8.5-6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'pages'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="4" y="3" width="16" height="18" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M8 8h8M8 12h8M8 16h5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'settings' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.5"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-2.9 1.2v.2a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 7 19.4a1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1A1.7 1.7 0 0 0 2.6 14H2.4a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 7a1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1A1.7 1.7 0 0 0 10 2.6V2.4a2 2 0 1 1 4 0v.1A1.7 1.7 0 0 0 17 4.6a1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9v.1a1.7 1.7 0 0 0 1.6 1h.2a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.6 1z" stroke="currentColor" stroke-width="1.3"/></svg>',
    'users'    => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="9" cy="8" r="3.4" stroke="currentColor" stroke-width="1.5"/><path d="M2.5 20a6.5 6.5 0 0 1 13 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/><path d="M16.5 5.2a3.4 3.4 0 0 1 0 6.6M18 14.4a6.5 6.5 0 0 1 3.5 5.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'site'     => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.5"/><path d="M3 12h18M12 3c2.5 2.6 3.8 5.7 3.8 9S14.5 18.4 12 21c-2.5-2.6-3.8-5.7-3.8-9S9.5 5.6 12 3z" stroke="currentColor" stroke-width="1.5"/></svg>',
    'orders'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21.5 8H7" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10.5" cy="20" r="1.2" fill="currentColor"/><circle cx="18" cy="20" r="1.2" fill="currentColor"/></svg>',
    'calendar' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.5"/><path d="M3 10h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'wrench'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M14.5 3.5l6 6-9 9H5.5v-6l9-9z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/><path d="M12.5 5.5l6 6" stroke="currentColor" stroke-width="1.5"/></svg>',
    'people'   => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.5"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>',
    'services' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M12 3l7.5 3.5v5c0 4.4-3.1 8.4-7.5 9.5-4.4-1.1-7.5-5.1-7.5-9.5v-5L12 3z" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/><path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
    'transfer' => '<svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M4 8h13l-3-3M20 16H7l3 3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>',
];
?>
<!doctype html>
<html lang="en-KE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle ?? 'Admin') ?> — <?= e(setting('site_name', 'Tack Rack')) ?> Admin</title>
<?php require APP_PATH . '/Views/partials/favicons.php'; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('/assets/css/admin.css')) ?>">
</head>
<body>
<div class="a-shell">

  <aside class="a-side" id="a-side">
    <div class="a-side__brand">
      <img class="a-side__logo" src="<?= e(asset('/assets/img/logo-light.png')) ?>"
           alt="<?= e(setting('site_name', 'Tack Rack')) ?>" width="420" height="191">
      <span>Admin</span>
    </div>

    <nav class="a-side__nav" aria-label="Admin">
      <div class="a-side__group">
        <p class="a-side__label">Overview</p>
        <?php $navLink('/admin', 'Dashboard', $ico['dash'], 0, true); ?>
        <?php $navLink('/admin/quotes', 'Quote requests', $ico['quotes'], $newQuotes); ?>
        <?php $navLink('/admin/orders', 'Orders', $ico['orders'], $openOrders); ?>
        <?php $navLink('/admin/messages', 'Messages', $ico['msgs'], $newMessages); ?>
      </div>

      <div class="a-side__group">
        <p class="a-side__label">Services</p>
        <?php $navLink('/admin/bookings', 'Saddle fittings', $ico['calendar'], $newBookings); ?>
        <?php $navLink('/admin/repairs', 'Workshop repairs', $ico['wrench'], $openRepairs); ?>
      </div>

      <div class="a-side__group">
        <p class="a-side__label">Catalog</p>
        <?php $navLink('/admin/products', 'Products', $ico['products']); ?>
        <?php $navLink('/admin/categories', 'Categories', $ico['cats']); ?>
        <?php $navLink('/admin/brands', 'Brands', $ico['brands']); ?>
        <?php $navLink('/admin/import', 'Import & export', $ico['transfer']); ?>
      </div>

      <div class="a-side__group">
        <p class="a-side__label">People &amp; content</p>
        <?php $navLink('/admin/customers', 'Customers', $ico['people']); ?>
        <?php $navLink('/admin/pages', 'Pages', $ico['pages']); ?>
        <?php $navLink('/admin/services', 'Services', $ico['services']); ?>
      </div>

      <?php if (Auth::isAdmin()): ?>
        <div class="a-side__group">
          <p class="a-side__label">Configuration</p>
          <?php $navLink('/admin/settings', 'Site settings', $ico['settings']); ?>
          <?php $navLink('/admin/users', 'Staff accounts', $ico['users']); ?>
        </div>
      <?php endif; ?>
    </nav>

    <div class="a-side__foot">
      <a href="<?= e(url('/')) ?>" target="_blank" rel="noopener" style="display:flex;align-items:center;gap:.5rem">
        <?= $ico['site'] ?> View the website
      </a>
    </div>
  </aside>

  <div class="a-scrim" id="a-scrim"></div>

  <div class="a-main">
    <header class="a-top">
      <button class="a-burger" id="a-burger" type="button" aria-label="Menu">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/></svg>
      </button>

      <h1><?= e($pageTitle ?? 'Admin') ?></h1>

      <div class="a-top__actions">
        <a class="a-user" href="<?= e(url('/admin/account')) ?>">
          <span class="a-avatar"><?= e($initials) ?></span>
          <span class="a-nowrap"><?= e($user['name'] ?? '') ?></span>
        </a>

        <form method="post" action="<?= e(url('/admin/logout')) ?>">
          <?= csrf_field() ?>
          <button class="a-icon-btn" type="submit" aria-label="Sign out" title="Sign out">
            <svg width="15" height="15" viewBox="0 0 24 24" fill="none"><path d="M15 17l5-5-5-5M20 12H9M12 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </button>
        </form>
      </div>
    </header>

    <div class="a-body">
      <?php if ($flashes !== []): ?>
        <div class="a-flashes">
          <?php foreach ($flashes as $flash): ?>
            <div class="a-flash a-flash--<?= e($flash['type']) ?>">
              <span><?= e($flash['message']) ?></span>
              <button type="button" aria-label="Dismiss">&times;</button>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <?= $content ?>
    </div>
  </div>
</div>

<script src="<?= e(asset('/assets/js/admin.js')) ?>" defer></script>
</body>
</html>
