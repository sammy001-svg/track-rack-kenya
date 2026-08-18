<?php /** @var array $services */ ?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li>Services</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Beyond the shelf</p>
    <h1>What we do, not just what we sell.</h1>
    <p class="lede">
      Two things separate Tack Rack from a catalog: a qualified saddle fitter, and a
      working saddlery behind the shop.
    </p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="service-grid">
      <?php foreach ($services as $index => $service): ?>
        <?php
          $isFitting = $service['slug'] === 'saddle-fitting';
          $serviceImage = !empty($service['image'])
              ? image($service['image'])
              : asset('/assets/img/service-' . ($isFitting ? 'fitting' : 'repairs') . '.jpg');
          $serviceAlt = $isFitting
              ? 'A dressage rider working a horse in a correctly fitted saddle'
              : 'Saddlery in the workshop awaiting repair';
        ?>
        <article class="service-card" data-reveal>
          <div class="service-card__media">
            <?= picture($serviceImage, $serviceAlt, ['loading' => 'lazy', 'width' => 900, 'height' => 600]) ?>
          </div>

          <div class="service-card__body">
            <span class="service-card__index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></span>
            <h2><?= e($service['name']) ?></h2>
            <p class="service-card__tag"><?= e($service['tagline']) ?></p>
            <p class="service-card__desc"><?= e(excerpt($service['description'], 240)) ?></p>

            <ul class="service-card__meta">
              <?php if (!empty($service['duration_minutes'])): ?>
                <li><strong>Typically</strong> <?= (int) $service['duration_minutes'] ?> minutes</li>
              <?php endif; ?>
              <?php if ((int) $service['travel_available'] === 1): ?>
                <li><strong>We travel</strong> to your yard</li>
              <?php else: ?>
                <li><strong>In our</strong> Nairobi workshop</li>
              <?php endif; ?>
              <?php if (!empty($service['price_from'])): ?>
                <li><strong>From</strong> <?= e(money($service['price_from'])) ?></li>
              <?php endif; ?>
            </ul>

            <a class="btn" href="<?= e(url($isFitting ? '/services/saddle-fitting' : '/services/repairs')) ?>">
              <?= $isFitting ? 'Book a Fitting' : 'Request a Repair' ?>
            </a>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="spotlight">
  <div class="shell shell--wide">
    <div class="spotlight__inner">
      <div data-reveal>
        <p class="eyebrow">The Credential</p>
        <h2>The only one in East Africa.</h2>
        <p>
          Sharon Ashley is the only Saddle Fitter in East Africa qualified with the
          Society of Master Saddlers. That matters because a saddle which does not fit
          will quietly damage a horse's back for months before the rider notices a thing.
        </p>
        <div class="spotlight__actions">
          <a class="btn btn--light" href="<?= e(url('/services/saddle-fitting')) ?>">Book a Fitting</a>
          <a class="btn btn--outline-light" href="<?= e(url('/heritage')) ?>">Our heritage</a>
        </div>
      </div>

      <div class="spotlight__grid" data-reveal>
        <article class="spot-card">
          <h4>Tree repair</h4>
          <span>Where others replace</span>
        </article>
        <article class="spot-card">
          <h4>Re-flocking &amp; panel work</h4>
          <span>Fit restored, not patched</span>
        </article>
        <article class="spot-card">
          <h4>Rugs, numnahs &amp; girths</h4>
          <span>Cut to your horse</span>
        </article>
        <article class="spot-card">
          <h4>Stirrup leathers</h4>
          <span>Single hide, nylon cored</span>
        </article>
      </div>
    </div>
  </div>
</section>

<section class="cta-banner">
  <div class="shell shell--wide">
    <div class="cta-banner__inner">
      <div data-reveal>
        <p class="eyebrow" style="color:rgba(255,255,255,.72)">Not sure which you need?</p>
        <h2>Describe it and we will tell you.</h2>
        <p>Send us a photograph or a description. If it is a fit problem we will book a fitting; if it is damage we will quote the repair.</p>
      </div>
      <div class="cta-banner__actions" data-reveal>
        <a class="btn btn--lg" href="<?= e(url('/contact')) ?>">Ask Us</a>
      </div>
    </div>
  </div>
</section>
