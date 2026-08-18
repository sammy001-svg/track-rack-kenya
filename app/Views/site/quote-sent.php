<?php /** @var array $quote @var array $items */ ?>

<section class="section" style="padding-top:calc(var(--header-h) + clamp(3.5rem,8vw,6rem))">
  <div class="shell shell--narrow">

    <div style="text-align:center" data-reveal>
      <svg width="56" height="56" viewBox="0 0 24 24" fill="none" style="margin-inline:auto;color:var(--tan)" aria-hidden="true">
        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.1" opacity=".35"/>
        <path d="M8 12.4l2.6 2.6L16 9.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>

      <p class="eyebrow eyebrow--center eyebrow--plain" style="margin-top:1.75rem">Request received</p>
      <h1 style="font-weight:300;margin-top:.9rem">Thank you, <?= e(explode(' ', $quote['customer_name'])[0]) ?>.</h1>

      <p class="lede" style="margin:1.5rem auto 0">
        Your request is with our team. We will come back with pricing, availability and
        delivery — usually within one working day.
      </p>

      <div style="display:inline-flex;align-items:center;gap:.85rem;margin-top:2.25rem;padding:.9rem 1.5rem;border:1px solid var(--rule);border-radius:999px;background:var(--paper)">
        <span style="font-size:.66rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--text-faint)">Reference</span>
        <strong style="font-family:var(--display);font-size:1.15rem;letter-spacing:.02em"><?= e($quote['reference']) ?></strong>
      </div>

      <p class="field__hint" style="margin-top:1rem">
        Quote a reference number if you call or message us about this request.
      </p>
    </div>

    <div class="quote-summary" style="margin-top:clamp(2.5rem,5vw,3.5rem)" data-reveal>
      <h3>What you asked us to price</h3>

      <div class="mini-list" style="margin-top:1.35rem">
        <?php foreach ($items as $item): ?>
          <div class="mini-item">
            <div>
              <div class="mini-item__title"><?= e($item['product_name']) ?></div>
              <div class="mini-item__meta">
                <?= e($item['product_sku'] ?? '') ?><?php if (!empty($item['variant'])): ?> &middot; <?= e($item['variant']) ?><?php endif; ?>
              </div>
            </div>
            <span class="mini-item__qty">&times;<?= (int) $item['quantity'] ?></span>
          </div>
        <?php endforeach; ?>
      </div>

      <dl style="margin-top:1.5rem;padding-top:1.25rem;border-top:1px solid var(--rule-soft)">
        <div><dt>Sent</dt><dd style="font-weight:500"><?= e(pretty_date($quote['created_at'], true)) ?></dd></div>
        <div><dt>Reply to</dt><dd style="font-weight:500"><?= e($quote['email']) ?></dd></div>
      </dl>
    </div>

    <div style="display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.5rem" data-reveal>
      <a class="btn" href="<?= e(url('/shop')) ?>">Keep browsing</a>
      <?php $wa = whatsapp_link('Hello Tack Rack, I have just sent quote request ' . $quote['reference'] . '.'); ?>
      <?php if ($wa !== ''): ?>
        <a class="btn btn--ghost" href="<?= e($wa) ?>" target="_blank" rel="noopener">Follow up on WhatsApp</a>
      <?php endif; ?>
    </div>
  </div>
</section>
