<?php
/** @var array $items @var array $disciplines @var array $errors */
$err = static fn (string $field): string => $errors[$field] ?? '';
$unitCount = 0;
foreach ($items as $row) {
    $unitCount += $row['quantity'];
}
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li><a href="<?= e(url('/quote')) ?>">Quote list</a></li>
      <li>Request</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Step 2 of 2</p>
    <h1>Request a quote.</h1>
    <p class="lede">
      Tell us how to reach you and anything we should know about sizing or fitting.
      Most quotes are back within one working day.
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="quote-layout">

      <form method="post" action="<?= e(url('/request-a-quote')) ?>" novalidate>
        <?= csrf_field() ?>

        <div class="honeypot" aria-hidden="true">
          <label>Leave this field empty
            <input type="text" name="website" tabindex="-1" autocomplete="off">
          </label>
        </div>

        <?php if ($err('items') !== ''): ?>
          <p class="field__error" style="margin-bottom:1.5rem"><?= e($err('items')) ?></p>
        <?php endif; ?>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>01</i> Your details</legend>

          <div class="form-grid form-grid--2">
            <label class="field <?= $err('name') ? 'has-error' : '' ?>">
              <span class="field__label">Full name <span class="req">*</span></span>
              <input class="field__input" type="text" name="name" value="<?= e(old('name')) ?>"
                     required autocomplete="name" maxlength="150">
              <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('email') ? 'has-error' : '' ?>">
              <span class="field__label">Email address <span class="req">*</span></span>
              <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>"
                     required autocomplete="email" maxlength="190">
              <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('phone') ? 'has-error' : '' ?>">
              <span class="field__label">Phone / WhatsApp <span class="req">*</span></span>
              <input class="field__input" type="tel" name="phone" value="<?= e(old('phone')) ?>"
                     required autocomplete="tel" placeholder="+254 7…" maxlength="60">
              <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('location') ? 'has-error' : '' ?>">
              <span class="field__label">Location in Kenya</span>
              <input class="field__input" type="text" name="location" value="<?= e(old('location')) ?>"
                     placeholder="Karen, Nairobi" maxlength="150">
              <span class="field__hint">Helps us cost delivery accurately.</span>
            </label>

            <label class="field field--full">
              <span class="field__label">Discipline</span>
              <select class="field__select" name="discipline">
                <option value="">Not specified</option>
                <?php foreach ($disciplines as $discipline): ?>
                  <option value="<?= e($discipline) ?>" <?= old('discipline') === $discipline ? 'selected' : '' ?>>
                    <?= e($discipline) ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>02</i> Sizing &amp; special notes</legend>

          <label class="field <?= $err('notes') ? 'has-error' : '' ?>">
            <span class="field__label">Anything we should know</span>
            <textarea class="field__textarea" name="notes" rows="6" maxlength="4000"
              placeholder="Horse height and build, saddle seat size, rug measurement, colour preferences, whether you need a fitting visit…"><?= e(old('notes')) ?></textarea>
            <span class="field__hint">
              For saddles, tell us the horse and the rider. For rugs, the measurement from
              chest to point of buttock. If you are unsure, say so — we will advise.
            </span>
            <?php if ($err('notes')): ?><span class="field__error"><?= e($err('notes')) ?></span><?php endif; ?>
          </label>
        </fieldset>

        <div style="margin-top:2.25rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center">
          <button class="btn btn--lg" type="submit">Send Quote Request</button>
          <a class="link" href="<?= e(url('/quote')) ?>" style="color:var(--text-faint)">Back to the list</a>
        </div>

        <p class="field__hint" style="margin-top:1.25rem;max-width:46ch">
          By sending this request you agree to us storing your details for the purpose of
          preparing your quote. See our <a href="<?= e(url('/page/privacy-policy')) ?>" style="color:var(--tan)">privacy policy</a>.
        </p>
      </form>

      <!-- Selected items summary — pulled automatically from the catalog -->
      <aside class="quote-aside">
        <div class="quote-summary">
          <h3>Selected items</h3>

          <?php if ($items === []): ?>
            <p class="quote-summary__note" style="margin-top:1rem;border:0;padding:0">
              Your quote list is empty. <a href="<?= e(url('/shop')) ?>" style="color:var(--tan)">Add something from the catalog</a>
              before sending your request.
            </p>
          <?php else: ?>
            <div class="mini-list" style="margin-top:1.35rem">
              <?php foreach ($items as $row): ?>
                <?php $p = $row['product']; ?>
                <div class="mini-item">
                  <div class="mini-item__media">
                    <img src="<?= e(image($p['primary_image'] ?? null)) ?>" alt="" loading="lazy" width="70" height="70">
                  </div>
                  <div>
                    <div class="mini-item__title"><?= e($p['name']) ?></div>
                    <div class="mini-item__meta">
                      <?= e($p['sku'] ?? '') ?><?php if ($row['variant'] !== ''): ?> &middot; <?= e($row['variant']) ?><?php endif; ?>
                    </div>
                  </div>
                  <span class="mini-item__qty">&times;<?= (int) $row['quantity'] ?></span>
                </div>
              <?php endforeach; ?>
            </div>

            <dl style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
              <div><dt>Distinct items</dt><dd><?= count($items) ?></dd></div>
              <div><dt>Total units</dt><dd><?= (int) $unitCount ?></dd></div>
            </dl>

            <p class="quote-summary__note">
              <a href="<?= e(url('/quote')) ?>" style="color:var(--tan)">Edit the list</a> if you need to
              change quantities before sending.
            </p>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  </div>
</section>
