<?php /** @var array $page */ ?>

<header class="page-head">
  <div class="shell shell--narrow">
    <ul class="crumbs">
      <li><a href="<?= e(url('/')) ?>">Home</a></li>
      <li><?= e($page['title']) ?></li>
    </ul>
    <p class="eyebrow" style="margin-top:1.5rem">Customer Care</p>
    <h1><?= e($page['title']) ?></h1>
    <?php if (!empty($page['subtitle'])): ?>
      <p class="lede"><?= e($page['subtitle']) ?></p>
    <?php endif; ?>
  </div>
</header>

<section class="section">
  <div class="shell shell--narrow">
    <div class="prose" data-reveal>
      <?= $page['body'] ?>
    </div>

    <div style="margin-top:clamp(3rem,6vw,4.5rem);padding-top:2rem;border-top:1px solid var(--rule);display:flex;flex-wrap:wrap;gap:1rem;align-items:center;justify-content:space-between">
      <p class="faint" style="font-size:.8rem">Last updated <?= e(pretty_date($page['updated_at'])) ?></p>
      <div style="display:flex;flex-wrap:wrap;gap:.75rem">
        <a class="btn btn--sm btn--ghost" href="<?= e(url('/contact')) ?>">Contact us</a>
        <a class="btn btn--sm" href="<?= e(url('/request-a-quote')) ?>">Request a Quote</a>
      </div>
    </div>
  </div>
</section>
