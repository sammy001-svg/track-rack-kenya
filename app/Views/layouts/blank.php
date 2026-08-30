<?php
use App\Core\Session;
$flashes = Session::flashes();
?>
<!doctype html>
<html lang="en-KE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle ?? 'Tack Rack') ?> — <?= e(setting('site_name', 'Tack Rack')) ?></title>
<?php require APP_PATH . '/Views/partials/favicons.php'; ?>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,400..600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('/assets/css/admin.css')) ?>">
</head>
<body>

<?php if ($flashes !== []): ?>
  <div class="a-flashes" style="max-width:25rem;margin:1.25rem auto 0;padding-inline:1.25rem">
    <?php foreach ($flashes as $flash): ?>
      <div class="a-flash a-flash--<?= e($flash['type']) ?>">
        <span><?= e($flash['message']) ?></span>
        <button type="button" aria-label="Dismiss">&times;</button>
      </div>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?= $content ?>

<script src="<?= e(asset('/assets/js/admin.js')) ?>" defer></script>
</body>
</html>
