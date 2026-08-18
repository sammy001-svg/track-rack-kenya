<?php
use App\Models\Order;
/** @var array $customer @var array $orders */
?>

<header class="page-head">
  <div class="shell shell--wide">
    <ul class="crumbs">
      <li><a href="<?= e(url('/account')) ?>">Account</a></li>
      <li>Orders</li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Your account</p>
    <h1>Orders.</h1>
  </div>
</header>

<section class="section section--tight">
  <div class="shell shell--wide">
    <div class="account-layout">
      <?php require APP_PATH . '/Views/partials/account-nav.php'; ?>

      <div class="account-main">
        <section class="account-panel">
          <?php if ($orders === []): ?>
            <p class="account-empty">
              You have not placed an order yet. Items with a listed price can be bought
              directly — everything else goes through a
              <a href="<?= e(url('/request-a-quote')) ?>">quote request</a>.
            </p>
          <?php else: ?>
            <ul class="record-list">
              <?php foreach ($orders as $order): ?>
                <li>
                  <a href="<?= e(url('/account/orders/' . $order['reference'])) ?>">
                    <div>
                      <strong><?= e($order['reference']) ?></strong>
                      <small>
                        <?= (int) $order['item_count'] ?> item<?= (int) $order['item_count'] === 1 ? '' : 's' ?>
                        &middot; <?= e(pretty_date($order['created_at'], true)) ?>
                        &middot; <?= e(Order::DELIVERY_METHODS[$order['delivery_method']]) ?>
                      </small>
                    </div>
                    <div class="record-list__right">
                      <span class="pill pill--<?= e($order['status']) ?>"><?= e(Order::STATUSES[$order['status']]) ?></span>
                      <span class="pill pill--<?= e($order['payment_status']) ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?></span>
                      <strong><?= e(money($order['total'])) ?></strong>
                    </div>
                  </a>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </section>
      </div>
    </div>
  </div>
</section>
