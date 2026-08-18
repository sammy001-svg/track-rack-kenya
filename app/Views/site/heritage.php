<?php /** @var array $page @var array $pillars */ ?>

<section class="heritage-hero">
  <div class="heritage-hero__media">
    <img src="<?= e(asset('/assets/img/heritage.svg')) ?>"
         alt="The Tack Rack workshop bench — hand tools, waxed thread and cut leather"
         width="1600" height="900" fetchpriority="high">
  </div>

  <div class="shell shell--wide">
    <p class="eyebrow">Since <?= e(setting('founded_year', '1997')) ?> &middot; Ngong Road, Nairobi</p>
    <h1><?= e($page['title']) ?></h1>
    <?php if (!empty($page['subtitle'])): ?>
      <p class="lede" style="color:rgba(247,244,239,.72);margin-top:1.25rem"><?= e($page['subtitle']) ?></p>
    <?php endif; ?>
  </div>
</section>

<section class="section">
  <div class="shell">
    <div style="display:grid;gap:clamp(2.5rem,5vw,4.5rem);grid-template-columns:1fr" class="heritage-body">
      <div class="prose" data-reveal>
        <?= $page['body'] ?>
      </div>
    </div>
  </div>
</section>

<!-- The record -->
<section class="section section--tight bg-paper">
  <div class="shell">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">The Record</p>
        <h2>What we are known for.</h2>
      </div>
    </div>

    <div class="timeline">
      <div class="timeline__row" data-reveal>
        <div class="timeline__year"><?= e(setting('founded_year', '1997')) ?></div>
        <div class="timeline__body">
          <h4>Tack Rack Limited is founded</h4>
          <p>Established to give Kenyan riders a dependable local source for quality equipment, rather than relying on what could be carried back in a suitcase.</p>
        </div>
      </div>

      <div class="timeline__row" data-reveal>
        <div class="timeline__year">Craft</div>
        <div class="timeline__body">
          <h4>The workshop opens behind the shop</h4>
          <p>Tree repair, re-flocking, panel work and restitching — plus manufacture of rugs, numnahs, girths and stirrup leathers, cut to the horse rather than to a pattern.</p>
        </div>
      </div>

      <div class="timeline__row" data-reveal>
        <div class="timeline__year">Fitting</div>
        <div class="timeline__body">
          <h4>Society of Master Saddlers qualification</h4>
          <p>Sharon Ashley becomes — and remains — the only Saddle Fitter in East Africa qualified with the Society of Master Saddlers. Saddles are fitted on the horse, at the shop or at your yard.</p>
        </div>
      </div>

      <div class="timeline__row" data-reveal>
        <div class="timeline__year">Today</div>
        <div class="timeline__body">
          <h4>MacNaughton Business Centre, Ngong Road</h4>
          <p>A bright, accessible premises with parking at the door, stocking equipment and supplements across racing, polo, showjumping, dressage, hacking and safari riding.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Pillars -->
<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="section-head" data-reveal>
      <div class="section-head__text">
        <p class="eyebrow">Explore</p>
        <h2>Start with the catalog.</h2>
      </div>
    </div>
  </div>

  <div class="pillars">
    <?php foreach ($pillars as $index => $pillar): ?>
      <article class="pillar" data-reveal>
        <div class="pillar__media">
          <img src="<?= e(image($pillar['image'] ?? null, $pillar['slug'])) ?>" alt="<?= e($pillar['name']) ?>" loading="lazy" width="800" height="1000">
        </div>
        <span class="pillar__index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
        <span class="pillar__tag"><?= e($pillar['tagline']) ?></span>
        <h3><?= e($pillar['name']) ?></h3>
        <p class="pillar__desc"><?= e($pillar['description']) ?></p>
        <a class="pillar__cta" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
          <span class="sr-only">Shop <?= e($pillar['name']) ?></span>
        </a>
      </article>
    <?php endforeach; ?>
  </div>
</section>

<section class="cta-banner">
  <div class="shell shell--wide">
    <div class="cta-banner__inner">
      <div data-reveal>
        <p class="eyebrow" style="color:rgba(255,255,255,.72)">Book a fitting</p>
        <h2>A saddle that fits is not a luxury.</h2>
        <p>A poorly fitted saddle damages a horse's back long before the rider notices. Send us a request and we will arrange an assessment — at the shop or at your yard.</p>
      </div>
      <div class="cta-banner__actions" data-reveal>
        <a class="btn btn--lg" href="<?= e(url('/request-a-quote')) ?>">Request a Fitting</a>
        <a class="btn btn--lg btn--outline-light" href="<?= e(url('/contact')) ?>">Contact Us</a>
      </div>
    </div>
  </div>
</section>
