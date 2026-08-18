<?php
/** @var array $service @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$val = static function (string $field, $default = '') use ($service) {
    $old = old($field, null);
    return $old !== null ? $old : ($service[$field] ?? $default);
};
$publicUrl = $service['slug'] === 'saddle-fitting' ? '/services/saddle-fitting' : '/services/repairs';
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/services')) ?>">&larr; All services</a>
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url($publicUrl)) ?>" target="_blank" rel="noopener">View on the site</a>
</div>

<form method="post" action="<?= e(url('/admin/services/' . $service['id'] . '/update')) ?>" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-split">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Service copy</h2></div>
      <div class="a-panel__body">
        <div class="a-form-grid">

          <label class="a-field <?= $err('name') ? 'has-error' : '' ?>">
            <span class="a-label">Name <span class="req">*</span></span>
            <input class="a-input" type="text" name="name" value="<?= e($val('name')) ?>" maxlength="150" required>
            <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
          </label>

          <label class="a-field <?= $err('tagline') ? 'has-error' : '' ?>">
            <span class="a-label">Tagline</span>
            <input class="a-input" type="text" name="tagline" value="<?= e($val('tagline')) ?>" maxlength="255">
            <span class="a-hint">The single line under the page heading.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Description</span>
            <textarea class="a-textarea" name="description" rows="6"><?= e($val('description')) ?></textarea>
            <span class="a-hint">Shown on the services index card.</span>
          </label>

          <label class="a-field">
            <span class="a-label">What to expect</span>
            <textarea class="a-textarea" name="what_to_expect" rows="6"><?= e($val('what_to_expect')) ?></textarea>
            <span class="a-hint">Shown in the sidebar beside the booking form.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Meta title</span>
            <input class="a-input" type="text" name="meta_title" value="<?= e($val('meta_title')) ?>" maxlength="190"
                   placeholder="Defaults to the service name">
            <span class="a-hint">Shown in Google. Under 60 characters including the site name.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Meta description</span>
            <textarea class="a-textarea" name="meta_desc" rows="3" maxlength="300"
                      placeholder="Defaults to the tagline"><?= e($val('meta_desc')) ?></textarea>
            <span class="a-hint">Around 155 characters.</span>
          </label>

          <div class="a-field">
            <span class="a-label">Service image</span>
            <label class="a-drop">
              <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
              <p><strong>Choose an image</strong> or drop it here</p>
              <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif">
              <span class="a-drop__list"></span>
            </label>

            <?php if (!empty($service['image'])): ?>
              <div style="margin-top:.7rem;display:flex;align-items:center;gap:.75rem">
                <img class="a-thumb" src="<?= e(image($service['image'])) ?>" alt="" style="width:64px;height:64px">
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
        <div class="a-panel__head"><h2>Details</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-field">
            <span class="a-label">Typical duration (minutes)</span>
            <input class="a-input" type="number" name="duration_minutes" value="<?= e($val('duration_minutes')) ?>" min="0" step="5">
            <span class="a-hint">Leave blank if it varies.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Price from</span>
            <div class="a-input-group">
              <input class="a-input" type="number" name="price_from" value="<?= e($val('price_from')) ?>" step="0.01" min="0" placeholder="0.00">
              <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
            </div>
            <span class="a-hint">Leave blank to say nothing about price.</span>
          </label>

          <label class="a-field">
            <span class="a-label">Sort order</span>
            <input class="a-input" type="number" name="sort_order" value="<?= e($val('sort_order', 0)) ?>" step="1">
          </label>

          <label class="a-switch">
            <input type="checkbox" name="travel_available" value="1" <?= (int) $val('travel_available', 1) === 1 ? 'checked' : '' ?>>
            <span class="a-switch__text">We travel for this<small>Shows the "come to my yard" option.</small></span>
          </label>

          <label class="a-switch">
            <input type="checkbox" name="is_active" value="1" <?= (int) $val('is_active', 1) === 1 ? 'checked' : '' ?>>
            <span class="a-switch__text">Live on the website<small>Hidden services disappear from the services page.</small></span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit">Save service</button>
        </div>
      </section>
    </div>
  </div>
</form>
