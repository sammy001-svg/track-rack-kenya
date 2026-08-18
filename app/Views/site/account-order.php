<?php
use App\Models\Order;
use App\Models\Payment;
/** @var array $customer @var array $order @var array $items @var array $payments */
$outstanding = round((float) $order['total'] - (float) $order['amount_paid'], 2);
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li><a href="<?= e(url('/account/orders')) ?>">Orders</a></li>
      <li><?= e($order['reference']) ?></li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Order <?= e($order['reference']) ?></p>
    <h1>Your order.</h1>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">

        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Items</h2>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap">
              <span class="pill pill--<?= e($order['status']) ?>"><?= e(Order::STATUSES[$order['status']]) ?></span>
              <span class="pill pill--<?= e($order['payment_status']) ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?></span>
            </div>
          </div>

          <table class="mini-table">
            <thead>
              <tr><th>Item</th><th class="num">Qty</th><th class="num">Price</th><th class="num">Total</th></tr>
            </thead>
            <tbody>
              <?php foreach ($items as $item): ?>
                <tr>
                  <td>
                    <?php if ($item['product_slug']): ?>
                      <a href="<?= e(url('/product/' . $item['product_slug'])) ?>"><?= e($item['product_name']) ?></a>
                    <?php else: ?>
                      <?= e($item['product_name']) ?>
                    <?php endif; ?>
                    <?php if ($item['variant']): ?><small><?= e($item['variant']) ?></small><?php endif; ?>
                  </td>
                  <td class="num"><?= (int) $item['quantity'] ?></td>
                  <td class="num"><?= e(money($item['unit_price'])) ?></td>
                  <td class="num"><?= e(money($item['line_total'])) ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
            <tfoot>
              <tr><td colspan="3" class="num">Subtotal</td><td class="num"><?= e(money($order['subtotal'])) ?></td></tr>
              <tr><td colspan="3" class="num">Delivery</td><td class="num">
                <?= (float) $order['delivery_cost'] > 0 ? e(money($order['delivery_cost'])) : 'Free' ?>
              </td></tr>
              <tr class="total"><td colspan="3" class="num">Total</td><td class="num"><?= e(money($order['total'])) ?></td></tr>
              <?php if ((float) $order['amount_paid'] > 0): ?>
                <tr><td colspan="3" class="num">Paid</td><td class="num"><?= e(money($order['amount_paid'])) ?></td></tr>
              <?php endif; ?>
              <?php if ($outstanding > 0.01): ?>
                <tr class="total"><td colspan="3" class="num">Outstanding</td><td class="num"><?= e(money($outstanding)) ?></td></tr>
              <?php endif; ?>
            </tfoot>
          </table>

          <?php if ($outstanding > 0.01 && $order['status'] !== 'cancelled'): ?>
            <a class="btn" href="<?= e(url('/checkout/pay/' . $order['reference'])) ?>" style="margin-top:1.5rem">
              Pay the balance
            </a>
          <?php endif; ?>
        </section>

        <section class="account-panel">
          <div class="account-panel__head"><h2>Delivery</h2></div>
          <dl class="detail-list">
            <div><dt>Method</dt><dd><?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?></dd></div>
            <?php if ($order['delivery_address']): ?>
              <div><dt>Address</dt><dd><?= nl2br(e($order['delivery_address'])) ?><br><?= e($order['delivery_town']) ?></dd></div>
            <?php endif; ?>
            <div><dt>Placed</dt><dd><?= e(pretty_date($order['created_at'], true)) ?></dd></div>
            <?php if ($order['notes']): ?>
              <div><dt>Your notes</dt><dd><?= nl2br(e($order['notes'])) ?></dd></div>
            <?php endif; ?>
          </dl>
        </section>

        <?php if ($payments !== []): ?>
          <section class="account-panel">
            <div class="account-panel__head"><h2>Payments</h2></div>
            <ul class="record-list">
              <?php foreach ($payments as $payment): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e(Payment::METHODS[$payment['method']] ?? $payment['method']) ?></strong>
                      <small>
                        <?= e(pretty_date($payment['created_at'], true)) ?>
                        <?php if ($payment['mpesa_receipt']): ?> &middot; <?= e($payment['mpesa_receipt']) ?><?php endif; ?>
                      </small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= $payment['status'] === 'success' ? 'paid' : 'unpaid' ?>">
                        <?= e(ucfirst($payment['status'])) ?>
                      </span>
                      <strong><?= e(money($payment['amount'])) ?></strong>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
