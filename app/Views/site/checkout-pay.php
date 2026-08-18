<?php
use App\Models\Order;
use App\Models\Payment;
/** @var array $order @var array $items @var array $payments @var bool $mpesaEnabled @var string $bankDetails */
$outstanding = round((float) $order['total'] - (float) $order['amount_paid'], 2);
$awaiting    = (string) ($_GET['awaiting'] ?? '');
?>

<section class="section" style="padding-top:calc(var(--header-h) + clamp(2.5rem,6vw,4rem))">
  <div class="shell shell--wide">

    <div style="text-align:center;max-width:40rem;margin-inline:auto">
      <p class="eyebrow eyebrow--center eyebrow--plain">Order <?= e($order['reference']) ?></p>
      <h1 style="font-weight:300;margin-top:.8rem">Payment.</h1>
      <p class="lede" style="margin:1.25rem auto 0">
        <?= e(money($outstanding)) ?> outstanding.
        <?= $mpesaEnabled ? 'Pay by M-Pesa now, or choose another method below.' : 'Choose a payment method below.' ?>
      </p>
    </div>

    <div class="quote-layout" style="margin-top:clamp(2.5rem,5vw,3.5rem)">
      <div>

        <?php if ($awaiting !== ''): ?>
          <div class="pay-waiting" id="pay-waiting"
               data-status-url="<?= e(url('/checkout/status/' . $order['reference'])) ?>">
            <div class="pay-waiting__spinner" aria-hidden="true"></div>
            <div>
              <strong>Check your phone</strong>
              <p>Enter your M-Pesa PIN on the prompt we have just sent. This page updates itself
                 the moment payment clears — no need to refresh.</p>
            </div>
          </div>
        <?php endif; ?>

        <!-- M-Pesa -->
        <?php if ($mpesaEnabled && $outstanding >= 1): ?>
          <section class="pay-method">
            <header>
              <h2>Pay by M-Pesa</h2>
              <span class="pay-method__badge">Instant</span>
            </header>

            <form method="post" action="<?= e(url('/checkout/mpesa')) ?>">
              <?= csrf_field() ?>
              <input type="hidden" name="reference" value="<?= e($order['reference']) ?>">

              <div class="form-grid form-grid--2">
                <label class="field">
                  <span class="field__label">M-Pesa number</span>
                  <input class="field__input" type="tel" name="phone" value="<?= e($order['phone']) ?>"
                         required placeholder="0722 123 456" inputmode="tel">
                  <span class="field__hint">Safaricom or Airtel. We send a PIN prompt to this number.</span>
                </label>

                <label class="field">
                  <span class="field__label">Amount</span>
                  <input class="field__input" type="text" value="<?= e(money($outstanding)) ?>" readonly
                         style="background:var(--bone-deep);cursor:not-allowed">
                </label>
              </div>

              <button class="btn btn--lg" type="submit" style="margin-top:1.35rem">
                Send M-Pesa Prompt
              </button>
            </form>
          </section>
        <?php endif; ?>

        <!-- Bank transfer -->
        <?php if (trim($bankDetails) !== ''): ?>
          <section class="pay-method">
            <header><h2>Bank transfer</h2></header>
            <div class="pay-method__body"><?= nl2br(e($bankDetails)) ?></div>
            <p class="field__hint">
              Quote <strong><?= e($order['reference']) ?></strong> as the reference, then email
              the confirmation to <?= e(setting('contact_email')) ?>.
            </p>
          </section>
        <?php endif; ?>

        <!-- Cash -->
        <section class="pay-method">
          <header><h2>Pay on collection</h2></header>
          <div class="pay-method__body">
            Collect and pay at <?= e(setting('contact_address')) ?>.<br>
            Open <?= e(setting('contact_hours')) ?>.
          </div>
          <p class="field__hint">
            We will hold this order for seven days. Call <?= e(setting('contact_phone')) ?> if you need longer.
          </p>
        </section>

        <?php if ($payments !== []): ?>
          <section class="pay-method">
            <header><h2>Payment attempts</h2></header>
            <ul class="record-list">
              <?php foreach ($payments as $payment): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e(Payment::METHODS[$payment['method']] ?? $payment['method']) ?></strong>
                      <small>
                        <?= e(pretty_date($payment['created_at'], true)) ?>
                        <?php if ($payment['mpesa_receipt']): ?> &middot; <?= e($payment['mpesa_receipt']) ?><?php endif; ?>
                        <?php if ($payment['result_desc'] && $payment['status'] !== 'success'): ?>
                          <br><?= e($payment['result_desc']) ?>
                        <?php endif; ?>
                      </small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= $payment['status'] === 'success' ? 'paid' : ($payment['status'] === 'pending' ? 'new' : 'unpaid') ?>">
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

      <!-- Summary -->
      <aside class="quote-aside">
        <div class="quote-summary">
          <h3>Order summary</h3>

          <div class="mini-list" style="margin-top:1.35rem">
            <?php foreach ($items as $item): ?>
              <div class="mini-item">
                <div>
                  <div class="mini-item__title"><?= e($item['product_name']) ?></div>
                  <div class="mini-item__meta">
                    <?= e(money($item['unit_price'])) ?> each
                    <?php if ($item['variant']): ?><br><?= e($item['variant']) ?><?php endif; ?>
                  </div>
                </div>
                <span class="mini-item__qty">&times;<?= (int) $item['quantity'] ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <dl style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
            <div><dt>Subtotal</dt><dd><?= e(money($order['subtotal'])) ?></dd></div>
            <div><dt>Delivery</dt><dd><?= (float) $order['delivery_cost'] > 0 ? e(money($order['delivery_cost'])) : 'Free' ?></dd></div>
            <?php if ((float) $order['amount_paid'] > 0): ?>
              <div><dt>Already paid</dt><dd>&minus;<?= e(money($order['amount_paid'])) ?></dd></div>
            <?php endif; ?>
            <div style="padding-top:.7rem;margin-top:.35rem;border-top:1px solid var(--rule-soft)">
              <dt style="color:var(--text);font-weight:600">To pay</dt>
              <dd style="font-family:var(--display);font-size:1.35rem"><?= e(money($outstanding)) ?></dd>
            </div>
          </dl>

          <p class="quote-summary__note">
            <?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?>.
            A confirmation has been emailed to <?= e($order['email']) ?>.
          </p>
        </div>
      </aside>
    </div>
  </div>
</section>
