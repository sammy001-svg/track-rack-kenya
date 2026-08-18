<?php
/**
 * Admin pagination.
 * Expects: $page (int), $pages (int), $total (int)
 */
if (($pages ?? 1) < 2) {
    return;
}

$window = 2;
$start  = max(1, $page - $window);
$end    = min($pages, $page + $window);
?>
<div class="a-panel__foot">
  <p class="a-faint" style="font-size:.78rem">
    Page <?= (int) $page ?> of <?= (int) $pages ?> &middot; <?= (int) $total ?> record<?= (int) $total === 1 ? '' : 's' ?>
  </p>

  <nav class="a-pagination" aria-label="Pagination" style="margin-left:auto">
    <a class="<?= $page <= 1 ? 'is-disabled' : '' ?>" href="<?= e(query_string(['page' => $page - 1])) ?>" aria-label="Previous">&larr;</a>

    <?php if ($start > 1): ?>
      <a href="<?= e(query_string(['page' => 1])) ?>">1</a>
      <?php if ($start > 2): ?><span class="is-disabled">…</span><?php endif; ?>
    <?php endif; ?>

    <?php for ($i = $start; $i <= $end; $i++): ?>
      <?php if ($i === $page): ?>
        <span class="is-current" aria-current="page"><?= $i ?></span>
      <?php else: ?>
        <a href="<?= e(query_string(['page' => $i])) ?>"><?= $i ?></a>
      <?php endif; ?>
    <?php endfor; ?>

    <?php if ($end < $pages): ?>
      <?php if ($end < $pages - 1): ?><span class="is-disabled">…</span><?php endif; ?>
      <a href="<?= e(query_string(['page' => $pages])) ?>"><?= $pages ?></a>
    <?php endif; ?>

    <a class="<?= $page >= $pages ? 'is-disabled' : '' ?>" href="<?= e(query_string(['page' => $page + 1])) ?>" aria-label="Next">&rarr;</a>
  </nav>
</div>
