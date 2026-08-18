<?php
/** @var array $items */
$unitCount = 0;
foreach ($items as $row) {
    $unitCount += $row['quantity'];
}
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li>Quote list</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Step 1 of 2</p>
    <h1>Your quote list.</h1>
    <p class="lede">
      Nothing here is an order. Review the items, adjust the quantities, then send it across
      and we will come back with pricing, availability and lead times.
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">

    <?php if ($items === []): ?>
      <div class="empty">
        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21.5 8H7"
                stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="10.5" cy="20" r="1.2" fill="currentColor"/>
          <circle cx="18" cy="20" r="1.2" fill="currentColor"/>
        </svg>
        <h3>Your quote list is empty.</h3>
        <p>Browse the catalog and add anything you would like us to price. You can add as much or as little as you like.</p>
        <a class="btn" href="<?= e(url('/shop')) ?>">Explore the catalog</a>
      </div>

    <?php else: ?>
      <div class="quote-layout">
        <div>
          <table class="quote-table">
            <thead>
              <tr>
                <th scope="col">Item</th>
                <th scope="col">Quantity</th>
                <th scope="col"><span class="sr-only">Remove</span></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($items as $row): ?>
                <?php $p = $row['product']; ?>
                <tr>
                  <td>
                    <div class="quote-item">
                      <div class="quote-item__media">
                        <img src="<?= e(image($p['primary_image'] ?? null)) ?>" alt="" loading="lazy" width="110" height="138">
                      </div>
                      <div>
                        <div class="quote-item__title">
                          <a href="<?= e(url('/product/' . $p['slug'])) ?>"><?= e($p['name']) ?></a>
                        </div>
                        <div class="quote-item__meta">
                          <?= e($p['category_name'] ?? '') ?>
                          <?php if (!empty($p['sku'])): ?> &middot; <?= e($p['sku']) ?><?php endif; ?>
                          <?php if ($row['variant'] !== ''): ?><br><?= e($row['variant']) ?><?php endif; ?>
                        </div>
                        <?php if (App\Core\QuoteList::isBuyable($p)): ?>
                          <div class="quote-item__price">
                            <strong><?= e(money($p['price'])) ?></strong>
                            <span class="tag-buy">Buy now</span>
                          </div>
                        <?php else: ?>
                          <div class="quote-item__price">
                            <span class="tag-quote">Priced on request</span>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>
                  </td>

                  <td>
                    <form action="<?= e(url('/quote/update')) ?>" method="post" data-qty-form>
                      <?= csrf_field() ?>
                      <input type="hidden" name="key" value="<?= e($row['key']) ?>">
                      <div class="qty">
                        <button type="button" data-qty="down" aria-label="Decrease quantity">&minus;</button>
                        <label class="sr-only" for="q-<?= e($row['key']) ?>">Quantity for <?= e($p['name']) ?></label>
                        <input type="number" id="q-<?= e($row['key']) ?>" name="quantity"
                               value="<?= (int) $row['quantity'] ?>" min="1" max="999" inputmode="numeric">
                        <button type="button" data-qty="up" aria-label="Increase quantity">+</button>
                      </div>
                      <noscript><button class="btn btn--sm" type="submit" style="margin-top:.5rem">Update</button></noscript>
                    </form>
                  </td>

                  <td>
                    <form action="<?= e(url('/quote/remove')) ?>" method="post">
                      <?= csrf_field() ?>
                      <input type="hidden" name="key" value="<?= e($row['key']) ?>">
                      <button class="icon-remove" type="submit" aria-label="Remove <?= e($p['name']) ?>">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                          <path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
                        </svg>
                      </button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <div style="display:flex;flex-wrap:wrap;gap:1rem;justify-content:space-between;margin-top:2rem">
            <a class="link" href="<?= e(url('/shop')) ?>">
              <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                <path d="M5 1L1 5l4 4M1 5h12" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
              Continue browsing
            </a>

            <form action="<?= e(url('/quote/clear')) ?>" method="post"
                  onsubmit="return confirm('Clear every item from your quote list?');">
              <?= csrf_field() ?>
              <button class="link" type="submit" style="border:0;background:none;cursor:pointer;color:var(--text-faint)">
                Clear the list
              </button>
            </form>
          </div>
        </div>

        <aside class="quote-aside">
          <?php if ($buyable !== []): ?>
            <div class="quote-summary quote-summary--buy">
              <h3>Buy now</h3>
              <p class="muted" style="margin-top:.5rem;font-size:.875rem">
                <?= count($buyable) ?> item<?= count($buyable) === 1 ? '' : 's' ?> on your list
                <?= count($buyable) === 1 ? 'carries' : 'carry' ?> a listed price, so you can
                pay for <?= count($buyable) === 1 ? 'it' : 'them' ?> straight away.
              </p>

              <dl style="margin-top:1.25rem">
                <div>
                  <dt>Subtotal</dt>
                  <dd style="font-family:var(--display);font-size:1.3rem"><?= e(money($subtotal)) ?></dd>
                </div>
              </dl>

              <a class="btn btn--tan btn--block" href="<?= e(url('/checkout')) ?>" style="margin-top:1.25rem">
                Checkout &amp; Pay
              </a>
              <p class="quote-summary__note">Delivery is costed at the next step.</p>
            </div>
          <?php endif; ?>

          <div class="quote-summary" <?= $buyable !== [] ? 'style="margin-top:1.25rem"' : '' ?>>
            <h3><?= $buyable !== [] ? 'Request a quote' : 'Summary' ?></h3>

            <?php if ($buyable !== [] && $quoteOnly !== []): ?>
              <p class="muted" style="margin-top:.5rem;font-size:.875rem">
                <?= count($quoteOnly) ?> item<?= count($quoteOnly) === 1 ? '' : 's' ?> need
                <?= count($quoteOnly) === 1 ? 's' : '' ?> pricing by hand — saddles,
                made-to-order work and anything requiring a fitting.
              </p>
            <?php endif; ?>

            <dl <?= $buyable !== [] ? 'style="margin-top:1.25rem"' : '' ?>>
              <div>
                <dt>Distinct items</dt>
                <dd><?= count($items) ?></dd>
              </div>
              <div>
                <dt>Total units</dt>
                <dd><?= (int) $unitCount ?></dd>
              </div>
              <div>
                <dt>Estimated response</dt>
                <dd>1 working day</dd>
              </div>
            </dl>

            <a class="btn <?= $buyable !== [] ? 'btn--ghost' : '' ?> btn--block"
               href="<?= e(url('/request-a-quote')) ?>" style="margin-top:1.75rem">
              <?= $buyable !== [] && $quoteOnly === [] ? 'Request a Quote Instead' : 'Continue to Request' ?>
            </a>

            <p class="quote-summary__note">
              We will confirm current pricing in Kenyan Shillings, availability or lead time,
              and delivery cost to your location. Nothing is reserved or charged until you
              accept the quote.
            </p>
          </div>
        </aside>
      </div>
    <?php endif; ?>
  </div>
</section>
