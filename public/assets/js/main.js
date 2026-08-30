/* =====================================================================
   TACK RACK KENYA — front-of-house behaviour
   Vanilla ES2020. No dependencies.
   ===================================================================== */
(function () {
  'use strict';

  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* -------------------------------------------------------------------
     Header: shrink / hide on scroll, transparent over the hero
     ------------------------------------------------------------------- */
  function initHeader() {
    const header = $('#header');
    if (!header) return;

    const startsOver = header.classList.contains('header--over');
    let lastY = window.scrollY;
    let ticking = false;

    function update() {
      const y = window.scrollY;

      if (startsOver) {
        header.classList.toggle('header--over', y < 60);
      }

      // Hide on downward scroll past the fold, reveal on the way back up.
      const menuOpen = document.body.classList.contains('nav-open')
        || $('.nav__item.is-open') !== null;

      if (!menuOpen && y > 320 && y > lastY + 6) {
        header.classList.add('header--hidden');
      } else if (y < lastY - 6 || y < 120) {
        header.classList.remove('header--hidden');
      }

      lastY = y;
      ticking = false;
    }

    window.addEventListener('scroll', () => {
      if (!ticking) {
        ticking = true;
        window.requestAnimationFrame(update);
      }
    }, { passive: true });

    update();
  }

  /* -------------------------------------------------------------------
     Mega menu (desktop) + mobile drawer
     ------------------------------------------------------------------- */
  function initNav() {
    const items = $$('.nav__item');

    items.forEach((item) => {
      const toggle = $('.nav__toggle', item);
      if (!toggle) return;

      let closeTimer = null;

      const open = () => {
        window.clearTimeout(closeTimer);
        items.forEach((other) => {
          if (other !== item) other.classList.remove('is-open');
        });
        item.classList.add('is-open');
        toggle.setAttribute('aria-expanded', 'true');
      };

      const close = () => {
        item.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      };

      const closeSoon = () => {
        closeTimer = window.setTimeout(close, 180);
      };

      toggle.addEventListener('click', (e) => {
        e.preventDefault();
        item.classList.contains('is-open') ? close() : open();
      });

      item.addEventListener('mouseenter', open);
      item.addEventListener('mouseleave', closeSoon);

      const mega = $('.mega', item);
      if (mega) {
        mega.addEventListener('mouseenter', () => window.clearTimeout(closeTimer));
        mega.addEventListener('mouseleave', closeSoon);
      }
    });

    document.addEventListener('keydown', (e) => {
      if (e.key !== 'Escape') return;
      items.forEach((item) => {
        item.classList.remove('is-open');
        const toggle = $('.nav__toggle', item);
        if (toggle) toggle.setAttribute('aria-expanded', 'false');
      });
      if (document.body.classList.contains('nav-open')) toggleDrawer(false);
    });

    document.addEventListener('click', (e) => {
      if (e.target.closest('.nav__item')) return;
      items.forEach((item) => item.classList.remove('is-open'));
    });

    // --- Mobile drawer ---
    const burger = $('#burger');

    function toggleDrawer(force) {
      const open = typeof force === 'boolean' ? force : !document.body.classList.contains('nav-open');
      document.body.classList.toggle('nav-open', open);
      if (burger) burger.setAttribute('aria-expanded', String(open));
    }

    if (burger) {
      burger.addEventListener('click', () => toggleDrawer());
    }

    $$('.drawer a').forEach((link) => link.addEventListener('click', () => toggleDrawer(false)));
  }

  /* -------------------------------------------------------------------
     Scroll reveal
     ------------------------------------------------------------------- */
  function initReveal() {
    const nodes = $$('[data-reveal]');
    if (!nodes.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      nodes.forEach((n) => n.classList.add('is-visible'));
      return;
    }

    const observer = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        observer.unobserve(entry.target);
      });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.06 });

    nodes.forEach((node, index) => {
      // Stagger siblings that share a parent for a sequenced feel.
      const siblings = node.parentElement
        ? Array.from(node.parentElement.children).filter((c) => c.hasAttribute('data-reveal'))
        : [];
      const position = siblings.indexOf(node);
      const step = position > -1 ? position : index;
      node.style.setProperty('--reveal-delay', Math.min(step, 6) * 70 + 'ms');
      observer.observe(node);
    });
  }

  /* -------------------------------------------------------------------
     Flash messages
     ------------------------------------------------------------------- */
  function dismissFlash(el) {
    el.classList.add('is-leaving');
    window.setTimeout(() => el.remove(), 350);
  }

  function initFlashes() {
    $$('.flash').forEach((flash) => {
      const close = $('button', flash);
      if (close) close.addEventListener('click', () => dismissFlash(flash));
      window.setTimeout(() => { if (flash.isConnected) dismissFlash(flash); }, 6000);
    });
  }

  function pushFlash(message, type = 'success') {
    let stack = $('.flash-stack');

    if (!stack) {
      stack = document.createElement('div');
      stack.className = 'flash-stack';
      document.body.appendChild(stack);
    }

    const flash = document.createElement('div');
    flash.className = 'flash flash--' + type;
    flash.setAttribute('role', 'status');
    flash.innerHTML = '<span></span><button type="button" aria-label="Dismiss">&times;</button>';
    $('span', flash).textContent = message;
    $('button', flash).addEventListener('click', () => dismissFlash(flash));

    stack.appendChild(flash);
    window.setTimeout(() => { if (flash.isConnected) dismissFlash(flash); }, 5000);
  }

  /* -------------------------------------------------------------------
     Quote list: add without a page reload
     ------------------------------------------------------------------- */
  function updateBadge(count) {
    const badge = $('#quote-count');
    if (!badge) return;

    badge.textContent = String(count);
    badge.hidden = count < 1;

    badge.classList.remove('is-bumped');
    void badge.offsetWidth; // restart the animation
    badge.classList.add('is-bumped');
  }

  function initQuoteForms() {
    document.addEventListener('submit', async (e) => {
      const form = e.target;
      if (!form.matches('form[data-quote-add]')) return;
      if (!window.fetch) return; // fall back to a normal submit

      e.preventDefault();

      const button = $('button[type="submit"]', form);
      const originalLabel = button ? button.textContent : '';

      if (button) {
        button.disabled = true;
        button.textContent = 'Adding…';
      }

      try {
        const response = await fetch(form.action, {
          method: 'POST',
          body: new FormData(form),
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
          credentials: 'same-origin',
        });

        const data = await response.json();

        if (data.ok) {
          updateBadge(data.count);
          pushFlash(data.message || 'Added to your quote list.', 'success');
          if (button) button.textContent = 'Added';
          window.setTimeout(() => { if (button) button.textContent = originalLabel; }, 1600);
        } else {
          pushFlash(data.error || 'That item could not be added.', 'error');
          if (button) button.textContent = originalLabel;
        }
      } catch (err) {
        // Network or parse failure: submit the old-fashioned way.
        form.removeAttribute('data-quote-add');
        form.submit();
        return;
      } finally {
        if (button) button.disabled = false;
      }
    });
  }

  /* -------------------------------------------------------------------
     Quantity steppers
     ------------------------------------------------------------------- */
  function initQty() {
    document.addEventListener('click', (e) => {
      const button = e.target.closest('[data-qty]');
      if (!button) return;

      const wrap  = button.closest('.qty');
      const input = wrap ? $('input', wrap) : null;
      if (!input) return;

      const step  = button.dataset.qty === 'up' ? 1 : -1;
      const min   = parseInt(input.min || '1', 10);
      const max   = parseInt(input.max || '999', 10);
      const value = Math.min(max, Math.max(min, (parseInt(input.value, 10) || min) + step));

      input.value = String(value);
      input.dispatchEvent(new Event('change', { bubbles: true }));
    });

    // On the quote list, changing a quantity submits its row form.
    $$('form[data-qty-form] input[name="quantity"]').forEach((input) => {
      let timer = null;
      input.addEventListener('change', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => input.form.submit(), 320);
      });
    });
  }

  /* -------------------------------------------------------------------
     Product gallery
     ------------------------------------------------------------------- */
  function initGallery() {
    const main = $('#gallery-main');
    if (!main) return;

    const image  = $('img', main);
    // When the main image sits inside a <picture>, the WebP <source> wins over
    // img.src — so it has to be swapped in step with it.
    const source = $('source', main);
    const thumbs = $$('.gallery__thumb');
    const desc   = $('#gallery-desc');
    const count  = $('#gallery-count');
    const total  = thumbs.length;

    let current = 0;

    function show(index, viaKeyboard) {
      if (total === 0) return;

      // Wrap around at both ends.
      current = (index + total) % total;

      const thumb = thumbs[current];
      const full  = thumb.dataset.full;
      if (!full || !image) return;

      const webp = thumb.dataset.fullWebp || '';
      const alt  = thumb.dataset.alt || '';

      image.style.opacity = '0';

      window.setTimeout(() => {
        if (source) {
          if (webp) {
            source.srcset = webp;
          } else {
            // No WebP for this one; drop the source so the JPEG is used.
            source.removeAttribute('srcset');
          }
        }
        image.src = full;
        image.alt = alt;
        image.style.opacity = '1';
      }, 160);

      // The caption describes the specific photograph, not the product.
      if (desc)  desc.textContent = alt;
      if (count) count.textContent = (current + 1) + ' / ' + total;

      thumbs.forEach((t, i) => t.classList.toggle('is-active', i === current));

      // Keep the active thumbnail in view in the scrolling strip.
      if (thumb.scrollIntoView) {
        thumb.scrollIntoView({ block: 'nearest', inline: 'nearest',
          behavior: viaKeyboard ? 'auto' : 'smooth' });
      }

      main.classList.remove('is-zoomed');
    }

    thumbs.forEach((thumb, i) => {
      thumb.addEventListener('click', () => show(i));
    });

    const prev = $('#gallery-prev');
    const next = $('#gallery-next');

    if (prev) prev.addEventListener('click', (e) => { e.stopPropagation(); show(current - 1); });
    if (next) next.addEventListener('click', (e) => { e.stopPropagation(); show(current + 1); });

    // Arrow keys, once the gallery has been interacted with or is in view.
    if (total > 1) {
      document.addEventListener('keydown', (e) => {
        if (e.target.matches('input, textarea, select')) return;

        const box = main.getBoundingClientRect();
        const onScreen = box.top < window.innerHeight && box.bottom > 0;
        if (!onScreen) return;

        if (e.key === 'ArrowLeft')  { show(current - 1, true); }
        if (e.key === 'ArrowRight') { show(current + 1, true); }
      });

      // Swipe on touch devices.
      let startX = 0;
      let startY = 0;
      let tracking = false;

      main.addEventListener('touchstart', (e) => {
        if (e.touches.length !== 1) return;
        startX = e.touches[0].clientX;
        startY = e.touches[0].clientY;
        tracking = true;
      }, { passive: true });

      main.addEventListener('touchend', (e) => {
        if (!tracking) return;
        tracking = false;

        const dx = e.changedTouches[0].clientX - startX;
        const dy = e.changedTouches[0].clientY - startY;

        // Horizontal intent only, so a vertical page scroll is not hijacked.
        if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.5) {
          show(current + (dx < 0 ? 1 : -1));
        }
      }, { passive: true });
    }

    // Click to zoom, but not when the click was on an arrow.
    main.addEventListener('click', (e) => {
      if (e.target.closest('.gallery__arrow')) return;
      main.classList.toggle('is-zoomed');
    });

    // Pan the zoomed image with the pointer.
    main.addEventListener('mousemove', (e) => {
      if (!main.classList.contains('is-zoomed') || !image) return;
      const rect = main.getBoundingClientRect();
      const x = ((e.clientX - rect.left) / rect.width) * 100;
      const y = ((e.clientY - rect.top) / rect.height) * 100;
      image.style.transformOrigin = x + '% ' + y + '%';
    });

    main.addEventListener('mouseleave', () => main.classList.remove('is-zoomed'));
  }

  /* -------------------------------------------------------------------
     Accordions
     ------------------------------------------------------------------- */
  function initAccordions() {
    $$('.accordion__head').forEach((head) => {
      head.addEventListener('click', () => {
        const item = head.closest('.accordion__item');
        const open = item.classList.toggle('is-open');
        head.setAttribute('aria-expanded', String(open));
      });
    });
  }

  /* -------------------------------------------------------------------
     Filter bar: submit on change, debounce the search box
     ------------------------------------------------------------------- */
  function initFilters() {
    const form = $('#filter-form');
    if (!form) return;

    $$('select', form).forEach((select) => {
      select.addEventListener('change', () => {
        // A new filter always returns to page 1.
        const page = $('input[name="page"]', form);
        if (page) page.value = '1';
        form.submit();
      });
    });

    const search = $('input[name="q"]', form);
    if (search) {
      let timer = null;
      search.addEventListener('input', () => {
        window.clearTimeout(timer);
        timer = window.setTimeout(() => form.submit(), 550);
      });
    }
  }

  /* -------------------------------------------------------------------
     Hero carousel: arrows, dots and autoplay over a scroll-snap track

     The track already scrolls and snaps on its own, so everything here is an
     enhancement — without it the hero is still a swipeable row of products.
     ------------------------------------------------------------------- */
  function initHeroCarousel() {
    const root = $('[data-hero-carousel]');
    if (!root) return;

    const track  = $('[data-hero-track]', root);
    const slides = $$('.hero-carousel__slide', root);
    if (!track || slides.length === 0) return;

    const controls = $('[data-hero-controls]', root);
    const dots     = $$('[data-hero-go]', root);
    const status   = $('[data-hero-status]', root);
    const total    = slides.length;

    let current   = 0;
    let timer     = null;
    // Hovering pauses; using an arrow or a dot means the visitor is driving,
    // and autoplay does not come back and fight them for it.
    let cancelled = false;

    // A single slide needs no controls at all.
    if (total > 1 && controls) controls.hidden = false;

    function mark(index) {
      current = index;

      dots.forEach((dot, i) => {
        if (i === index) {
          dot.setAttribute('aria-current', 'true');
        } else {
          dot.removeAttribute('aria-current');
        }
      });

      if (status) status.textContent = 'Slide ' + (index + 1) + ' of ' + total;
    }

    function go(index) {
      // Wrap at both ends so the arrows never dead-end.
      const target = (index + total) % total;

      // Each slide is exactly one track width, so the scroll position is just
      // arithmetic. offsetLeft is measured from the nearest positioned
      // ancestor rather than from the track, which put this in the wrong place.
      track.scrollTo({
        left: target * track.clientWidth,
        behavior: reduceMotion ? 'auto' : 'smooth',
      });
    }

    // Track position from the scroll itself rather than from our own clicks, so
    // a swipe or a keyboard scroll keeps the dots honest.
    if ('IntersectionObserver' in window) {
      const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) mark(slides.indexOf(entry.target));
        });
      }, { root: track, threshold: 0.6 });

      slides.forEach((slide) => observer.observe(slide));
    }

    mark(0);

    function start() {
      if (timer || cancelled || reduceMotion || total < 2) return;
      timer = window.setInterval(() => go(current + 1), 6000);
    }

    function pause() {
      if (timer) { window.clearInterval(timer); timer = null; }
    }

    function cancel() {
      cancelled = true;
      pause();
    }

    $('[data-hero-prev]', root)?.addEventListener('click', () => { cancel(); go(current - 1); });
    $('[data-hero-next]', root)?.addEventListener('click', () => { cancel(); go(current + 1); });

    dots.forEach((dot) => {
      dot.addEventListener('click', () => {
        cancel();
        go(Number(dot.dataset.heroGo));
      });
    });

    root.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowLeft')  { cancel(); go(current - 1); event.preventDefault(); }
      if (event.key === 'ArrowRight') { cancel(); go(current + 1); event.preventDefault(); }
    });

    // Autoplay is a courtesy, not a hijack: it yields while someone is reading
    // or has the tab in the background, and picks up again afterwards.
    root.addEventListener('mouseenter', pause);
    root.addEventListener('mouseleave', start);
    root.addEventListener('focusin', pause);
    root.addEventListener('focusout', start);
    root.addEventListener('touchstart', cancel, { passive: true });

    document.addEventListener('visibilitychange', () => {
      if (document.hidden) { pause(); } else { start(); }
    });

    start();
  }

  /* -------------------------------------------------------------------
     Marquee: duplicate the track so the loop is seamless
     ------------------------------------------------------------------- */
  function initMarquee() {
    const track = $('.marquee__track');
    if (!track || track.dataset.cloned === '1') return;

    track.innerHTML += track.innerHTML;
    track.dataset.cloned = '1';
  }

  /* -------------------------------------------------------------------
     Boot
     ------------------------------------------------------------------- */
  /* -------------------------------------------------------------------
     Saved-horse quick picker (saddle fitting form)
     ------------------------------------------------------------------- */
  function initHorsePicker() {
    const picker = $('[data-horse-picker]');
    if (!picker) return;

    const nameField       = $('#horse_name');
    const detailsField    = $('#horse_details');
    const disciplineField = $('#discipline');

    $$('button[data-horse-name]', picker).forEach((button) => {
      button.addEventListener('click', () => {
        const active = button.classList.contains('is-active');

        $$('button[data-horse-name]', picker).forEach((b) => b.classList.remove('is-active'));

        if (active) {
          // Second click clears the selection.
          if (nameField) nameField.value = '';
          if (detailsField) detailsField.value = '';
          return;
        }

        button.classList.add('is-active');

        if (nameField) nameField.value = button.dataset.horseName || '';
        if (detailsField) detailsField.value = button.dataset.horseDetails || '';

        if (disciplineField && button.dataset.horseDiscipline) {
          disciplineField.value = button.dataset.horseDiscipline;
        }
      });
    });
  }

  /* -------------------------------------------------------------------
     Repair photo drop zone with thumbnails
     ------------------------------------------------------------------- */
  function initPhotoDrop() {
    const zone = $('#photo-drop');
    if (!zone) return;

    const input   = $('input[type="file"]', zone);
    const preview = $('.photo-drop__preview', zone);
    if (!input || !preview) return;

    const MAX = 6;

    const render = () => {
      preview.innerHTML = '';

      const files = Array.from(input.files || []).slice(0, MAX);

      files.forEach((file) => {
        if (!file.type.startsWith('image/')) return;

        const img = document.createElement('img');
        img.alt = '';
        img.src = URL.createObjectURL(file);
        img.addEventListener('load', () => URL.revokeObjectURL(img.src), { once: true });
        preview.appendChild(img);
      });

      if ((input.files || []).length > MAX) {
        const note = document.createElement('p');
        note.className = 'field__hint';
        note.style.width = '100%';
        note.textContent = 'Only the first ' + MAX + ' images will be sent.';
        preview.appendChild(note);
      }
    };

    input.addEventListener('change', render);

    ['dragenter', 'dragover'].forEach((type) => {
      zone.addEventListener(type, (e) => { e.preventDefault(); zone.classList.add('is-over'); });
    });

    ['dragleave', 'drop'].forEach((type) => {
      zone.addEventListener(type, (e) => { e.preventDefault(); zone.classList.remove('is-over'); });
    });

    zone.addEventListener('drop', (e) => {
      if (!e.dataTransfer || !e.dataTransfer.files.length) return;
      input.files = e.dataTransfer.files;
      render();
    });
  }

  /* -------------------------------------------------------------------
     Checkout: live delivery cost in the summary
     ------------------------------------------------------------------- */
  function initCheckout() {
    const form = $('#checkout-form');
    if (!form) return;

    const fields   = $('#delivery-fields');
    const deliveryEl = $('#summary-delivery');
    const totalEl    = $('#summary-total');

    const subtotal = parseFloat(form.dataset.subtotal || '0');
    const freeOver = parseFloat(form.dataset.freeOver || '0');
    const costs = {
      collect: 0,
      nairobi: parseFloat(form.dataset.nairobi || '0'),
      courier: parseFloat(form.dataset.courier || '0'),
    };

    const currency = (n) => 'KSh ' + Math.round(n).toLocaleString('en-KE');

    const update = () => {
      const chosen = $('input[name="delivery_method"]:checked', form);
      const method = chosen ? chosen.value : 'collect';

      let cost = costs[method] || 0;
      if (freeOver > 0 && subtotal >= freeOver) cost = 0;

      if (fields) fields.hidden = method === 'collect';
      if (deliveryEl) deliveryEl.textContent = cost > 0 ? currency(cost) : 'Free';
      if (totalEl) totalEl.textContent = currency(subtotal + cost);
    };

    $$('input[name="delivery_method"]', form).forEach((radio) => {
      radio.addEventListener('change', update);
    });

    update();
  }

  /* -------------------------------------------------------------------
     Payment page: poll until M-Pesa clears
     ------------------------------------------------------------------- */
  function initPaymentPolling() {
    const panel = $('#pay-waiting');
    if (!panel || !window.fetch) return;

    const url = panel.dataset.statusUrl;
    if (!url) return;

    const strong = $('strong', panel);
    const text   = $('p', panel);

    let attempts = 0;
    const MAX_ATTEMPTS = 40;   // ~2 minutes at 3s
    const INTERVAL = 3000;

    const stop = (state, heading, message) => {
      panel.classList.add(state);
      if (strong) strong.textContent = heading;
      if (text) text.textContent = message;
    };

    const poll = async () => {
      attempts++;

      if (attempts > MAX_ATTEMPTS) {
        stop('is-failed', 'Still waiting',
          'We have not had confirmation yet. If you completed the payment it will appear shortly — ' +
          'reload this page, or call us and quote your order reference.');
        return;
      }

      try {
        const response = await fetch(url, {
          headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
          credentials: 'same-origin',
          cache: 'no-store',
        });

        const data = await response.json();

        if (data.ok && data.payment_status === 'paid' && data.redirect) {
          stop('is-done', 'Payment received', 'Taking you to your confirmation…');
          window.setTimeout(() => { window.location.href = data.redirect; }, 900);
          return;
        }

        const latest = data.latest;

        if (latest && (latest.status === 'failed' || latest.status === 'cancelled')) {
          stop('is-failed', latest.status === 'cancelled' ? 'Payment cancelled' : 'Payment failed',
            latest.message || 'The prompt was not completed. You can try again below.');
          return;
        }
      } catch (err) {
        // Network hiccup — keep trying quietly.
      }

      window.setTimeout(poll, INTERVAL);
    };

    window.setTimeout(poll, INTERVAL);
  }

  function boot() {
    initHeader();
    initNav();
    initReveal();
    initFlashes();
    initQuoteForms();
    initQty();
    initGallery();
    initHeroCarousel();
    initAccordions();
    initFilters();
    initMarquee();
    initHorsePicker();
    initPhotoDrop();
    initCheckout();
    initPaymentPolling();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
