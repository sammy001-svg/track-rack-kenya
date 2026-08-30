<?php
/** @var array $errors */
$err = static fn (string $field): string => $errors[$field] ?? '';
$mapEmbed = setting('map_embed');
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li>Contact</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Ngong Road, Nairobi</p>
    <h1>Come and see us.</h1>
    <p class="lede">
      The shop is at the MacNaughton Business Centre with parking at the door.
      Call, email or message — whichever suits you.
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="contact-layout">

      <!-- Details -->
      <div>
        <div class="contact-block">
          <p class="contact-block__label">Visit</p>
          <p class="contact-block__big"><?= e(setting('contact_address')) ?></p>
          <?php if (setting('contact_directions')): ?>
            <p class="muted" style="margin-top:.5rem"><?= e(setting('contact_directions')) ?></p>
          <?php endif; ?>
          <p class="muted" style="margin-top:.35rem"><?= e(setting('contact_postal')) ?></p>
          <?php if (setting('map_link')): ?>
            <a class="link" href="<?= e(setting('map_link')) ?>" target="_blank" rel="noopener" style="margin-top:.9rem">
              Get directions
              <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </a>
          <?php endif; ?>
        </div>

        <div class="contact-block">
          <p class="contact-block__label">Call</p>
          <p class="contact-block__big">
            <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a>
          </p>
          <?php if (setting('contact_phone_alt')): ?>
            <p style="margin-top:.35rem">
              <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone_alt'))) ?>" class="muted"><?= e(setting('contact_phone_alt')) ?></a>
            </p>
          <?php endif; ?>
        </div>

        <div class="contact-block">
          <p class="contact-block__label">Email</p>
          <p class="contact-block__big">
            <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a>
          </p>
        </div>

        <div class="contact-block">
          <p class="contact-block__label">Opening hours</p>
          <p class="muted"><?= e(setting('contact_hours')) ?></p>
        </div>

        <?php $wa = whatsapp_link('Hello Tack Rack, I would like to enquire about'); ?>
        <?php if ($wa !== ''): ?>
          <a class="btn" href="<?= e($wa) ?>" target="_blank" rel="noopener" style="margin-top:2rem">
            Message us on WhatsApp
          </a>
        <?php endif; ?>

        <div class="map-frame" style="margin-top:2.5rem">
          <?php if ($mapEmbed): ?>
            <iframe src="<?= e($mapEmbed) ?>" title="Map to Tack Rack, Ngong Road, Nairobi"
                    loading="lazy" referrerpolicy="no-referrer-when-downgrade" allowfullscreen></iframe>
          <?php else: ?>
            <div class="map-placeholder">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11z" stroke="currentColor" stroke-width="1.2"/>
                <circle cx="12" cy="10" r="2.6" stroke="currentColor" stroke-width="1.2"/>
              </svg>
              <p><?= e(setting('contact_address')) ?></p>
              <p style="font-size:.78rem">Add a Google Maps embed URL in the admin settings to show the map here.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- Form -->
      <div>
        <div class="quote-summary">
          <h3>Send a message</h3>
          <p class="muted" style="margin-top:.65rem;font-size:.9rem">
            For pricing on specific items, the
            <a href="<?= e(url('/request-a-quote')) ?>" style="color:var(--tan)">quote request form</a>
            gets you a faster answer.
          </p>

          <form method="post" action="<?= e(url('/contact')) ?>" novalidate style="margin-top:1.75rem">
            <?= csrf_field() ?>

            <div class="honeypot" aria-hidden="true">
              <label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
            </div>

            <div class="form-grid">
              <label class="field <?= $err('name') ? 'has-error' : '' ?>">
                <span class="field__label">Your name <span class="req">*</span></span>
                <input class="field__input" type="text" name="name" value="<?= e(old('name')) ?>" required autocomplete="name" maxlength="150">
                <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('email') ? 'has-error' : '' ?>">
                <span class="field__label">Email address <span class="req">*</span></span>
                <input class="field__input" type="email" name="email" value="<?= e(old('email')) ?>" required autocomplete="email" maxlength="190">
                <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('phone') ? 'has-error' : '' ?>">
                <span class="field__label">Phone</span>
                <input class="field__input" type="tel" name="phone" value="<?= e(old('phone')) ?>" autocomplete="tel" maxlength="60">
                <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
              </label>

              <label class="field <?= $err('subject') ? 'has-error' : '' ?>">
                <span class="field__label">Subject</span>
                <input class="field__input" type="text" name="subject" value="<?= e(old('subject')) ?>"
                       placeholder="Saddle fitting, repair, general enquiry…" maxlength="200">
              </label>

              <label class="field <?= $err('body') ? 'has-error' : '' ?>">
                <span class="field__label">Message <span class="req">*</span></span>
                <textarea class="field__textarea" name="body" rows="6" required maxlength="5000"><?= e(old('body')) ?></textarea>
                <?php if ($err('body')): ?><span class="field__error"><?= e($err('body')) ?></span><?php endif; ?>
              </label>
            </div>

            <button class="btn btn--block btn--lg" type="submit" style="margin-top:1.75rem">Send Message</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
