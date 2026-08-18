<?php
/** @var array|null $service @var array $disciplines @var array $slots @var array|null $customer @var array $horses @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$pre = static function (string $field, $fallback = '') use ($customer) {
    $old = old($field, null);
    if ($old !== null && $old !== '') {
        return $old;
    }
    return $customer[$fallback] ?? '';
};
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li><a href="<?= e(url('/services')) ?>">Services</a></li>
      <li>Saddle fitting</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Society of Master Saddlers qualified</p>
    <h1><?= e($service['name'] ?? 'Saddle Fitting') ?></h1>
    <p class="lede"><?= e($service['tagline'] ?? '') ?></p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="quote-layout">

      <!-- Booking form -->
      <form method="post" action="<?= e(url('/services/saddle-fitting')) ?>" novalidate>
        <?= csrf_field() ?>
        <div class="honeypot" aria-hidden="true">
          <label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>01</i> Your details</legend>

          <div class="form-grid form-grid--2">
            <label class="field <?= $err('name') ? 'has-error' : '' ?>">
              <span class="field__label">Full name <span class="req">*</span></span>
              <input class="field__input" type="text" name="name" value="<?= e($pre('name', 'name')) ?>" required maxlength="150" autocomplete="name">
              <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('email') ? 'has-error' : '' ?>">
              <span class="field__label">Email <span class="req">*</span></span>
              <input class="field__input" type="email" name="email" value="<?= e($pre('email', 'email')) ?>" required maxlength="190" autocomplete="email">
              <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('phone') ? 'has-error' : '' ?>">
              <span class="field__label">Phone / WhatsApp <span class="req">*</span></span>
              <input class="field__input" type="tel" name="phone" value="<?= e($pre('phone', 'phone')) ?>" required maxlength="60" placeholder="+254 7…" autocomplete="tel">
              <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
            </label>

            <label class="field">
              <span class="field__label">Yard or location</span>
              <input class="field__input" type="text" name="location" value="<?= e($pre('location', 'location')) ?>" maxlength="200" placeholder="Karen, Nairobi">
            </label>

            <div class="field field--full">
              <label class="check">
                <input type="checkbox" name="at_yard" value="1" <?= old('at_yard') ? 'checked' : '' ?>>
                <span>Please come to my yard rather than me bringing the horse to the shop.
                  <small style="display:block;color:var(--text-faint);margin-top:.2rem">Yard visits are charged by distance — we will confirm before travelling.</small>
                </span>
              </label>
            </div>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>02</i> The horse</legend>

          <?php if ($horses !== []): ?>
            <div class="saved-horses" data-horse-picker>
              <p class="field__label" style="margin-bottom:.6rem">Use a saved horse</p>
              <div class="chip-row">
                <?php foreach ($horses as $horse): ?>
                  <button class="chip" type="button"
                          data-horse-name="<?= e($horse['name']) ?>"
                          data-horse-details="<?= e(trim(
                              ($horse['height_hh'] ? $horse['height_hh'] . 'hh. ' : '')
                            . ($horse['breed'] ? $horse['breed'] . '. ' : '')
                            . ($horse['saddle_seat_size'] ? 'Current seat size ' . $horse['saddle_seat_size'] . '. ' : '')
                            . ($horse['gullet_width'] ? 'Gullet ' . $horse['gullet_width'] . '. ' : '')
                            . ($horse['notes'] ?? '')
                          )) ?>"
                          data-horse-discipline="<?= e($horse['discipline']) ?>">
                    <?= e($horse['name']) ?>
                  </button>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>

          <div class="form-grid form-grid--2" style="margin-top:<?= $horses !== [] ? '1.35rem' : '0' ?>">
            <label class="field">
              <span class="field__label">Horse name</span>
              <input class="field__input" type="text" name="horse_name" id="horse_name" value="<?= e(old('horse_name')) ?>" maxlength="120">
            </label>

            <label class="field">
              <span class="field__label">Discipline</span>
              <select class="field__select" name="discipline" id="discipline">
                <option value="">Not specified</option>
                <?php foreach ($disciplines as $discipline): ?>
                  <option value="<?= e($discipline) ?>" <?= old('discipline') === $discipline ? 'selected' : '' ?>><?= e($discipline) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="field field--full">
              <span class="field__label">Horse details</span>
              <textarea class="field__textarea" name="horse_details" id="horse_details" rows="3"
                placeholder="Height, breed, age, build, any back or behavioural issues you have noticed."><?= e(old('horse_details')) ?></textarea>
            </label>

            <label class="field field--full">
              <span class="field__label">Current saddle</span>
              <textarea class="field__textarea" name="saddle_details" rows="3"
                placeholder="Make, model and seat size if you know it — or tell us you are looking to buy."><?= e(old('saddle_details')) ?></textarea>
              <span class="field__hint">If you are buying, we bring a selection to try on the horse.</span>
            </label>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>03</i> When suits you</legend>

          <div class="form-grid form-grid--2">
            <label class="field <?= $err('preferred_date') ? 'has-error' : '' ?>">
              <span class="field__label">Preferred date</span>
              <input class="field__input" type="date" name="preferred_date" value="<?= e(old('preferred_date')) ?>" min="<?= date('Y-m-d') ?>">
              <?php if ($err('preferred_date')): ?><span class="field__error"><?= e($err('preferred_date')) ?></span><?php endif; ?>
            </label>

            <label class="field">
              <span class="field__label">Time of day</span>
              <select class="field__select" name="preferred_slot">
                <?php foreach ($slots as $value => $label): ?>
                  <option value="<?= e($value) ?>" <?= old('preferred_slot') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="field field--full">
              <span class="field__label">Alternative date</span>
              <input class="field__input" type="date" name="alternate_date" value="<?= e(old('alternate_date')) ?>" min="<?= date('Y-m-d') ?>">
              <span class="field__hint">Helpful if your first choice is not available.</span>
            </label>

            <label class="field field--full">
              <span class="field__label">Anything else</span>
              <textarea class="field__textarea" name="notes" rows="4"><?= e(old('notes')) ?></textarea>
            </label>
          </div>
        </fieldset>

        <div style="margin-top:2.25rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center">
          <button class="btn btn--lg" type="submit">Request This Fitting</button>
          <span class="field__hint" style="margin:0">We confirm by email or phone — nothing is charged now.</span>
        </div>
      </form>

      <!-- What to expect -->
      <aside class="quote-aside">
        <div class="quote-summary">
          <h3>What to expect</h3>
          <p class="muted" style="margin-top:.9rem;font-size:.92rem;line-height:1.7">
            <?= nl2br(e($service['what_to_expect'] ?? '')) ?>
          </p>

          <?php if (setting('fitting_fee_note')): ?>
            <p class="quote-summary__note"><?= e(setting('fitting_fee_note')) ?></p>
          <?php endif; ?>
        </div>

        <div class="quote-summary" style="margin-top:1.25rem">
          <h3>Why it matters</h3>
          <ul class="tick-list" style="margin-top:1rem">
            <li>A poor fit causes back pain, resistance and long-term damage</li>
            <li>Horses change shape with work, age and condition</li>
            <li>A saddle that fitted last season may not fit this one</li>
            <li>Re-flocking is far cheaper than a new saddle</li>
          </ul>
        </div>
      </aside>
    </div>
  </div>
</section>
