<?php
use App\Models\Order;
/** @var array $items @var array $quoteItems @var float $subtotal @var array|null $customer @var array $errors */
$err = static fn (string $f): string => $errors[$f] ?? '';
$pre = static function (string $field, string $fallback = '') use ($customer) {
    $old = old($field, null);
    if ($old !== null && $old !== '') {
        return $old;
    }
    return $fallback !== '' ? ($customer[$fallback] ?? '') : '';
};
$freeOver = (float) setting('free_delivery_over', '0');
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/quote')) ?>">Your list</a></li>
      <li>Checkout</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Buy now</p>
    <h1>Checkout.</h1>
    <p class="lede">
      These items carry a listed price, so you can pay for them directly.
      <?php if ($quoteItems !== []): ?>
        The rest of your list stays put for a quote request.
      <?php endif; ?>
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="quote-layout">

      <form method="post" action="<?= e(url('/checkout')) ?>" novalidate id="checkout-form"
            data-free-over="<?= e($freeOver) ?>"
            data-subtotal="<?= e($subtotal) ?>"
            data-nairobi="<?= e(setting('delivery_nairobi', '0')) ?>"
            data-courier="<?= e(setting('delivery_courier', '0')) ?>">
        <?= csrf_field() ?>
        <div class="honeypot" aria-hidden="true">
          <label>Leave empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label>
        </div>

        <?php if ($customer === null): ?>
          <div class="checkout-signin">
            <p>
              <strong>Have an account?</strong>
              <a href="<?= e(url('/account/login')) ?>">Sign in</a> to fill this in automatically
              and keep the order on your record. Or just carry on as a guest.
            </p>
          </div>
        <?php endif; ?>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>01</i> Your details</legend>

          <div class="form-grid form-grid--2">
            <label class="field <?= $err('name') ? 'has-error' : '' ?>">
              <span class="field__label">Full name <span class="req">*</span></span>
              <input class="field__input" type="text" name="name" value="<?= e($pre('name', 'name')) ?>" required maxlength="150" autocomplete="name">
              <?php if ($err('name')): ?><span class="field__error"><?= e($err('name')) ?></span><?php endif; ?>
            </label>

            <label class="field <?= $err('email') ? 'has-error' : '' ?>">
              <span class="field__label">Email <span class="req">*</span></span>
              <input class="field__input" type="email" name="email" value="<?= e($pre('email', 'email')) ?>" required maxlength="190" autocomplete="email">
              <?php if ($err('email')): ?><span class="field__error"><?= e($err('email')) ?></span><?php endif; ?>
            </label>

            <label class="field field--full <?= $err('phone') ? 'has-error' : '' ?>">
              <span class="field__label">Phone / M-Pesa number <span class="req">*</span></span>
              <input class="field__input" type="tel" name="phone" value="<?= e($pre('phone', 'phone')) ?>" required maxlength="60" placeholder="0722 123 456" autocomplete="tel">
              <span class="field__hint">We send the M-Pesa prompt to this number.</span>
              <?php if ($err('phone')): ?><span class="field__error"><?= e($err('phone')) ?></span><?php endif; ?>
            </label>
          </div>
        </fieldset>

        <fieldset class="fieldset" style="border:0;padding:0;margin:0">
          <legend class="fieldset-title"><i>02</i> Delivery</legend>

          <div class="delivery-options">
            <?php foreach (Order::DELIVERY_METHODS as $value => $label): ?>
              <?php
                $cost = $value === 'collect' ? 0.0
                    : ($freeOver > 0 && $subtotal >= $freeOver ? 0.0
                    : (float) setting($value === 'nairobi' ? 'delivery_nairobi' : 'delivery_courier', '0'));
                $checked = (old('delivery_method') ?: 'collect') === $value;
              ?>
              <label class="delivery-option">
                <input type="radio" name="delivery_method" value="<?= e($value) ?>" <?= $checked ? 'checked' : '' ?>>
                <span class="delivery-option__body">
                  <strong><?= e($label) ?></strong>
                  <small>
                    <?php if ($value === 'collect'): ?>
                      <?= e(setting('contact_address')) ?> &middot; <?= e(setting('contact_hours')) ?>
                    <?php elseif ($value === 'nairobi'): ?>
                      Usually next working day
                    <?php else: ?>
                      Courier to any town in Kenya
                    <?php endif; ?>
                  </small>
                </span>
                <span class="delivery-option__cost"><?= $cost > 0 ? e(money($cost)) : 'Free' ?></span>
              </label>
            <?php endforeach; ?>
          </div>

          <div class="form-grid form-grid--2" id="delivery-fields" hidden style="margin-top:1.35rem">
            <label class="field field--full <?= $err('delivery_address') ? 'has-error' : '' ?>">
              <span class="field__label">Delivery address</span>
              <textarea class="field__textarea" name="delivery_address" rows="3"
                        placeholder="Estate, road, house or gate number, any landmark."><?= e(old('delivery_address')) ?></textarea>
              <?php if ($err('delivery_address')): ?><span class="field__error"><?= e($err('delivery_address')) ?></span><?php endif; ?>
            </label>

            <label class="field field--full <?= $err('delivery_town') ? 'has-error' : '' ?>">
              <span class="field__label">Town or area</span>
              <input class="field__input" type="text" name="delivery_town" value="<?= e(old('delivery_town')) ?>" maxlength="120" placeholder="Karen, Nairobi">
              <?php if ($err('delivery_town')): ?><span class="field__error"><?= e($err('delivery_town')) ?></span><?php endif; ?>
            </label>
          </div>

          <label class="field" style="margin-top:1.35rem">
            <span class="field__label">Order notes</span>
            <textarea class="field__textarea" name="notes" rows="3" maxlength="2000"
                      placeholder="Anything we should know about sizing, timing or delivery."><?= e(old('notes')) ?></textarea>
          </label>
        </fieldset>

        <div style="margin-top:2.25rem;display:flex;flex-wrap:wrap;gap:1rem;align-items:center">
          <button class="btn btn--lg" type="submit">Place Order</button>
          <span class="field__hint" style="margin:0">Payment on the next step. Nothing is charged yet.</span>
        </div>
      </form>

      <!-- Summary -->
      <aside class="quote-aside">
        <div class="quote-summary">
          <h3>Your basket</h3>

          <div class="mini-list" style="margin-top:1.35rem">
            <?php foreach ($items as $item): ?>
              <?php $p = $item['product']; ?>
              <div class="mini-item">
                <div class="mini-item__media">
                  <img src="<?= e(image($p['primary_image'] ?? null)) ?>" alt="" loading="lazy" width="70" height="70">
                </div>
                <div>
                  <div class="mini-item__title"><?= e($p['name']) ?></div>
                  <div class="mini-item__meta">
                    <?= e(money($p['price'])) ?> each
                    <?php if ($item['variant'] !== ''): ?><br><?= e($item['variant']) ?><?php endif; ?>
                  </div>
                </div>
                <span class="mini-item__qty">&times;<?= (int) $item['quantity'] ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <dl style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
            <div><dt>Subtotal</dt><dd><?= e(money($subtotal)) ?></dd></div>
            <div><dt>Delivery</dt><dd id="summary-delivery">Free</dd></div>
            <div style="padding-top:.7rem;margin-top:.35rem;border-top:1px solid var(--rule-soft);font-size:1.05rem">
              <dt style="color:var(--text);font-weight:600">Total</dt>
              <dd id="summary-total" style="font-family:var(--display);font-size:1.35rem"><?= e(money($subtotal)) ?></dd>
            </div>
          </dl>

          <?php if ($freeOver > 0 && $subtotal < $freeOver): ?>
            <p class="quote-summary__note">
              Spend <?= e(money($freeOver - $subtotal)) ?> more for free delivery.
            </p>
          <?php endif; ?>
        </div>

        <?php if ($quoteItems !== []): ?>
          <div class="quote-summary" style="margin-top:1.25rem">
            <h3>Still needs a quote</h3>
            <p class="muted" style="margin-top:.6rem;font-size:.875rem">
              <?= count($quoteItems) ?> item<?= count($quoteItems) === 1 ? '' : 's' ?> on your list
              <?= count($quoteItems) === 1 ? 'is' : 'are' ?> priced individually — saddles, made-to-order
              work and anything needing a fitting.
            </p>
            <a class="btn btn--ghost btn--block btn--sm" href="<?= e(url('/request-a-quote')) ?>" style="margin-top:1rem">
              Send those for a quote
            </a>
          </div>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>
