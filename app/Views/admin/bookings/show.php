<?php
use App\Models\Booking;
/** @var array $booking */
$wa = whatsapp_link('Hello ' . $booking['name'] . ', regarding your Tack Rack saddle fitting ' . $booking['reference'] . ':');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/bookings')) ?>">&larr; All fittings</a>
  <div class="a-actions">
    <a class="a-btn a-btn--ghost a-btn--sm" href="mailto:<?= e($booking['email']) ?>?subject=<?= e(rawurlencode('Your saddle fitting ' . $booking['reference'])) ?>">Email</a>
    <?php if ($wa !== ''): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>The horse</h2>
        <span class="a-badge a-badge--plain"><?= e($booking['service_name'] ?? 'Saddle Fitting') ?></span>
      </div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Horse</dt><dd><?= e($booking['horse_name'] ?: '—') ?></dd></div>
          <div><dt>Discipline</dt><dd><?= e($booking['discipline'] ?: '—') ?></dd></div>
          <div><dt>Details</dt><dd><?= $booking['horse_details'] ? nl2br(e($booking['horse_details'])) : '—' ?></dd></div>
          <div><dt>Current saddle</dt><dd><?= $booking['saddle_details'] ? nl2br(e($booking['saddle_details'])) : '—' ?></dd></div>
        </dl>
      </div>
    </section>

    <?php if (!empty($booking['notes'])): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Customer notes</h2></div>
        <div class="a-panel__body"><div class="a-note"><?= e($booking['notes']) ?></div></div>
      </section>
    <?php endif; ?>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Schedule &amp; status</h2></div>

      <form method="post" action="<?= e(url('/admin/bookings/' . $booking['id'] . '/update')) ?>">
        <?= csrf_field() ?>

        <div class="a-panel__body">
          <div class="a-form-grid a-form-grid--3">
            <label class="a-field">
              <span class="a-label">Status</span>
              <select class="a-select" name="status">
                <?php foreach (Booking::STATUSES as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= $booking['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="a-field">
              <span class="a-label">Confirmed date &amp; time</span>
              <input class="a-input" type="datetime-local" name="scheduled_at"
                     value="<?= $booking['scheduled_at'] ? e(date('Y-m-d\TH:i', strtotime($booking['scheduled_at']))) : '' ?>">
            </label>

            <label class="a-field">
              <span class="a-label">Fee</span>
              <div class="a-input-group">
                <input class="a-input" type="number" name="fee" value="<?= e($booking['fee']) ?>" step="0.01" min="0" placeholder="0.00">
                <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
              </div>
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Internal notes</span>
              <textarea class="a-textarea" name="admin_notes" rows="5"
                placeholder="Travel arrangements, saddles to bring, findings from the assessment…"><?= e($booking['admin_notes']) ?></textarea>
              <span class="a-hint">Never shown to the customer.</span>
            </label>
          </div>

          <label class="a-check" style="margin-top:1.1rem">
            <input type="checkbox" name="notify" value="1" checked>
            Email the customer if the status changes to Confirmed or Scheduled
          </label>
        </div>

        <div class="a-panel__foot">
          <button class="a-btn" type="submit">Save booking</button>
        </div>
      </form>
    </section>
  </div>

  <div class="a-stack">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Customer</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Reference</dt><dd><span class="a-ref"><?= e($booking['reference']) ?></span></dd></div>
          <div><dt>Name</dt><dd><?= e($booking['name']) ?></dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?= e($booking['email']) ?>"><?= e($booking['email']) ?></a></dd></div>
          <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $booking['phone'])) ?>"><?= e($booking['phone']) ?></a></dd></div>
          <div><dt>Location</dt><dd><?= e($booking['location'] ?: '—') ?></dd></div>
          <div><dt>Where</dt><dd><?= (int) $booking['at_yard'] === 1 ? 'At their yard' : 'At the shop' ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Requested</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Preferred</dt><dd><?= $booking['preferred_date'] ? e(pretty_date($booking['preferred_date'])) : 'Flexible' ?></dd></div>
          <div><dt>Time of day</dt><dd><?= e(Booking::SLOTS[$booking['preferred_slot']] ?? '—') ?></dd></div>
          <div><dt>Alternative</dt><dd><?= $booking['alternate_date'] ? e(pretty_date($booking['alternate_date'])) : '—' ?></dd></div>
          <div><dt>Sent</dt><dd><?= e(pretty_date($booking['created_at'], true)) ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__body">
        <form method="post" action="<?= e(url('/admin/bookings/' . $booking['id'] . '/delete')) ?>"
              data-confirm="Delete booking <?= e($booking['reference']) ?>? This cannot be undone.">
          <?= csrf_field() ?>
          <button class="a-btn a-btn--danger a-btn--block" type="submit">Delete this booking</button>
        </form>
      </div>
    </section>
  </div>
</div>
