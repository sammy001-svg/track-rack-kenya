<?php
/** @var array $product @var array $images @var array $variants @var array $related */
$showPrice = (int) $product['price_visible'] === 1 && $product['price'] !== null;
$art       = $pillar['slug'] ?? 'product';
$art       = in_array($art, ['rider', 'horse', 'stable'], true) ? $art : 'product';
$mainImage = $images[0]['path'] ?? null;

// Variants grouped by their label ("Size", "Colour", ...)
$variantGroups = [];
foreach ($variants as $variant) {
    $variantGroups[$variant['label']][] = $variant['value'];
}
?>

<div class="shell shell--wide" style="padding-top:calc(var(--header-h) + 2rem)">
  <ul class="crumbs">
    <li><a href="<?= e(url('/')) ?>">Home</a></li>
    <li><a href="<?= e(url('/shop')) ?>">Catalog</a></li>
    <?php if ($pillar !== null): ?>
      <li><a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><?= e($pillar['name']) ?></a></li>
    <?php endif; ?>
    <?php if (!empty($product['category_slug'])): ?>
      <li><a href="<?= e(url('/shop/' . $product['category_slug'])) ?>"><?= e($product['category_name']) ?></a></li>
    <?php endif; ?>
  </ul>
</div>

<section class="shell shell--wide">
  <div class="product">

    <!-- Gallery -->
    <?php $imageCount = count($images); ?>
    <div class="gallery" id="gallery" data-count="<?= $imageCount ?>">
      <div class="gallery__main" id="gallery-main">
        <?= picture(
            image($mainImage, $art),
            $images[0]['alt'] ?? $product['name'],
            ['width' => 900, 'height' => 1125, 'fetchpriority' => 'high', 'decoding' => 'async']
        ) ?>

        <?php if ($imageCount > 1): ?>
          <button class="gallery__arrow gallery__arrow--prev" type="button" id="gallery-prev" aria-label="Previous image">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M15 5l-7 7 7 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
          <button class="gallery__arrow gallery__arrow--next" type="button" id="gallery-next" aria-label="Next image">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
              <path d="M9 5l7 7-7 7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        <?php endif; ?>

        <?php /* What this particular photograph shows, written per image. */ ?>
        <figcaption class="gallery__caption" id="gallery-caption" aria-live="polite">
          <?php if ($imageCount > 1): ?>
            <span class="gallery__count" id="gallery-count">1 / <?= $imageCount ?></span>
          <?php endif; ?>
          <span class="gallery__desc" id="gallery-desc"><?= e($images[0]['alt'] ?? $product['name']) ?></span>
        </figcaption>
      </div>

      <?php if ($imageCount > 1): ?>
        <div class="gallery__thumbs" id="gallery-thumbs">
          <?php foreach ($images as $index => $img): ?>
            <?php
              $full     = image($img['path'], $art);
              $fullDisk = local_path($full);
              $fullWebp = $fullDisk !== null && is_file(preg_replace('/\.[a-z0-9]+$/i', '', $fullDisk) . '.webp')
                  ? preg_replace('/\.[a-z0-9]+$/i', '', $full) . '.webp'
                  : '';
              $caption  = $img['alt'] ?: $product['name'];
            ?>
            <button class="gallery__thumb <?= $index === 0 ? 'is-active' : '' ?>" type="button"
                    data-index="<?= $index ?>"
                    data-full="<?= e($full) ?>"
                    data-full-webp="<?= e($fullWebp) ?>"
                    data-alt="<?= e($caption) ?>"
                    title="<?= e($caption) ?>"
                    aria-label="<?= e(sprintf('Image %d of %d — %s', $index + 1, $imageCount, $caption)) ?>">
              <?= picture($full, '', ['loading' => 'lazy', 'width' => 120, 'height' => 120]) ?>
            </button>
          <?php endforeach; ?>
        </div>

        <p class="gallery__hint">
          <?= $imageCount ?> photographs — use the arrows, swipe, or the arrow keys to move through them.
        </p>
      <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="product__info">
      <?php if (!empty($product['category_name'])): ?>
        <p class="eyebrow"><?= e($product['category_name']) ?></p>
      <?php endif; ?>

      <h1 class="product__title"><?= e($product['name']) ?></h1>

      <?php if (!empty($product['brand_name'])): ?>
        <p class="product__brand">
          By <?= e($product['brand_name']) ?>
          <?php if (!empty($product['sku'])): ?>
            &nbsp;&middot;&nbsp; SKU <?= e($product['sku']) ?>
          <?php endif; ?>
        </p>
      <?php endif; ?>

      <div class="product__price-row">
        <?php if ($showPrice): ?>
          <span class="product__price"><?= e(money($product['price'])) ?></span>
        <?php else: ?>
          <span class="product__price--quote">
            Priced on request — add this to your quote list and we will confirm
            current pricing, availability and lead time.
          </span>
        <?php endif; ?>

        <span class="stock-dot stock-dot--<?= e($product['stock_status']) ?>">
          <?= e(stock_label($product['stock_status'])) ?>
        </span>
      </div>

      <?php if (!empty($product['short_desc'])): ?>
        <p class="product__lede"><?= e($product['short_desc']) ?></p>
      <?php endif; ?>

      <!-- Add to quote -->
      <form class="product__form" action="<?= e(url('/quote/add')) ?>" method="post" data-quote-add>
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">

        <?php if ($variantGroups !== []): ?>
          <?php foreach ($variantGroups as $label => $values): ?>
            <label class="field">
              <span class="field__label"><?= e($label) ?></span>
              <select class="field__select" name="variant">
                <option value="">Select <?= e(strtolower($label)) ?> — or ask us to advise</option>
                <?php foreach ($values as $value): ?>
                  <option value="<?= e($label . ': ' . $value) ?>"><?= e($value) ?></option>
                <?php endforeach; ?>
              </select>
            </label>
            <?php break; // one selector; further specifics go in the notes field ?>
          <?php endforeach; ?>
        <?php endif; ?>

        <div class="qty-row">
          <div class="qty">
            <button type="button" data-qty="down" aria-label="Decrease quantity">&minus;</button>
            <label class="sr-only" for="qty">Quantity</label>
            <input type="number" id="qty" name="quantity" value="1" min="1" max="999" inputmode="numeric">
            <button type="button" data-qty="up" aria-label="Increase quantity">+</button>
          </div>
          <button class="btn btn--lg" type="submit">Add to Quote List</button>
        </div>
      </form>

      <div class="product__aside">
        <span>No payment taken &middot; No obligation</span>
        <?php $wa = whatsapp_link('Hello Tack Rack, I would like to ask about the ' . $product['name'] . '.'); ?>
        <?php if ($wa !== ''): ?>
          <a href="<?= e($wa) ?>" target="_blank" rel="noopener">Ask about this on WhatsApp</a>
        <?php endif; ?>
      </div>

      <!-- Detail accordion -->
      <div class="accordion">
        <?php if (!empty($product['description'])): ?>
          <div class="accordion__item is-open">
            <button class="accordion__head" type="button" aria-expanded="true">
              Description <span class="accordion__icon" aria-hidden="true"></span>
            </button>
            <div class="accordion__panel"><div>
              <div class="accordion__body"><?= nl2br(e($product['description'])) ?></div>
            </div></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['specifications'])): ?>
          <div class="accordion__item">
            <button class="accordion__head" type="button" aria-expanded="false">
              Specifications <span class="accordion__icon" aria-hidden="true"></span>
            </button>
            <div class="accordion__panel"><div>
              <div class="accordion__body"><?= e($product['specifications']) ?></div>
            </div></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($product['sizing_guide'])): ?>
          <div class="accordion__item">
            <button class="accordion__head" type="button" aria-expanded="false">
              Sizing &amp; Fitting <span class="accordion__icon" aria-hidden="true"></span>
            </button>
            <div class="accordion__panel"><div>
              <div class="accordion__body"><?= e($product['sizing_guide']) ?></div>
            </div></div>
          </div>
        <?php endif; ?>

        <div class="accordion__item">
          <button class="accordion__head" type="button" aria-expanded="false">
            Delivery &amp; Collection <span class="accordion__icon" aria-hidden="true"></span>
          </button>
          <div class="accordion__panel"><div>
            <div class="accordion__body">Collect from <?= e(setting('contact_address')) ?>, or have it delivered across Nairobi. We dispatch countrywide by courier — delivery cost is confirmed in your quote before you commit.

Opening hours: <?= e(setting('contact_hours')) ?></div>
          </div></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Related -->
<?php if ($related !== []): ?>
<section class="section bg-paper" style="margin-top:clamp(3.5rem,7vw,6rem)">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">Also in <?= e($product['category_name'] ?? 'this range') ?></p>
        <h2>Pairs well with.</h2>
      </div>
      <?php if (!empty($product['category_slug'])): ?>
        <a class="link" href="<?= e(url('/shop/' . $product['category_slug'])) ?>">
          View the category
          <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
            <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      <?php endif; ?>
    </div>

    <div class="grid-products grid-products--4">
      <?php $cardFallback = $art; ?>
      <?php foreach ($related as $product): ?>
        <?php require APP_PATH . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
