<?php /** @var array $products @var array $categories @var array $filters */ ?>

<section class="a-panel">
  <form class="a-filters" method="get" action="<?= e(url('/admin/products')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">

    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search name or SKU…" style="flex:1 1 12rem">

    <select class="a-select" name="category" aria-label="Category">
      <option value="">All categories</option>
      <?php foreach ($categories as $cat): ?>
        <option value="<?= (int) $cat['id'] ?>" <?= (int) ($filters['category_id'] ?? 0) === $cat['id'] ? 'selected' : '' ?>>
          <?= $cat['depth'] > 0 ? '— ' : '' ?><?= e($cat['name']) ?>
        </option>
      <?php endforeach; ?>
    </select>

    <select class="a-select" name="status" aria-label="Status">
      <option value="">Any status</option>
      <option value="1" <?= $filters['is_active'] === 1 ? 'selected' : '' ?>>Live</option>
      <option value="0" <?= $filters['is_active'] === 0 ? 'selected' : '' ?>>Draft</option>
    </select>

    <noscript><button class="a-btn a-btn--sm" type="submit">Filter</button></noscript>

    <span class="a-filters__count"><?= (int) $total ?> product<?= (int) $total === 1 ? '' : 's' ?></span>
    <a class="a-btn a-btn--sm" href="<?= e(url('/admin/products/create')) ?>">+ New product</a>
  </form>

  <?php if ($products === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><path d="M20.5 7.5L12 3 3.5 7.5v9L12 21l8.5-4.5v-9z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/><path d="M3.5 7.5L12 12l8.5-4.5M12 12v9" stroke="currentColor" stroke-width="1.3"/></svg>
      <h3>No products match</h3>
      <p>Adjust the filters above, or add the first product to this catalog.</p>
      <a class="a-btn" href="<?= e(url('/admin/products/create')) ?>">Add a product</a>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>Product</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Stock</th>
            <th class="a-table__num">Price</th>
            <th>Status</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($products as $product): ?>
            <tr>
              <td>
                <div class="a-cell-media">
                  <img class="a-thumb" src="<?= e(image($product['primary_image'] ?? null)) ?>" alt="" loading="lazy">
                  <div>
                    <a class="a-strong" href="<?= e(url('/admin/products/' . $product['id'] . '/edit')) ?>"><?= e($product['name']) ?></a>
                    <div class="a-cell-media__meta">
                      <?= e($product['sku'] ?: 'No SKU') ?>
                      <?php if ((int) $product['is_featured'] === 1): ?> &middot; Featured<?php endif; ?>
                      <?php if ((int) $product['is_new'] === 1): ?> &middot; New<?php endif; ?>
                    </div>
                  </div>
                </div>
              </td>
              <td class="a-muted"><?= e($product['category_name'] ?: '—') ?></td>
              <td class="a-muted"><?= e($product['brand_name'] ?: '—') ?></td>
              <td><span class="a-badge a-badge--plain"><?= e(stock_label($product['stock_status'])) ?></span></td>
              <td class="a-table__num">
                <?php if ($product['price'] !== null): ?>
                  <?= e(money($product['price'])) ?>
                  <?php if ((int) $product['price_visible'] !== 1): ?>
                    <div class="a-cell-media__meta">hidden</div>
                  <?php endif; ?>
                <?php else: ?>
                  <span class="a-faint">On request</span>
                <?php endif; ?>
              </td>
              <td>
                <span class="a-badge a-badge--<?= (int) $product['is_active'] === 1 ? 'live' : 'draft' ?>">
                  <?= (int) $product['is_active'] === 1 ? 'Live' : 'Draft' ?>
                </span>
              </td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/product/' . $product['slug'])) ?>" target="_blank" rel="noopener" title="View on the site">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <a class="a-icon-btn" href="<?= e(url('/admin/products/' . $product['id'] . '/edit')) ?>" title="Edit">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M15 4l5 5-11 11H4v-5L15 4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/products/' . $product['id'] . '/delete')) ?>"
                        data-confirm="Delete &quot;<?= e($product['name']) ?>&quot;? This cannot be undone.">
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
