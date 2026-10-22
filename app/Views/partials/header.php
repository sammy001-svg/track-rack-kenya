<?php
use App\Models\Category;

$navTree   = (new Category())->tree();
$quoteQty  = quote_count();
$overClass = !empty($overHeader) ? ' header--over' : '';
?>
<header class="header<?= $overClass ?>" id="header">
  <div class="header__inner">

    <a class="brand" href="<?= e(url('/')) ?>" aria-label="<?= e(setting('site_name', 'Tack Rack')) ?> &mdash; home">
      <?php /* Two renditions: ink for the solid header, bone for when the
                header is transparent over the dark hero. CSS swaps them. */ ?>
      <img class="brand__logo brand__logo--ink" src="<?= e(asset('/assets/img/logo.png')) ?>"
           alt="<?= e(setting('site_name', 'Tack Rack')) ?> Ltd &mdash; Equine Supplies"
           width="420" height="191">
      <img class="brand__logo brand__logo--bone" src="<?= e(asset('/assets/img/logo-light.png')) ?>"
           alt="" aria-hidden="true" width="420" height="191">
    </a>

    <nav class="nav" aria-label="Primary">
      <?php // One item per department. Hovering one opens its own shelves,
            // rather than every department opening one shared panel. ?>
      <?php foreach ($navTree as $pillar): ?>
        <div class="nav__item">
          <button class="nav__link nav__toggle" type="button" aria-expanded="false" aria-haspopup="true">
            <?= e($pillar['name']) ?>
            <svg width="10" height="7" viewBox="0 0 10 7" fill="none" aria-hidden="true">
              <path d="M1 1l4 4 4-4" stroke="currentColor" stroke-width="1.4" stroke-linecap="round"/>
            </svg>
          </button>

          <div class="mega">
            <div class="mega__inner">
              <div class="mega__head">
                <div>
                  <h4><?= e($pillar['name']) ?></h4>
                  <span class="mega__tag"><?= e($pillar['tagline']) ?></span>
                </div>
                <a class="mega__all" href="<?= e(url('/shop/' . $pillar['slug'])) ?>">
                  All <?= e($pillar['name']) ?> &middot; <?= (int) $pillar['product_count'] ?> pieces
                  <svg width="14" height="10" viewBox="0 0 14 10" fill="none" aria-hidden="true">
                    <path d="M9 1l4 4-4 4M13 5H1" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round"/>
                  </svg>
                </a>
              </div>

              <ul class="mega__list">
                <?php foreach ($pillar['children'] as $section): ?>
                  <li>
                    <a href="<?= e(url('/shop/' . $section['slug'])) ?>">
                      <?= e($section['name']) ?>
                      <span><?= (int) $section['product_count'] ?></span>
                    </a>

                    <?php if ($section['children'] !== []): ?>
                      <ul class="mega__sub">
                        <?php foreach ($section['children'] as $sub): ?>
                          <?php if ((int) $sub['product_count'] === 0) { continue; } ?>
                          <li>
                            <a href="<?= e(url('/shop/' . $sub['slug'])) ?>">
                              <?= e($sub['name']) ?>
                              <span><?= (int) $sub['product_count'] ?></span>
                            </a>
                          </li>
                        <?php endforeach; ?>

                          <?php // A product can sit on the section itself rather than on
                                // any shelf beneath it — a saddle with no maker shelf, say.
                                // Give it a row of its own so the figures on screen always
                                // account for the number beside the section. ?>
                          <?php if ((int) $section['own_count'] > 0): ?>
                            <li>
                              <a href="<?= e(url('/shop/' . $section['slug'])) ?>">
                                More in <?= e($section['name']) ?>
                                <span><?= (int) $section['own_count'] ?></span>
                              </a>
                            </li>
                          <?php endif; ?>
                      </ul>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      <?php endforeach; ?>

      <a class="nav__link" href="<?= e(url('/services')) ?>" <?= is_active('/services') ? 'aria-current="page"' : '' ?>>Services</a>
      <a class="nav__link" href="<?= e(url('/heritage')) ?>" <?= is_active('/heritage') ? 'aria-current="page"' : '' ?>>Heritage</a>
      <a class="nav__link" href="<?= e(url('/contact')) ?>" <?= is_active('/contact') ? 'aria-current="page"' : '' ?>>Contact</a>
    </nav>

    <div class="header__actions">
      <?php $signedIn = App\Core\CustomerAuth::check(); ?>
      <a class="icon-btn" href="<?= e(url($signedIn ? '/account' : '/account/login')) ?>"
         aria-label="<?= $signedIn ? 'Your account' : 'Sign in' ?>" title="<?= $signedIn ? 'Your account' : 'Sign in' ?>">
        <svg width="19" height="19" viewBox="0 0 24 24" fill="none" aria-hidden="true">
          <circle cx="12" cy="8.5" r="3.6" stroke="currentColor" stroke-width="1.5"/>
          <path d="M5 20a7 7 0 0 1 14 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
        </svg>
        <?php if ($signedIn): ?><span class="icon-btn__dot" aria-hidden="true"></span><?php endif; ?>
      </a>

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
      <?php foreach ($pillar['children'] as $section): ?>
        <a href="<?= e(url('/shop/' . $section['slug'])) ?>"><?= e($section['name']) ?></a>
        <?php foreach ($section['children'] as $sub): ?>
          <?php if ((int) $sub['product_count'] === 0) { continue; } ?>
          <a class="drawer__sub" href="<?= e(url('/shop/' . $sub['slug'])) ?>"><?= e($sub['name']) ?></a>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><em>View all <?= e($pillar['name']) ?></em></a>
    </div>
  <?php endforeach; ?>

  <div class="drawer__group">
    <div class="drawer__title">Services</div>
    <a href="<?= e(url('/services/saddle-fitting')) ?>">Saddle fitting</a>
    <a href="<?= e(url('/services/repairs')) ?>">Workshop repairs</a>
  </div>

  <div class="drawer__group">
    <a href="<?= e(url('/shop')) ?>">Full catalogue</a>
    <a href="<?= e(url('/heritage')) ?>">Our heritage</a>
    <a href="<?= e(url('/page/how-to-order')) ?>">How to order</a>
    <a href="<?= e(url('/contact')) ?>">Contact us</a>
    <a href="<?= e(url('/quote')) ?>">Quote list<?= $quoteQty > 0 ? ' (' . (int) $quoteQty . ')' : '' ?></a>
    <?php if (App\Core\CustomerAuth::check()): ?>
      <a href="<?= e(url('/account')) ?>">My account</a>
    <?php else: ?>
      <a href="<?= e(url('/account/login')) ?>">Sign in</a>
    <?php endif; ?>
  </div>

  <a class="btn btn--block" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
</div>
