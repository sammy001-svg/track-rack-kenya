<?php
/** @var array $groups */
$groupTitles = [
    'general' => ['Brand & identity', 'The name, tagline and introduction used across the site.'],
    'contact' => ['Contact details', 'Shown in the header, footer, contact page and quote emails.'],
    'social'  => ['Social profiles', 'Leave a field blank to hide that icon.'],
    'quotes'  => ['Quote handling', 'Where requests are sent and how references are formed.'],
];
?>

<form method="post" action="<?= e(url('/admin/settings')) ?>" enctype="multipart/form-data" data-dirty-guard>
  <?= csrf_field() ?>

  <div class="a-stack">
    <?php foreach ($groups as $groupName => $rows): ?>
      <?php [$title, $blurb] = $groupTitles[$groupName] ?? [ucfirst($groupName), '']; ?>

      <section class="a-panel">
        <div class="a-panel__head">
          <h2><?= e($title) ?></h2>
          <?php if ($blurb): ?><p><?= e($blurb) ?></p><?php endif; ?>
        </div>

        <div class="a-panel__body">
          <div class="a-form-grid a-form-grid--2">
            <?php foreach ($rows as $row): ?>
              <?php
                $key      = $row['key_name'];
                $value    = $row['value'];
                $isWide   = in_array($row['input_type'], ['textarea', 'url'], true) || str_contains($key, 'address');
                $fieldCls = 'a-field' . ($isWide ? ' a-col-full' : '');
              ?>

              <?php if ($row['input_type'] === 'textarea'): ?>
                <label class="<?= $fieldCls ?>">
                  <span class="a-label"><?= e($row['label'] ?: $key) ?></span>
                  <textarea class="a-textarea" name="settings[<?= e($key) ?>]" rows="3"><?= e($value) ?></textarea>
                  <span class="a-hint"><?= e($key) ?></span>
                </label>

              <?php elseif ($row['input_type'] === 'image'): ?>
                <div class="<?= $fieldCls ?>">
                  <span class="a-label"><?= e($row['label'] ?: $key) ?></span>
                  <label class="a-drop">
                    <p><strong>Choose an image</strong> or drop it here</p>
                    <input type="file" name="settings[<?= e($key) ?>]" accept="image/jpeg,image/png,image/webp,image/gif">
                    <span class="a-drop__list"></span>
                  </label>
                  <?php if ($value): ?>
                    <img class="a-thumb" src="<?= e(image($value)) ?>" alt="" style="width:72px;height:72px;margin-top:.6rem;object-fit:contain;background:#fff">
                  <?php endif; ?>
                </div>

              <?php else: ?>
                <label class="<?= $fieldCls ?>">
                  <span class="a-label"><?= e($row['label'] ?: $key) ?></span>
                  <input class="a-input" type="<?= e($row['input_type']) ?>"
                         name="settings[<?= e($key) ?>]" value="<?= e($value) ?>">
                  <span class="a-hint"><?= e($key) ?></span>
                </label>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
    <?php endforeach; ?>

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Technical notes</h2>
      </div>
      <div class="a-panel__body">
        <div class="a-note">Database name, credentials, debug mode and email sending are set in <strong>config/config.php</strong> (or config.local.php) rather than here — they are deployment settings, not content.

Quote requests are always written to the database first, so a mail failure never loses an enquiry. If notification emails are not arriving, set mail.enabled to true and configure sendmail on the server.</div>
      </div>
      <div class="a-panel__foot">
        <button class="a-btn" type="submit">Save all settings</button>
        <span class="a-hint" style="margin:0">Changes take effect immediately across the website.</span>
      </div>
    </section>
  </div>
</form>
