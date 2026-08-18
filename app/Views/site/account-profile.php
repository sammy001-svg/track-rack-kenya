<?php
/** @var array $customer @var array $disciplines @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$val = static function (string $field) use ($customer) {
    $old = old($field, null);
    return $old !== null ? $old : ($customer[$field] ?? '');
};
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li>My details</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Your account</p>
    <h1>My details.</h1>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">
        <form method="post" action="<?= e(url('/account/profile')) ?>" novalidate>
          <?= csrf_field() ?>

          <section class="account-panel">
            <div class="account-panel__head"><h2>Contact details</h2></div>

            <div class="form-grid form-grid--2">
              <label class="field <?= $err('name') ? 'has-error' : '' ?>">
                <span class="field__label">Full name <span class="req">*</span></span>
                <input class="field__input" type="text" name="name" value="<?= e($val('name')) ?>" required maxlength="150">
                <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('email') ? 'has-error' : '' ?>">
                <span class="field__label">Email address <span class="req">*</span></span>
                <input class="field__input" type="email" name="email" value="<?= e($val('email')) ?>" required maxlength="190" autocomplete="username">
                <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('phone') ? 'has-error' : '' ?>">
                <span class="field__label">Phone / WhatsApp <span class="req">*</span></span>
                <input class="field__input" type="tel" name="phone" value="<?= e($val('phone')) ?>" required maxlength="60">
                <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
              </label>

              <label class="field">
                <span class="field__label">Location</span>
                <input class="field__input" type="text" name="location" value="<?= e($val('location')) ?>" maxlength="150">
              </label>

              <label class="field field--full">
                <span class="field__label">Main discipline</span>
                <select class="field__select" name="discipline">
                  <option value="">Not specified</option>
                  <?php foreach ($disciplines as $discipline): ?>
                    <option value="<?= e($discipline) ?>" <?= $val('discipline') === $discipline ? 'selected' : '' ?>>
                      <?= e($discipline) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </label>
            </div>
          </section>

          <section class="account-panel">
            <div class="account-panel__head"><h2>Change password</h2></div>

            <div class="form-grid form-grid--2">
              <label class="field field--full <?= $err('current_password') ? 'has-error' : '' ?>">
                <span class="field__label">Current password</span>
                <input class="field__input" type="password" name="current_password" autocomplete="current-password">
                <?php if ($err('current_password')): ?><span class="field__error"><?= e($err('current_password')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('password') ? 'has-error' : '' ?>">
                <span class="field__label">New password</span>
                <input class="field__input" type="password" name="password" autocomplete="new-password">
                <span class="field__hint">At least 10 characters.</span>
                <?php if ($err('password')): ?><span class="field__error"><?= e($err('password')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('password_confirm') ? 'has-error' : '' ?>">
                <span class="field__label">Confirm new password</span>
                <input class="field__input" type="password" name="password_confirm" autocomplete="new-password">
                <?php if ($err('password_confirm')): ?><span class="field__error"><?= e($err('password_confirm')) ?></span><?php endif; ?>
              </label>
            </div>

            <p class="field__hint" style="margin-top:.9rem">Leave all three blank to keep your current password.</p>
          </section>

          <button class="btn btn--lg" type="submit">Save My Details</button>
        </form>
      </div>
    </div>
  </div>
</section>
