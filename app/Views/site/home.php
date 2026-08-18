<?php
/** @var array $pillars @var array $featured @var array $latest @var array $brands */
$spotlight = array_slice($featured, 0, 4);
$grid      = array_slice($featured, 0, 8);
if (count($grid) < 4) {
    $grid = array_slice(array_merge($grid, $latest), 0, 8);
}
$pillarArt = ['rider' => 'rider', 'horse' => 'horse', 'stable' => 'stable'];
?>

<!-- ================================================================
     Section 1 — Hero Banner
     ================================================================ -->
<section class="hero">
  <div class="hero__copy">
    <p class="eyebrow">Nairobi, Kenya &middot; Since <?= e(setting('founded_year', '1997')) ?></p>

    <h1>Premium Equestrian&nbsp;Gear.<br><em>Trusted Heritage.</em></h1>

    <p class="hero__lede">
      Saddlery, rider apparel and yard essentials for every discipline ridden in Kenya —
      selected, fitted and maintained by people who ride.
    </p>

    <div class="hero__actions">
      <a class="btn btn--light" href="<?= e(url('/shop')) ?>">Explore the Catalog</a>
      <a class="btn btn--outline-light" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
    </div>

    <dl class="hero__meta">
      <div class="hero__stat">
        <dt>Serving riders since</dt>
        <dd><?= e(setting('founded_year', '1997')) ?></dd>
      </div>
      <div class="hero__stat">
        <dt>Saddle fitting</dt>
        <dd>SMS Qualified</dd>
      </div>
      <div class="hero__stat">
        <dt>On-site workshop</dt>
        <dd>Repairs &amp; Bespoke</dd>
      </div>
    </dl>
  </div>

  <div class="hero__visual">
    <?= picture(
        asset('/assets/img/hero-leather.jpg'),
        'Close detail of quilted leather with hand-run saddle stitching',
        ['width' => 1100, 'height' => 1375, 'fetchpriority' => 'high', 'decoding' => 'async']
    ) ?>
  </div>

  <div class="hero__scroll" aria-hidden="true">
    <span>Scroll</span>
    <i></i>
  </div>
</section>

<!-- Marquee -->
<div class="marquee" aria-hidden="true">
  <div class="marquee__track">
    <span>Racing</span><span>Polo</span><span>Showjumping</span><span>Dressage</span>
    <span>Eventing</span><span>Hacking</span><span>Safari Riding</span><span>Pony Club</span>
  </div>
</div>

<!-- ================================================================
     Section 2 — The Curation
     ================================================================ -->
<section class="section" id="curation">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">The Curation</p>
        <h2>Three pillars, one standard.</h2>
        <p class="lede">
          Our catalog is organised the way a yard actually works — what the rider wears,
          what the horse carries, and what keeps both in condition.
        </p>
      </div>
      <a class="link" href="<?= e(url('/shop')) ?>">
        Browse everything
        <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
          <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>
  </div>

  <div class="pillars">
    <?php foreach ($pillars as $index => $pillar): ?>
      <?php
        $art = $pillarArt[$pillar['slug']] ?? null;
        $pillarImage = !empty($pillar['image'])
            ? image($pillar['image'])
            : asset('/assets/img/' . ($art !== null ? 'pillar-' . $art : 'placeholder-product') . '.jpg');
        $pillarAlt = [
            'rider'  => 'A showjumping rider clearing a fence',
            'horse'  => 'An English saddle fitted with a sheepskin numnah',
            'stable' => 'Grooming a horse with a body brush',
        ][$pillar['slug']] ?? $pillar['name'];
      ?>
      <article class="pillar" data-reveal>
        <div class="pillar__media">
          <?= picture($pillarImage, $pillarAlt, ['loading' => 'lazy', 'width' => 800, 'height' => 1000]) ?>
        </div>

        <span class="pillar__index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>

        <span class="pillar__tag"><?= e($pillar['tagline']) ?></span>
        <h3><?= e($pillar['name']) ?></h3>
        <p class="pillar__desc"><?= e($pillar['description']) ?></p>

        <?php if (!empty($pillar['children'])): ?>
          <div class="pillar__links">
            <?php foreach (array_slice($pillar['children'], 0, 5) as $child): ?>
              <a href="<?= e(url('/shop/' . $child['slug'])) ?>"><?= e($child['name']) ?></a>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <a class="pillar__cta" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
          <span class="sr-only">Shop <?= e($pillar['name']) ?></span>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<!-- ================================================================
     Section 3 — Hero Product Spotlight
     ================================================================ -->
<?php if ($spotlight !== []): ?>
<section class="spotlight">
  <div class="shell shell--wide">
    <div class="spotlight__inner">
      <div data-reveal>
        <p class="eyebrow">The Spotlight</p>
        <h2>What our riders come back for.</h2>
        <p>
          The pieces that move fastest off our shelves — chosen because they hold up
          to Kenyan conditions, not because they photograph well. Several are made
          in our own Nairobi workshop.
        </p>
        <div class="spotlight__actions">
          <a class="btn btn--light" href="<?= e(url('/shop')) ?>">See the full range</a>
          <a class="btn btn--outline-light" href="<?= e(url('/heritage')) ?>">Why it matters</a>
        </div>
      </div>

      <div class="spotlight__grid" data-reveal>
        <?php foreach ($spotlight as $item): ?>
          <article class="spot-card">
            <div class="spot-card__media">
              <?= picture(
                  image($item['primary_image'] ?? null, 'product'),
                  $item['name'],
                  ['loading' => 'lazy', 'width' => 400, 'height' => 267, 'decoding' => 'async']
              ) ?>
            </div>
            <h4><a href="<?= e(url('/product/' . $item['slug'])) ?>"><?= e($item['name']) ?></a></h4>
            <span><?= e($item['category_name'] ?? 'Tack Rack') ?></span>
          </article>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     The craft strip
     ================================================================ -->
<section class="section">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">Why Tack Rack</p>
        <h2>Not just a shop.</h2>
      </div>
    </div>

    <div class="craft">
      <div class="craft__item" data-reveal>
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 2l2.6 5.6 6.1.8-4.5 4.2 1.2 6.1L12 15.8 6.6 18.7l1.2-6.1L3.3 8.4l6.1-.8L12 2z"
                stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
        </svg>
        <h4>Qualified saddle fitting</h4>
        <p>Sharon Ashley is the only saddle fitter in East Africa qualified with the Society of Master Saddlers. Every saddle is fitted on the horse.</p>
      </div>

      <div class="craft__item" data-reveal>
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M14.5 3.5l6 6-9 9H5.5v-6l9-9z" stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
          <path d="M12.5 5.5l6 6" stroke="currentColor" stroke-width="1.3"/>
        </svg>
        <h4>An in-house workshop</h4>
        <p>We repair what others replace — broken trees, torn panels, restitching — and manufacture rugs, numnahs, girths and stirrup leathers on site.</p>
      </div>

      <div class="craft__item" data-reveal>
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M12 3l7.5 3.5v5c0 4.4-3.1 8.4-7.5 9.5-4.4-1.1-7.5-5.1-7.5-9.5v-5L12 3z"
                stroke="currentColor" stroke-width="1.3" stroke-linejoin="round"/>
          <path d="M9 12l2 2 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
        <h4>Every discipline covered</h4>
        <p>Racing, polo, showjumping, dressage, hacking and safari riding — stocked and advised on by staff who ride themselves.</p>
      </div>

      <div class="craft__item" data-reveal>
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 7h13l3 4v6h-3M4 17V7m0 10h3m10 0H7" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="7" cy="17.5" r="1.8" stroke="currentColor" stroke-width="1.3"/>
          <circle cx="17" cy="17.5" r="1.8" stroke="currentColor" stroke-width="1.3"/>
        </svg>
        <h4>Nairobi &amp; countrywide</h4>
        <p>Collect from Ngong Road, have it delivered across Nairobi, or dispatched countrywide by courier — costed in your quote before you commit.</p>
      </div>
    </div>
  </div>
</section>

<!-- ================================================================
     New arrivals
     ================================================================ -->
<?php if ($grid !== []): ?>
<section class="section section--tight bg-paper">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">Recently Received</p>
        <h2>New on the rack.</h2>
      </div>
      <a class="link" href="<?= e(url('/shop?sort=newest')) ?>">
        All new arrivals
        <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
          <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>

    <div class="grid-products grid-products--4">
      <?php foreach (array_slice($grid, 0, 8) as $product): ?>
        <?php require APP_PATH . '/Views/partials/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     Brand wall
     ================================================================ -->
<?php if ($brands !== []): ?>
<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">Our Makers</p>
        <h2>Marques we stand behind.</h2>
      </div>
    </div>

    <div class="brand-wall" data-reveal>
      <?php foreach ($brands as $brand): ?>
        <div class="brand-wall__item" title="<?= e($brand['description'] ?? $brand['name']) ?>">
          <?php if (!empty($brand['logo'])): ?>
            <img src="<?= e(image($brand['logo'])) ?>" alt="<?= e($brand['name']) ?>" loading="lazy">
          <?php else: ?>
            <span><?= e($brand['name']) ?></span>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     Section 4 — Effortless Action
     ================================================================ -->
<section class="cta-banner">
  <div class="shell shell--wide">
    <div class="cta-banner__inner">
      <div data-reveal>
        <p class="eyebrow" style="color:rgba(255,255,255,.72)">Effortless Action</p>
        <h2>Tell us what you need. We will quote it.</h2>
        <p>
          Build a list from the catalog and send it across. You will have current pricing,
          availability and lead times — usually within one working day. No account, no obligation.
        </p>
      </div>

      <div class="cta-banner__actions" data-reveal>
        <a class="btn btn--lg" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
        <a class="btn btn--lg btn--outline-light" href="<?= e(url('/page/quote-process')) ?>">How it works</a>
      </div>
    </div>
  </div>
</section>
