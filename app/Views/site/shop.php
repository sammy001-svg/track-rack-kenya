<?php
/** @var array $products @var array $filters @var array $subCategories @var array $brands */
$sortOptions = [
    ''          => 'Curated order',
    'newest'    => 'Newest first',
    'name_asc'  => 'Name A–Z',
    'name_desc' => 'Name Z–A',
    'popular'   => 'Most viewed',
];
$stockOptions = [
    ''             => 'Any availability',
    'in_stock'     => 'In stock',
    'low_stock'    => 'Low stock',
    'on_order'     => 'Available on order',
];
$fallbackArt = $pillar['slug'] ?? 'product';
$cardFallback = in_array($fallbackArt, ['rider', 'horse', 'stable'], true) ? $fallbackArt : 'product';
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li><a href="<?= e(url('/shop')) ?>">Catalog</a></li>
      <?php if ($pillar !== null && ($category['id'] ?? 0) !== ($pillar['id'] ?? 0)): ?>
        <li><a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><?= e($pillar['name']) ?></a></li>
      <?php endif; ?>
      <?php if ($category !== null): ?><li><?= e($category['name']) ?></li><?php endif; ?>
    </ul>

    <p class="eyebrow" style="margin-top:1.5rem"><?= e($category['tagline'] ?? 'The Complete Range') ?></p>
    <h1><?= e($heading) ?></h1>
    <?php if (!empty($category['description'])): ?>
      <p class="lede"><?= e($category['description']) ?></p>
    <?php else: ?>
      <p class="lede"><?= e($tagline) ?></p>
    <?php endif; ?>
  </div>
</header>

<!-- Filter bar -->
<div class="filters">
  <div class="shell shell--wide">
    <form class="filters__row" id="filter-form" method="get" action="<?= e(url($category !== null ? '/shop/' . $category['slug'] : '/shop')) ?>">
      <input type="hidden" name="page" value="1">

      <label class="search-field">
        <span class="sr-only">Search the catalog</span>
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.6"/>
          <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"/>
        </svg>
        <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Search saddles, bits, rugs…" autocomplete="off">
      </label>

      <?php if ($subCategories !== []): ?>
        <div class="select">
          <label class="sr-only" for="f-category">Category</label>
          <select id="f-category" name="category">
            <option value="">All <?= e($pillar['name'] ?? 'categories') ?></option>
            <?php foreach ($subCategories as $sub): ?>
              <option value="<?= (int) $sub['id'] ?>" <?= $activeSubId === (int) $sub['id'] ? 'selected' : '' ?>>
                <?= e($sub['name']) ?> (<?= (int) $sub['product_count'] ?>)
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      <?php else: ?>
        <div class="select">
          <label class="sr-only" for="f-pillar">Section</label>
          <select id="f-pillar" name="category">
            <option value="">All sections</option>
            <?php foreach ($pillars as $p): ?>
              <optgroup label="<?= e($p['name']) ?>">
                <option value="<?= (int) $p['id'] ?>" <?= $activeSubId === (int) $p['id'] ? 'selected' : '' ?>>
                  All <?= e($p['name']) ?>
                </option>
                <?php foreach ((new App\Models\Category())->children((int) $p['id']) as $child): ?>
                  <option value="<?= (int) $child['id'] ?>" <?= $activeSubId === (int) $child['id'] ? 'selected' : '' ?>>
                    <?= e($child['name']) ?>
                  </option>
                <?php endforeach; ?>
              </optgroup>
            <?php endforeach; ?>
          </select>
        </div>
      <?php endif; ?>

      <div class="select">
        <label class="sr-only" for="f-brand">Brand</label>
        <select id="f-brand" name="brand">
          <option value="">All makers</option>
          <?php foreach ($brands as $brand): ?>
            <option value="<?= (int) $brand['id'] ?>" <?= (int) ($filters['brand_id'] ?? 0) === (int) $brand['id'] ? 'selected' : '' ?>>
              <?= e($brand['name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="select">
        <label class="sr-only" for="f-stock">Availability</label>
        <select id="f-stock" name="stock">
          <?php foreach ($stockOptions as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= ($filters['stock'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="select">
        <label class="sr-only" for="f-sort">Sort</label>
        <select id="f-sort" name="sort">
          <?php foreach ($sortOptions as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= ($filters['sort'] ?? '') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>

      <noscript><button class="btn btn--sm" type="submit">Apply</button></noscript>

      <p class="filters__count">
        <?= (int) $total ?> item<?= $total === 1 ? '' : 's' ?>
      </p>
    </form>
  </div>
</div>

<section class="section section--tight">
  <div class="shell shell--wide">
    <?php if ($products === []): ?>
      <div class="empty">
        <svg width="42" height="42" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.3"/>
          <path d="M20 20l-3.5-3.5" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/>
        </svg>
        <h3>Nothing matches those filters.</h3>
        <p>
          Try widening your search — or send us a quote request describing what you need.
          We source a great deal that never makes it onto the shelf.
        </p>
        <a class="btn" href="<?= e(url('/request-a-quote')) ?>">Ask us to source it</a>
      </div>
    <?php else: ?>
      <div class="grid-products">
        <?php foreach ($products as $product): ?>
          <?php require APP_PATH . '/Views/partials/product-card.php'; ?>
        <?php endforeach; ?>
      </div>

      <?php if ($pages > 1): ?>
        <nav class="pagination" aria-label="Pagination">
          <?php
            $window = 2;
            $start  = max(1, $page - $window);
            $end    = min($pages, $page + $window);
          ?>
          <a class="<?= $page <= 1 ? 'is-disabled' : '' ?>" href="<?= e(query_string(['page' => $page - 1])) ?>" aria-label="Previous page">&larr;</a>

          <?php if ($start > 1): ?>
            <a href="<?= e(query_string(['page' => 1])) ?>">1</a>
            <?php if ($start > 2): ?><span>…</span><?php endif; ?>
          <?php endif; ?>

          <?php for ($i = $start; $i <= $end; $i++): ?>
            <?php if ($i === $page): ?>
              <span class="is-current" aria-current="page"><?= $i ?></span>
            <?php else: ?>
              <a href="<?= e(query_string(['page' => $i])) ?>"><?= $i ?></a>
            <?php endif; ?>
          <?php endfor; ?>

          <?php if ($end < $pages): ?>
            <?php if ($end < $pages - 1): ?><span>…</span><?php endif; ?>
            <a href="<?= e(query_string(['page' => $pages])) ?>"><?= $pages ?></a>
          <?php endif; ?>

          <a class="<?= $page >= $pages ? 'is-disabled' : '' ?>" href="<?= e(query_string(['page' => $page + 1])) ?>" aria-label="Next page">&rarr;</a>
        </nav>
      <?php endif; ?>
    <?php endif; ?>
  </div>
</section>

<section class="cta-banner">
  <div class="shell shell--wide">
    <div class="cta-banner__inner">
      <div data-reveal>
        <p class="eyebrow" style="color:rgba(255,255,255,.72)">Can't find it?</p>
        <h2>We source well beyond what is on the shelf.</h2>
        <p>Describe what you are after — make, size, discipline — and we will come back with options, pricing and lead times.</p>
      </div>
      <div class="cta-banner__actions" data-reveal>
        <a class="btn btn--lg" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
      </div>
    </div>
  </div>
</section>
