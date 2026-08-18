<?php
use App\Models\Booking;
use App\Models\RepairRequest;

/** @var array $booking @var string $kind */
$isFitting = $kind === 'fitting';
$photos    = $photos ?? [];
?>

<section class="section" style="padding-top:calc(var(--header-h) + clamp(3.5rem,8vw,6rem))">
  <div class="shell shell--narrow">

    <div style="text-align:center" data-reveal>
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="margin-inline:auto;color:var(--tan)" aria-hidden="true">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.1" opacity=".35"/>
        <path d="M8 12.4l2.6 2.6L16 9.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>

      <p class="eyebrow eyebrow--center eyebrow--plain" style="margin-top:1.75rem">
        <?= $isFitting ? 'Fitting requested' : 'Repair request received' ?>
      </p>
      <h1 style="font-weight:300;margin-top:.9rem">Thank you, <?= e(explode(' ', $booking['name'])[0]) ?>.</h1>

      <p class="lede" style="margin:1.5rem auto 0">
        <?php if ($isFitting): ?>
          We have your saddle fitting request and will be in touch to confirm a date and time.
          Fittings are carried out by Sharon Ashley.
        <?php else: ?>
          Your request has reached our Nairobi workshop. We will assess it and come back
          with a quote before any work begins.
        <?php endif; ?>
      </p>

      <div style="display:inline-flex;align-items:center;gap:.85rem;margin-top:2.25rem;padding:.9rem 1.5rem;border:1px solid var(--rule);border-radius:999px;background:var(--paper)">
        <span style="font-size:.66rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--text-faint)">Reference</span>
        <strong style="font-family:var(--display);font-size:1.15rem;letter-spacing:.02em"><?= e($booking['reference']) ?></strong>
      </div>
    </div>

    <div class="quote-summary" style="margin-top:clamp(2.5rem,5vw,3.5rem)" data-reveal>
      <h3><?= $isFitting ? 'Your request' : 'What you sent us' ?></h3>

      <dl style="margin-top:1.35rem">
        <?php if ($isFitting): ?>
          <div><dt>Horse</dt><dd style="font-weight:500"><?= e($booking['horse_name'] ?: 'Not given') ?></dd></div>
          <div><dt>Preferred date</dt><dd style="font-weight:500">
            <?= $booking['preferred_date'] ? e(pretty_date($booking['preferred_date'])) : 'Flexible' ?>
          </dd></div>
          <div><dt>Time of day</dt><dd style="font-weight:500">
            <?= e(Booking::SLOTS[$booking['preferred_slot']] ?? 'Flexible') ?>
          </dd></div>
          <div><dt>Where</dt><dd style="font-weight:500">
            <?= (int) $booking['at_yard'] === 1 ? e($booking['location'] ?: 'Your yard') : 'At the shop' ?>
          </dd></div>
        <?php else: ?>
          <div><dt>Item</dt><dd style="font-weight:500"><?= e($booking['item_type']) ?></dd></div>
          <?php if ($booking['item_make']): ?>
            <div><dt>Make</dt><dd style="font-weight:500"><?= e($booking['item_make']) ?></dd></div>
          <?php endif; ?>
          <div><dt>Urgency</dt><dd style="font-weight:500">
            <?= e(RepairRequest::URGENCY[$booking['urgency']] ?? 'Standard') ?>
          </dd></div>
          <div><dt>Photographs</dt><dd style="font-weight:500"><?= count($photos) ?></dd></div>
        <?php endif; ?>
        <div><dt>Sent</dt><dd style="font-weight:500"><?= e(pretty_date($booking['created_at'], true)) ?></dd></div>
        <div><dt>Reply to</dt><dd style="font-weight:500"><?= e($booking['email']) ?></dd></div>
      </dl>

      <?php if (!$isFitting && $photos !== []): ?>
        <div style="display:flex;flex-wrap:wrap;gap:.6rem;margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
          <?php foreach ($photos as $photo): ?>
            <img src="<?= e(image($photo['path'])) ?>" alt=""
                 style="width:5rem;height:5rem;object-fit:cover;border-radius:3px;border:1px solid var(--rule)">
          <?php endforeach; ?>
        </div>
      <?php endif; ?>

      <p class="quote-summary__note">
        Quote this reference if you call us on <?= e(setting('contact_phone')) ?>.
      </p>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.5rem" data-reveal>
      <a class="btn" href="<?= e(url('/shop')) ?>">Browse the catalog</a>
      <?php $wa = whatsapp_link('Hello Tack Rack, this is about ' . $booking['reference'] . '.'); ?>
      <?php if ($wa !== ''): ?>
        <a class="btn btn--ghost" href="<?= e($wa) ?>" target="_blank" rel="noopener">Follow up on WhatsApp</a>
      <?php endif; ?>
    </div>
  </div>
</section>
