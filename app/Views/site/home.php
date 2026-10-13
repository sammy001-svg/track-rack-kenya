<?php
/** @var array $departments @var array $services @var array $heroSlides @var array $featured @var array $latest */
$heroSlides = $heroSlides ?? [];

/*
 * Three product sections, three different sets of products.
 *
 * The hero carousel, the spotlight and the new arrivals were all fed from the
 * featured list, which is also what the carousel leads with — so the same four
 * products appeared three times on the way down the page, the third time under
 * a heading that said they had just come in. Each section now takes what the
 * ones above it have not used.
 */
$taken = static function (array $pool, array $used, int $limit, bool $spread = false): array {
    $out = [];

    // When spreading, the first pass takes at most one product per category
    // and the second fills whatever is still short. Everything was imported on
    // the same day, so "most recent" otherwise means "the last rows of the
    // file" — which is four tins of leather soap under a heading about new
    // arrivals.
    $passes = $spread ? [true, false] : [false];

    foreach ($passes as $onePerCategory) {
        foreach ($pool as $item) {
            if (count($out) >= $limit) {
                return $out;
            }

            $id = (int) $item['id'];

            if (in_array($id, $used, true) || in_array($id, array_column($out, 'id'), true)) {
                continue;
            }

            $category = (string) ($item['category_slug'] ?? '');

            if ($onePerCategory && $category !== ''
                && in_array($category, array_column($out, 'category_slug'), true)) {
                continue;
            }

            $out[] = $item;
        }
    }

    return $out;
};

$used      = array_map(static fn (array $s): int => (int) $s['id'], $heroSlides);
$spotlight = $taken($featured, $used, 4);

$used      = array_merge($used, array_map(static fn (array $s): int => (int) $s['id'], $spotlight));
$grid      = $taken($latest, $used, 4, true);

// If the catalogue is too small for three distinct sets, fall back to showing
// something rather than an empty section.
if (count($grid) < 4) {
    $grid = array_merge($grid, $taken($featured, array_merge($used, array_map(
        static fn (array $s): int => (int) $s['id'], $grid
    )), 4 - count($grid)));
}
if ($spotlight === []) {
    $spotlight = array_slice($featured, 0, 4);
}
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
      selected, fitted and maintained by specialists who know every product we sell.
    </p>

    <div class="hero__actions">
      <a class="btn btn--light" href="<?= e(url('/shop')) ?>">Explore the Catalogue</a>
      <a class="btn btn--outline-light" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
    </div>

    <dl class="hero__meta">
      <div class="hero__stat">
        <dt>Serving riders since</dt>
        <dd><?= e(setting('founded_year', '1997')) ?></dd>
      </div>
      <div class="hero__stat">
        <dt>Qualified fitting</dt>
        <dd>Master Saddlers</dd>
      </div>
      <div class="hero__stat">
        <dt>Our own workshop</dt>
        <dd>Repairs &amp; Bespoke</dd>
      </div>
    </dl>
  </div>

  <?php if ($heroSlides === []): ?>
    <div class="hero__visual">
      <?= picture(
          asset('/assets/img/hero-leather.jpg'),
          'Close detail of quilted leather with hand-run saddle stitching',
          ['width' => 1100, 'height' => 1375, 'fetchpriority' => 'high', 'decoding' => 'async']
      ) ?>
    </div>
  <?php else: ?>
    <?php $slideCount = count($heroSlides); ?>
    <div class="hero__visual hero__visual--carousel">
      <div class="hero-carousel"
           role="group"
           aria-roledescription="carousel"
           aria-label="Selected products from the catalogue"
           data-hero-carousel>

        <?php // Native scroll-snap, so this is swipeable even before the script runs. ?>
        <ul class="hero-carousel__track" data-hero-track>
          <?php foreach ($heroSlides as $i => $slide): ?>
            <?php $showPrice = (int) ($slide['price_visible'] ?? 0) === 1 && $slide['price'] !== null; ?>
            <li class="hero-carousel__slide"
                role="group"
                aria-roledescription="slide"
                aria-label="<?= $i + 1 ?> of <?= $slideCount ?>">
              <a class="hero-carousel__link" href="<?= e(url('/product/' . $slide['slug'])) ?>">
                <span class="hero-carousel__frame">
                  <?= picture(
                      image($slide['primary_image'] ?? null, 'product'),
                      $slide['name'],
                      [
                          'width'   => 900,
                          'height'  => 1125,
                          'decoding' => 'async',
                          // Only the first slide is above the fold; the rest can wait.
                          'loading'  => $i === 0 ? 'eager' : 'lazy',
                          'fetchpriority' => $i === 0 ? 'high' : 'low',
                      ]
                  ) ?>
                </span>

                <span class="hero-carousel__label">
                  <?php if (!empty($slide['category_name'])): ?>
                    <span class="hero-carousel__cat"><?= e($slide['category_name']) ?></span>
                  <?php endif; ?>
                  <span class="hero-carousel__name"><?= e($slide['name']) ?></span>
                  <span class="hero-carousel__price">
                    <?= $showPrice ? e(money($slide['price'])) : 'Price on request' ?>
                  </span>
                </span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <?php // Controls are script-driven, so they stay out of the way until it loads. ?>
        <div class="hero-carousel__controls" hidden data-hero-controls>
          <button class="hero-carousel__arrow" type="button" data-hero-prev aria-label="Previous product">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path d="M15 4 7 12l8 8" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>

          <ol class="hero-carousel__dots" data-hero-dots>
            <?php foreach ($heroSlides as $i => $slide): ?>
              <li>
                <button class="hero-carousel__dot" type="button" data-hero-go="<?= $i ?>"
                        aria-label="Show <?= e($slide['name']) ?>"></button>
              </li>
            <?php endforeach; ?>
          </ol>

          <button class="hero-carousel__arrow" type="button" data-hero-next aria-label="Next product">
            <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false">
              <path d="M9 4l8 8-8 8" fill="none" stroke="currentColor" stroke-width="1.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </button>
        </div>

        <p class="sr-only" aria-live="polite" data-hero-status></p>
      </div>
    </div>
  <?php endif; ?>

  <?php // The carousel puts its own controls along the bottom edge, so the
        // scroll cue would sit on top of them. ?>
  <?php if ($heroSlides === []): ?>
    <div class="hero__scroll" aria-hidden="true">
      <span>Scroll</span>
      <i></i>
    </div>
  <?php endif; ?>
</section>

<!-- Marquee -->
<div class="marquee" aria-hidden="true">
  <div class="marquee__track">
    <span>Racing</span><span>Polo</span><span>Showjumping</span><span>Dressage</span>
    <span>Eventing</span><span>Hacking</span><span>Safari Riding</span><span>Pony Club</span>
  </div>
</div>

<!-- ================================================================
     Section 2 — Shop by department
     ================================================================ -->
<section class="section" id="curation">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">The Catalogue</p>
        <h2>Find it the way the shop is laid out.</h2>
        <p class="lede">
          Three departments, the way a yard actually works — what the rider wears,
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

    <?php
      // How much there is to search, counted rather than claimed.
      $catalogueCount = 0;
      foreach ($departments as $d) {
          foreach ($d['sections'] as $sec) { $catalogueCount += (int) $sec['product_count']; }
      }
    ?>
    <form class="home-search" action="<?= e(url('/shop')) ?>" method="get" role="search" data-reveal>
      <label class="sr-only" for="home-search">Search the catalogue</label>
      <svg class="home-search__icon" width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.4"/>
        <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      </svg>
      <input class="home-search__input" id="home-search" type="search" name="q"
             placeholder="Search <?= $catalogueCount ?> products &mdash; girths, bits, hoof oil&hellip;"
             autocomplete="off">
      <button class="home-search__go" type="submit">Search</button>
    </form>

    <div class="departments">
      <?php foreach ($departments as $index => $dept): ?>
        <?php
          $pillar = $dept['pillar'];
          $art    = pillar_art($pillar['slug']);
          $image  = !empty($pillar['image'])
              ? image($pillar['image'])
              : asset('/assets/img/' . ($art !== 'product' ? 'pillar-' . $art : 'placeholder-product') . '.jpg');
          $alt = [
              'rider' => 'A showjumping rider clearing a fence',
              'horse' => 'An English saddle fitted with a sheepskin numnah',
              'yard'  => 'Grooming a horse with a body brush',
          ][$pillar['slug']] ?? $pillar['name'];

          $total = 0;
          foreach ($dept['sections'] as $s) { $total += (int) $s['product_count']; }
        ?>
        <article class="dept" data-reveal>
          <a class="dept__card" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
            <span class="dept__media">
              <?= picture($image, $alt, ['loading' => 'lazy', 'width' => 760, 'height' => 560, 'decoding' => 'async']) ?>
            </span>
            <span class="dept__caption">
              <span class="dept__index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
              <span class="dept__tag"><?= e($pillar['tagline']) ?></span>
              <span class="dept__name"><?= e($pillar['name']) ?></span>
            </span>
          </a>

          <?php
            // The busiest sections first, and only a handful of them. Rider has
            // three sections and Yard eleven, so listing every one left the
            // three cards wildly uneven on a desktop and ran to three screens
            // on a phone. The rest are one tap away under the department.
            $sections = $dept['sections'];
            usort($sections, static fn (array $a, array $b): int
                => (int) $b['product_count'] <=> (int) $a['product_count']);

            $shown = array_slice($sections, 0, 6);
            $rest  = count($sections) - count($shown);
          ?>
          <ul class="dept__chips">
            <?php foreach ($shown as $section): ?>
              <li>
                <a href="<?= e(url('/shop/' . $section['slug'])) ?>">
                  <?= e($section['name']) ?>
                  <span class="dept__count"><?= (int) $section['product_count'] ?></span>
                </a>
              </li>
            <?php endforeach; ?>
            <?php if ($rest > 0): ?>
              <li>
                <a class="dept__chip-more" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
                  +<?= $rest ?> more
                </a>
              </li>
            <?php endif; ?>
          </ul>

          <a class="dept__all" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
            All <?= e($pillar['name']) ?> &middot; <?= $total ?> items
            <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
              <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </a>
        </article>
      <?php endforeach; ?>
    </div>
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
     Services — the part of Tack Rack that is not a shelf
     ================================================================ -->
<?php if ($services !== []): ?>
<?php
  $serviceArt = [
      'saddle-fitting'   => 'service-fitting',
      'workshop-repairs' => 'service-repairs',
  ];
  $serviceCta = [
      'saddle-fitting'   => 'Book a fitting',
      'workshop-repairs' => 'Send photos for a quote',
  ];
?>
<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">What We Do</p>
        <h2>More than a shelf.</h2>
        <p class="lede">
          A saddle that does not fit damages a horse's back long before the rider
          notices — so we fit on the horse, and we repair in our own workshop.
        </p>
      </div>
      <a class="link" href="<?= e(url('/services')) ?>">
        All services
        <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
          <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
      </a>
    </div>

    <div class="services-home">
      <?php foreach ($services as $service): ?>
        <?php
          $art = $serviceArt[$service['slug']] ?? null;
          $img = !empty($service['image'])
              ? image($service['image'])
              : asset('/assets/img/' . ($art ?? 'placeholder-product') . '.jpg');
        ?>
        <article class="service-card" data-reveal>
          <a class="service-card__media" href="<?= e(url('/services/' . $service['slug'])) ?>" tabindex="-1" aria-hidden="true">
            <?= picture($img, $service['name'], ['loading' => 'lazy', 'width' => 760, 'height' => 500, 'decoding' => 'async']) ?>
          </a>
          <div class="service-card__body">
            <h3><a href="<?= e(url('/services/' . $service['slug'])) ?>"><?= e($service['name']) ?></a></h3>
            <p><?= e($service['tagline']) ?></p>
            <span class="service-card__cta">
              <?= e($serviceCta[$service['slug']] ?? 'Find out more') ?>
              <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
              </svg>
            </span>
          </div>
        </article>
      <?php endforeach; ?>
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
        <p>Racing, polo, showjumping, dressage, hacking and safari riding — stocked by a team that knows the kit for each one inside out.</p>
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
      <?php foreach ($grid as $product): ?>
        <?php require APP_PATH . '/Views/partials/product-card.php'; ?>
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
          Build a list from the catalogue and send it across. You will have current pricing,
          availability and lead times — usually within one working day. No account, no obligation.
        </p>
      </div>

      <?php // Nothing on the site shows a price, so the ordering model is
            // spelled out here rather than left to be worked out. ?>
      <ol class="steps" data-reveal>
        <li>
          <span class="steps__num">1</span>
          <span class="steps__text"><strong>Build a list.</strong> Add anything from the catalogue — no account needed.</span>
        </li>
        <li>
          <span class="steps__num">2</span>
          <span class="steps__text"><strong>We quote it.</strong> Current pricing, availability and lead times, usually within a working day.</span>
        </li>
        <li>
          <span class="steps__num">3</span>
          <span class="steps__text"><strong>Collect or we deliver.</strong> From the shop, across Nairobi, or countrywide by courier.</span>
        </li>
      </ol>

      <div class="cta-banner__actions" data-reveal>
        <a class="btn btn--lg" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
        <a class="btn btn--lg btn--outline-light" href="<?= e(url('/page/quote-process')) ?>">How it works</a>
      </div>
    </div>
  </div>
</section>
