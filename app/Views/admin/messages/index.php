<?php /** @var array $messages @var array $filters */ ?>

<section class="a-panel">
  <div class="a-panel__head">
    <div class="a-tabs">
      <a class="a-tab <?= $filters['is_read'] === '' ? 'is-active' : '' ?>" href="<?= e(url('/admin/messages')) ?>">All</a>
      <a class="a-tab <?= $filters['is_read'] === 0 ? 'is-active' : '' ?>" href="<?= e(url('/admin/messages?read=0')) ?>">Unread <span><?= (int) $unreadCount ?></span></a>
      <a class="a-tab <?= $filters['is_read'] === 1 ? 'is-active' : '' ?>" href="<?= e(url('/admin/messages?read=1')) ?>">Read</a>
    </div>
  </div>

  <form class="a-filters" method="get" action="<?= e(url('/admin/messages')) ?>" data-auto-submit>
    <input type="hidden" name="page" value="1">
    <?php if ($filters['is_read'] !== ''): ?><input type="hidden" name="read" value="<?= (int) $filters['is_read'] ?>"><?php endif; ?>

    <input class="a-input" type="search" name="q" value="<?= e($filters['q']) ?>"
           placeholder="Search name, email, subject or body…" style="flex:1 1 16rem">

    <noscript><button class="a-btn a-btn--sm" type="submit">Search</button></noscript>
    <span class="a-filters__count"><?= (int) $total ?> message<?= (int) $total === 1 ? '' : 's' ?></span>
  </form>

  <?php if ($messages === []): ?>
    <div class="a-empty">
      <svg width="34" height="34" viewBox="0 0 24 24" fill="none"><rect x="3" y="5" width="18" height="14" rx="2" stroke="currentColor" stroke-width="1.3"/><path d="M3.5 6.5l8.5 6 8.5-6" stroke="currentColor" stroke-width="1.3" stroke-linecap="round"/></svg>
      <h3>The inbox is empty</h3>
      <p>Messages sent through the contact form on the website land here.</p>
    </div>
  <?php else: ?>
    <div class="a-table-wrap">
      <table class="a-table">
        <thead>
          <tr>
            <th>From</th>
            <th>Subject</th>
            <th>Preview</th>
            <th>Received</th>
            <th class="a-table__actions">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($messages as $message): ?>
            <tr>
              <td>
                <a class="a-strong" href="<?= e(url('/admin/messages/' . $message['id'])) ?>">
                  <?= (int) $message['is_read'] === 0 ? '<span class="a-badge a-badge--new" style="margin-right:.4rem">New</span>' : '' ?>
                  <?= e($message['name']) ?>
                </a>
                <div class="a-cell-media__meta"><?= e($message['email']) ?></div>
              </td>
              <td><?= e($message['subject'] ?: 'No subject') ?></td>
              <td class="a-muted"><?= excerpt($message['body'], 62) ?></td>
              <td class="a-faint a-nowrap" title="<?= e(pretty_date($message['created_at'], true)) ?>"><?= e(time_ago($message['created_at'])) ?></td>
              <td class="a-table__actions">
                <div class="a-actions">
                  <a class="a-icon-btn" href="<?= e(url('/admin/messages/' . $message['id'])) ?>" title="Open">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M9 6l6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  </a>
                  <form method="post" action="<?= e(url('/admin/messages/' . $message['id'] . '/delete')) ?>"
                        data-confirm="Delete this message?">
                    <?= csrf_field() ?>
                    <button class="a-icon-btn a-icon-btn--danger" type="submit" title="Delete">
                      <svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M4 7h16M9 7V5h6v2M6 7l1 13h10l1-13" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <?php require APP_PATH . '/Views/partials/admin-pagination.php'; ?>
  <?php endif; ?>
</section>
