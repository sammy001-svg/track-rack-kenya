<?php
use App\Models\Order;
/** @var array $order @var array $items @var array $payments */
$paid    = $order['payment_status'] === 'paid';
$receipt = null;
foreach ($payments as $payment) {
    if ($payment['status'] === 'success' && $payment['mpesa_receipt']) {
        $receipt = $payment['mpesa_receipt'];
        break;
    }
}
?>

<section class="section" style="padding-top:calc(var(--header-h) + clamp(3.5rem,8vw,6rem))">
  <div class="shell shell--narrow">

    <div style="text-align:center" data-reveal>
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="margin-inline:auto;color:var(--tan)" aria-hidden="true">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.1" opacity=".35"/>
        <path d="M8 12.4l2.6 2.6L16 9.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>

      <p class="eyebrow eyebrow--center eyebrow--plain" style="margin-top:1.75rem">
        <?= $paid ? 'Payment received' : 'Order placed' ?>
      </p>
      <h1 style="font-weight:300;margin-top:.9rem">
        Thank you, <?= e(explode(' ', $order['customer_name'])[0]) ?>.
      </h1>

      <p class="lede" style="margin:1.5rem auto 0">
        <?php if ($paid): ?>
          Your payment has cleared and the order is confirmed. We will be in touch as soon
          as it is ready.
        <?php else: ?>
          Your order is placed. It will be confirmed once payment reaches us.
        <?php endif; ?>
      </p>

      <div style="display:flex;flex-wrap:wrap;gap:.75rem;justify-content:center;margin-top:2.25rem">
        <span style="display:inline-flex;align-items:center;gap:.85rem;padding:.9rem 1.5rem;border:1px solid var(--rule);border-radius:999px;background:var(--paper)">
          <span style="font-size:.66rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--text-faint)">Order</span>
          <strong style="font-family:var(--display);font-size:1.15rem"><?= e($order['reference']) ?></strong>
        </span>

        <?php if ($receipt !== null): ?>
          <span style="display:inline-flex;align-items:center;gap:.85rem;padding:.9rem 1.5rem;border:1px solid var(--rule);border-radius:999px;background:var(--paper)">
            <span style="font-size:.66rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--text-faint)">M-Pesa</span>
            <strong style="font-family:var(--display);font-size:1.15rem"><?= e($receipt) ?></strong>
          </span>
        <?php endif; ?>
      </div>
    </div>

    <div class="quote-summary" style="margin-top:clamp(2.5rem,5vw,3.5rem)" data-reveal>
      <h3>What you ordered</h3>

      <div class="mini-list" style="margin-top:1.35rem">
        <?php foreach ($items as $item): ?>
          <div class="mini-item">
            <div>
              <div class="mini-item__title"><?= e($item['product_name']) ?></div>
              <div class="mini-item__meta">
                <?= e(money($item['unit_price'])) ?> each
                <?php if ($item['variant']): ?> &middot; <?= e($item['variant']) ?><?php endif; ?>
              </div>
            </div>
            <span class="mini-item__qty">&times;<?= (int) $item['quantity'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <dl style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
        <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
        <div><dt>Delivery</dt><dd><?= (float) $order['delivery_cost'] > 0 ? e(money($order['delivery_cost'])) : 'Free' ?></dd></div>
        <div style="padding-top:.7rem;margin-top:.35rem;border-top:1px solid var(--rule-soft)">
          <dt style="color:var(--text);font-weight:600">Total</dt>
          <dd style="font-family:var(--display);font-size:1.3rem"><?= e(money($order['total'])) ?></dd>
        </div>
      </dl>

      <p class="quote-summary__note">
        <?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?>.
        A confirmation has gone to <?= e($order['email']) ?>.
      </p>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.5rem" data-reveal>
      <a class="btn" href="<?= e(url('/shop')) ?>">Keep browsing</a>
      <?php if (App\Core\CustomerAuth::check()): ?>
        <a class="btn btn--ghost" href="<?= e(url('/account/orders')) ?>">View your orders</a>
      <?php else: ?>
        <a class="btn btn--ghost" href="<?= e(url('/account/register')) ?>">Create an account</a>
      <?php endif; ?>
    </div>
  </div>
</section>
