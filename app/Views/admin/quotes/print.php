<?php
use App\Models\Quote;
/** @var array $quote @var array $items */
$units = array_sum(array_column($items, 'quantity'));
?>

<div class="a-print">
  <div class="a-no-print a-row" style="margin-bottom:1.5rem">
    <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>">&larr; Back to the request</a>
    <button class="a-btn a-btn--sm" type="button" onclick="window.print()">Print this sheet</button>
  </div>

  <header class="a-print__head">
    <div>
      <h1><?= e(setting('site_name', 'Tack Rack')) ?></h1>
      <p class="a-muted" style="font-size:.82rem">Equine Supplies &middot; Est. <?= e(setting('founded_year', '1997')) ?></p>
      <p style="margin-top:1rem;font-size:.75rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Quote Request</p>
      <p style="font-family:var(--a-mono);font-size:1.1rem;margin-top:.2rem"><?= e($quote['reference']) ?></p>
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
      <p style="margin-top:.4rem;font-weight:600"><?= e($quote['customer_name']) ?></p>
      <p class="a-muted" style="font-size:.85rem">
        <?= e($quote['email']) ?><br>
        <?= e($quote['phone']) ?><br>
        <?= e($quote['location'] ?: '') ?>
      </p>
    </div>

    <div>
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Details</p>
      <p class="a-muted" style="margin-top:.4rem;font-size:.85rem">
        Received: <?= e(pretty_date($quote['created_at'], true)) ?><br>
        Discipline: <?= e($quote['discipline'] ?: 'Not specified') ?><br>
        Status: <?= e(Quote::STATUSES[$quote['status']]) ?>
      </p>
    </div>
  </div>

  <table class="a-table" style="margin-top:1.5rem">
    <thead>
      <tr>
        <th>Item</th>
        <th>SKU</th>
        <th>Option</th>
        <th class="a-table__num">Qty</th>
        <th class="a-table__num">Unit price</th>
        <th class="a-table__num">Line total</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
        <tr>
          <td><?= e($item['product_name']) ?></td>
          <td class="a-muted"><?= e($item['product_sku'] ?: '') ?></td>
          <td class="a-muted"><?= e($item['variant'] ?: '') ?></td>
          <td class="a-table__num"><?= (int) $item['quantity'] ?></td>
          <td class="a-table__num"><?= $item['unit_price'] !== null ? e(money($item['unit_price'])) : '' ?></td>
          <td class="a-table__num"><?= $item['unit_price'] !== null ? e(money($item['unit_price'] * $item['quantity'])) : '' ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <th colspan="3" style="background:transparent;border-bottom:0"></th>
        <th class="a-table__num" style="background:transparent;border-bottom:0"><?= (int) $units ?></th>
        <th class="a-table__num" style="background:transparent;border-bottom:0">Total</th>
        <th class="a-table__num" style="background:transparent;border-bottom:0;font-size:.95rem">
          <?= $quote['quoted_total'] !== null ? e(money($quote['quoted_total'])) : '__________' ?>
        </th>
      </tr>
    </tfoot>
  </table>

  <?php if (!empty($quote['notes'])): ?>
    <div style="margin-top:1.75rem">
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Customer notes</p>
      <div class="a-note" style="margin-top:.5rem"><?= e($quote['notes']) ?></div>
    </div>
  <?php endif; ?>

  <?php if (!empty($quote['admin_notes'])): ?>
    <div class="a-no-print" style="margin-top:1.5rem">
      <p style="font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:var(--a-faint)">Internal notes (not printed)</p>
      <div class="a-note" style="margin-top:.5rem"><?= e($quote['admin_notes']) ?></div>
    </div>
  <?php endif; ?>

  <p class="a-muted" style="margin-top:2.5rem;padding-top:1.25rem;border-top:1px solid var(--a-line);font-size:.78rem">
    Pricing is quoted in Kenyan Shillings and is subject to VAT where applicable and to stock availability
    at the time of confirmation. Imported items may be re-quoted if freight, duty or exchange rates move
    materially before an order is confirmed.
  </p>
</div>
