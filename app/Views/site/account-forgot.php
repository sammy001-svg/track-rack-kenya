<?php
/** @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>

<section class="auth-screen">
  <div class="auth-card" data-reveal>
    <p class="eyebrow eyebrow--center eyebrow--plain">Password reset</p>
    <h1>Forgotten it?</h1>
    <p class="muted" style="margin-top:.75rem">
      Enter the address on your account and we will send you a link to choose a new
      password. The link lasts an hour.
    </p>

    <form method="post" action="<?= e(url('/account/forgot')) ?>" novalidate style="margin-top:2rem">
      <?= csrf_field() ?>

      <label class="field <?= $err('email') ? 'has-error' : '' ?>">
        <span class="field__label">Email address</span>
        <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>" required autocomplete="email" autofocus>
        <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
      </label>

      <button class="btn btn--block btn--lg" type="submit" style="margin-top:1.5rem">Send Reset Link</button>
    </form>

    <div class="auth-card__foot">
      <a href="<?= e(url('/account/login')) ?>">&larr; Back to sign in</a>
    </div>
  </div>
</section>
