<?php
/** @var array $page @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$val = static function (string $field, $default = '') use ($page) {
    $old = old($field, null);
    return $old !== null ? $old : ($page[$field] ?? $default);
};
$publicUrl = $page['slug'] === 'heritage' ? '/heritage' : '/page/' . $page['slug'];
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/pages')) ?>">&larr; All pages</a>
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url($publicUrl)) ?>" target="_blank" rel="noopener">View on the site</a>
</div>

<form method="post" action="<?= e(url('/admin/pages/' . $page['id'] . '/update')) ?>" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-split">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Content</h2></div>
      <div class="a-panel__body">
        <div class="a-form-grid">

          <label class="a-field <?= $err('title') ? 'has-error' : '' ?>">
            <span class="a-label">Title <span class="req">*</span></span>
            <input class="a-input" type="text" name="title" value="<?= e($val('title')) ?>" maxlength="200" required>
            <?php if ($err('title')): ?><span class="a-error"><?= e($err('title')) ?></span><?php endif; ?>
          </label>

          <label class="a-field <?= $err('subtitle') ? 'has-error' : '' ?>">
            <span class="a-label">Subtitle</span>
            <input class="a-input" type="text" name="subtitle" value="<?= e($val('subtitle')) ?>" maxlength="300">
            <span class="a-hint">The single line shown under the heading.</span>
          </label>

          <label class="a-field <?= $err('body') ? 'has-error' : '' ?>">
            <span class="a-label">Page content <span class="req">*</span></span>
            <textarea class="a-textarea a-textarea--tall a-textarea--code" name="body" rows="22" required><?= e($val('body')) ?></textarea>
            <?php if ($err('body')): ?><span class="a-error"><?= e($err('body')) ?></span><?php endif; ?>
            <span class="a-hint">
              Basic HTML: &lt;p&gt;, &lt;h3&gt;, &lt;ul&gt;/&lt;li&gt;, &lt;ol&gt;/&lt;li&gt;, &lt;strong&gt;,
              &lt;em&gt;, &lt;a href&gt;, &lt;blockquote&gt;. Scripts and forms are stripped on save.
            </span>
          </label>
        </div>
      </div>
    </section>

    <div class="a-stack">
      <section class="a-panel">
        <div class="a-panel__head"><h2>Publishing</h2></div>
        <div class="a-panel__body a-stack">
          <label class="a-switch">
            <input type="checkbox" name="is_active" value="1" <?= (int) $val('is_active', 1) === 1 ? 'checked' : '' ?>>
            <span class="a-switch__text">Live on the website<small>Hidden pages return a 404.</small></span>
          </label>

          <label class="a-field <?= $err('meta_desc') ? 'has-error' : '' ?>">
            <span class="a-label">Meta description</span>
            <textarea class="a-textarea" name="meta_desc" rows="3" maxlength="300"><?= e($val('meta_desc')) ?></textarea>
            <span class="a-hint">Shown in search results. Around 155 characters.</span>
          </label>

          <dl class="a-def">
            <div><dt>URL</dt><dd><?= e($publicUrl) ?></dd></div>
            <div><dt>Updated</dt><dd><?= e(pretty_date($page['updated_at'], true)) ?></dd></div>
          </dl>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit">Save page</button>
        </div>
      </section>
    </div>
  </div>
</form>
