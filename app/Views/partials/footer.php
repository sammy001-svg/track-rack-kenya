<?php
use App\Models\Category;

$footerPillars = (new Category())->pillars();
$facebook  = setting('social_facebook');
$instagram = setting('social_instagram');
$youtube   = setting('social_youtube');
$wa        = whatsapp_link('Hello Tack Rack, I would like to enquire about');
?>
<footer class="footer">
  <div class="shell shell--wide">

    <div class="footer__top">
      <div class="footer__brand">
        <div class="brand">
          <img class="brand__logo brand__logo--bone" src="<?= e(asset('/assets/img/logo-light.png')) ?>"
               alt="<?= e(setting('site_name', 'Tack Rack')) ?> Ltd &mdash; Equine Supplies"
               width="420" height="191" loading="lazy">
        </div>
        <p class="footer__pitch"><?= e(setting('site_intro')) ?></p>

        <div class="footer__social">
          <?php if ($facebook): ?>
            <a href="<?= e($facebook) ?>" target="_blank" rel="noopener" aria-label="Facebook">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.9h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.78-1.63 1.57v1.88h2.78l-.45 2.9h-2.33V22c4.78-.79 8.44-4.93 8.44-9.94z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($instagram): ?>
            <a href="<?= e($instagram) ?>" target="_blank" rel="noopener" aria-label="Instagram">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2.16c3.2 0 3.58.01 4.85.07 1.17.05 1.8.25 2.23.41.56.22.96.48 1.38.9.42.42.68.82.9 1.38.16.42.36 1.06.41 2.23.06 1.27.07 1.65.07 4.85s-.01 3.58-.07 4.85c-.05 1.17-.25 1.8-.41 2.23-.22.56-.48.96-.9 1.38-.42.42-.82.68-1.38.9-.42.16-1.06.36-2.23.41-1.27.06-1.65.07-4.85.07s-3.58-.01-4.85-.07c-1.17-.05-1.8-.25-2.23-.41a3.8 3.8 0 0 1-1.38-.9 3.8 3.8 0 0 1-.9-1.38c-.16-.42-.36-1.06-.41-2.23C2.17 15.58 2.16 15.2 2.16 12s.01-3.58.07-4.85c.05-1.17.25-1.8.41-2.23.22-.56.48-.96.9-1.38.42-.42.82-.68 1.38-.9.42-.16 1.06-.36 2.23-.41C8.42 2.17 8.8 2.16 12 2.16zm0 1.98c-3.14 0-3.51.01-4.75.07-1.15.05-1.77.24-2.18.4-.55.22-.94.47-1.35.88-.41.41-.66.8-.88 1.35-.16.41-.35 1.03-.4 2.18-.06 1.24-.07 1.61-.07 4.75s.01 3.51.07 4.75c.05 1.15.24 1.77.4 2.18.22.55.47.94.88 1.35.41.41.8.66 1.35.88.41.16 1.03.35 2.18.4 1.24.06 1.61.07 4.75.07s3.51-.01 4.75-.07c1.15-.05 1.77-.24 2.18-.4.55-.22.94-.47 1.35-.88.41-.41.66-.8.88-1.35.16-.41.35-1.03.4-2.18.06-1.24.07-1.61.07-4.75s-.01-3.51-.07-4.75c-.05-1.15-.24-1.77-.4-2.18a3.6 3.6 0 0 0-.88-1.35 3.6 3.6 0 0 0-1.35-.88c-.41-.16-1.03-.35-2.18-.4-1.24-.06-1.61-.07-4.75-.07zm0 3.37a5.49 5.49 0 1 1 0 10.98 5.49 5.49 0 0 1 0-10.98zm0 9.05a3.56 3.56 0 1 0 0-7.12 3.56 3.56 0 0 0 0 7.12zm6.99-9.27a1.28 1.28 0 1 1-2.56 0 1.28 1.28 0 0 1 2.56 0z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($youtube): ?>
            <a href="<?= e($youtube) ?>" target="_blank" rel="noopener" aria-label="YouTube">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M23.5 6.9a3 3 0 0 0-2.12-2.12C19.5 4.27 12 4.27 12 4.27s-7.5 0-9.38.51A3 3 0 0 0 .5 6.9C0 8.78 0 12 0 12s0 3.22.5 5.1a3 3 0 0 0 2.12 2.12c1.88.51 9.38.51 9.38.51s7.5 0 9.38-.51a3 3 0 0 0 2.12-2.12C24 15.22 24 12 24 12s0-3.22-.5-5.1zM9.6 15.6V8.4l6.24 3.6-6.24 3.6z"/></svg>
            </a>
          <?php endif; ?>
          <?php if ($wa !== ''): ?>
            <a href="<?= e($wa) ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38a9.9 9.9 0 0 0 4.79 1.22c5.46 0 9.91-4.45 9.91-9.91S17.5 2 12.04 2zm5.43 12.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.08-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.05-.17-.3-.02-.46.13-.6.13-.14.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37s-1.04 1.01-1.04 2.47 1.06 2.86 1.21 3.06c.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35z"/></svg>
            </a>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <h4>The Catalog</h4>
        <ul class="footer__links">
          <?php foreach ($footerPillars as $pillar): ?>
            <li><a href="<?= e(url('/shop/' . $pillar['slug'])) ?>"><?= e($pillar['name']) ?></a></li>
          <?php endforeach; ?>
          <li><a href="<?= e(url('/shop')) ?>">Everything</a></li>
          <li><a href="<?= e(url('/shop?sort=newest')) ?>">New arrivals</a></li>
        </ul>
      </div>

      <div>
        <h4>Services &amp; Care</h4>
        <ul class="footer__links">
          <li><a href="<?= e(url('/services/saddle-fitting')) ?>">Saddle fitting</a></li>
          <li><a href="<?= e(url('/services/repairs')) ?>">Workshop repairs</a></li>
          <li><a href="<?= e(url('/page/how-to-order')) ?>">How to order</a></li>
          <li><a href="<?= e(url('/page/quote-process')) ?>">The quote process</a></li>
          <li><a href="<?= e(url('/request-a-quote')) ?>">Request a quote</a></li>
          <li><a href="<?= e(url('/contact')) ?>">Contact us</a></li>
          <li><a href="<?= e(url('/heritage')) ?>">About our heritage</a></li>
          <li><a href="<?= e(url('/account')) ?>">My account</a></li>
        </ul>
      </div>

      <div>
        <h4>Visit &amp; Contact</h4>
        <div class="footer__contact">
          <p>
            <strong><?= e(setting('contact_address')) ?></strong>
            <?php if (setting('contact_directions')): ?>
              <?= e(setting('contact_directions')) ?><br>
            <?php endif; ?>
            <?= e(setting('contact_postal')) ?>
            <?php if (setting('map_link')): ?>
              <br><a href="<?= e(setting('map_link')) ?>" target="_blank" rel="noopener">Get directions &rarr;</a>
            <?php endif; ?>
          </p>
          <p>
            <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a><br>
            <?php if (setting('contact_phone_alt')): ?>
              <a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone_alt'))) ?>"><?= e(setting('contact_phone_alt')) ?></a>
            <?php endif; ?>
          </p>
          <p><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></p>
          <p class="faint" style="font-size:.8rem"><?= e(setting('contact_hours')) ?></p>
        </div>
      </div>
    </div>

    <div class="footer__bottom">
      <p>&copy; <?= date('Y') ?> <?= e(setting('site_name', 'Tack Rack')) ?> Limited. All rights reserved.</p>
      <nav aria-label="Legal">
        <a href="<?= e(url('/page/privacy-policy')) ?>">Privacy Policy</a>
        <a href="<?= e(url('/page/terms-of-service')) ?>">Terms of Service</a>
        <a href="<?= e(url('/admin')) ?>">Staff Login</a>
      </nav>
    </div>
  </div>
</footer>
