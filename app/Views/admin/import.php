<?php /** @var array $categories @var array $brands @var string $imageNote @var array|null $report */ ?>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <?php if (is_array($report)): ?>
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Last import<?= !empty($report['dry_run']) ? ' (dry run)' : '' ?></h2>
          <span class="a-badge a-badge--plain">
            <?= (int) $report['created'] ?> created &middot; <?= (int) $report['updated'] ?> updated &middot; <?= (int) $report['skipped'] ?> skipped
          </span>
        </div>

        <?php if (!empty($report['errors'])): ?>
          <div class="a-panel__body">
            <p class="a-section-title">Warnings</p>
            <ul style="font-size:.82rem;color:var(--a-red);display:grid;gap:.3rem">
              <?php foreach ($report['errors'] as $error): ?>
                <li><?= e($error) ?></li>
              <?php endforeach; ?>
            </ul>
          </div>
        <?php endif; ?>

        <?php if (!empty($report['rows'])): ?>
          <div class="a-table-wrap">
            <table class="a-table">
              <thead><tr><th>Line</th><th>Product</th><th>Action</th></tr></thead>
              <tbody>
                <?php foreach (array_slice($report['rows'], 0, 40) as $row): ?>
                  <tr>
                    <td class="a-faint"><?= (int) $row['line'] ?></td>
                    <td><?= e($row['name']) ?></td>
                    <td>
                      <span class="a-badge a-badge--<?= $row['action'] === 'create' ? 'live' : 'quoted' ?>">
                        <?= $row['action'] === 'create' ? 'Created' : 'Updated' ?>
                      </span>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
          <?php if (count($report['rows']) > 40): ?>
            <div class="a-panel__foot"><span class="a-hint" style="margin:0">Showing the first 40 rows.</span></div>
          <?php endif; ?>
        <?php endif; ?>
      </section>
    <?php endif; ?>

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Import products from CSV</h2>
        <p>Bring the real catalog across in one go. Existing products are matched on SKU first,
           then on an exact name — so re-importing updates rather than duplicates.</p>
      </div>

      <form method="post" action="<?= e(url('/admin/import/products')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="a-panel__body a-stack">
          <label class="a-drop">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <p><strong>Choose a CSV file</strong> or drop it here</p>
            <p class="a-hint">UTF-8, comma separated. Download the template below for the exact columns.</p>
            <input type="file" name="csv" accept=".csv,text/csv" required>
            <span class="a-drop__list"></span>
          </label>

          <label class="a-check">
            <input type="checkbox" name="dry_run" value="1" checked>
            Dry run — show me what would happen without saving anything
          </label>
        </div>

        <div class="a-panel__foot">
          <button class="a-btn" type="submit">Run import</button>
          <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/import/template')) ?>">Download template</a>
        </div>
      </form>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Column reference</h2></div>
      <div class="a-table-wrap">
        <table class="a-table">
          <thead><tr><th>Column</th><th>Notes</th></tr></thead>
          <tbody>
            <tr><td><code>name</code></td><td><strong>Required.</strong> The product name.</td></tr>
            <tr><td><code>sku</code></td><td>Your stock code. Used to match existing products on re-import.</td></tr>
            <tr><td><code>category</code></td><td>Must match a category name exactly. Unknown names are left uncategorised and reported.</td></tr>
            <tr><td><code>brand</code></td><td>Must match a brand name exactly.</td></tr>
            <tr><td><code>short_desc</code></td><td>One line for the product card.</td></tr>
            <tr><td><code>description</code></td><td>Full description. Wrap in quotes if it contains commas or line breaks.</td></tr>
            <tr><td><code>specifications</code></td><td>One spec per line.</td></tr>
            <tr><td><code>sizing_guide</code></td><td>Fitting and sizing guidance.</td></tr>
            <tr><td><code>price</code></td><td>Numbers only, no currency symbol. Blank means quote-only.</td></tr>
            <tr><td><code>price_visible</code></td><td><code>1</code> to show the price publicly, <code>0</code> for "Price on request".</td></tr>
            <tr><td><code>buyable</code></td><td><code>1</code> to allow direct purchase and payment. Needs a visible price.</td></tr>
            <tr><td><code>stock_status</code></td><td><code>in_stock</code>, <code>low_stock</code>, <code>on_order</code> or <code>out_of_stock</code>.</td></tr>
            <tr><td><code>stock_qty</code></td><td>Optional number. Decrements automatically when an order is paid.</td></tr>
            <tr><td><code>is_featured</code></td><td><code>1</code> puts it in the homepage spotlight.</td></tr>
            <tr><td><code>is_new</code></td><td><code>1</code> shows a "New" flag.</td></tr>
            <tr><td><code>is_active</code></td><td><code>1</code> live, <code>0</code> draft. Defaults to live.</td></tr>
            <tr><td><code>sort_order</code></td><td>Lower numbers appear first.</td></tr>
          </tbody>
        </table>
      </div>
      <div class="a-panel__foot">
        <span class="a-hint" style="margin:0">
          Photographs are not imported — add them per product after the import, or upload in bulk from the product screen.
        </span>
      </div>
    </section>
  </div>

  <div class="a-stack">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Export</h2></div>
      <div class="a-panel__body a-stack">
        <a class="a-btn a-btn--ghost a-btn--block" href="<?= e(url('/admin/export/products')) ?>">Products CSV</a>
        <a class="a-btn a-btn--ghost a-btn--block" href="<?= e(url('/admin/export/quotes')) ?>">Quote requests CSV</a>
        <a class="a-btn a-btn--ghost a-btn--block" href="<?= e(url('/admin/export/orders')) ?>">Orders CSV</a>
        <p class="a-hint">
          Exports open cleanly in Excel. The products export uses the same columns as the
          import, so you can export, edit in a spreadsheet and re-import.
        </p>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Available categories</h2></div>
      <div class="a-panel__body">
        <ul style="font-size:.82rem;display:grid;gap:.2rem">
          <?php foreach ($categories as $cat): ?>
            <li class="<?= $cat['depth'] > 0 ? 'a-muted' : '' ?>" style="<?= $cat['depth'] > 0 ? 'padding-left:1rem' : 'font-weight:600' ?>">
              <?= e($cat['name']) ?>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Available brands</h2></div>
      <div class="a-panel__body">
        <ul style="font-size:.82rem;display:grid;gap:.2rem">
          <?php foreach ($brands as $brand): ?>
            <li><?= e($brand['name']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Image handling</h2></div>
      <div class="a-panel__body">
        <div class="a-note"><?= e($imageNote) ?></div>
      </div>
    </section>
  </div>
</div>
