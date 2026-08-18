<?php
use App\Core\Session;

$siteName   = setting('site_name', 'Tack Rack');
$pageTitle  = $pageTitle ?? $siteName;
$fullTitle  = $pageTitle === $siteName ? $siteName . ' — Equestrian Supplies, Kenya' : $pageTitle . ' — ' . $siteName;
$metaDesc   = $metaDesc ?? setting('site_intro');
$bodyClass  = $bodyClass ?? '';
$overHeader = !empty($transparentHeader);
$flashes    = Session::flashes();
?>
<!doctype html>
<html lang="en-KE">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($fullTitle) ?></title>
<meta name="description" content="<?= e(excerpt($metaDesc, 158)) ?>">
<?php if (!empty($noindex)): ?><meta name="robots" content="noindex, nofollow"><?php endif; ?>
<meta name="theme-color" content="#14110E">

<meta property="og:type" content="website">
<meta property="og:site_name" content="<?= e($siteName) ?>">
<meta property="og:title" content="<?= e($fullTitle) ?>">
<meta property="og:description" content="<?= e(excerpt($metaDesc, 158)) ?>">
<meta property="og:image" content="<?= e(asset('/assets/img/og-default.svg')) ?>">
<meta name="twitter:card" content="summary_large_image">

<link rel="canonical" href="<?= e(url(CURRENT_PATH)) ?>">
<link rel="icon" href="<?= e(asset('/assets/img/favicon.svg')) ?>" type="image/svg+xml">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300..600;1,9..144,300..500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= e(asset('/assets/css/main.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('/assets/css/account.css')) ?>">

<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@type'    => 'SportingGoodsStore',
    'name'     => setting('site_name', 'Tack Rack Limited'),
    'description' => setting('site_intro'),
    'url'      => url('/'),
    'telephone' => setting('contact_phone'),
    'email'    => setting('contact_email'),
    'foundingDate' => setting('founded_year', '1997'),
    'address'  => [
        '@type'           => 'PostalAddress',
        'streetAddress'   => setting('contact_address'),
        'addressLocality' => 'Nairobi',
        'addressCountry'  => 'KE',
    ],
    'openingHours' => setting('contact_hours'),
], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
</head>

<body class="<?= e($bodyClass) ?>">

<a class="skip-link" href="#main">Skip to content</a>

<?php require APP_PATH . '/Views/partials/header.php'; ?>

<?php if ($flashes !== []): ?>
<div class="flash-stack">
  <?php foreach ($flashes as $flash): ?>
    <div class="flash flash--<?= e($flash['type']) ?>" role="status">
      <span><?= e($flash['message']) ?></span>
      <button type="button" aria-label="Dismiss">&times;</button>
    </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<main id="main">
<?= $content ?>
</main>

<?php require APP_PATH . '/Views/partials/footer.php'; ?>

<?php $wa = whatsapp_link('Hello Tack Rack, I would like to enquire about'); ?>
<?php if ($wa !== ''): ?>
<a class="wa-float" href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp">
  <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37s-1.04 1.01-1.04 2.47 1.06 2.86 1.21 3.06c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35zM12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm0 18.13h-.01a8.23 8.23 0 0 1-4.19-1.15l-.3-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.36c0-4.54 3.7-8.24 8.25-8.24 2.2 0 4.27.86 5.83 2.42a8.2 8.2 0 0 1 2.41 5.83c0 4.54-3.7 8.24-8.24 8.24z"/></svg>
  <span>WhatsApp</span>
</a>
<?php endif; ?>

<script src="<?= e(asset('/assets/js/main.js')) ?>" defer></script>
</body>
</html>
