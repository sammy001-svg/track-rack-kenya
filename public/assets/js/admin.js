/* =====================================================================
   TACK RACK KENYA — admin console behaviour
   ===================================================================== */
(function () {
  'use strict';

  const $  = (s, c = document) => c.querySelector(s);
  const $$ = (s, c = document) => Array.from(c.querySelectorAll(s));

  /* --- Sidebar (mobile) --- */
  function initSidebar() {
    const burger = $('#a-burger');
    const scrim  = $('#a-scrim');

    const toggle = (force) => {
      const open = typeof force === 'boolean' ? force : !document.body.classList.contains('a-nav-open');
      document.body.classList.toggle('a-nav-open', open);
    };

    if (burger) burger.addEventListener('click', () => toggle());
    if (scrim)  scrim.addEventListener('click', () => toggle(false));

    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') toggle(false);
    });
  }

  /* --- Flash dismissal --- */
  function initFlashes() {
    $$('.a-flash').forEach((flash) => {
      const close = $('button', flash);
      if (close) close.addEventListener('click', () => flash.remove());
      window.setTimeout(() => flash.remove(), 7000);
    });
  }

  /* --- Confirm destructive actions --- */
  function initConfirm() {
    document.addEventListener('submit', (e) => {
      const form = e.target;
      const message = form.dataset.confirm;
      if (message && !window.confirm(message)) e.preventDefault();
    });
  }

  /* --- Auto-slug from a name field --- */
  function slugify(text) {
    return text.toLowerCase()
      .normalize('NFD').replace(/[̀-ͯ]/g, '')
      .replace(/[^a-z0-9]+/g, '-')
      .replace(/^-+|-+$/g, '');
  }

  function initSlug() {
    const source = $('[data-slug-source]');
    const target = $('[data-slug-target]');
    if (!source || !target) return;

    // Only track the name while the slug has not been hand-edited.
    let linked = target.value.trim() === '' || target.dataset.linked === '1';

    target.addEventListener('input', () => { linked = false; });

    source.addEventListener('input', () => {
      if (linked) target.value = slugify(source.value);
    });
  }

  /* --- Filter bar: submit on change --- */
  function initFilters() {
    $$('form[data-auto-submit]').forEach((form) => {
      $$('select', form).forEach((select) => {
        select.addEventListener('change', () => {
          const page = $('input[name="page"]', form);
          if (page) page.value = '1';
          form.submit();
        });
      });

      const search = $('input[type="search"]', form);
      if (search) {
        let timer = null;
        search.addEventListener('input', () => {
          window.clearTimeout(timer);
          timer = window.setTimeout(() => form.submit(), 550);
        });
      }
    });
  }

  /* --- File drop zone --- */
  function initDropzones() {
    $$('.a-drop').forEach((zone) => {
      const input = $('input[type="file"]', zone);
      const list  = $('.a-drop__list', zone);
      if (!input) return;

      const describe = (files) => {
        if (!list) return;
        if (!files || !files.length) { list.textContent = ''; return; }
        list.textContent = files.length === 1
          ? files[0].name
          : files.length + ' files selected';
      };

      zone.addEventListener('click', (e) => {
        if (e.target !== input) input.click();
      });

      input.addEventListener('change', () => describe(input.files));

      ['dragenter', 'dragover'].forEach((type) => {
        zone.addEventListener(type, (e) => {
          e.preventDefault();
          zone.classList.add('is-over');
        });
      });

      ['dragleave', 'drop'].forEach((type) => {
        zone.addEventListener(type, (e) => {
          e.preventDefault();
          zone.classList.remove('is-over');
        });
      });

      zone.addEventListener('drop', (e) => {
        if (!e.dataTransfer || !e.dataTransfer.files.length) return;
        input.files = e.dataTransfer.files;
        describe(input.files);
      });
    });
  }

  /* --- Variant repeater --- */
  function initRepeater() {
    const wrap = $('#variant-rows');
    const add  = $('#variant-add');
    if (!wrap || !add) return;

    const buildRow = () => {
      const row = document.createElement('div');
      row.className = 'a-repeat-row';
      row.innerHTML =
        '<input class="a-input" type="text" name="variant_label[]" placeholder="Size" maxlength="80">' +
        '<input class="a-input" type="text" name="variant_value[]" placeholder="17.5 in" maxlength="120">' +
        '<button class="a-icon-btn a-icon-btn--danger" type="button" data-remove-row aria-label="Remove">' +
          '<svg width="13" height="13" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>' +
        '</button>';
      return row;
    };

    add.addEventListener('click', () => {
      const row = buildRow();
      wrap.appendChild(row);
      const first = $('input', row);
      if (first) first.focus();
    });

    wrap.addEventListener('click', (e) => {
      const button = e.target.closest('[data-remove-row]');
      if (button) button.closest('.a-repeat-row').remove();
    });
  }

  /* --- Warn before leaving a form with unsaved edits --- */
  function initDirtyGuard() {
    const form = $('form[data-dirty-guard]');
    if (!form) return;

    let dirty = false;

    form.addEventListener('input', () => { dirty = true; });
    form.addEventListener('change', () => { dirty = true; });
    form.addEventListener('submit', () => { dirty = false; });

    window.addEventListener('beforeunload', (e) => {
      if (!dirty) return;
      e.preventDefault();
      e.returnValue = '';
    });
  }

  /* --- Live Google-result preview on the SEO panel --- */
  function initSerpPreview() {
    const panel = $('#seo-panel');
    if (!panel) return;

    const titleField = $('[data-seo-title]', panel);
    const descField  = $('[data-seo-desc]', panel);
    const nameField  = $('[data-slug-source]');
    const slugField  = $('[data-slug-target]');

    const outTitle = $('[data-serp-title]', panel);
    const outDesc  = $('[data-serp-desc]', panel);
    const outUrl   = $('[data-serp-url]', panel);

    const suffix = panel.dataset.seoSuffix || '';
    const host   = panel.dataset.seoHost || '';

    // Google truncates around these lengths.
    const TITLE_MAX = 60;
    const DESC_MAX  = 158;
    const DESC_MIN  = 70;

    function meter(field, length, max, min) {
      const wrap = field.closest('.a-field');
      if (!wrap) return;

      const bar   = $('.a-serp__bar', wrap);
      const fill  = bar ? $('i', bar) : null;
      const count = $('[data-seo-title-count], [data-seo-desc-count]', wrap);

      if (fill) fill.style.width = Math.min(100, (length / max) * 100) + '%';

      if (bar) {
        bar.classList.toggle('is-over', length > max);
        bar.classList.toggle('is-short', min !== undefined && length > 0 && length < min);
      }

      if (count) {
        count.textContent = length + ' / ' + max
          + (length > max ? ' — will be cut off' : '');
      }
    }

    function update() {
      const rawTitle = (titleField.value.trim())
        || (nameField ? nameField.value.trim() : '')
        || titleField.dataset.seoFallback
        || 'Product name';

      const full = suffix && rawTitle !== suffix ? rawTitle + ' | ' + suffix : rawTitle;

      const rawDesc = (descField.value.trim())
        || descField.dataset.seoFallback
        || 'A short description helps this product earn clicks from search results.';

      if (outTitle) outTitle.textContent = full;
      if (outDesc)  outDesc.textContent  = rawDesc;

      if (outUrl) {
        const slug = slugField && slugField.value.trim()
          ? slugField.value.trim()
          : (panel.dataset.seoPath || '').replace('/product/', '');
        outUrl.textContent = host + ' › product › ' + slug;
      }

      meter(titleField, full.length, TITLE_MAX);
      meter(descField, rawDesc.length, DESC_MAX, DESC_MIN);
    }

    [titleField, descField, nameField, slugField].forEach((field) => {
      if (field) field.addEventListener('input', update);
    });

    update();
  }

  function boot() {
    initSidebar();
    initFlashes();
    initConfirm();
    initSlug();
    initFilters();
    initDropzones();
    initRepeater();
    initDirtyGuard();
    initSerpPreview();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
