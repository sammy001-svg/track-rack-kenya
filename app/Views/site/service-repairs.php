<?php
/** @var array|null $service @var array $itemTypes @var array $urgency @var array|null $customer @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$pre = static function (string $field, string $fallback = '') use ($customer) {
    $old = old($field, null);
    if ($old !== null && $old !== '') {
        return $old;
    }
    return $fallback !== '' ? ($customer[$fallback] ?? '') : '';
};
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li><a href="<?= e(url('/services')) ?>">Services</a></li>
      <li>Workshop repairs</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Our Nairobi workshop</p>
    <h1><?= e($service['name'] ?? 'Workshop Repairs') ?></h1>
    <p class="lede"><?= e($service['tagline'] ?? 'We repair what most suppliers replace.') ?></p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="quote-layout">

      <form method="post" action="<?= e(url('/services/repairs')) ?>" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>
        <div class="honeypot" aria-hidden="true">
          <label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>01</i> What needs repairing</legend>

          <div class="form-grid form-grid--2">
            <label class="field <?= $err('item_type') ? 'has-error' : '' ?>">
              <span class="field__label">Item <span class="req">*</span></span>
              <select class="field__select" name="item_type" required>
                <option value="">Choose…</option>
                <?php foreach ($itemTypes as $type): ?>
                  <option value="<?= e($type) ?>" <?= old('item_type') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                <?php endforeach; ?>
              </select>
              <?php if ($err('item_type')): ?><span class="field__error"><?= e($err('item_type')) ?></span><?php endif; ?>
            </label>

            <label class="field">
              <span class="field__label">Make or model</span>
              <input class="field__input" type="text" name="item_make" value="<?= e(old('item_make')) ?>" maxlength="150" placeholder="If you know it">
            </label>

            <label class="field field--full <?= $err('damage') ? 'has-error' : '' ?>">
              <span class="field__label">What is wrong <span class="req">*</span></span>
              <textarea class="field__textarea" name="damage" rows="6" required maxlength="4000"
                placeholder="Describe the damage — where it is, how it happened, and whether the item is still usable."><?= e(old('damage')) ?></textarea>
              <?php if ($err('damage')): ?><span class="field__error"><?= e($err('damage')) ?></span><?php endif; ?>
            </label>

            <label class="field field--full">
              <span class="field__label">How soon do you need it</span>
              <select class="field__select" name="urgency">
                <?php foreach ($urgency as $value => $label): ?>
                  <option value="<?= e($value) ?>" <?= old('urgency') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>02</i> Photographs</legend>

          <div class="photo-drop" id="photo-drop">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <rect x="3" y="6" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.4"/>
              <path d="M8 6l1.5-2h5L16 6" stroke="currentColor" stroke-width="1.4" stroke-linejoin="round"/>
              <circle cx="12" cy="13" r="3.4" stroke="currentColor" stroke-width="1.4"/>
            </svg>
            <p><strong>Add photographs</strong> or drop them here</p>
            <p class="field__hint">Two or three angles of the damage, up to six images. This speeds the assessment up considerably.</p>
            <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            <div class="photo-drop__preview"></div>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>03</i> Your details</legend>

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
              <input class="field__input" type="tel" name="phone" value="<?= e($pre('phone', 'phone')) ?>" required maxlength="60" autocomplete="tel">
              <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
            </label>

            <label class="field">
              <span class="field__label">Location</span>
              <input class="field__input" type="text" name="location" value="<?= e($pre('location', 'location')) ?>" maxlength="200">
            </label>
          </div>
        </fieldset>

        <div style="margin-top:2.25rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center">
          <button class="btn btn--lg" type="submit">Send Repair Request</button>
          <span class="field__hint" style="margin:0">We quote before any work begins.</span>
        </div>
      </form>

      <aside class="quote-aside">
        <div class="quote-summary">
          <h3>How it works</h3>
          <ol class="numbered-list" style="margin-top:1.1rem">
            <li><strong>You send photographs.</strong> Two or three angles of the damage.</li>
            <li><strong>We assess and quote.</strong> Usually within a working day or two.</li>
            <li><strong>You approve.</strong> Nothing starts until you say so.</li>
            <li><strong>We repair and call you.</strong> Most jobs take about a week.</li>
          </ol>
        </div>

        <div class="quote-summary" style="margin-top:1.25rem">
          <h3>What we handle</h3>
          <ul class="tick-list" style="margin-top:1rem">
            <li>Broken and cracked saddle trees</li>
            <li>Re-flocking and panel repair</li>
            <li>Restitching bridles, girths and leathers</li>
            <li>Replacement billets and fittings</li>
            <li>Rug repairs and re-proofing</li>
            <li>Brass nameplate engraving</li>
          </ul>
          <p class="quote-summary__note">
            Drop the item at <?= e(setting('contact_address')) ?>, or send photographs first
            if you would rather have a figure before you travel.
          </p>
        </div>
      </aside>
    </div>
  </div>
</section>
