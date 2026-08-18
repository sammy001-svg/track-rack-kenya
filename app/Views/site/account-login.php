<?php
/** @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>

<section class="auth-screen">
  <div class="auth-card" data-reveal>
    <p class="eyebrow eyebrow--center eyebrow--plain">Welcome back</p>
    <h1>Sign in.</h1>
    <p class="muted" style="margin-top:.75rem">
      Track your quotes, orders, fittings and repairs — and keep your horses' sizes on file.
    </p>

    <form method="post" action="<?= e(url('/account/login')) ?>" novalidate style="margin-top:2rem">
      <?= csrf_field() ?>

      <div class="form-grid">
        <label class="field <?= $err('email') ? 'has-error' : '' ?>">
          <span class="field__label">Email address</span>
          <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>" required autocomplete="username" autofocus>
          <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
        </label>

        <label class="field <?= $err('password') ? 'has-error' : '' ?>">
          <span class="field__label">Password</span>
          <input class="field__input" type="password" name="password" required autocomplete="current-password">
          <?php if ($err('password')): ?><span class="field__error"><?= e($err('password')) ?></span><?php endif; ?>
        </label>
      </div>

      <button class="btn btn--block btn--lg" type="submit" style="margin-top:1.5rem">Sign In</button>
    </form>

    <div class="auth-card__foot">
      <a href="<?= e(url('/account/forgot')) ?>">Forgotten your password?</a>
      <span>New here? <a href="<?= e(url('/account/register')) ?>">Create an account</a></span>
    </div>
  </div>

  <p class="auth-aside">
    You do not need an account to send a quote request or book a fitting —
    <a href="<?= e(url('/shop')) ?>">browse the catalog</a> whenever you like.
  </p>
</section>
