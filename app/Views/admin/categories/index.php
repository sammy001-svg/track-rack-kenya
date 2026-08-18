<?php /** @var array $categories */ ?>

<section class="a-panel">
  <div class="a-panel__head">
    <h2>Catalog structure</h2>
    <p>Top-level sections are the three pillars shown on the homepage. Sub-categories fill the shop filters and mega menu.</p>
    <a class="a-btn a-btn--sm" href="<?= e(url('/admin/categories/create')) ?>">+ New category</a>
  </div>

  <?php if ($categories === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M3 6h18M3 12h18M3 18h11" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
      <h3>No categories yet</h3>
      <p>Create Rider, Horse and Stable as top-level sections, then add sub-categories beneath them.</p>
      <a class="a-btn" href="<?= e(url('/admin/categories/create')) ?>">Add a category</a>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Category</th>
            <th>Tagline</th>
            <th class="a-table__num">Products</th>
            <th class="a-table__num">Order</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($categories as $cat): ?>
            <tr>
              <td>
                <div class="<?= $cat['parent_id'] !== null ? 'a-indent' : '' ?>">
                  <a class="a-strong" href="<?= e(url('/admin/categories/' . $cat['id'] . '/edit')) ?>"><?= e($cat['name']) ?></a>
                  <div class="a-cell-media__meta">
                    /shop/<?= e($cat['slug']) ?>
                    <?php if ($cat['parent_name']): ?> &middot; in <?= e($cat['parent_name']) ?><?php endif; ?>
                  </div>
                </div>
              </td>
              <td class="a-muted"><?= e(excerpt($cat['tagline'], 46) ?: '—') ?></td>
              <td class="a-table__num"><?= (int) $cat['product_count'] ?></td>
              <td class="a-table__num a-faint"><?= (int) $cat['sort_order'] ?></td>
              <td>
                <span class="a-badge a-badge--<?= (int) $cat['is_active'] === 1 ? 'live' : 'draft' ?>">
                  <?= (int) $cat['is_active'] === 1 ? 'Live' : 'Hidden' ?>
                </span>
              </td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/shop/' . $cat['slug'])) ?>" target="_blank" rel="noopener" title="View on the site">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="a-icon-btn" href="<?= e(url('/admin/categories/' . $cat['id'] . '/edit')) ?>" title="Edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M15 4l5 5-11 11H4v-5L15 4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/categories/' . $cat['id'] . '/delete')) ?>"
                        data-confirm="Delete &quot;<?= e($cat['name']) ?>&quot;? Its <?= (int) $cat['product_count'] ?> product(s) will become uncategorised.">
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
