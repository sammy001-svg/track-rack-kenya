<?php
/** @var array $customer @var array $horses @var array|null $editing @var array $disciplines @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$val = static function (string $field) use ($editing) {
    $old = old($field, null);
    return $old !== null ? $old : ($editing[$field] ?? '');
};
$tidy = static fn ($n): string => $n === null || $n === '' ? '' : rtrim(rtrim((string) $n, '0'), '.');
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li>My horses</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Your account</p>
    <h1>My horses.</h1>
    <p class="lede">
      Save each horse's sizes once and we will have them to hand for every quote,
      fitting and rug order.
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">

        <?php if ($horses !== []): ?>
          <section class="account-panel">
            <div class="account-panel__head"><h2>On file</h2></div>

            <div class="horse-grid">
              <?php foreach ($horses as $horse): ?>
                <article class="horse-card <?= $editing && (int) $editing['id'] === (int) $horse['id'] ? 'is-editing' : '' ?>">
                  <header>
                    <h3><?= e($horse['name']) ?></h3>
                    <?php if ($horse['discipline']): ?><span><?= e($horse['discipline']) ?></span><?php endif; ?>
                  </header>

                  <dl>
                    <?php if ($horse['height_hh']): ?><div><dt>Height</dt><dd><?= e($tidy($horse['height_hh'])) ?> hh</dd></div><?php endif; ?>
                    <?php if ($horse['breed']): ?><div><dt>Breed</dt><dd><?= e($horse['breed']) ?></dd></div><?php endif; ?>
                    <?php if ($horse['saddle_seat_size']): ?><div><dt>Seat</dt><dd><?= e($horse['saddle_seat_size']) ?></dd></div><?php endif; ?>
                    <?php if ($horse['gullet_width']): ?><div><dt>Gullet</dt><dd><?= e($horse['gullet_width']) ?></dd></div><?php endif; ?>
                    <?php if ($horse['rug_size']): ?><div><dt>Rug</dt><dd><?= e($horse['rug_size']) ?></dd></div><?php endif; ?>
                    <?php if ($horse['girth_size']): ?><div><dt>Girth</dt><dd><?= e($horse['girth_size']) ?></dd></div><?php endif; ?>
                    <?php if ($horse['bit_size']): ?><div><dt>Bit</dt><dd><?= e($horse['bit_size']) ?></dd></div><?php endif; ?>
                  </dl>

                  <?php if ($horse['notes']): ?>
                    <p class="horse-card__notes"><?= e(excerpt($horse['notes'], 130)) ?></p>
                  <?php endif; ?>

                  <footer>
                    <a class="link" href="<?= e(url('/account/horses?edit=' . $horse['id'])) ?>#horse-form">Edit</a>
                    <form method="post" action="<?= e(url('/account/horses/delete')) ?>"
                          onsubmit="return confirm('Remove <?= e($horse['name']) ?> from your account?');">
                      <?= csrf_field() ?>
                      <input type="hidden" name="horse_id" value="<?= (int) $horse['id'] ?>">
                      <button type="submit">Remove</button>
                    </form>
                  </footer>
                </article>
              <?php endforeach; ?>
            </div>
          </section>
        <?php endif; ?>

        <section class="account-panel" id="horse-form">
          <div class="account-panel__head">
            <h2><?= $editing ? 'Edit ' . e($editing['name']) : 'Add a horse' ?></h2>
            <?php if ($editing): ?>
              <a class="link" href="<?= e(url('/account/horses')) ?>">Cancel</a>
            <?php endif; ?>
          </div>

          <form method="post" action="<?= e(url('/account/horses')) ?>" novalidate>
            <?= csrf_field() ?>
            <?php if ($editing): ?>
              <input type="hidden" name="horse_id" value="<?= (int) $editing['id'] ?>">
            <?php endif; ?>

            <div class="form-grid form-grid--2">
              <label class="field <?= $err('name') ? 'has-error' : '' ?>">
                <span class="field__label">Horse name <span class="req">*</span></span>
                <input class="field__input" type="text" name="name" value="<?= e($val('name')) ?>" required maxlength="120">
                <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('height_hh') ? 'has-error' : '' ?>">
                <span class="field__label">Height (hands)</span>
                <input class="field__input" type="text" name="height_hh" value="<?= e($tidy($val('height_hh'))) ?>"
                       inputmode="decimal" placeholder="16.2">
                <?php if ($err('height_hh')): ?><span class="field__error"><?= e($err('height_hh')) ?></span><?php endif; ?>
              </label>

              <label class="field">
                <span class="field__label">Breed or type</span>
                <input class="field__input" type="text" name="breed" value="<?= e($val('breed')) ?>" maxlength="120"
                       placeholder="Thoroughbred, Boerperd…">
              </label>

              <label class="field">
                <span class="field__label">Discipline</span>
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

            <p class="field__label" style="margin:1.75rem 0 .9rem">Sizes we will remember</p>

            <div class="form-grid form-grid--2">
              <label class="field">
                <span class="field__label">Saddle seat size</span>
                <input class="field__input" type="text" name="saddle_seat_size" value="<?= e($val('saddle_seat_size')) ?>" maxlength="40" placeholder="17.5 in">
              </label>

              <label class="field">
                <span class="field__label">Gullet width</span>
                <input class="field__input" type="text" name="gullet_width" value="<?= e($val('gullet_width')) ?>" maxlength="40" placeholder="Medium-wide">
              </label>

              <label class="field">
                <span class="field__label">Rug size</span>
                <input class="field__input" type="text" name="rug_size" value="<?= e($val('rug_size')) ?>" maxlength="40" placeholder="6 ft 3 in">
              </label>

              <label class="field">
                <span class="field__label">Girth size</span>
                <input class="field__input" type="text" name="girth_size" value="<?= e($val('girth_size')) ?>" maxlength="40" placeholder="52 in">
              </label>

              <label class="field">
                <span class="field__label">Bit size</span>
                <input class="field__input" type="text" name="bit_size" value="<?= e($val('bit_size')) ?>" maxlength="40" placeholder="5.5 in">
              </label>

              <label class="field field--full">
                <span class="field__label">Notes</span>
                <textarea class="field__textarea" name="notes" rows="3" maxlength="2000"
                  placeholder="Sensitive back, high wither, prone to rubs — anything we should know."><?= e($val('notes')) ?></textarea>
              </label>
            </div>

            <button class="btn btn--lg" type="submit" style="margin-top:1.75rem">
              <?= $editing ? 'Save Changes' : 'Save This Horse' ?>
            </button>
          </form>
        </section>
      </div>
    </div>
  </div>
</section>
