<?php
use App\Models\Booking;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RepairRequest;

/** @var array $customer @var array $counts @var array $orders @var array $quotes @var array $bookings @var array $repairs @var array $horses */
?>

<header class="page-head">
  <div class="shell shell--wide">
    <p class="eyebrow">Your account</p>
    <h1>Hello, <?= e(explode(' ', $customer['name'])[0]) ?>.</h1>
    <p class="lede">Everything you have sent us, in one place.</p>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">

        <!-- Tiles -->
        <div class="account-tiles">
          <a class="account-tile" href="<?= e(url('/account/orders')) ?>">
            <span class="account-tile__n"><?= (int) $counts['orders'] ?></span>
            <span class="account-tile__l">Orders</span>
          </a>
          <a class="account-tile" href="<?= e(url('/account/quotes')) ?>">
            <span class="account-tile__n"><?= (int) $counts['quotes'] ?></span>
            <span class="account-tile__l">Quotes</span>
          </a>
          <a class="account-tile" href="<?= e(url('/account/activity')) ?>">
            <span class="account-tile__n"><?= (int) $counts['bookings'] + (int) $counts['repairs'] ?></span>
            <span class="account-tile__l">Fittings &amp; repairs</span>
          </a>
          <a class="account-tile" href="<?= e(url('/account/horses')) ?>">
            <span class="account-tile__n"><?= (int) $counts['horses'] ?></span>
            <span class="account-tile__l">Horses on file</span>
          </a>
        </div>

        <!-- Orders -->
        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Recent orders</h2>
            <?php if ($orders !== []): ?>
              <a class="link" href="<?= e(url('/account/orders')) ?>">All orders</a>
            <?php endif; ?>
          </div>

          <?php if ($orders === []): ?>
            <p class="account-empty">
              No orders yet. Items with a listed price can be bought directly from the
              <a href="<?= e(url('/shop')) ?>">catalog</a>.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($orders as $order): ?>
                <li>
                  <a href="<?= e(url('/account/orders/' . $order['reference'])) ?>">
                    <div>
                      <strong><?= e($order['reference']) ?></strong>
                      <small><?= (int) $order['item_count'] ?> item<?= (int) $order['item_count'] === 1 ? '' : 's' ?>
                        &middot; <?= e(pretty_date($order['created_at'])) ?></small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= e($order['payment_status']) ?>">
                        <?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?>
                      </span>
                      <strong><?= e(money($order['total'])) ?></strong>
                    </div>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>

        <!-- Quotes -->
        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Recent quotes</h2>
            <?php if ($quotes !== []): ?>
              <a class="link" href="<?= e(url('/account/quotes')) ?>">All quotes</a>
            <?php endif; ?>
          </div>

          <?php if ($quotes === []): ?>
            <p class="account-empty">
              No quote requests yet. <a href="<?= e(url('/shop')) ?>">Build a quote list</a>
              and we will price it for you.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($quotes as $quote): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($quote['reference']) ?></strong>
                      <small><?= (int) $quote['item_count'] ?> item<?= (int) $quote['item_count'] === 1 ? '' : 's' ?>
                        &middot; <?= e(pretty_date($quote['created_at'])) ?></small>
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
          <?php endif; ?>
        </section>

        <!-- Fittings & repairs -->
        <?php if ($bookings !== [] || $repairs !== []): ?>
          <section class="account-panel">
            <div class="account-panel__head">
              <h2>Fittings &amp; repairs</h2>
              <a class="link" href="<?= e(url('/account/activity')) ?>">See all</a>
            </div>

            <ul class="record-list">
              <?php foreach ($bookings as $booking): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($booking['reference']) ?> &middot; Saddle fitting</strong>
                      <small><?= e($booking['horse_name'] ?: 'Horse not named') ?>
                        &middot; <?= $booking['scheduled_at'] ? e(pretty_date($booking['scheduled_at'], true)) : e(pretty_date($booking['created_at'])) ?></small>
                    </div>
                    <span class="pill pill--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span>
                  </div>
                </li>
              <?php endforeach; ?>

              <?php foreach ($repairs as $repair): ?>
                <li>
                  <div class="record-list__static">
                    <div>
                      <strong><?= e($repair['reference']) ?> &middot; <?= e($repair['item_type']) ?></strong>
                      <small><?= e(pretty_date($repair['created_at'])) ?></small>
                    </div>
                    <span class="pill pill--<?= e($repair['status']) ?>"><?= e(RepairRequest::STATUSES[$repair['status']]) ?></span>
                  </div>
                </li>
              <?php endforeach; ?>
            </ul>
          </section>
        <?php endif; ?>

        <!-- Horses -->
        <section class="account-panel">
          <div class="account-panel__head">
            <h2>Your horses</h2>
            <a class="link" href="<?= e(url('/account/horses')) ?>">Manage</a>
          </div>

          <?php if ($horses === []): ?>
            <p class="account-empty">
              <a href="<?= e(url('/account/horses')) ?>">Add a horse</a> and we will have its
              sizes to hand whenever you request a quote or book a fitting.
            </p>
          <?php else: ?>
            <div class="horse-chips">
              <?php foreach ($horses as $horse): ?>
                <a class="horse-chip" href="<?= e(url('/account/horses?edit=' . $horse['id'])) ?>">
                  <strong><?= e($horse['name']) ?></strong>
                  <small>
                    <?= $horse['height_hh'] ? e(rtrim(rtrim($horse['height_hh'], '0'), '.')) . 'hh' : '' ?>
                    <?= $horse['saddle_seat_size'] ? ' &middot; ' . e($horse['saddle_seat_size']) : '' ?>
                  </small>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>
</section>
