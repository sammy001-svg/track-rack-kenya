<?php
use App\Models\Booking;
use App\Models\RepairRequest;
/** @var array $customer @var array $bookings @var array $repairs */
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li>Fittings &amp; repairs</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Your account</p>
    <h1>Fittings &amp; repairs.</h1>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">

        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Saddle fittings</h2>
            <a class="link" href="<?= e(url('/services/saddle-fitting')) ?>">Book another</a>
          </div>

          <?php if ($bookings === []): ?>
            <p class="account-empty">
              No fittings booked. A saddle that does not fit will damage a horse's back long
              before the rider notices —
              <a href="<?= e(url('/services/saddle-fitting')) ?>">book an assessment</a>.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($bookings as $booking): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($booking['reference']) ?></strong>
                      <small>
                        <?= e($booking['horse_name'] ?: 'Horse not named') ?>
                        &middot;
                        <?php if ($booking['scheduled_at']): ?>
                          Scheduled <?= e(pretty_date($booking['scheduled_at'], true)) ?>
                        <?php elseif ($booking['preferred_date']): ?>
                          Requested for <?= e(pretty_date($booking['preferred_date'])) ?>
                        <?php else: ?>
                          Sent <?= e(pretty_date($booking['created_at'])) ?>
                        <?php endif; ?>
                        <?= (int) $booking['at_yard'] === 1 ? ' &middot; at your yard' : '' ?>
                      </small>
                    </div>
                    <span class="pill pill--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Workshop repairs</h2>
            <a class="link" href="<?= e(url('/services/repairs')) ?>">Send another</a>
          </div>

          <?php if ($repairs === []): ?>
            <p class="account-empty">
              No repairs on record. We repair what most suppliers replace — broken trees,
              torn panels, restitching.
              <a href="<?= e(url('/services/repairs')) ?>">Send us photographs</a>.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($repairs as $repair): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($repair['reference']) ?> &middot; <?= e($repair['item_type']) ?></strong>
                      <small>
                        <?= e(pretty_date($repair['created_at'])) ?>
                        <?php if ((int) $repair['photo_count'] > 0): ?>
                          &middot; <?= (int) $repair['photo_count'] ?> photo<?= (int) $repair['photo_count'] === 1 ? '' : 's' ?>
                        <?php endif; ?>
                        <?php if ($repair['estimated_ready']): ?>
                          &middot; ready around <?= e(pretty_date($repair['estimated_ready'])) ?>
                        <?php endif; ?>
                      </small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= e($repair['status']) ?>"><?= e(RepairRequest::STATUSES[$repair['status']]) ?></span>
                      <?php if ($repair['quoted_amount'] !== null): ?>
                        <strong><?= e(money($repair['quoted_amount'])) ?></strong>
                      <?php endif; ?>
                    </div>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>
</section>
