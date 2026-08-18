<?php
use App\Models\Quote;
/** @var array $quotes @var array $filters @var array $statusCounts */
$currentStatus = $filters['status'] ?? '';
?>

<section class="a-panel">
  <div class="a-panel__head">
    <div class="a-tabs">
      <a class="a-tab <?= $currentStatus === '' || $currentStatus === null ? 'is-active' : '' ?>"
         href="<?= e(url('/admin/quotes')) ?>">All <span><?= array_sum($statusCounts) ?></span></a>
      <?php foreach (Quote::STATUSES as $key => $label): ?>
        <a class="a-tab <?= $currentStatus === $key ? 'is-active' : '' ?>"
           href="<?= e(url('/admin/quotes?status=' . $key)) ?>"><?= e($label) ?> <span><?= (int) $statusCounts[$key] ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/quotes')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <?php if ($currentStatus): ?><input type="hidden" name="status" value="<?= e($currentStatus) ?>"><?php endif; ?>

    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search reference, name, email or phone…" style="flex:1 1 16rem">

    <noscript><button class="a-btn a-btn--sm" type="submit">Search</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> request<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($quotes === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M6 3h9l4 4v14H6z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M15 3v4h4M9 12h6M9 16h6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
      <h3>Nothing here</h3>
      <p>No quote requests match this view. New requests arrive the moment a visitor sends one.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Customer</th>
            <th>Contact</th>
            <th class="a-table__num">Items</th>
            <th>Status</th>
            <th>Received</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($quotes as $quote): ?>
            <tr>
              <td><a class="a-ref" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>"><?= e($quote['reference']) ?></a></td>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>"><?= e($quote['customer_name']) ?></a>
                <?php if ($quote['location']): ?>
                  <div class="a-cell-media__meta"><?= e($quote['location']) ?></div>
                <?php endif; ?>
              </td>
              <td class="a-muted" style="font-size:.8rem">
                <a href="mailto:<?= e($quote['email']) ?>"><?= e($quote['email']) ?></a><br>
                <a href="tel:<?= e(preg_replace('/\s+/', '', $quote['phone'])) ?>"><?= e($quote['phone']) ?></a>
              </td>
              <td class="a-table__num"><?= (int) $quote['item_count'] ?></td>
              <td><span class="a-badge a-badge--<?= e($quote['status']) ?>"><?= e(Quote::STATUSES[$quote['status']]) ?></span></td>
              <td class="a-faint a-nowrap" title="<?= e(pretty_date($quote['created_at'], true)) ?>"><?= e(time_ago($quote['created_at'])) ?></td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>" title="Open">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/quotes/' . $quote['id'] . '/delete')) ?>"
                        data-confirm="Delete quote <?= e($quote['reference']) ?>? This cannot be undone.">
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
