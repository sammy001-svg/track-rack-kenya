<?php
use App\Core\Auth;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Quote;
use App\Models\RepairRequest;
/** @var array $customer @var array $counts @var array $horses @var array $orders @var array $quotes @var array $bookings @var array $repairs */
$tidy = static fn ($n): string => $n === null || $n === '' ? '' : rtrim(rtrim((string) $n, '0'), '.');
?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/customers')) ?>">&larr; All customers</a>
  <div class="a-actions">
    <a class="a-btn a-btn--ghost a-btn--sm" href="mailto:<?= e($customer['email']) ?>">Email</a>
    <?php if ($customer['phone']): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="tel:<?= e(preg_replace('/\s+/', '', $customer['phone'])) ?>">Call</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-grid a-grid--4 a-mb">
  <dl class="a-stat"><dt>Orders</dt><dd><?= (int) $counts['orders'] ?></dd></dl>
  <dl class="a-stat"><dt>Quotes</dt><dd><?= (int) $counts['quotes'] ?></dd></dl>
  <dl class="a-stat"><dt>Fittings</dt><dd><?= (int) $counts['bookings'] ?></dd></dl>
  <dl class="a-stat"><dt>Repairs</dt><dd><?= (int) $counts['repairs'] ?></dd></dl>
</div>

<div class="a-split a-split--wide-aside">
  <div class="a-stack">

    <?php if ($horses !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head">
          <h2>Horses on file</h2>
          <span class="a-badge a-badge--plain"><?= count($horses) ?></span>
        </div>
        <div class="a-table-wrap">
          <table class="a-table">
            <thead>
              <tr><th>Name</th><th>Height</th><th>Breed</th><th>Seat</th><th>Gullet</th><th>Rug</th><th>Girth</th></tr>
            </thead>
            <tbody>
              <?php foreach ($horses as $horse): ?>
                <tr>
                  <td><strong><?= e($horse['name']) ?></strong>
                    <?php if ($horse['discipline']): ?>
                      <div class="a-cell-media__meta"><?= e($horse['discipline']) ?></div>
                    <?php endif; ?>
                  </td>
                  <td class="a-muted"><?= $horse['height_hh'] ? e($tidy($horse['height_hh'])) . ' hh' : '—' ?></td>
                  <td class="a-muted"><?= e($horse['breed'] ?: '—') ?></td>
                  <td class="a-muted"><?= e($horse['saddle_seat_size'] ?: '—') ?></td>
                  <td class="a-muted"><?= e($horse['gullet_width'] ?: '—') ?></td>
                  <td class="a-muted"><?= e($horse['rug_size'] ?: '—') ?></td>
                  <td class="a-muted"><?= e($horse['girth_size'] ?: '—') ?></td>
                </tr>
                <?php if ($horse['notes']): ?>
                  <tr><td colspan="7" class="a-muted" style="font-size:.8rem;padding-top:0"><?= e($horse['notes']) ?></td></tr>
                <?php endif; ?>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($orders !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Orders</h2></div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($orders as $order): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/orders/' . $order['id'])) ?>"><?= e($order['reference']) ?></a></td>
                  <td class="a-faint"><?= e(pretty_date($order['created_at'])) ?></td>
                  <td class="a-table__num"><?= (int) $order['item_count'] ?> item<?= (int) $order['item_count'] === 1 ? '' : 's' ?></td>
                  <td class="a-table__num"><strong><?= e(money($order['total'])) ?></strong></td>
                  <td><span class="a-badge a-badge--<?= $order['payment_status'] === 'paid' ? 'won' : 'new' ?>"><?= e(Order::PAYMENT_STATUSES[$order['payment_status']]) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($quotes !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Quote requests</h2></div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($quotes as $quote): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/quotes/' . $quote['id'])) ?>"><?= e($quote['reference']) ?></a></td>
                  <td class="a-faint"><?= e(pretty_date($quote['created_at'])) ?></td>
                  <td class="a-table__num"><?= (int) $quote['item_count'] ?> item<?= (int) $quote['item_count'] === 1 ? '' : 's' ?></td>
                  <td><span class="a-badge a-badge--<?= e($quote['status']) ?>"><?= e(Quote::STATUSES[$quote['status']]) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>

    <?php if ($bookings !== [] || $repairs !== []): ?>
      <section class="a-panel">
        <div class="a-panel__head"><h2>Fittings &amp; repairs</h2></div>
        <div class="a-table-wrap">
          <table class="a-table">
            <tbody>
              <?php foreach ($bookings as $booking): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/bookings/' . $booking['id'])) ?>"><?= e($booking['reference']) ?></a></td>
                  <td>Saddle fitting<?= $booking['horse_name'] ? ' — ' . e($booking['horse_name']) : '' ?></td>
                  <td class="a-faint"><?= e(pretty_date($booking['created_at'])) ?></td>
                  <td><span class="a-badge a-badge--<?= e($booking['status']) ?>"><?= e(Booking::STATUSES[$booking['status']]) ?></span></td>
                </tr>
              <?php endforeach; ?>
              <?php foreach ($repairs as $repair): ?>
                <tr>
                  <td><a class="a-ref" href="<?= e(url('/admin/repairs/' . $repair['id'])) ?>"><?= e($repair['reference']) ?></a></td>
                  <td>Repair — <?= e($repair['item_type']) ?></td>
                  <td class="a-faint"><?= e(pretty_date($repair['created_at'])) ?></td>
                  <td><span class="a-badge a-badge--<?= e($repair['status']) ?>"><?= e(RepairRequest::STATUSES[$repair['status']]) ?></span></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </section>
    <?php endif; ?>
  </div>

  <div class="a-stack">
    <section class="a-panel">
      <div class="a-panel__head"><h2>Account</h2></div>
      <div class="a-panel__body">
        <dl class="a-def">
          <div><dt>Name</dt><dd><?= e($customer['name']) ?></dd></div>
          <div><dt>Email</dt><dd><a href="mailto:<?= e($customer['email']) ?>"><?= e($customer['email']) ?></a></dd></div>
          <div><dt>Phone</dt><dd><?= e($customer['phone'] ?: '—') ?></dd></div>
          <div><dt>Location</dt><dd><?= e($customer['location'] ?: '—') ?></dd></div>
          <div><dt>Discipline</dt><dd><?= e($customer['discipline'] ?: '—') ?></dd></div>
          <div><dt>Joined</dt><dd><?= e(pretty_date($customer['created_at'])) ?></dd></div>
          <div><dt>Last signed in</dt><dd><?= $customer['last_login_at'] ? e(pretty_date($customer['last_login_at'], true)) : 'Never' ?></dd></div>
        </dl>
      </div>

      <form method="post" action="<?= e(url('/admin/customers/' . $customer['id'] . '/update')) ?>">
        <?= csrf_field() ?>
        <div class="a-panel__foot">
          <label class="a-check">
            <input type="checkbox" name="is_active" value="1" <?= (int) $customer['is_active'] === 1 ? 'checked' : '' ?>>
            Account is active
          </label>
          <button class="a-btn a-btn--sm" type="submit" style="margin-left:auto">Save</button>
        </div>
      </form>
    </section>

    <?php if (Auth::isAdmin()): ?>
      <section class="a-panel">
        <div class="a-panel__body">
          <form method="post" action="<?= e(url('/admin/customers/' . $customer['id'] . '/delete')) ?>"
                data-confirm="Delete this account? Their orders, quotes, fittings and repairs are kept as business records.">
            <?= csrf_field() ?>
            <button class="a-btn a-btn--danger a-btn--block" type="submit">Delete account</button>
          </form>
          <p class="a-hint" style="margin-top:.6rem">
            Orders and quotes are kept — only the login is removed.
          </p>
        </div>
      </section>
    <?php endif; ?>
  </div>
</div>
