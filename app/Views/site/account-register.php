<?php
/** @var array $errors @var array $disciplines */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>

<section class="auth-screen">
  <div class="auth-card auth-card--wide" data-reveal>
    <p class="eyebrow eyebrow--center eyebrow--plain">Join us</p>
    <h1>Create an account.</h1>
    <p class="muted" style="margin-top:.75rem">
      Keep your quotes, orders, fittings and repairs in one place — and save each
      horse's sizes so we never have to ask twice.
    </p>

    <form method="post" action="<?= e(url('/account/register')) ?>" novalidate style="margin-top:2rem">
      <?= csrf_field() ?>
      <div class="honeypot" aria-hidden="true">
        <label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
      </div>

      <div class="form-grid form-grid--2">
        <label class="field <?= $err('name') ? 'has-error' : '' ?>">
          <span class="field__label">Full name <span class="req">*</span></span>
          <input class="field__input" type="text" name="name" value="<?= e(old('name')) ?>" required maxlength="150" autocomplete="name">
          <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
        </label>

        <label class="field <?= $err('email') ? 'has-error' : '' ?>">
          <span class="field__label">Email address <span class="req">*</span></span>
          <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>" required maxlength="190" autocomplete="email">
          <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
        </label>

        <label class="field <?= $err('phone') ? 'has-error' : '' ?>">
          <span class="field__label">Phone / WhatsApp <span class="req">*</span></span>
          <input class="field__input" type="tel" name="phone" value="<?= e(old('phone')) ?>" required maxlength="60" placeholder="+254 7…" autocomplete="tel">
          <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
        </label>

        <label class="field">
          <span class="field__label">Location</span>
          <input class="field__input" type="text" name="location" value="<?= e(old('location')) ?>" maxlength="150" placeholder="Karen, Nairobi">
        </label>

        <label class="field field--full">
          <span class="field__label">Main discipline</span>
          <select class="field__select" name="discipline">
            <option value="">Not specified</option>
            <?php foreach ($disciplines as $discipline): ?>
              <option value="<?= e($discipline) ?>" <?= old('discipline') === $discipline ? 'selected' : '' ?>><?= e($discipline) ?></option>
            <?php endforeach; ?>
          </select>
        </label>

        <label class="field <?= $err('password') ? 'has-error' : '' ?>">
          <span class="field__label">Password <span class="req">*</span></span>
          <input class="field__input" type="password" name="password" required autocomplete="new-password">
          <span class="field__hint">At least 10 characters.</span>
          <?php if ($err('password')): ?><span class="field__error"><?= e($err('password')) ?></span><?php endif; ?>
        </label>

        <label class="field <?= $err('password_confirm') ? 'has-error' : '' ?>">
          <span class="field__label">Confirm password <span class="req">*</span></span>
          <input class="field__input" type="password" name="password_confirm" required autocomplete="new-password">
          <?php if ($err('password_confirm')): ?><span class="field__error"><?= e($err('password_confirm')) ?></span><?php endif; ?>
        </label>
      </div>

      <button class="btn btn--block btn--lg" type="submit" style="margin-top:1.75rem">Create My Account</button>

      <p class="field__hint" style="margin-top:1.1rem;text-align:center">
        By creating an account you agree to our
        <a href="<?= e(url('/page/terms-of-service')) ?>" style="color:var(--tan)">terms</a> and
        <a href="<?= e(url('/page/privacy-policy')) ?>" style="color:var(--tan)">privacy policy</a>.
      </p>
    </form>

    <div class="auth-card__foot">
      <span>Already have an account? <a href="<?= e(url('/account/login')) ?>">Sign in</a></span>
    </div>
  </div>
</section>
