<?php
/**
 * Product card.
 * Expects: $product (row from Product::catalog / featured / related)
 * Optional: $cardFallback (placeholder key), $cardReveal (bool)
 */
$fallback  = $cardFallback ?? 'product';
$showPrice = (int) ($product['price_visible'] ?? 0) === 1 && $product['price'] !== null;
$reveal    = $cardReveal ?? true;
?>
<article class="card"<?= $reveal ? ' data-reveal' : '' ?>>
  <div class="card__media">
    <img src="<?= e(image($product['primary_image'] ?? null, $fallback)) ?>"
         alt="<?= e($product['name']) ?>" loading="lazy" width="480" height="600">

    <?php if (!empty($product['is_new']) || ($product['stock_status'] ?? '') === 'out_of_stock' || !empty($product['is_featured'])): ?>
      <div class="card__flags">
        <?php if (!empty($product['is_new'])): ?><span class="flag flag--new">New</span><?php endif; ?>
        <?php if (!empty($product['is_featured'])): ?><span class="flag">Signature</span><?php endif; ?>
        <?php if (($product['stock_status'] ?? '') === 'out_of_stock'): ?>
          <span class="flag flag--stock">On request</span>
        <?php endif; ?>
      </div>
    <?php endif; ?>

    <div class="card__quick">
      <form action="<?= e(url('/quote/add')) ?>" method="post" data-quote-add>
        <?= csrf_field() ?>
        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
        <input type="hidden" name="quantity" value="1">
        <button class="btn btn--sm" type="submit">Add to Quote</button>
      </form>
    </div>
  </div>

  <div class="card__body">
    <?php if (!empty($product['category_name'])): ?>
      <span class="card__cat"><?= e($product['category_name']) ?></span>
    <?php endif; ?>

    <h3 class="card__title">
      <a href="<?= e(url('/product/' . $product['slug'])) ?>"><?= e($product['name']) ?></a>
    </h3>

    <?php if (!empty($product['short_desc'])): ?>
      <p class="card__desc"><?= excerpt($product['short_desc'], 92) ?></p>
    <?php endif; ?>

    <div class="card__foot">
      <?php if ($showPrice): ?>
        <span class="card__price"><?= e(money($product['price'])) ?></span>
      <?php else: ?>
        <span class="card__enquire">Price on request</span>
      <?php endif; ?>

      <span class="stock-dot stock-dot--<?= e($product['stock_status'] ?? 'in_stock') ?>">
        <?= e(stock_label($product['stock_status'] ?? 'in_stock')) ?>
      </span>
    </div>
  </div>
</article>
