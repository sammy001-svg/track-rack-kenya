<?php
/** @var array|null $brand @var array $errors */
$isNew = $brand === null;
$err   = static fn (string $f): string => $errors[$f] ?? '';
$val   = static function (string $field, $default = '') use ($brand) {
    $old = old($field, null);
    return $old !== null ? $old : ($brand[$field] ?? $default);
};
$checked = static function (string $field, int $default) use ($brand, $errors): bool {
    if ($errors !== []) {
        return isset($_POST[$field]);
    }
    return (int) ($brand[$field] ?? $default) === 1;
};
$action = $isNew ? url('/admin/brands/store') : url('/admin/brands/' . $brand['id'] . '/update');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/brands')) ?>">&larr; All brands</a>

  <?php if (!$isNew): ?>
    <form method="post" action="<?= e(url('/admin/brands/' . $brand['id'] . '/delete')) ?>"
          data-confirm="Delete &quot;<?= e($brand['name']) ?>&quot;?">
      <?= csrf_field() ?>
      <button class="a-btn a-btn--danger a-btn--sm" type="submit">Delete brand</button>
    </form>
  <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-split">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Brand details</h2></div>
      <div class="a-panel__body">
        <div class="a-form-grid a-form-grid--2">

          <label class="a-field <?= $err('name') ? 'has-error' : '' ?>">
            <span class="a-label">Brand name <span class="req">*</span></span>
            <input class="a-input" type="text" name="name" value="<?= e($val('name')) ?>" maxlength="150" required data-slug-source>
            <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
          </label>

          <label class="a-field">
            <span class="a-label">URL slug</span>
            <input class="a-input" type="text" name="slug" value="<?= e($val('slug')) ?>" maxlength="170"
                   placeholder="auto-generated" data-slug-target data-linked="<?= $isNew ? '1' : '0' ?>">
          </label>

          <label class="a-field a-col-full">
            <span class="a-label">Description</span>
            <textarea class="a-textarea" name="description" rows="3"
                      placeholder="One or two lines about what this maker is known for."><?= e($val('description')) ?></textarea>
          </label>

          <div class="a-field a-col-full">
            <span class="a-label">Logo</span>
            <label class="a-drop">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              <p><strong>Choose a logo</strong> or drop it here</p>
              <p class="a-hint">A transparent PNG or SVG-exported PNG works best.</p>
              <input type="file" name="logo" accept="image/jpeg,image/png,image/webp,image/gif">
              <span class="a-drop__list"></span>
            </label>

            <?php if (!empty($brand['logo'])): ?>
              <div style="margin-top:.7rem;display:flex;align-items:center;gap:.75rem">
                <img class="a-thumb" src="<?= e(image($brand['logo'])) ?>" alt="" style="width:64px;height:64px;object-fit:contain;background:#fff">
                <span class="a-hint">Current logo — uploading a new one replaces it.</span>
              </div>
            <?php else: ?>
              <span class="a-hint">Without a logo the brand wall shows the name set in a serif face.</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <div class="a-stack">
      <section class="a-panel">
        <div class="a-panel__head"><h2>Display</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-field">
            <span class="a-label">Sort order</span>
            <input class="a-input" type="number" name="sort_order" value="<?= e($val('sort_order', 0)) ?>" step="1">
          </label>

          <label class="a-switch">
            <input type="checkbox" name="is_active" value="1" <?= $checked('is_active', 1) ? 'checked' : '' ?>>
            <span class="a-switch__text">Show on the website<small>Appears on the brand wall and in the shop filter.</small></span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit"><?= $isNew ? 'Create brand' : 'Save changes' ?></button>
        </div>
      </section>
    </div>
  </div>
</form>
