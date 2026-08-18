<?php
use App\Core\Auth;
/** @var array $users @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
?>

<div class="a-split a-split--wide-aside">

  <section class="a-panel">
    <div class="a-panel__head">
      <h2>Staff accounts</h2>
      <p>Administrators can change settings and manage accounts. Managers can do everything else.</p>
    </div>

    <div class="a-panel__body a-stack">
      <?php foreach ($users as $user): ?>
        <?php $isSelf = (int) $user['id'] === Auth::id(); ?>

        <article style="border:1px solid var(--a-line);border-radius:var(--a-radius-sm);padding:1rem">
          <div class="a-spread" style="margin-bottom:.9rem">
            <div>
              <strong style="font-size:.95rem"><?= e($user['name']) ?></strong>
              <?php if ($isSelf): ?><span class="a-badge a-badge--plain" style="margin-left:.4rem">You</span><?php endif; ?>
              <div class="a-cell-media__meta"><?= e($user['email']) ?></div>
            </div>

            <div style="text-align:right">
              <span class="a-badge a-badge--<?= (int) $user['is_active'] === 1 ? 'live' : 'draft' ?>">
                <?= (int) $user['is_active'] === 1 ? 'Active' : 'Disabled' ?>
              </span>
              <div class="a-cell-media__meta">
                <?= $user['last_login_at'] ? 'Last in ' . e(time_ago($user['last_login_at'])) : 'Never signed in' ?>
              </div>
            </div>
          </div>

          <form method="post" action="<?= e(url('/admin/users/' . $user['id'] . '/update')) ?>">
            <?= csrf_field() ?>

            <div class="a-form-grid a-form-grid--3">
              <label class="a-field">
                <span class="a-label">Name</span>
                <input class="a-input" type="text" name="name" value="<?= e($user['name']) ?>" maxlength="120">
              </label>

              <label class="a-field">
                <span class="a-label">Role</span>
                <select class="a-select" name="role">
                  <option value="manager" <?= $user['role'] === 'manager' ? 'selected' : '' ?>>Manager</option>
                  <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Administrator</option>
                </select>
              </label>

              <label class="a-field">
                <span class="a-label">Reset password</span>
                <input class="a-input" type="password" name="password" placeholder="Leave blank to keep"
                       autocomplete="new-password">
              </label>
            </div>

            <div class="a-spread" style="margin-top:.9rem">
              <label class="a-check">
                <input type="checkbox" name="is_active" value="1" <?= (int) $user['is_active'] === 1 ? 'checked' : '' ?>>
                Account is active
              </label>

              <button class="a-btn a-btn--ghost a-btn--sm" type="submit">Save changes</button>
            </div>
          </form>

          <?php if (!$isSelf): ?>
            <form method="post" action="<?= e(url('/admin/users/' . $user['id'] . '/delete')) ?>"
                  data-confirm="Delete the account for <?= e($user['name']) ?>? This cannot be undone."
                  style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--a-line-soft);text-align:right">
              <?= csrf_field() ?>
              <button class="a-btn a-btn--danger a-btn--sm" type="submit">Delete account</button>
            </form>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="a-panel">
    <div class="a-panel__head"><h2>Add a staff account</h2></div>

    <form method="post" action="<?= e(url('/admin/users/store')) ?>">
      <?= csrf_field() ?>

      <div class="a-panel__body">
        <div class="a-form-grid">
          <label class="a-field <?= $err('name') ? 'has-error' : '' ?>">
            <span class="a-label">Name <span class="req">*</span></span>
            <input class="a-input" type="text" name="name" value="<?= e(old('name')) ?>" maxlength="120" required>
            <?php if ($err('name')): ?><span class="a-error"><?= e($err('name')) ?></span><?php endif; ?>
          </label>

          <label class="a-field <?= $err('email') ? 'has-error' : '' ?>">
            <span class="a-label">Email address <span class="req">*</span></span>
            <input class="a-input" type="email" name="email" value="<?= e(old('email')) ?>" maxlength="190" required>
            <?php if ($err('email')): ?><span class="a-error"><?= e($err('email')) ?></span><?php endif; ?>
          </label>

          <label class="a-field">
            <span class="a-label">Role</span>
            <select class="a-select" name="role">
              <option value="manager" <?= old('role') === 'manager' ? 'selected' : '' ?>>Manager</option>
              <option value="admin" <?= old('role') === 'admin' ? 'selected' : '' ?>>Administrator</option>
            </select>
          </label>

          <label class="a-field <?= $err('password') ? 'has-error' : '' ?>">
            <span class="a-label">Password <span class="req">*</span></span>
            <input class="a-input" type="password" name="password" required autocomplete="new-password">
            <span class="a-hint">At least 10 characters.</span>
            <?php if ($err('password')): ?><span class="a-error"><?= e($err('password')) ?></span><?php endif; ?>
          </label>

          <label class="a-field <?= $err('password_confirm') ? 'has-error' : '' ?>">
            <span class="a-label">Confirm password <span class="req">*</span></span>
            <input class="a-input" type="password" name="password_confirm" required autocomplete="new-password">
            <?php if ($err('password_confirm')): ?><span class="a-error"><?= e($err('password_confirm')) ?></span><?php endif; ?>
          </label>
        </div>
      </div>

      <div class="a-panel__foot">
        <button class="a-btn a-btn--block" type="submit">Create account</button>
      </div>
    </form>
  </section>
</div>
