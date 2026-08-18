<?php
use App\Models\Order;
use App\Models\Payment;
/** @var array $order @var array $items @var array $payments */
$outstanding = round((float) $order['total'] - (float) $order['amount_paid'], 2);
$wa = whatsapp_link('Hello ' . $order['customer_name'] . ', regarding your Tack Rack order ' . $order['reference'] . ':');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/orders')) ?>">&larr; All orders</a>
  <div class="a-actions">
    <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/orders/' . $order['id'] . '/print')) ?>" target="_blank" rel="noopener">Print</a>
    <a class="a-btn a-btn--ghost a-btn--sm" href="mailto:<?= e($order['email']) ?>?subject=<?= e(rawurlencode('Your order ' . $order['reference'])) ?>">Email</a>
    <?php if ($wa !== ''): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e($wa) ?>" target="_blank" rel="noopener">WhatsApp</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <section class="a-panel">
      <div class="a-panel__head">
        <h2>Items</h2>
        <span class="a-badge a-badge--plain"><?= count($items) ?> line<?= count($items) === 1 ? '' : 's' ?></span>
      </div>

      <div class="a-table-wrap">
        <table class="a-table">
          <thead>
            <tr><th>Item</th><th>SKU</th><th>Option</th><th class="a-table__num">Qty</th><th class="a-table__num">Unit</th><th class="a-table__num">Total</th></tr>
          </thead>
          <tbody>
            <?php foreach ($items as $item): ?>
              <tr>
                <td>
                  <?php if ($item['product_slug']): ?>
                    <a class="a-strong" href="<?= e(url('/product/' . $item['product_slug'])) ?>" target="_blank" rel="noopener"><?= e($item['product_name']) ?></a>
                  <?php else: ?>
                    <?= e($item['product_name']) ?>
                    <div class="a-cell-media__meta">no longer in the catalog</div>
                  <?php endif; ?>
                </td>
                <td class="a-muted"><?= e($item['product_sku'] ?: '—') ?></td>
                <td class="a-muted"><?= e($item['variant'] ?: '—') ?></td>
                <td class="a-table__num"><?= (int) $item['quantity'] ?></td>
                <td class="a-table__num"><?= e(money($item['unit_price'])) ?></td>
                <td class="a-table__num"><strong><?= e(money($item['line_total'])) ?></strong></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
          <tfoot>
            <tr><th colspan="5" class="a-table__num" style="background:transparent">Subtotal</th>
                <th class="a-table__num" style="background:transparent"><?= e(money($order['subtotal'])) ?></th></tr>
            <tr><th colspan="5" class="a-table__num" style="background:transparent">Delivery</th>
                <th class="a-table__num" style="background:transparent"><?= e(money($order['delivery_cost'])) ?></th></tr>
            <tr><th colspan="5" class="a-table__num" style="background:transparent;font-size:.9rem">Total</th>
                <th class="a-table__num" style="background:transparent;font-size:.95rem"><?= e(money($order['total'])) ?></th></tr>
            <?php if ((float) $order['amount_paid'] > 0): ?>
              <tr><th colspan="5" class="a-table__num" style="background:transparent">Paid</th>
                  <th class="a-table__num" style="background:transparent;color:var(--a-green)"><?= e(money($order['amount_paid'])) ?></th></tr>
            <?php endif; ?>
            <?php if ($outstanding > 0.01): ?>
              <tr><th colspan="5" class="a-table__num" style="background:transparent">Outstanding</th>
                  <th class="a-table__num" style="background:transparent;color:var(--a-red)"><?= e(money($outstanding)) ?></th></tr>
            <?php endif; ?>
          </tfoot>
        </table>
      </div>
    </section>

    <!-- Payments -->
    <section class="a-panel">
      <div class="a-panel__head"><h2>Payments</h2></div>

      <?php if ($payments !== []): ?>
        <div class="a-table-wrap">
          <table class="a-table">
            <thead>
              <tr><th>Method</th><th>Reference</th><th>Result</th><th class="a-table__num">Amount</th><th>Status</th><th>When</th></tr>
            </thead>
            <tbody>
              <?php foreach ($payments as $payment): ?>
                <tr>
                  <td><?= e(Payment::METHODS[$payment['method']] ?? $payment['method']) ?></td>
                  <td class="a-muted">
                    <?= $payment['mpesa_receipt'] ? '<span class="a-ref">' . e($payment['mpesa_receipt']) . '</span>' : '—' ?>
                  </td>
                  <td class="a-muted" style="max-width:16rem"><?= e($payment['result_desc'] ?: '—') ?></td>
                  <td class="a-table__num"><?= e(money($payment['amount'])) ?></td>
                  <td>
                    <span class="a-badge a-badge--<?= $payment['status'] === 'success' ? 'won' : ($payment['status'] === 'pending' ? 'new' : 'danger') ?>">
                      <?= e(ucfirst($payment['status'])) ?>
                    </span>
                  </td>
                  <td class="a-faint a-nowrap"><?= e(pretty_date($payment['created_at'], true)) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="a-panel__body"><p class="a-muted" style="font-size:.855rem">No payment attempts yet.</p></div>
      <?php endif; ?>

      <form method="post" action="<?= e(url('/admin/orders/' . $order['id'] . '/payment')) ?>">
        <?= csrf_field() ?>
        <div class="a-panel__body" style="border-top:1px solid var(--a-line-soft)">
          <p class="a-section-title">Record a payment taken outside the site</p>
          <div class="a-form-grid a-form-grid--3">
            <label class="a-field">
              <span class="a-label">Method</span>
              <select class="a-select" name="method">
                <option value="bank">Bank transfer</option>
                <option value="cash">Cash on collection</option>
                <option value="mpesa">M-Pesa (manual)</option>
                <option value="card">Card</option>
              </select>
            </label>

            <label class="a-field">
              <span class="a-label">Amount</span>
              <div class="a-input-group">
                <input class="a-input" type="number" name="amount" step="0.01" min="0"
                       value="<?= $outstanding > 0 ? e(number_format($outstanding, 2, '.', '')) : '' ?>" placeholder="0.00">
                <span class="a-input-group__addon"><?= e(config('app.currency', 'KSh')) ?></span>
              </div>
            </label>

            <label class="a-field">
              <span class="a-label">Note</span>
              <input class="a-input" type="text" name="note" maxlength="200" placeholder="Bank ref, receipt number…">
            </label>
          </div>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn a-btn--sm" type="submit">Record payment</button>
          <span class="a-hint" style="margin:0">This marks the order paid once the total is met.</span>
        </div>
      </form>
    </section>

    <!-- Status -->
    <section class="a-panel">
      <div class="a-panel__head"><h2>Fulfilment</h2></div>

      <form method="post" action="<?= e(url('/admin/orders/' . $order['id'] . '/update')) ?>">
        <?= csrf_field() ?>
        <div class="a-panel__body">
          <div class="a-form-grid a-form-grid--2">
            <label class="a-field">
              <span class="a-label">Status</span>
              <select class="a-select" name="status">
                <?php foreach (Order::STATUSES as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </label>

            <label class="a-field a-col-full">
              <span class="a-label">Internal notes</span>
              <textarea class="a-textarea" name="admin_notes" rows="4"
                placeholder="Courier waybill, who packed it, anything the customer should be told on dispatch."><?= e($order['admin_notes']) ?></textarea>
              <span class="a-hint">Included in the dispatch email if you tick the box below.</span>
            </label>
          </div>

          <label class="a-check" style="margin-top:1.1rem">
            <input type="checkbox" name="notify" value="1" checked>
            Email the customer when the status changes
          </label>
        </div>
        <div class="a-panel__foot">
          <button class="a-btn" type="submit">Save order</button>
        </div>
      </form>
    </section>
  </div>

  <div class="a-stack">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Customer</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Reference</dt><dd><span class="a-ref"><?= e($order['reference']) ?></span></dd></div>
          <div><dt>Name</dt><dd>
            <?php if ($order['customer_id']): ?>
              <a href="<?= e(url('/admin/customers/' . $order['customer_id'])) ?>"><?= e($order['customer_name']) ?></a>
            <?php else: ?>
              <?= e($order['customer_name']) ?> <span class="a-faint">(guest)</span>
            <?php endif; ?>
          </dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a></dd></div>
          <div><dt>Phone</dt><dd><a href="tel:<?= e(preg_replace('/\s+/', '', $order['phone'])) ?>"><?= e($order['phone']) ?></a></dd></div>
          <div><dt>Placed</dt><dd><?= e(pretty_date($order['created_at'], true)) ?></dd></div>
          <div><dt>IP</dt><dd class="a-faint"><?= e($order['ip_address'] ?: '—') ?></dd></div>
        </dl>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__head"><h2>Delivery</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Method</dt><dd><?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?></dd></div>
          <?php if ($order['delivery_address']): ?>
            <div><dt>Address</dt><dd><?= nl2br(e($order['delivery_address'])) ?></dd></div>
            <div><dt>Town</dt><dd><?= e($order['delivery_town']) ?></dd></div>
          <?php endif; ?>
          <div><dt>Cost</dt><dd><?= e(money($order['delivery_cost'])) ?></dd></div>
        </dl>

        <?php if ($order['notes']): ?>
          <p class="a-section-title" style="margin-top:1.25rem">Customer notes</p>
          <div class="a-note"><?= e($order['notes']) ?></div>
        <?php endif; ?>
      </div>
    </section>

    <section class="a-panel">
      <div class="a-panel__body">
        <form method="post" action="<?= e(url('/admin/orders/' . $order['id'] . '/delete')) ?>"
              data-confirm="Delete order <?= e($order['reference']) ?> and its payment records? This cannot be undone.">
          <?= csrf_field() ?>
          <button class="a-btn a-btn--danger a-btn--block" type="submit">Delete this order</button>
        </form>
      </div>
    </section>
  </div>
</div>
