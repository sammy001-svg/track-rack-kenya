<?php
use App\Models\RepairRequest;
/** @var array $repair @var array $photos */
$wa = whatsapp_link('Hello ' . $repair['name'] . ', regarding your Tack Rack repair ' . $repair['reference'] . ':');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/repairs')) ?>">&larr; All repairs</a>
  <div class="a-actions">
    <a class="a-btn a-btn--ghost a-btn--sm" href="mailto:<?= e($repair['email']) ?>?subject=<?= e(rawurlencode('Your repair ' . $repair['reference'])) ?>">Email</a>
    <?php if ($wa !== ''): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head">
        <h2><?= e($repair['item_type']) ?><?= $repair['item_make'] ? ' — ' . e($repair['item_make']) : '' ?></h2>
        <span class="a-badge <?= $repair['urgency'] !== 'standard' ? 'a-badge--new' : 'a-badge--plain' ?>">
          <?= e(RepairRequest::URGENCY[$repair['urgency']] ?? 'Standard') ?>
        </span>
      </div>
      <div class="a-panel__body">
        <p class="a-section-title">Reported damage</p>
        <div class="a-note"><?= e($repair['damage']) ?></div>
      </div>
    </section>

    <!-- Photographs -->
    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Photographs</h2>
        <span class="a-badge a-badge--plain"><?= count($photos) ?></span>
      </div>

      <div class="a-panel__body a-stack">
        <?php if ($photos !== []): ?>
          <div class="a-images">
            <?php foreach ($photos as $photo): ?>
              <div class="a-image-card">
                <span class="a-image-card__flag"><?= $photo['uploaded_by'] === 'staff' ? 'Ours' : 'Customer' ?></span>
                <a href="<?= e(image($photo['path'])) ?>" target="_blank" rel="noopener">
                  <img src="<?= e(image($photo['path'])) ?>" alt="" loading="lazy">
                </a>
                <div class="a-image-card__bar">
                  <a class="a-icon-btn" href="<?= e(image($photo['path'])) ?>" target="_blank" rel="noopener" title="Open full size">
                    <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M14 4h6v6M20 4l-9 9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/repair-photos/' . $photo['id'] . '/delete')) ?>"
                        data-confirm="Delete this photograph?" style="margin-left:auto">
                    <?= csrf_field() ?>
                    <button class="a-icon-btn a-icon-btn--danger" type="submit" title="Delete">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                  </form>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="a-muted" style="font-size:.855rem">
            The customer did not attach photographs. Ask them to reply to the acknowledgement
            email with a couple of shots of the damage.
          </p>
        <?php endif; ?>

        <form method="post" action="<?= e(url('/admin/repairs/' . $repair['id'] . '/photos')) ?>" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <label class="a-drop">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/><path d="M4 16v3a1 1 0 0 0 1 1h14a1 1 0 0 0 1-1v-3" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/></svg>
            <p><strong>Add assessment photographs</strong> or drop them here</p>
            <input type="file" name="photos[]" accept="image/jpeg,image/png,image/webp,image/gif" multiple>
            <span class="a-drop__list"></span>
          </label>
          <button class="a-btn a-btn--ghost a-btn--sm" type="submit" style="margin-top:.7rem">Upload photographs</button>
        </form>
      </div>
    </section>

    <!-- Assessment -->
    <section class="a-panel">
      <div class="a-panel__head"><h2>Assessment &amp; quote</h2></div>

      <form method="post" action="<?= e(url('/admin/repairs/' . $repair['id'] . '/update')) ?>">
        <?= csrf_field() ?>

        <div class="a-panel__body">
          <div class="a-form-grid a-form-grid--3">
            <label class="a-field">
              <span class="a-label">Status</span>
              <select class="a-select" name="status">
                <?php foreach (RepairRequest::STATUSES as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= $repair['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="a-field">
              <span class="a-label">Quoted amount</span>
              <div class="a-input-group">
                <input class="a-input" type="number" name="quoted_amount" value="<?= e($repair['quoted_amount']) ?>" step="0.01" min="0" placeholder="0.00">
                <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
              </div>
            </label>

            <label class="a-field">
              <span class="a-label">Estimated ready</span>
              <input class="a-input" type="date" name="estimated_ready" value="<?= e($repair['estimated_ready']) ?>">
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Notes to include in the customer email</span>
              <textarea class="a-textarea" name="admin_notes" rows="5"
                placeholder="What the repair involves, what it will cost, how long it will take."><?= e($repair['admin_notes']) ?></textarea>
              <span class="a-hint">Included in the email when you move the status to Quoted.</span>
            </label>
          </div>

          <label class="a-check" style="margin-top:1.1rem">
            <input type="checkbox" name="notify" value="1" checked>
            Email the customer when the status changes
          </label>
        </div>

        <div class="a-panel__foot">
          <button class="a-btn" type="submit">Save repair</button>
        </div>
      </form>
    </section>
  </div>

  <div class="a-stack">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Customer</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Reference</dt><dd><span class="a-ref"><?= e($repair['reference']) ?></span></dd></div>
          <div><dt>Name</dt><dd><?= e($repair['name']) ?></dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?= e($repair['email']) ?>"><?= e($repair['email']) ?></a></dd></div>
          <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $repair['phone'])) ?>"><?= e($repair['phone']) ?></a></dd></div>
          <div><dt>Location</dt><dd><?= e($repair['location'] ?: '—') ?></dd></div>
          <div><dt>Received</dt><dd><?= e(pretty_date($repair['created_at'], true)) ?></dd></div>
          <div><dt>Updated</dt><dd><?= e(pretty_date($repair['updated_at'], true)) ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__body">
        <form method="post" action="<?= e(url('/admin/repairs/' . $repair['id'] . '/delete')) ?>"
              data-confirm="Delete repair <?= e($repair['reference']) ?> and all its photographs?">
          <?= csrf_field() ?>
          <button class="a-btn a-btn--danger a-btn--block" type="submit">Delete this request</button>
        </form>
      </div>
    </section>
  </div>
</div>
