<?php
use App\Models\Quote;
/** @var array $customer @var array $quotes */
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li>Quotes</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Your account</p>
    <h1>Quote requests.</h1>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">
        <section class="account-panel">
          <?php if ($quotes === []): ?>
            <p class="account-empty">
              No quote requests yet. <a href="<?= e(url('/shop')) ?>">Build a quote list</a>
              from the catalog and we will price it, usually within a working day.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($quotes as $quote): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($quote['reference']) ?></strong>
                      <small>
                        <?= (int) $quote['item_count'] ?> item<?= (int) $quote['item_count'] === 1 ? '' : 's' ?>
                        &middot; <?= e(pretty_date($quote['created_at'], true)) ?>
                      </small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= e($quote['status']) ?>"><?= e(Quote::STATUSES[$quote['status']]) ?></span>
                      <?php if ($quote['quoted_total'] !== null): ?>
                        <strong><?= e(money($quote['quoted_total'])) ?></strong>
                      <?php endif; ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>

            <p class="account-note">
              Quotes are prepared by hand. If you have not heard back within a working day,
              call us on <?= e(setting('contact_phone')) ?>.
            </p>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>
</section>
