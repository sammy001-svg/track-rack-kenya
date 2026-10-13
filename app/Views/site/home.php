<?php
/**
 * The home page, written as an editorial sequence rather than a stack of
 * merchandising blocks.
 *
 * It reads top to bottom as one piece: an opening, a statement, three chapters
 * for the three departments, the pieces themselves, the two things Tack Rack
 * does that a catalogue cannot, and where to find us.
 *
 * @var array $departments  each pillar with its sections and counts
 * @var array $services     the active services
 * @var array $selected     a few products to stand for the rest
 */

$catalogueCount = 0;
foreach ($departments as $department) {
    foreach ($department['sections'] as $section) {
        $catalogueCount += (int) $section['product_count'];
    }
}

// The two services each have a section of their own further down, so they are
// pulled out by slug rather than looped over.
$bySlug = [];
foreach ($services as $service) {
    $bySlug[$service['slug']] = $service;
}

$fitting = $bySlug['saddle-fitting']   ?? null;
$repairs = $bySlug['workshop-repairs'] ?? null;

$chapterAlt = [
    'rider' => 'A showjumping rider clearing a fence',
    'horse' => 'An English saddle fitted with a sheepskin numnah',
    'yard'  => 'Grooming a horse with a body brush',
];
?>

<!-- ================================================================
     1 — The opening
     ================================================================ -->
<section class="hp-hero">
  <div class="hp-hero__media">
    <?= picture(
        asset('/assets/img/heritage.jpg'),
        'A saddler&rsquo;s bench: round knife, awl and cut leather',
        ['width' => 1600, 'height' => 900, 'fetchpriority' => 'high', 'decoding' => 'async']
    ) ?>
  </div>

  <div class="hp-hero__inner">
    <div class="shell shell--wide">
      <p class="hp-hero__eyebrow">Nairobi &middot; Since <?= e(setting('founded_year', '1997')) ?></p>

      <h1 class="hp-hero__title">Fitted on<br>the&nbsp;horse.</h1>

      <p class="hp-hero__lede">
        Saddlery, rider apparel and yard essentials for every discipline ridden in
        Kenya &mdash; chosen, fitted and mended by people who know the kit.
      </p>

      <div class="hp-hero__actions">
        <a class="hp-cta hp-cta--light" href="<?= e(url('/shop')) ?>">
          Browse the catalogue
          <svg width="15" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
            <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
        <a class="hp-cta hp-cta--ghost" href="<?= e(url('/services/saddle-fitting')) ?>">Book a fitting</a>
      </div>
    </div>
  </div>

  <span class="hp-hero__cue" aria-hidden="true"><i></i></span>
</section>

<!-- ================================================================
     2 — The statement
     ================================================================ -->
<section class="hp-statement">
  <div class="shell">
    <p class="hp-statement__text" data-reveal>
      A saddle that does not fit damages a horse&rsquo;s back long before the rider
      notices. <em>So nothing leaves here unfitted</em> &mdash; and what breaks comes
      back to our own workshop rather than the bin.
    </p>

    <dl class="hp-facts" data-reveal>
      <div>
        <dt>Trading since</dt>
        <dd><?= e(setting('founded_year', '1997')) ?></dd>
      </div>
      <div>
        <dt>Saddle fitting</dt>
        <dd>Society of Master Saddlers</dd>
      </div>
      <div>
        <dt>In the catalogue</dt>
        <dd><?= $catalogueCount ?> pieces</dd>
      </div>
    </dl>
  </div>
</section>

<!-- ================================================================
     3 — Three chapters, one for each department
     ================================================================ -->
<section class="hp-chapters" id="catalogue">
  <?php foreach ($departments as $index => $department): ?>
    <?php
      $pillar = $department['pillar'];
      $art    = pillar_art($pillar['slug']);
      $image  = !empty($pillar['image'])
          ? image($pillar['image'])
          : asset('/assets/img/' . ($art !== 'product' ? 'pillar-' . $art : 'placeholder-product') . '.jpg');

      // The busiest sections first; the rest are one tap away.
      $sections = $department['sections'];
      usort($sections, static fn (array $a, array $b): int
          => (int) $b['product_count'] <=> (int) $a['product_count']);

      $shown = array_slice($sections, 0, 5);
      $rest  = count($sections) - count($shown);

      $total = 0;
      foreach ($department['sections'] as $s) { $total += (int) $s['product_count']; }
    ?>
    <article class="hp-chapter" data-reveal>
      <figure class="hp-chapter__figure">
        <a href="<?= e(url('/shop/' . $pillar['slug'])) ?>" tabindex="-1" aria-hidden="true">
          <?= picture(
              $image,
              $chapterAlt[$pillar['slug']] ?? $pillar['name'],
              ['loading' => $index === 0 ? 'eager' : 'lazy', 'width' => 800, 'height' => 1000, 'decoding' => 'async']
          ) ?>
        </a>
      </figure>

      <div class="hp-chapter__body">
        <p class="hp-chapter__index"><?= str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) ?></p>
        <p class="hp-head__eyebrow"><?= e($pillar['tagline']) ?></p>
        <h2 class="hp-chapter__title">
          <a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><?= e($pillar['name']) ?></a>
        </h2>

        <ul class="hp-chapter__sections">
          <?php foreach ($shown as $section): ?>
            <li>
              <a href="<?= e(url('/shop/' . $section['slug'])) ?>">
                <span><?= e($section['name']) ?></span>
                <span class="hp-chapter__count"><?= (int) $section['product_count'] ?></span>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>

        <a class="hp-cta hp-cta--dark" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
          All <?= e($pillar['name']) ?> &middot; <?= $total ?> pieces
          <svg width="15" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
            <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>
    </article>
  <?php endforeach; ?>
</section>

<!-- ================================================================
     4 — The pieces themselves
     ================================================================ -->
<?php if ($selected !== []): ?>
<section class="hp-pieces">
  <div class="shell shell--wide">
    <div class="hp-head" data-reveal>
      <p class="hp-head__eyebrow">From the shelf</p>
      <h2 class="hp-head__title">Photographed where they sit.</h2>
      <p class="hp-head__lede">
        Every photograph on this site is of the thing itself, in our own studio on
        Ngong Road. Pricing comes back with your quote.
      </p>
    </div>

    <form class="hp-search" action="<?= e(url('/shop')) ?>" method="get" role="search" data-reveal>
      <label class="sr-only" for="hp-search">Search the catalogue</label>
      <svg width="17" height="17" viewBox="0 0 24 24" fill="none" aria-hidden="true">
        <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.4"/>
        <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
      </svg>
      <input id="hp-search" class="hp-search__input" type="search" name="q"
             placeholder="Search <?= $catalogueCount ?> pieces &mdash; girths, bits, hoof oil&hellip;" autocomplete="off">
      <button class="hp-search__go" type="submit">Search</button>
    </form>

    <div class="hp-pieces__grid">
      <?php foreach ($selected as $item): ?>
        <figure class="hp-piece" data-reveal>
          <a class="hp-piece__media" href="<?= e(url('/product/' . $item['slug'])) ?>">
            <?= picture(
                image($item['primary_image'] ?? null, 'product'),
                $item['name'],
                ['loading' => 'lazy', 'width' => 600, 'height' => 600, 'decoding' => 'async']
            ) ?>
          </a>
          <figcaption>
            <p class="hp-piece__cat"><?= e($item['category_name'] ?? 'Tack Rack') ?></p>
            <h3 class="hp-piece__name">
              <a href="<?= e(url('/product/' . $item['slug'])) ?>"><?= e($item['name']) ?></a>
            </h3>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     5 — The fitting room
     ================================================================ -->
<?php if ($fitting !== null): ?>
<section class="hp-feature">
  <div class="hp-feature__media">
    <?= picture(
        !empty($fitting['image']) ? image($fitting['image']) : asset('/assets/img/service-fitting.jpg'),
        'A saddle being fitted on a horse',
        ['loading' => 'lazy', 'width' => 900, 'height' => 600, 'decoding' => 'async']
    ) ?>
  </div>

  <div class="hp-feature__body" data-reveal>
    <p class="hp-head__eyebrow">The fitting room</p>
    <h2 class="hp-head__title">The only Master Saddlers fitter in East Africa.</h2>
    <p>
      Sharon Ashley fits every saddle we supply on the horse it is for, at its own
      yard. Horses change shape with work, age and condition, so a saddle that
      fitted last season may not fit this one.
    </p>
    <a class="hp-cta hp-cta--dark" href="<?= e(url('/services/saddle-fitting')) ?>">
      Book a fitting
      <svg width="15" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
        <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     6 — The workshop
     ================================================================ -->
<?php if ($repairs !== null): ?>
<section class="hp-feature hp-feature--reverse hp-feature--dark">
  <div class="hp-feature__media">
    <?= picture(
        !empty($repairs['image']) ? image($repairs['image']) : asset('/assets/img/service-repairs.jpg'),
        'Close detail of a black leather saddle flap and its stitching',
        ['loading' => 'lazy', 'width' => 900, 'height' => 600, 'decoding' => 'async']
    ) ?>
  </div>

  <div class="hp-feature__body" data-reveal>
    <p class="hp-head__eyebrow">The workshop</p>
    <h2 class="hp-head__title">We repair what most suppliers replace.</h2>
    <p>
      Broken trees, torn panels, restitching &mdash; mended on Ngong Road rather than
      written off. The same benches make rugs, numnahs, girths and stirrup leathers
      to measure.
    </p>
    <a class="hp-cta hp-cta--light" href="<?= e(url('/services/repairs')) ?>">
      Send photographs for a quote
      <svg width="15" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
        <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
      </svg>
    </a>
  </div>
</section>
<?php endif; ?>

<!-- ================================================================
     7 — Where to find us, and how to ask
     ================================================================ -->
<section class="hp-visit">
  <div class="shell shell--wide">
    <div class="hp-visit__grid">
      <div data-reveal>
        <p class="hp-head__eyebrow">Come and see it</p>
        <h2 class="hp-head__title">Nothing here is sold blind.</h2>
        <p class="hp-visit__lede">
          Send us a list and we will come back with pricing, availability and lead
          times &mdash; usually within a working day. No account, no obligation.
        </p>
        <a class="hp-cta hp-cta--dark" href="<?= e(url('/request-a-quote')) ?>">
          Request a quote
          <svg width="15" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
            <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </a>
      </div>

      <dl class="hp-visit__details" data-reveal>
        <div>
          <dt>The shop</dt>
          <dd>
            <?= e(setting('contact_address')) ?>
            <?php if (setting('map_link')): ?>
              <br><a href="<?= e(setting('map_link')) ?>" target="_blank" rel="noopener">Get directions &rarr;</a>
            <?php endif; ?>
          </dd>
        </div>
        <div>
          <dt>Opening hours</dt>
          <dd><?= e(setting('contact_hours')) ?></dd>
        </div>
        <div>
          <dt>Talk to us</dt>
          <dd>
            <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a><br>
            <a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a>
          </dd>
        </div>
      </dl>
    </div>
  </div>
</section>
