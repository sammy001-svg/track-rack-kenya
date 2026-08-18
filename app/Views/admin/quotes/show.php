<?php
use App\Models\Quote;
/** @var array $quote @var array $items */
$units = array_sum(array_column($items, 'quantity'));
$wa    = whatsapp_link('Hello ' . $quote['customer_name'] . ', regarding your Tack Rack quote request ' . $quote['reference'] . ':');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/quotes')) ?>">&larr; All requests</a>

  <div class="a-actions">
    <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/quotes/' . $quote['id'] . '/print')) ?>" target="_blank" rel="noopener">Print sheet</a>
    <a class="a-btn a-btn--ghost a-btn--sm" href="mailto:<?= e($quote['email']) ?>?subject=<?= e(rawurlencode('Your Tack Rack quote ' . $quote['reference'])) ?>">Reply by email</a>
    <?php if ($wa !== ''): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Requested items</h2>
        <span class="a-badge a-badge--plain"><?= count($items) ?> line<?= count($items) === 1 ? '' : 's' ?>, <?= (int) $units ?> unit<?= (int) $units === 1 ? '' : 's' ?></span>
      </div>

      <div class="a-table-wrap">
        <table class="a-table">
          <thead>
            <tr>
              <th>Item</th>
              <th>SKU</th>
              <th>Option</th>
              <th class="a-table__num">Qty</th>
              <th class="a-table__num">Listed price</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <?php if (!empty($item['product_slug'])): ?>
                    <a class="a-strong" href="<?= e(url('/product/' . $item['product_slug'])) ?>" target="_blank" rel="noopener"><?= e($item['product_name']) ?></a>
                  <?php else: ?>
                    <?= e($item['product_name']) ?>
                    <div class="a-cell-media__meta">no longer in the catalog</div>
                  <?php endif; ?>
                </td>
                <td class="a-muted"><?= e($item['product_sku'] ?: '—') ?></td>
                <td class="a-muted"><?= e($item['variant'] ?: '—') ?></td>
                <td class="a-table__num"><?= (int) $item['quantity'] ?></td>
                <td class="a-table__num"><?= $item['unit_price'] !== null ? e(money($item['unit_price'])) : '<span class="a-faint">On request</span>' ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </section>

    <?php if (!empty($quote['notes'])): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Customer notes</h2></div>
        <div class="a-panel__body">
          <div class="a-note"><?= e($quote['notes']) ?></div>
        </div>
      </section>
    <?php endif; ?>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Internal working notes</h2></div>
      <form method="post" action="<?= e(url('/admin/quotes/' . $quote['id'] . '/update')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="status" value="<?= e($quote['status']) ?>">
        <input type="hidden" name="quoted_total" value="<?= e($quote['quoted_total']) ?>">

        <div class="a-panel__body">
          <label class="a-field">
            <span class="a-sr">Internal notes</span>
            <textarea class="a-textarea" name="admin_notes" rows="6"
              placeholder="Supplier lead times, agreed discount, fitting appointment, follow-up dates…"><?= e($quote['admin_notes']) ?></textarea>
            <span class="a-hint">Never shown to the customer.</span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--sm" type="submit">Save notes</button>
        </div>
      </form>
    </section>
  </div>

  <!-- Aside -->
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Status &amp; value</h2>
      </div>
      <form method="post" action="<?= e(url('/admin/quotes/' . $quote['id'] . '/update')) ?>">
        <?= csrf_field() ?>
        <input type="hidden" name="admin_notes" value="<?= e($quote['admin_notes']) ?>">

        <div class="a-panel__body a-stack">
          <label class="a-field">
            <span class="a-label">Status</span>
            <select class="a-select" name="status">
              <?php foreach (Quote::STATUSES as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= $quote['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
          </label>

          <label class="a-field">
            <span class="a-label">Quoted total</span>
            <div class="a-input-group">
              <input class="a-input" type="number" name="quoted_total" value="<?= e($quote['quoted_total']) ?>" step="0.01" min="0" placeholder="0.00">
              <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
            </div>
            <span class="a-hint">For your records and reporting.</span>
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--block" type="submit">Update quote</button>
        </div>
      </form>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Customer</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Reference</dt><dd><span class="a-ref"><?= e($quote['reference']) ?></span></dd></div>
          <div><dt>Name</dt><dd><?= e($quote['customer_name']) ?></dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?= e($quote['email']) ?>"><?= e($quote['email']) ?></a></dd></div>
          <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $quote['phone'])) ?>"><?= e($quote['phone']) ?></a></dd></div>
          <div><dt>Location</dt><dd><?= e($quote['location'] ?: '—') ?></dd></div>
          <div><dt>Discipline</dt><dd><?= e($quote['discipline'] ?: '—') ?></dd></div>
          <div><dt>Received</dt><dd><?= e(pretty_date($quote['created_at'], true)) ?></dd></div>
          <div><dt>Last updated</dt><dd><?= e(pretty_date($quote['updated_at'], true)) ?></dd></div>
          <div><dt>IP address</dt><dd class="a-faint"><?= e($quote['ip_address'] ?: '—') ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__body">
        <form method="post" action="<?= e(url('/admin/quotes/' . $quote['id'] . '/delete')) ?>"
              data-confirm="Delete quote <?= e($quote['reference']) ?>? This cannot be undone.">
          <?= csrf_field() ?>
          <button class="a-btn a-btn--danger a-btn--block" type="submit">Delete this request</button>
        </form>
      </div>
    </section>
  </div>
</div>
