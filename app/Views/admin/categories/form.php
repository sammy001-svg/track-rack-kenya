<?php
/** @var array|null $category @var array $pillars @var array $errors */
$isNew = $category === null;
$err   = static fn (string $f): string => $errors[$f] ?? '';
$val   = static function (string $field, $default = '') use ($category) {
    $old = old($field, null);
    return $old !== null ? $old : ($category[$field] ?? $default);
};
$checked = static function (string $field, int $default) use ($category, $errors): bool {
    if ($errors !== []) {
        return isset($_POST[$field]);
    }
    return (int) ($category[$field] ?? $default) === 1;
};
$action = $isNew ? url('/admin/categories/store') : url('/admin/categories/' . $category['id'] . '/update');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/categories')) ?>">&larr; All categories</a>

  <?php if (!$isNew): ?>
    <form method="post" action="<?= e(url('/admin/categories/' . $category['id'] . '/delete')) ?>"
          data-confirm="Delete &quot;<?= e($category['name']) ?>&quot;?">
      <?= csrf_field() ?>
      <button class="a-btn a-btn--danger a-btn--sm" type="submit">Delete category</button>
    </form>
  <?php endif; ?>
</div>

<form method="post" action="<?= e($action) ?>" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-split">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Category details</h2></div>
      <div class="a-panel__body">
        <div class="a-form-grid a-form-grid--2">

          <label class="a-field <?= $err('name') ? 'has-error' : '' ?>">
            <span class="a-label">Name <span class="req">*</span></span>
            <input class="a-input" type="text" name="name" value="<?= e($val('name')) ?>" maxlength="150" required data-slug-source>
            <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
          </label>

          <label class="a-field">
            <span class="a-label">URL slug</span>
            <input class="a-input" type="text" name="slug" value="<?= e($val('slug')) ?>" maxlength="170"
                   placeholder="auto-generated" data-slug-target data-linked="<?= $isNew ? '1' : '0' ?>">
            <span class="a-hint">/shop/<?= e($val('slug') ?: 'your-category') ?></span>
          </label>

          <label class="a-field a-col-full <?= $err('tagline') ? 'has-error' : '' ?>">
            <span class="a-label">Tagline</span>
            <input class="a-input" type="text" name="tagline" value="<?= e($val('tagline')) ?>" maxlength="255"
                   placeholder="Short line shown above the heading, e.g. Short Boots, Paddock Boots &amp; Chaps">
            <?php if ($err('tagline')): ?><span class="a-error"><?= e($err('tagline')) ?></span><?php endif; ?>
          </label>

          <label class="a-field a-col-full">
            <span class="a-label">Description</span>
            <textarea class="a-textarea" name="description" rows="4"><?= e($val('description')) ?></textarea>
            <span class="a-hint">Shown beneath the heading on the category page and in the homepage pillar.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Meta title</span>
            <input class="a-input" type="text" name="meta_title" value="<?= e($val('meta_title')) ?>" maxlength="190"
                   placeholder="Defaults to the category name">
            <span class="a-hint">
              Shown in Google. Aim for under 60 characters including the site name, and lead
              with what people search for — "Riding Boots, Paddock Boots &amp; Chaps".
            </span>
          </label>

          <label class="a-field">
            <span class="a-label">Meta description</span>
            <textarea class="a-textarea" name="meta_desc" rows="3" maxlength="300"
                      placeholder="Defaults to the description above"><?= e($val('meta_desc')) ?></textarea>
            <span class="a-hint">The grey text under the title in search results. Around 155 characters.</span>
          </label>

          <div class="a-field a-col-full">
            <span class="a-label">Category image</span>
            <label class="a-drop">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              <p><strong>Choose an image</strong> or drop it here</p>
              <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
              <span class="a-drop__list"></span>
            </label>

            <?php if (!empty($category['image'])): ?>
              <div style="margin-top:.7rem;display:flex;align-items:center;gap:.75rem">
                <img class="a-thumb" src="<?= e(image($category['image'])) ?>" alt="" style="width:64px;height:64px">
                <span class="a-hint">Current image — uploading a new one replaces it.</span>
              </div>
            <?php else: ?>
              <span class="a-hint">Without an image the site uses a branded placeholder.</span>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </section>

    <div class="a-stack">
      <section class="a-panel">
        <div class="a-panel__head"><h2>Placement</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-field">
            <span class="a-label">Parent section</span>
            <select class="a-select" name="parent_id">
              <option value="">Top level (a pillar)</option>
              <?php foreach ($pillars as $pillar): ?>
                <?php if (!$isNew && (int) $pillar['id'] === (int) $category['id']) continue; ?>
                <option value="<?= (int) $pillar['id'] ?>" <?= (int) $val('parent_id', 0) === (int) $pillar['id'] ? 'selected' : '' ?>>
                  <?= e($pillar['name']) ?>
                </option>
              <?php endforeach; ?>
            </select>
            <span class="a-hint">Top-level categories appear as pillars on the homepage.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Sort order</span>
            <input class="a-input" type="number" name="sort_order" value="<?= e($val('sort_order', 0)) ?>" step="1">
          </label>

          <label class="a-switch">
            <input type="checkbox" name="is_active" value="1" <?= $checked('is_active', 1) ? 'checked' : '' ?>>
            <span class="a-switch__text">Visible on the website<small>Hidden categories keep their products but disappear from the menu.</small></span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit"><?= $isNew ? 'Create category' : 'Save changes' ?></button>
        </div>
      </section>
    </div>
  </div>
</form>
