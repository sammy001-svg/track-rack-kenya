<?php
/** @var array $user @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$val = static function (string $field, $default = '') use ($user) {
    $old = old($field, null);
    return $old !== null ? $old : ($user[$field] ?? $default);
};
?>

<div class="a-split">
  <section class="a-panel">
    <div class="a-panel__head">
      <h2>My account</h2>
      <p>Update your own name, sign-in address and password.</p>
    </div>

    <form method="post" action="<?= e(url('/admin/account')) ?>" data-dirty-guard>
      <?= csrf_field() ?>

      <div class="a-panel__body a-stack">
        <div class="a-form-grid a-form-grid--2">
          <label class="a-field <?= $err('name') ? 'has-error' : '' ?>">
            <span class="a-label">Your name <span class="req">*</span></span>
            <input class="a-input" type="text" name="name" value="<?= e($val('name')) ?>" maxlength="120" required>
            <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
          </label>

          <label class="a-field <?= $err('email') ? 'has-error' : '' ?>">
            <span class="a-label">Email address <span class="req">*</span></span>
            <input class="a-input" type="email" name="email" value="<?= e($val('email')) ?>" maxlength="190" required autocomplete="username">
            <?php if ($err('email')): ?><span class="a-error"><?= e($err('email')) ?></span><?php endif; ?>
          </label>
        </div>

        <div>
          <p class="a-section-title" style="margin-top:.5rem">Change password</p>
          <div class="a-form-grid a-form-grid--3">
            <label class="a-field <?= $err('current_password') ? 'has-error' : '' ?>">
              <span class="a-label">Current password</span>
              <input class="a-input" type="password" name="current_password" autocomplete="current-password">
              <?php if ($err('current_password')): ?><span class="a-error"><?= e($err('current_password')) ?></span><?php endif; ?>
            </label>

            <label class="a-field <?= $err('password') ? 'has-error' : '' ?>">
              <span class="a-label">New password</span>
              <input class="a-input" type="password" name="password" autocomplete="new-password">
              <span class="a-hint">At least 10 characters.</span>
              <?php if ($err('password')): ?><span class="a-error"><?= e($err('password')) ?></span><?php endif; ?>
            </label>

            <label class="a-field <?= $err('password_confirm') ? 'has-error' : '' ?>">
              <span class="a-label">Confirm new password</span>
              <input class="a-input" type="password" name="password_confirm" autocomplete="new-password">
              <?php if ($err('password_confirm')): ?><span class="a-error"><?= e($err('password_confirm')) ?></span><?php endif; ?>
            </label>
          </div>
          <p class="a-hint">Leave all three blank to keep your current password.</p>
        </div>
      </div>

      <div class="a-panel__foot">
        <button class="a-btn" type="submit">Save my account</button>
      </div>
    </form>
  </section>

  <section class="a-panel">
    <div class="a-panel__head"><h2>Account record</h2></div>
    <div class="a-panel__body">
      <dl class="a-def">
        <div><dt>Role</dt><dd><?= $user['role'] === 'admin' ? 'Administrator' : 'Manager' ?></dd></div>
        <div><dt>Status</dt><dd><span class="a-badge a-badge--live">Active</span></dd></div>
        <div><dt>Last signed in</dt><dd><?= $user['last_login_at'] ? e(pretty_date($user['last_login_at'], true)) : '—' ?></dd></div>
        <div><dt>Account created</dt><dd><?= e(pretty_date($user['created_at'])) ?></dd></div>
      </dl>
    </div>
    <div class="a-panel__foot">
      <form method="post" action="<?= e(url('/admin/logout')) ?>">
        <?= csrf_field() ?>
        <button class="a-btn a-btn--ghost a-btn--sm" type="submit">Sign out</button>
      </form>
    </div>
  </section>
</div>
