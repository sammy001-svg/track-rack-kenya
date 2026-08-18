<?php
/**
 * Account sidebar. Expects $customer and $counts (optional).
 */
$counts = $counts ?? [];

$links = [
    ['/account',          'Overview',           null],
    ['/account/orders',   'Orders',             $counts['orders']   ?? null],
    ['/account/quotes',   'Quotes',             $counts['quotes']   ?? null],
    ['/account/activity', 'Fittings & repairs', isset($counts['bookings']) ? (int) $counts['bookings'] + (int) ($counts['repairs'] ?? 0) : null],
    ['/account/horses',   'My horses',          $counts['horses']   ?? null],
    ['/account/profile',  'My details',         null],
];
?>
<aside class="account-nav">
  <div class="account-nav__card">
    <span class="account-nav__avatar"><?php
        $initials = '';
        foreach (preg_split('/\s+/', (string) $customer['name']) as $part) {
            if ($part !== '') { $initials .= mb_strtoupper(mb_substr($part, 0, 1)); }
        }
        echo e(mb_substr($initials ?: 'TR', 0, 2));
    ?></span>
    <div>
      <strong><?= e($customer['name']) ?></strong>
      <small><?= e($customer['email']) ?></small>
    </div>
  </div>

  <nav aria-label="Your account">
    <?php foreach ($links as [$path, $label, $count]): ?>
      <a class="account-nav__link <?= is_active($path, $path === '/account') ? 'is-active' : '' ?>"
         href="<?= e(url($path)) ?>">
        <span><?= e($label) ?></span>
        <?php if ($count !== null && $count > 0): ?><i><?= (int) $count ?></i><?php endif; ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <form method="post" action="<?= e(url('/account/logout')) ?>" class="account-nav__out">
    <?= csrf_field() ?>
    <button type="submit">Sign out</button>
  </form>
</aside>
