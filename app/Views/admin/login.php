<?php
/** @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>
<main class="a-login">
  <div class="a-login__card">
    <div class="a-login__brand">
      <strong><?= e(setting('site_name', 'Tack Rack')) ?></strong>
      <span>Staff Console</span>
    </div>

    <form method="post" action="<?= e(url('/admin/login')) ?>" novalidate>
      <?= csrf_field() ?>

      <div class="a-form-grid">
        <label class="a-field <?= $err('email') ? 'has-error' : '' ?>">
          <span class="a-label">Email address</span>
          <input class="a-input" type="email" name="email" value="<?= e(old('email')) ?>"
                 required autocomplete="username" autofocus>
          <?php if ($err('email')): ?><span class="a-error"><?= e($err('email')) ?></span><?php endif; ?>
        </label>

        <label class="a-field <?= $err('password') ? 'has-error' : '' ?>">
          <span class="a-label">Password</span>
          <input class="a-input" type="password" name="password" required autocomplete="current-password">
          <?php if ($err('password')): ?><span class="a-error"><?= e($err('password')) ?></span><?php endif; ?>
        </label>
      </div>

      <button class="a-btn a-btn--block" type="submit" style="margin-top:1.35rem">Sign in</button>
    </form>

    <div class="a-login__foot">
      <a href="<?= e(url('/')) ?>">&larr; Back to the website</a>
    </div>
  </div>
</main>
