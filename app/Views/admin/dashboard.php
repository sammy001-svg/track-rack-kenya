<?php
use App\Models\Booking;
use App\Models\Order;
use App\Models\Quote;
/** @var array $stats @var array $series @var array $recentQuotes @var array $revenue @var array $recentOrders @var array $upcoming */
?>

<div class="a-grid a-grid--4 a-mb">
  <dl class="a-stat <?= $stats['quotes_new'] > 0 ? 'a-stat--alert' : '' ?>">
    <dt>New quote requests</dt>
    <dd><?= (int) $stats['quotes_new'] ?></dd>
    <small><?= (int) $stats['quotes_open'] ?> open in total</small>
  </dl>

  <dl class="a-stat <?= $stats['orders_open'] > 0 ? 'a-stat--alert' : '' ?>">
    <dt>Open orders</dt>
    <dd><?= (int) $stats['orders_open'] ?></dd>
    <small><?= e(money($revenue['awaiting'])) ?> awaiting payment</small>
  </dl>

  <dl class="a-stat a-stat--good">
    <dt>Received this month</dt>
    <dd><?= e(money($revenue['month'])) ?></dd>
    <small>Cleared payments only</small>
  </dl>

  <dl class="a-stat <?= ($stats['bookings_new'] + $stats['repairs_open']) > 0 ? 'a-stat--alert' : '' ?>">
    <dt>Services to action</dt>
    <dd><?= (int) $stats['bookings_new'] + (int) $stats['repairs_open'] ?></dd>
    <small><?= (int) $stats['bookings_new'] ?> new fitting<?= (int) $stats['bookings_new'] === 1 ? '' : 's' ?>,
           <?= (int) $stats['repairs_open'] ?> open repair<?= (int) $stats['repairs_open'] === 1 ? '' : 's' ?></small>
  </dl>
</div>

<div class="a-grid a-grid--4 a-mb">
  <dl class="a-stat">
    <dt>All quote requests</dt>
    <dd><?= (int) $stats['quotes_total'] ?></dd>
    <small><?= (int) $stats['quotes_won'] ?> converted</small>
  </dl>

  <dl class="a-stat">
    <dt>Live products</dt>
    <dd><?= (int) $stats['products_live'] ?></dd>
    <small><?= (int) $stats['products_total'] ?> total, <?= (int) $stats['categories'] ?> categories</small>
  </dl>

  <dl class="a-stat">
    <dt>Registered customers</dt>
    <dd><?= (int) $stats['customers'] ?></dd>
    <small><a href="<?= e(url('/admin/customers')) ?>" style="color:var(--a-tan)">View accounts</a></small>
  </dl>

  <dl class="a-stat <?= $stats['messages_new'] > 0 ? 'a-stat--alert' : '' ?>">
    <dt>Unread messages</dt>
    <dd><?= (int) $stats['messages_new'] ?></dd>
    <small><a href="<?= e(url('/admin/messages')) ?>" style="color:var(--a-tan)">Open the inbox</a></small>
  </dl>
</div>

<?php if ($upcoming !== [] || $recentOrders !== []): ?>
  <div class="a-grid a-grid--2 a-mb">
    <?php if ($upcoming !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Fittings coming up</h2>
          <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/bookings')) ?>">All fittings</a>
        </div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($upcoming as $booking): ?>
                <tr>
                  <td style="width:8.5rem">
                    <strong><?= $booking['scheduled_at']
                        ? e(pretty_date($booking['scheduled_at'], true))
                        : e(pretty_date($booking['preferred_date'])) ?></strong>
                  </td>
                  <td>
                    <a class="a-strong" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>"><?= e($booking['name']) ?></a>
                    <div class="a-cell-media__meta"><?= e($booking['horse_name'] ?: 'Horse not named') ?></div>
                  </td>
                  <td class="a-right">
                    <span class="a-badge a-badge--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($recentOrders !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Latest orders</h2>
          <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/orders')) ?>">All orders</a>
        </div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($recentOrders as $order): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/orders/' . $order['id'])) ?>"><?= e($order['reference']) ?></a></td>
                  <td>
                    <?= e($order['customer_name']) ?>
                    <div class="a-cell-media__meta"><?= e(time_ago($order['created_at'])) ?></div>
                  </td>
                  <td class="a-table__num"><strong><?= e(money($order['total'])) ?></strong></td>
                  <td class="a-right">
                    <span class="a-badge a-badge--<?= $order['payment_status'] === 'paid' ? 'won' : 'new' ?>">
                      <?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?>
                    </span>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="a-split">
  <div class="a-stack">

    <!-- Activity chart -->
    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Quote requests, last 14 days</h2>
        <span class="a-badge a-badge--plain"><?= array_sum(array_column($series, 'total')) ?> in the period</span>
      </div>
      <div class="a-panel__body">
        <div class="a-chart">
          <?php foreach ($series as $point): ?>
            <?php $height = $point['total'] > 0 ? max(6, round(($point['total'] / $peak) * 100)) : 2; ?>
            <div class="a-chart__bar" style="height:<?= $height ?>%">
              <span><?= e(date('j M', strtotime($point['date']))) ?>: <?= (int) $point['total'] ?></span>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="a-chart-axis">
          <span><?= e(date('j M', strtotime($series[0]['date']))) ?></span>
          <span><?= e(date('j M', strtotime($series[count($series) - 1]['date']))) ?></span>
        </div>
      </div>
    </section>

    <!-- Recent quotes -->
    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Latest quote requests</h2>
        <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/quotes')) ?>">View all</a>
      </div>

      <?php if ($recentQuotes === []): ?>
        <div class="a-empty">
          <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l4 4v14H6z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M15 3v4h4" stroke="currentColor" stroke-width="1.3"/></svg>
          <h3>No quote requests yet</h3>
          <p>They will appear here the moment a visitor sends one from the website.</p>
        </div>
      <?php else: ?>
        <div class="a-table-wrap">
          <table class="a-table">
            <thead>
              <tr>
                <th>Reference</th>
                <th>Customer</th>
                <th class="a-table__num">Items</th>
                <th>Status</th>
                <th>Received</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($recentQuotes as $quote): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>"><?= e($quote['reference']) ?></a></td>
                  <td>
                    <a class="a-strong" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>"><?= e($quote['customer_name']) ?></a>
                    <div class="a-cell-media__meta"><?= e($quote['email']) ?></div>
                  </td>
                  <td class="a-table__num"><?= (int) $quote['item_count'] ?></td>
                  <td><span class="a-badge a-badge--<?= e($quote['status']) ?>"><?= e(Quote::STATUSES[$quote['status']]) ?></span></td>
                  <td class="a-faint a-nowrap"><?= e(time_ago($quote['created_at'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </div>

  <!-- Aside -->
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head"><h2>Pipeline</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <?php foreach (Quote::STATUSES as $key => $label): ?>
            <div>
              <dt><span class="a-badge a-badge--<?= e($key) ?>"><?= e($label) ?></span></dt>
              <dd style="font-weight:600;font-variant-numeric:tabular-nums">
                <a href="<?= e(url('/admin/quotes?status=' . $key)) ?>"><?= (int) ($statusCounts[$key] ?? 0) ?></a>
              </dd>
            </div>
          <?php endforeach; ?>
        </dl>
      </div>
    </section>

    <?php if ($missingImages > 0 || $emptyCategories !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Needs attention</h2></div>
        <div class="a-panel__body a-stack">
          <?php if ($missingImages > 0): ?>
            <div class="a-note">
              <strong><?= (int) $missingImages ?></strong> live product<?= $missingImages === 1 ? '' : 's' ?>
              still show the placeholder image.
              <a href="<?= e(url('/admin/products')) ?>" style="color:var(--a-tan)">Add photographs</a>
            </div>
          <?php endif; ?>

          <?php if ($emptyCategories !== []): ?>
            <div class="a-note">
              <?= count($emptyCategories) ?> categor<?= count($emptyCategories) === 1 ? 'y has' : 'ies have' ?>
              no live products:
              <?= e(implode(', ', array_slice(array_column($emptyCategories, 'name'), 0, 4))) ?><?= count($emptyCategories) > 4 ? '…' : '' ?>
            </div>
          <?php endif; ?>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($topRequested !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Most requested</h2></div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($topRequested as $row): ?>
                <tr>
                  <td><?= e($row['product_name']) ?></td>
                  <td class="a-table__num a-faint"><?= (int) $row['requests'] ?>&times;</td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($mostViewed !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Most viewed</h2></div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($mostViewed as $row): ?>
                <tr>
                  <td><a href="<?= e(url('/product/' . $row['slug'])) ?>" target="_blank" rel="noopener"><?= e($row['name']) ?></a></td>
                  <td class="a-table__num a-faint"><?= (int) $row['views'] ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Quick actions</h2></div>
      <div class="a-panel__body a-stack">
        <a class="a-btn a-btn--block" href="<?= e(url('/admin/products/create')) ?>">Add a product</a>
        <a class="a-btn a-btn--ghost a-btn--block" href="<?= e(url('/admin/categories/create')) ?>">Add a category</a>
        <a class="a-btn a-btn--ghost a-btn--block" href="<?= e(url('/')) ?>" target="_blank" rel="noopener">Open the website</a>
      </div>
    </section>
  </div>
</div>
