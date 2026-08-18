<?php
use App\Models\Booking;
/** @var array $bookings @var array $filters @var array $statusCounts @var array $upcoming */
$current = $filters['status'] ?? '';
?>

<?php if ($upcoming !== []): ?>
  <section class="a-panel a-mb">
    <div class="a-panel__head"><h2>Coming up</h2></div>
    <div class="a-table-wrap">
      <table class="a-table">
        <tbody>
          <?php foreach ($upcoming as $booking): ?>
            <tr>
              <td style="width:9rem">
                <strong><?= $booking['scheduled_at']
                    ? e(pretty_date($booking['scheduled_at'], true))
                    : e(pretty_date($booking['preferred_date'])) ?></strong>
                <div class="a-cell-media__meta"><?= e(Booking::SLOTS[$booking['preferred_slot']] ?? '') ?></div>
              </td>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>"><?= e($booking['name']) ?></a>
                <div class="a-cell-media__meta">
                  <?= e($booking['horse_name'] ?: 'Horse not named') ?>
                  <?= (int) $booking['at_yard'] === 1 ? ' &middot; at ' . e($booking['location'] ?: 'their yard') : ' &middot; at the shop' ?>
                </div>
              </td>
              <td class="a-right"><span class="a-badge a-badge--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
<?php endif; ?>

<section class="a-panel">
  <div class="a-panel__head">
    <div class="a-tabs">
      <a class="a-tab <?= $current === '' || $current === null ? 'is-active' : '' ?>" href="<?= e(url('/admin/bookings')) ?>">
        All <span><?= array_sum($statusCounts) ?></span>
      </a>
      <?php foreach (Booking::STATUSES as $key => $label): ?>
        <a class="a-tab <?= $current === $key ? 'is-active' : '' ?>" href="<?= e(url('/admin/bookings?status=' . $key)) ?>">
          <?= e($label) ?> <span><?= (int) $statusCounts[$key] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/bookings')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <?php if ($current): ?><input type="hidden" name="status" value="<?= e($current) ?>"><?php endif; ?>
    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search reference, name, email or horse…" style="flex:1 1 16rem">
    <noscript><button class="a-btn a-btn--sm" type="submit">Search</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> booking<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($bookings === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="16" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M3 9h18M8 3v4M16 3v4" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
      <h3>No fittings here</h3>
      <p>Requests made through the saddle fitting page appear in this list.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Customer</th>
            <th>Horse</th>
            <th>Where</th>
            <th>When</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($bookings as $booking): ?>
            <tr>
              <td><a class="a-ref" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>"><?= e($booking['reference']) ?></a></td>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>"><?= e($booking['name']) ?></a>
                <div class="a-cell-media__meta"><?= e($booking['phone']) ?></div>
              </td>
              <td class="a-muted">
                <?= e($booking['horse_name'] ?: '—') ?>
                <?php if ($booking['discipline']): ?>
                  <div class="a-cell-media__meta"><?= e($booking['discipline']) ?></div>
                <?php endif; ?>
              </td>
              <td class="a-muted">
                <?= (int) $booking['at_yard'] === 1
                    ? '<strong>Yard</strong><div class="a-cell-media__meta">' . e($booking['location'] ?: 'not given') . '</div>'
                    : 'Shop' ?>
              </td>
              <td class="a-nowrap">
                <?php if ($booking['scheduled_at']): ?>
                  <strong><?= e(pretty_date($booking['scheduled_at'], true)) ?></strong>
                <?php elseif ($booking['preferred_date']): ?>
                  <?= e(pretty_date($booking['preferred_date'])) ?>
                  <div class="a-cell-media__meta">requested</div>
                <?php else: ?>
                  <span class="a-faint">Flexible</span>
                <?php endif; ?>
              </td>
              <td><span class="a-badge a-badge--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span></td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>" title="Open">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/bookings/' . $booking['id'] . '/delete')) ?>"
                        data-confirm="Delete booking <?= e($booking['reference']) ?>?">
                    <?= csrf_field() ?>
                    <button class="a-icon-btn a-icon-btn--danger" type="submit" title="Delete">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                  </form>
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
