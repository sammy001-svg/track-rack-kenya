<?php /** @var array $brands */ ?>

<section class="a-panel">
  <div class="a-panel__head">
    <h2>Brands &amp; makers</h2>
    <p>Shown on the homepage brand wall and used as a filter in the shop.</p>
    <a class="a-btn a-btn--sm" href="<?= e(url('/admin/brands/create')) ?>">+ New brand</a>
  </div>

  <?php if ($brands === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.4 5.1 5.6.8-4.1 3.9 1 5.6L12 15.8 7.1 18.4l1-5.6L4 8.9l5.6-.8L12 3z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/></svg>
      <h3>No brands yet</h3>
      <p>Add the marques you carry so customers can filter by them.</p>
      <a class="a-btn" href="<?= e(url('/admin/brands/create')) ?>">Add a brand</a>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Brand</th>
            <th>Description</th>
            <th class="a-table__num">Products</th>
            <th class="a-table__num">Order</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($brands as $brand): ?>
            <tr>
              <td>
                <div class="a-cell-media">
                  <?php if (!empty($brand['logo'])): ?>
                    <img class="a-thumb" src="<?= e(image($brand['logo'])) ?>" alt="" loading="lazy" style="object-fit:contain;padding:3px;background:#fff">
                  <?php endif; ?>
                  <div>
                    <a class="a-strong" href="<?= e(url('/admin/brands/' . $brand['id'] . '/edit')) ?>"><?= e($brand['name']) ?></a>
                    <div class="a-cell-media__meta"><?= e($brand['slug']) ?></div>
                  </div>
                </div>
              </td>
              <td class="a-muted"><?= excerpt($brand['description'], 64) ?: '—' ?></td>
              <td class="a-table__num"><?= (int) $brand['product_count'] ?></td>
              <td class="a-table__num a-faint"><?= (int) $brand['sort_order'] ?></td>
              <td>
                <span class="a-badge a-badge--<?= (int) $brand['is_active'] === 1 ? 'live' : 'draft' ?>">
                  <?= (int) $brand['is_active'] === 1 ? 'Live' : 'Hidden' ?>
                </span>
              </td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/brands/' . $brand['id'] . '/edit')) ?>" title="Edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M15 4l5 5-11 11H4v-5L15 4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/brands/' . $brand['id'] . '/delete')) ?>"
                        data-confirm="Delete &quot;<?= e($brand['name']) ?>&quot;? Its products stay, without a brand.">
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
  <?php endif; ?>
</section>
