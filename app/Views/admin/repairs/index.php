<?php
use App\Models\RepairRequest;
/** @var array $repairs @var array $filters @var array $statusCounts */
$current = $filters['status'] ?? '';
?>

<section class="a-panel">
  <div class="a-panel__head">
    <div class="a-tabs">
      <a class="a-tab <?= $current === '' || $current === null ? 'is-active' : '' ?>" href="<?= e(url('/admin/repairs')) ?>">
        All <span><?= array_sum($statusCounts) ?></span>
      </a>
      <?php foreach (RepairRequest::STATUSES as $key => $label): ?>
        <?php if ($statusCounts[$key] === 0 && !in_array($key, ['new', 'assessing', 'quoted', 'in_progress', 'ready'], true)) continue; ?>
        <a class="a-tab <?= $current === $key ? 'is-active' : '' ?>" href="<?= e(url('/admin/repairs?status=' . $key)) ?>">
          <?= e($label) ?> <span><?= (int) $statusCounts[$key] ?></span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/repairs')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <?php if ($current): ?><input type="hidden" name="status" value="<?= e($current) ?>"><?php endif; ?>
    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search reference, name, email or item…" style="flex:1 1 16rem">
    <noscript><button class="a-btn a-btn--sm" type="submit">Search</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> request<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($repairs === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M14.5 3.5l6 6-9 9H5.5v-6l9-9z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M12.5 5.5l6 6" stroke="currentColor" stroke-width="1.3"/></svg>
      <h3>No repair requests here</h3>
      <p>Requests sent through the workshop repairs page appear in this list.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Reference</th>
            <th>Customer</th>
            <th>Item</th>
            <th>Urgency</th>
            <th class="a-table__num">Photos</th>
            <th class="a-table__num">Quoted</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($repairs as $repair): ?>
            <tr>
              <td><a class="a-ref" href="<?= e(url('/admin/repairs/' . $repair['id'])) ?>"><?= e($repair['reference']) ?></a></td>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/repairs/' . $repair['id'])) ?>"><?= e($repair['name']) ?></a>
                <div class="a-cell-media__meta"><?= e($repair['phone']) ?></div>
              </td>
              <td>
                <?= e($repair['item_type']) ?>
                <?php if ($repair['item_make']): ?>
                  <div class="a-cell-media__meta"><?= e($repair['item_make']) ?></div>
                <?php endif; ?>
              </td>
              <td>
                <?php $urgent = $repair['urgency'] !== 'standard'; ?>
                <span class="a-badge <?= $urgent ? 'a-badge--new' : 'a-badge--plain' ?>">
                  <?= e(ucfirst($repair['urgency'])) ?>
                </span>
              </td>
              <td class="a-table__num"><?= (int) $repair['photo_count'] ?></td>
              <td class="a-table__num">
                <?= $repair['quoted_amount'] !== null ? e(money($repair['quoted_amount'])) : '<span class="a-faint">—</span>' ?>
              </td>
              <td><span class="a-badge a-badge--<?= e($repair['status']) ?>"><?= e(RepairRequest::STATUSES[$repair['status']]) ?></span></td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/repairs/' . $repair['id'])) ?>" title="Open">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/repairs/' . $repair['id'] . '/delete')) ?>"
                        data-confirm="Delete repair <?= e($repair['reference']) ?> and its photographs?">
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
