<?php
use App\Models\Order;
use App\Models\Payment;
/** @var array $order @var array $items @var array $payments */
$outstanding = round((float) $order['total'] - (float) $order['amount_paid'], 2);
?>

<div class="a-print">
  <div class="a-no-print a-row" style="margin-bottom:1.5rem">
    <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/orders/' . $order['id'])) ?>">&larr; Back to the order</a>
    <button class="a-btn a-btn--sm" type="button" onclick="window.print()">Print</button>
  </div>

  <header class="a-print__head">
    <div>
      <h1><?= e(setting('site_name', 'Tack Rack')) ?></h1>
      <p class="a-muted" style="font-size:.82rem">Equine Supplies &middot; Est. <?= e(setting('founded_year', '1997')) ?></p>
      <p style="margin-top:1rem;font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">
        <?= $order['payment_status'] === 'paid' ? 'Receipt' : 'Order' ?>
      </p>
      <p style="font-family:var(--a-mono);font-size:1.1rem;margin-top:.2rem"><?= e($order['reference']) ?></p>
    </div>

    <address>
      <?= e(setting('contact_address')) ?><br>
      <?= e(setting('contact_postal')) ?><br>
      <?= e(setting('contact_phone')) ?><br>
      <?= e(setting('contact_email')) ?>
    </address>
  </header>

  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(14rem,1fr));gap:1.5rem;padding-block:1.5rem;border-bottom:1px solid var(--a-line)">
    <div>
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Customer</p>
      <p style="margin-top:.4rem;font-weight:600"><?= e($order['customer_name']) ?></p>
      <p class="a-muted" style="font-size:.85rem">
        <?= e($order['email']) ?><br><?= e($order['phone']) ?>
      </p>
    </div>

    <div>
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Delivery</p>
      <p class="a-muted" style="margin-top:.4rem;font-size:.85rem">
        <?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?>
        <?php if ($order['delivery_address']): ?>
          <br><?= nl2br(e($order['delivery_address'])) ?><br><?= e($order['delivery_town']) ?>
        <?php endif; ?>
      </p>
    </div>

    <div>
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Details</p>
      <p class="a-muted" style="margin-top:.4rem;font-size:.85rem">
        Placed: <?= e(pretty_date($order['created_at'], true)) ?><br>
        Status: <?= e(Order::STATUSES[$order['status']]) ?><br>
        Payment: <?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?>
      </p>
    </div>
  </div>

  <table class="a-table" style="margin-top:1.5rem">
    <thead>
      <tr>
        <th>Item</th><th>SKU</th><th>Option</th>
        <th class="a-table__num">Qty</th><th class="a-table__num">Unit</th><th class="a-table__num">Total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= e($item['product_name']) ?></td>
          <td class="a-muted"><?= e($item['product_sku'] ?: '') ?></td>
          <td class="a-muted"><?= e($item['variant'] ?: '') ?></td>
          <td class="a-table__num"><?= (int) $item['quantity'] ?></td>
          <td class="a-table__num"><?= e(money($item['unit_price'])) ?></td>
          <td class="a-table__num"><?= e(money($item['line_total'])) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr><th colspan="5" class="a-table__num" style="background:transparent;border-bottom:0">Subtotal</th>
          <th class="a-table__num" style="background:transparent;border-bottom:0"><?= e(money($order['subtotal'])) ?></th></tr>
      <tr><th colspan="5" class="a-table__num" style="background:transparent;border-bottom:0">Delivery</th>
          <th class="a-table__num" style="background:transparent;border-bottom:0"><?= e(money($order['delivery_cost'])) ?></th></tr>
      <tr><th colspan="5" class="a-table__num" style="background:transparent;border-bottom:0;font-size:.95rem">Total</th>
          <th class="a-table__num" style="background:transparent;border-bottom:0;font-size:.95rem"><?= e(money($order['total'])) ?></th></tr>
      <?php if ((float) $order['amount_paid'] > 0): ?>
        <tr><th colspan="5" class="a-table__num" style="background:transparent;border-bottom:0">Paid</th>
            <th class="a-table__num" style="background:transparent;border-bottom:0"><?= e(money($order['amount_paid'])) ?></th></tr>
      <?php endif; ?>
      <?php if ($outstanding > 0.01): ?>
        <tr><th colspan="5" class="a-table__num" style="background:transparent;border-bottom:0">Outstanding</th>
            <th class="a-table__num" style="background:transparent;border-bottom:0"><?= e(money($outstanding)) ?></th></tr>
      <?php endif; ?>
    </tfoot>
  </table>

  <?php
    $receipts = array_filter($payments, static fn ($p) => $p['status'] === 'success' && $p['mpesa_receipt']);
  ?>
  <?php if ($receipts !== []): ?>
    <p class="a-muted" style="margin-top:1.25rem;font-size:.82rem">
      Payment references:
      <?= e(implode(', ', array_column($receipts, 'mpesa_receipt'))) ?>
    </p>
  <?php endif; ?>

  <p class="a-muted" style="margin-top:2.5rem;padding-top:1.25rem;border-top:1px solid var(--a-line);font-size:.78rem">
    Prices are in Kenyan Shillings and include VAT where applicable. Unused stock items in
    original condition may be returned within 14 days. Made-to-order and workshop-manufactured
    goods are not returnable unless faulty. Thank you for your custom.
  </p>
</div>
