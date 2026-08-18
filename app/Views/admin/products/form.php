<?php
/** @var array|null $product @var array $images @var array $variants @var array $categories @var array $brands @var array $errors */
$isNew  = $product === null;
$err    = static fn (string $f): string => $errors[$f] ?? '';
$val    = static function (string $field, $default = '') use ($product) {
    $old = old($field, null);
    if ($old !== null) {
        return $old;
    }
    return $product[$field] ?? $default;
};
$checked = static function (string $field, int $default) use ($product, $errors): bool {
    // After a failed submit, fall back to what was posted.
    if ($errors !== []) {
        return isset($_POST[$field]);
    }
    return (int) ($product[$field] ?? $default) === 1;
};
$action = $isNew ? url('/admin/products/store') : url('/admin/products/' . $product['id'] . '/update');
$stockStatuses = ['in_stock' => 'In stock', 'low_stock' => 'Low stock', 'on_order' => 'Available on order', 'out_of_stock' => 'Currently unavailable'];
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/products')) ?>">&larr; All products</a>

  <?php if (!$isNew): ?>
    <div class="a-actions">
      <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/product/' . $product['slug'])) ?>" target="_blank" rel="noopener">View on the site</a>
      <form method="post" action="<?= e(url('/admin/products/' . $product['id'] . '/delete')) ?>"
            data-confirm="Delete &quot;<?= e($product['name']) ?>&quot;? This cannot be undone.">
        <?= csrf_field() ?>
        <button class="a-btn a-btn--danger a-btn--sm" type="submit">Delete product</button>
      </form>
    </div>
  <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-split a-split--wide-aside">

    <!-- ---- Main column ---- -->
    <div class="a-stack">

      <section class="a-panel">
        <div class="a-panel__head"><h2>Product details</h2></div>
        <div class="a-panel__body">
          <div class="a-form-grid a-form-grid--2">

            <label class="a-field a-col-full <?= $err('name') ? 'has-error' : '' ?>">
              <span class="a-label">Product name <span class="req">*</span></span>
              <input class="a-input" type="text" name="name" value="<?= e($val('name')) ?>"
                     maxlength="200" required data-slug-source>
              <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
            </label>

            <label class="a-field">
              <span class="a-label">URL slug</span>
              <input class="a-input" type="text" name="slug" value="<?= e($val('slug')) ?>"
                     maxlength="220" placeholder="auto-generated" data-slug-target
                     data-linked="<?= $isNew ? '1' : '0' ?>">
              <span class="a-hint">/product/<span><?= e($val('slug') ?: 'your-product') ?></span></span>
            </label>

            <label class="a-field <?= $err('sku') ? 'has-error' : '' ?>">
              <span class="a-label">SKU / stock code</span>
              <input class="a-input" type="text" name="sku" value="<?= e($val('sku')) ?>" maxlength="80" placeholder="TR-SD-100">
              <?php if ($err('sku')): ?><span class="a-error"><?= e($err('sku')) ?></span><?php endif; ?>
            </label>

            <label class="a-field a-col-full <?= $err('short_desc') ? 'has-error' : '' ?>">
              <span class="a-label">Short description</span>
              <textarea class="a-textarea" name="short_desc" rows="2" maxlength="500"
                        placeholder="One line shown on the product card and under the title."><?= e($val('short_desc')) ?></textarea>
              <?php if ($err('short_desc')): ?><span class="a-error"><?= e($err('short_desc')) ?></span><?php endif; ?>
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Full description</span>
              <textarea class="a-textarea a-textarea--tall" name="description" rows="9"><?= e($val('description')) ?></textarea>
              <span class="a-hint">Plain text. Line breaks are preserved on the website.</span>
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Specifications</span>
              <textarea class="a-textarea a-textarea--code" name="specifications" rows="6"
                        placeholder="Leather: vegetable-tanned&#10;Fittings: stainless steel&#10;Sizes: Pony, Cob, Full"><?= e($val('specifications')) ?></textarea>
              <span class="a-hint">One specification per line.</span>
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Sizing &amp; fitting guidance</span>
              <textarea class="a-textarea" name="sizing_guide" rows="4"><?= e($val('sizing_guide')) ?></textarea>
            </label>
          </div>
        </div>
      </section>

      <!-- Variants -->
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Options offered</h2>
          <p>Sizes or colours the customer can pick when adding this to a quote list.</p>
          <button class="a-btn a-btn--ghost a-btn--sm" type="button" id="variant-add">+ Add option</button>
        </div>
        <div class="a-panel__body">
          <div id="variant-rows">
            <?php if ($variants === []): ?>
              <div class="a-repeat-row">
                <input class="a-input" type="text" name="variant_label[]" placeholder="Size" maxlength="80">
                <input class="a-input" type="text" name="variant_value[]" placeholder="17.5 in" maxlength="120">
                <button class="a-icon-btn a-icon-btn--danger" type="button" data-remove-row aria-label="Remove">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                </button>
              </div>
            <?php else: ?>
              <?php foreach ($variants as $variant): ?>
                <div class="a-repeat-row">
                  <input class="a-input" type="text" name="variant_label[]" value="<?= e($variant['label']) ?>" maxlength="80">
                  <input class="a-input" type="text" name="variant_value[]" value="<?= e($variant['value']) ?>" maxlength="120">
                  <button class="a-icon-btn a-icon-btn--danger" type="button" data-remove-row aria-label="Remove">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                  </button>
                </div>
              <?php endforeach; ?>
            <?php endif; ?>
          </div>
          <p class="a-hint" style="margin-top:.7rem">
            Use the same label across rows to build one dropdown, e.g. Size / 16.5 in, Size / 17 in.
          </p>
        </div>
      </section>

      <!-- Images -->
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Photographs</h2>
          <p>The first image is the primary one shown on cards and search results.</p>
        </div>
        <div class="a-panel__body a-stack">

          <?php if ($images !== []): ?>
            <div class="a-images">
              <?php foreach ($images as $img): ?>
                <div class="a-image-card">
                  <?php if ((int) $img['is_primary'] === 1): ?>
                    <span class="a-image-card__flag">Primary</span>
                  <?php endif; ?>
                  <img src="<?= e(image($img['path'])) ?>" alt="" loading="lazy">
                  <div class="a-image-card__bar">
                    <?php if ((int) $img['is_primary'] !== 1): ?>
                      <button class="a-icon-btn" type="submit" title="Make primary"
                              formaction="<?= e(url('/admin/images/' . $img['id'] . '/primary')) ?>" formnovalidate>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M12 3l2.4 5.1 5.6.8-4.1 3.9 1 5.6L12 15.8 7.1 18.4l1-5.6L4 8.9l5.6-.8L12 3z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                      </button>
                    <?php endif; ?>
                    <button class="a-icon-btn a-icon-btn--danger" type="submit" title="Delete image"
                            formaction="<?= e(url('/admin/images/' . $img['id'] . '/delete')) ?>" formnovalidate
                            onclick="return confirm('Delete this image?')" style="margin-left:auto">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <label class="a-drop">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <p><strong>Choose images</strong> or drop them here</p>
            <p class="a-hint">JPG, PNG, WEBP or GIF, up to 5&nbsp;MB each. Portrait 4:5 works best.</p>
            <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            <span class="a-drop__list"></span>
          </label>

          <?php if (!$isNew): ?>
            <p class="a-hint">Images upload when you save the product.</p>
          <?php else: ?>
            <p class="a-hint">Images will be attached once the product is created.</p>
          <?php endif; ?>
        </div>
      </section>

      <!-- SEO -->
      <section class="a-panel">
        <div class="a-panel__head"><h2>Search engine listing</h2></div>
        <div class="a-panel__body">
          <div class="a-form-grid">
            <label class="a-field <?= $err('meta_title') ? 'has-error' : '' ?>">
              <span class="a-label">Meta title</span>
              <input class="a-input" type="text" name="meta_title" value="<?= e($val('meta_title')) ?>" maxlength="190"
                     placeholder="Defaults to the product name">
            </label>

            <label class="a-field <?= $err('meta_desc') ? 'has-error' : '' ?>">
              <span class="a-label">Meta description</span>
              <textarea class="a-textarea" name="meta_desc" rows="2" maxlength="300"
                        placeholder="Defaults to the short description"><?= e($val('meta_desc')) ?></textarea>
            </label>
          </div>
        </div>
      </section>
    </div>

    <!-- ---- Aside ---- -->
    <div class="a-stack">

      <section class="a-panel">
        <div class="a-panel__head"><h2>Publishing</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-switch">
            <input type="checkbox" name="is_active" value="1" <?= $checked('is_active', 1) ? 'checked' : '' ?>>
            <span class="a-switch__text">Live on the website<small>Uncheck to keep it as a draft.</small></span>
          </label>

          <label class="a-switch">
            <input type="checkbox" name="is_featured" value="1" <?= $checked('is_featured', 0) ? 'checked' : '' ?>>
            <span class="a-switch__text">Featured<small>Appears in the homepage spotlight.</small></span>
          </label>

          <label class="a-switch">
            <input type="checkbox" name="is_new" value="1" <?= $checked('is_new', 0) ? 'checked' : '' ?>>
            <span class="a-switch__text">Mark as new<small>Shows a "New" flag on the card.</small></span>
          </label>

          <label class="a-field">
            <span class="a-label">Sort order</span>
            <input class="a-input" type="number" name="sort_order" value="<?= e($val('sort_order', 0)) ?>" step="1">
            <span class="a-hint">Lower numbers appear first.</span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit"><?= $isNew ? 'Create product' : 'Save changes' ?></button>
        </div>
      </section>

      <section class="a-panel">
        <div class="a-panel__head"><h2>Organisation</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-field">
            <span class="a-label">Category</span>
            <select class="a-select" name="category_id">
              <option value="">Uncategorised</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?= (int) $cat['id'] ?>" <?= (int) $val('category_id', 0) === $cat['id'] ? 'selected' : '' ?>>
                  <?= $cat['depth'] > 0 ? '— ' : '' ?><?= e($cat['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="a-field">
            <span class="a-label">Brand</span>
            <select class="a-select" name="brand_id">
              <option value="">No brand</option>
              <?php foreach ($brands as $brand): ?>
                <option value="<?= (int) $brand['id'] ?>" <?= (int) $val('brand_id', 0) === (int) $brand['id'] ? 'selected' : '' ?>>
                  <?= e($brand['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="a-field">
            <span class="a-label">Availability</span>
            <select class="a-select" name="stock_status">
              <?php foreach ($stockStatuses as $value => $label): ?>
                <option value="<?= e($value) ?>" <?= $val('stock_status', 'in_stock') === $value ? 'selected' : '' ?>>
                  <?= e($label) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </label>
        </div>
      </section>

      <section class="a-panel">
        <div class="a-panel__head"><h2>Pricing</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-field <?= $err('price') ? 'has-error' : '' ?>">
            <span class="a-label">Price</span>
            <div class="a-input-group">
              <input class="a-input" type="number" name="price" value="<?= e($val('price')) ?>" step="0.01" min="0" placeholder="0.00">
              <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
            </div>
            <?php if ($err('price')): ?><span class="a-error"><?= e($err('price')) ?></span><?php endif; ?>
            <span class="a-hint">Leave blank for a pure quote-request item.</span>
          </label>

          <label class="a-switch">
            <input type="checkbox" name="price_visible" value="1" <?= $checked('price_visible', 0) ? 'checked' : '' ?>>
            <span class="a-switch__text">Show this price publicly<small>Off: the site says "Price on request".</small></span>
          </label>

          <label class="a-switch">
            <input type="checkbox" name="buyable" value="1" <?= $checked('buyable', 0) ? 'checked' : '' ?>>
            <span class="a-switch__text">Allow direct purchase
              <small>Customers can pay by M-Pesa without waiting for a quote. Needs a visible price above zero.</small>
            </span>
          </label>

          <label class="a-field">
            <span class="a-label">Stock quantity</span>
            <input class="a-input" type="number" name="stock_qty" value="<?= e($val('stock_qty')) ?>" min="0" step="1" placeholder="Not tracked">
            <span class="a-hint">
              Optional. When set, paid orders reduce it automatically and the product is
              marked unavailable at zero. Leave blank for made-to-order items.
            </span>
          </label>
        </div>
      </section>

      <?php if (!$isNew): ?>
        <section class="a-panel">
          <div class="a-panel__head"><h2>Record</h2></div>
          <div class="a-panel__body">
            <dl class="a-def">
              <div><dt>Views</dt><dd><?= (int) $product['views'] ?></dd></div>
              <div><dt>Created</dt><dd><?= e(pretty_date($product['created_at'], true)) ?></dd></div>
              <div><dt>Updated</dt><dd><?= e(pretty_date($product['updated_at'], true)) ?></dd></div>
            </dl>
          </div>
        </section>
      <?php endif; ?>
    </div>
  </div>
</form>
