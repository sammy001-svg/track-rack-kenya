<?php
use App\Models\Category;

$navTree   = (new Category())->tree();
$quoteQty  = quote_count();
$overClass = !empty($overHeader) ? ' header--over' : '';
?>
<header class="header<?= $overClass ?>" id="header">
  <div class="header__inner">

    <a class="brand" href="<?= e(url('/')) ?>" aria-label="<?= e(setting('site_name', 'Tack Rack')) ?> home">
      <span class="brand__mark"><?= e(setting('site_name', 'Tack Rack')) ?></span>
      <span class="brand__sub">Est. <?= e(setting('founded_year', '1997')) ?></span>
    </a>

    <nav class="nav" aria-label="Primary">
      <div class="nav__item">
        <button class="nav__link nav__toggle" type="button" aria-expanded="false" aria-haspopup="true">
          Shop
          <svg width="10" height="7" viewBox="0 0 10 7" fill="none" aria-hidden="true">
            <path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
          </svg>
        </button>

        <div class="mega">
          <div class="mega__inner">
            <?php foreach ($navTree as $pillar): ?>
              <div class="mega__col">
                <h4><?= e($pillar['name']) ?></h4>
                <span class="mega__tag"><?= e($pillar['tagline']) ?></span>
                <ul class="mega__list">
                  <?php foreach ($pillar['children'] as $child): ?>
                    <li>
                      <a href="<?= e(url('/shop/' . $child['slug'])) ?>">
                        <?= e($child['name']) ?>
                        <span><?= (int) $child['product_count'] ?></span>
                      </a>
                    </li>
                  <?php endforeach; ?>
                  <li>
                    <a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><strong>View all <?= e($pillar['name']) ?></strong></a>
                  </li>
                </ul>
              </div>
            <?php endforeach; ?>

            <div class="mega__feature">
              <div>
                <span class="mega__tag" style="color:var(--brass)">Fitted by a specialist</span>
                <p>Every saddle fitted on the horse by the only Society of Master Saddlers qualified fitter in East&nbsp;Africa.</p>
              </div>
              <a class="link link--light" href="<?= e(url('/heritage')) ?>">
                Our heritage
                <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                  <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
              </a>
            </div>
          </div>
        </div>
      </div>

      <a class="nav__link" href="<?= e(url('/shop')) ?>" <?= is_active('/shop', true) ? 'aria-current="page"' : '' ?>>Catalog</a>
      <a class="nav__link" href="<?= e(url('/heritage')) ?>" <?= is_active('/heritage') ? 'aria-current="page"' : '' ?>>Heritage</a>
      <a class="nav__link" href="<?= e(url('/contact')) ?>" <?= is_active('/contact') ? 'aria-current="page"' : '' ?>>Contact</a>
    </nav>

    <div class="header__actions">
      <a class="icon-btn" href="<?= e(url('/quote')) ?>" aria-label="Your quote list<?= $quoteQty > 0 ? ' (' . $quoteQty . ' items)' : '' ?>">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <path d="M4 4h2l2.4 11.2a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.55L21.5 8H7"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
          <circle cx="10.5" cy="20" r="1.2" fill="currentColor"/>
          <circle cx="18" cy="20" r="1.2" fill="currentColor"/>
        </svg>
        <span class="icon-btn__badge" id="quote-count" <?= $quoteQty < 1 ? 'hidden' : '' ?>><?= (int) $quoteQty ?></span>
      </a>

      <a class="btn btn--sm header__cta" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>

      <button class="burger" id="burger" type="button" aria-label="Menu" aria-expanded="false" aria-controls="drawer">
        <span></span><span></span><span></span>
      </button>
    </div>
  </div>
</header>

<!-- Mobile drawer -->
<div class="drawer" id="drawer">
  <?php foreach ($navTree as $pillar): ?>
    <div class="drawer__group">
      <div class="drawer__title"><?= e($pillar['name']) ?></div>
      <?php foreach ($pillar['children'] as $child): ?>
        <a href="<?= e(url('/shop/' . $child['slug'])) ?>"><?= e($child['name']) ?></a>
      <?php endforeach; ?>
      <a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><em>View all <?= e($pillar['name']) ?></em></a>
    </div>
  <?php endforeach; ?>

  <div class="drawer__group">
    <a href="<?= e(url('/shop')) ?>">Full catalog</a>
    <a href="<?= e(url('/heritage')) ?>">Our heritage</a>
    <a href="<?= e(url('/page/how-to-order')) ?>">How to order</a>
    <a href="<?= e(url('/contact')) ?>">Contact us</a>
    <a href="<?= e(url('/quote')) ?>">Quote list<?= $quoteQty > 0 ? ' (' . (int) $quoteQty . ')' : '' ?></a>
  </div>

  <a class="btn btn--block" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
</div>
