<?php /** @var array $services */ ?>

<section class="a-panel">
  <div class="a-panel__head">
    <h2>Services</h2>
    <p>The copy behind the saddle fitting and workshop repair pages.</p>
  </div>

  <div class="a-table-wrap">
    <table class="a-table">
      <thead>
        <tr>
          <th>Service</th>
          <th>Tagline</th>
          <th class="a-table__num">Bookings</th>
          <th>Travel</th>
          <th>Status</th>
          <th class="a-table__actions">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($services as $service): ?>
          <?php $publicUrl = $service['slug'] === 'saddle-fitting' ? '/services/saddle-fitting' : '/services/repairs'; ?>
          <tr>
            <td>
              <a class="a-strong" href="<?= e(url('/admin/services/' . $service['id'] . '/edit')) ?>"><?= e($service['name']) ?></a>
              <div class="a-cell-media__meta"><?= e($publicUrl) ?></div>
            </td>
            <td class="a-muted"><?= e(excerpt($service['tagline'], 60) ?: '—') ?></td>
            <td class="a-table__num"><?= (int) $service['booking_count'] ?></td>
            <td class="a-muted"><?= (int) $service['travel_available'] === 1 ? 'Yes' : 'No' ?></td>
            <td>
              <span class="a-badge a-badge--<?= (int) $service['is_active'] === 1 ? 'live' : 'draft' ?>">
                <?= (int) $service['is_active'] === 1 ? 'Live' : 'Hidden' ?>
              </span>
            </td>
            <td class="a-table__actions">
              <div class="a-actions">
                <a class="a-icon-btn" href="<?= e(url($publicUrl)) ?>" target="_blank" rel="noopener" title="View on the site">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M14 4h6v6M20 4l-9 9M18 14v5a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h5" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </a>
                <a class="a-icon-btn" href="<?= e(url('/admin/services/' . $service['id'] . '/edit')) ?>" title="Edit">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M15 4l5 5-11 11H4v-5L15 4z" stroke="currentColor" stroke-width="1.6" stroke-linejoin="round"/></svg>
                </a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>
