<?php /** @var int $code @var string $message */ ?>

<section class="error-page">
  <div>
    <div class="error-page__code" aria-hidden="true"><?= (int) ($code ?? 404) ?></div>
    <h1>A wrong turn.</h1>
    <p><?= e($message ?? 'The page you were looking for could not be found.') ?></p>

    <div style="display:flex;flex-wrap:wrap;gap:.9rem;justify-content:center;margin-top:2.25rem">
      <a class="btn" href="<?= e(url('/')) ?>">Return home</a>
      <a class="btn btn--ghost" href="<?= e(url('/shop')) ?>">Browse the catalog</a>
    </div>
  </div>
</section>
