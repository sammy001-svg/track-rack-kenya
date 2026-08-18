<?php /** @var array $customers @var array $filters */ ?>

<section class="a-panel">
  <div class="a-panel__head">
    <h2>Customer accounts</h2>
    <p>People who have registered on the website. Guests who order without an account are not listed here.</p>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/customers')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search name, email or phone…" style="flex:1 1 16rem">
    <noscript><button class="a-btn a-btn--sm" type="submit">Search</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> customer<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($customers === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="3.6" stroke="currentColor" stroke-width="1.3"/><path d="M4.5 20a7.5 7.5 0 0 1 15 0" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
      <h3>No accounts yet</h3>
      <p>Customers can register from the website to track their quotes, orders, fittings and repairs.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Name</th>
            <th>Contact</th>
            <th>Location</th>
            <th class="a-table__num">Orders</th>
            <th class="a-table__num">Quotes</th>
            <th>Last signed in</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($customers as $customer): ?>
            <tr>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/customers/' . $customer['id'])) ?>"><?= e($customer['name']) ?></a>
                <div class="a-cell-media__meta">Joined <?= e(pretty_date($customer['created_at'])) ?></div>
              </td>
              <td class="a-muted" style="font-size:.8rem">
                <a href="mailto:<?= e($customer['email']) ?>"><?= e($customer['email']) ?></a>
                <?php if ($customer['phone']): ?><br><?= e($customer['phone']) ?><?php endif; ?>
              </td>
              <td class="a-muted"><?= e($customer['location'] ?: '—') ?></td>
              <td class="a-table__num"><?= (int) $customer['order_count'] ?></td>
              <td class="a-table__num"><?= (int) $customer['quote_count'] ?></td>
              <td class="a-faint a-nowrap"><?= $customer['last_login_at'] ? e(time_ago($customer['last_login_at'])) : 'Never' ?></td>
              <td>
                <span class="a-badge a-badge--<?= (int) $customer['is_active'] === 1 ? 'live' : 'draft' ?>">
                  <?= (int) $customer['is_active'] === 1 ? 'Active' : 'Disabled' ?>
                </span>
              </td>
              <td class="a-table__actions">
                <a class="a-icon-btn" href="<?= e(url('/admin/customers/' . $customer['id'])) ?>" title="Open">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php require APP_PATH . '/Views/partials/admin-pagination.php'; ?>
  <?php endif; ?>
</section>
