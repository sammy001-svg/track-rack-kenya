<?php /** @var array $message */ ?>

<div class="a-spread a-mb">
  <a class="a-btn a-btn--ghost a-btn--sm" href="<?= e(url('/admin/messages')) ?>">&larr; Inbox</a>

  <div class="a-actions">
    <a class="a-btn a-btn--sm" href="mailto:<?= e($message['email']) ?>?subject=<?= e(rawurlencode('Re: ' . ($message['subject'] ?: 'Your enquiry'))) ?>">Reply by email</a>
    <?php if (!empty($message['phone'])): ?>
      <a class="a-btn a-btn--ghost a-btn--sm" href="tel:<?= e(preg_replace('/\s+/', '', $message['phone'])) ?>">Call</a>
    <?php endif; ?>
  </div>
</div>

<div class="a-split">
  <section class="a-panel">
    <div class="a-panel__head">
      <h2><?= e($message['subject'] ?: 'No subject') ?></h2>
    </div>
    <div class="a-panel__body">
      <div class="a-note"><?= e($message['body']) ?></div>
    </div>
    <div class="a-panel__foot">
      <form method="post" action="<?= e(url('/admin/messages/' . $message['id'] . '/delete')) ?>"
            data-confirm="Delete this message? This cannot be undone.">
        <?= csrf_field() ?>
        <button class="a-btn a-btn--danger a-btn--sm" type="submit">Delete message</button>
      </form>
    </div>
  </section>

  <section class="a-panel">
    <div class="a-panel__head"><h2>Sender</h2></div>
    <div class="a-panel__body">
      <dl class="a-def">
        <div><dt>Name</dt><dd><?= e($message['name']) ?></dd></div>
        <div><dt>Email</dt><dd><a href="mailto:<?= e($message['email']) ?>"><?= e($message['email']) ?></a></dd></div>
        <div><dt>Phone</dt><dd><?= $message['phone'] ? '<a href="tel:' . e(preg_replace('/\s+/', '', $message['phone'])) . '">' . e($message['phone']) . '</a>' : '—' ?></dd></div>
        <div><dt>Received</dt><dd><?= e(pretty_date($message['created_at'], true)) ?></dd></div>
        <div><dt>IP address</dt><dd class="a-faint"><?= e($message['ip_address'] ?: '—') ?></dd></div>
      </dl>
    </div>
  </section>
</div>
