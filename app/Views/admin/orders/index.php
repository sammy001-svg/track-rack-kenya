<?php
use App\Models\Order;
/** @var array $orders @var array $filters @var array $statusCounts @var array $revenue */
$current = $filters['status'] ?? '';
?>

<div class="a-grid a-grid--4 a-mb">
  <dl class="a-stat a-stat--good">
    <dt>Received this month</dt>
    <dd><?= e(money($revenue['month'])) ?></dd>
    <small>Cleared payments only</small>
  </dl>
  <dl class="a-stat">
    <dt>Received all time</dt>
    <dd><?= e(money($revenue['total'])) ?></dd>
  </dl>
  <dl class="a-stat <?= $revenue['awaiting'] > 0 ? 'a-stat--alert' : '' ?>">
    <dt>Awaiting payment</dt>
    <dd><?= e(money($revenue['awaiting'])) ?></dd>
    <small>Across open orders</small>
  </dl>
  <dl class="a-stat">
    <dt>Orders</dt>
    <dd><?= array_sum($statusCounts) ?></dd>
    <small><a href="<?= e(url('/admin/export/orders')) ?>" style="color:var(--a-tan)">Export as CSV</a></small>
  </dl>
</div>

<section class="a-panel">
  <div class="a-panel__head">
    <div class="a-tabs">
      <a class="a-tab <?= $current === '' || $current === null ? 'is-active' : '' ?>" href="<?= e(url('/admin/orders')) ?>">
        All <span><?= array_sum($statusCounts) ?></span>
      </a>
      <?php foreach (Order::STATUSES as $key => $label): ?>
        <a class="a-tab <?= $current === $key ? 'is-active' : '' ?>" href="<?= e(url('/admin/orders?status=' . $key)) ?>">
          <?= e($label) ?> <span><?= (int) $statusCounts[$key] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/orders')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <?php if ($current): ?><input type="hidden" name="status" value="<?= e($current) ?>"><?php endif; ?>

    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search reference, name, email or phone…" style="flex:1 1 14rem">

    <select class="a-select" name="payment" aria-label="Payment status">
      <option value="">Any payment status</option>
      <?php foreach (Order::PAYMENT_STATUSES as $key => $label): ?>
        <option value="<?= e($key) ?>" <?= ($filters['payment_status'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>

    <noscript><button class="a-btn a-btn--sm" type="submit">Filter</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> order<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($orders === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M4 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21.5 8H7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="10.5" cy="20" r="1.2" fill="currentColor"/><circle cx="18" cy="20" r="1.2" fill="currentColor"/></svg>
      <h3>No orders here</h3>
      <p>Orders appear when a customer buys an item that carries a listed price. Mark products
         as <em>buyable</em> with a visible price to enable direct purchase.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Customer</th>
            <th>Delivery</th>
            <th class="a-table__num">Items</th>
            <th class="a-table__num">Total</th>
            <th>Payment</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($orders as $order): ?>
            <tr>
              <td>
                <a class="a-ref" href="<?= e(url('/admin/orders/' . $order['id'])) ?>"><?= e($order['reference']) ?></a>
                <div class="a-cell-media__meta"><?= e(time_ago($order['created_at'])) ?></div>
              </td>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/orders/' . $order['id'])) ?>"><?= e($order['customer_name']) ?></a>
                <div class="a-cell-media__meta"><?= e($order['phone']) ?></div>
              </td>
              <td class="a-muted">
                <?= e(ucfirst($order['delivery_method'])) ?>
                <?php if ($order['delivery_town']): ?>
                  <div class="a-cell-media__meta"><?= e($order['delivery_town']) ?></div>
                <?php endif; ?>
              </td>
              <td class="a-table__num"><?= (int) $order['item_count'] ?></td>
              <td class="a-table__num">
                <strong><?= e(money($order['total'])) ?></strong>
                <?php if ((float) $order['amount_paid'] > 0 && $order['payment_status'] !== 'paid'): ?>
                  <div class="a-cell-media__meta"><?= e(money($order['amount_paid'])) ?> paid</div>
                <?php endif; ?>
              </td>
              <td><span class="a-badge a-badge--<?= $order['payment_status'] === 'paid' ? 'won' : ($order['payment_status'] === 'partial' ? 'quoted' : 'new') ?>">
                <?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?>
              </span></td>
              <td><span class="a-badge a-badge--plain"><?= e(Order::STATUSES[$order['status']]) ?></span></td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/orders/' . $order['id'] . '/print')) ?>" target="_blank" rel="noopener" title="Print">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M7 8V3h10v5M7 18H5a1 1 0 0 1-1-1v-6a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v6a1 1 0 0 1-1 1h-2M7 15h10v6H7z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="a-icon-btn" href="<?= e(url('/admin/orders/' . $order['id'])) ?>" title="Open">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php require APP_PATH . '/Views/partials/admin-pagination.php'; ?>
  <?php endif; ?>
</section>
