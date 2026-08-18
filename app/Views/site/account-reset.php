<?php
/** @var array $errors @var string $token */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>

<section class="auth-screen">
  <div class="auth-card" data-reveal>
    <p class="eyebrow eyebrow--center eyebrow--plain">Almost there</p>
    <h1>New password.</h1>
    <p class="muted" style="margin-top:.75rem">
      Choose something at least ten characters long. You will be signed in straight away.
    </p>

    <form method="post" action="<?= e(url('/account/reset/' . $token)) ?>" novalidate style="margin-top:2rem">
      <?= csrf_field() ?>

      <div class="form-grid">
        <label class="field <?= $err('password') ? 'has-error' : '' ?>">
          <span class="field__label">New password</span>
          <input class="field__input" type="password" name="password" required autocomplete="new-password" autofocus>
          <?php if ($err('password')): ?><span class="field__error"><?= e($err('password')) ?></span><?php endif; ?>
        </label>

        <label class="field <?= $err('password_confirm') ? 'has-error' : '' ?>">
          <span class="field__label">Confirm new password</span>
          <input class="field__input" type="password" name="password_confirm" required autocomplete="new-password">
          <?php if ($err('password_confirm')): ?><span class="field__error"><?= e($err('password_confirm')) ?></span><?php endif; ?>
        </label>
      </div>

      <button class="btn btn--block btn--lg" type="submit" style="margin-top:1.5rem">Save New Password</button>
    </form>
  </div>
</section>
