// public/assets/js/app.js
// Shared front-end behaviour. Alpine components are registered on `alpine:init`,
// so this file must load before Alpine itself.

(() => {
  'use strict';

  /* ------------------------------------------------------------------------
     Scroll reveal: elements with .reveal fade up the first time they appear
     ------------------------------------------------------------------------ */
  const revealObserver = 'IntersectionObserver' in window
    ? new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
          if (!entry.isIntersecting) return;
          const delay = Number(entry.target.dataset.delay || 0);
          setTimeout(() => entry.target.classList.add('is-in'), delay);
          revealObserver.unobserve(entry.target);
        });
      }, { rootMargin: '0px 0px -8% 0px' })
    : null;

  function initReveal(root = document) {
    root.querySelectorAll('.reveal:not(.is-in)').forEach((el) => {
      revealObserver ? revealObserver.observe(el) : el.classList.add('is-in');
    });
  }

  /* ------------------------------------------------------------------------
     Count-up numbers: <span data-count="1250" data-prefix="$">0</span>
     ------------------------------------------------------------------------ */
  function initCounters(root = document) {
    root.querySelectorAll('[data-count]:not([data-counted])').forEach((el) => {
      el.dataset.counted = '1';
      const target = parseFloat(el.dataset.count) || 0;
      const decimals = Number(el.dataset.decimals || 0);
      const prefix = el.dataset.prefix || '';
      const suffix = el.dataset.suffix || '';
      const format = (n) => prefix + n.toLocaleString(undefined, { minimumFractionDigits: decimals, maximumFractionDigits: decimals }) + suffix;

      if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || target === 0) {
        el.textContent = format(target);
        return;
      }
      const duration = 900;
      const start = performance.now();
      const tick = (now) => {
        const p = Math.min(1, (now - start) / duration);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = format(target * eased);
        if (p < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    });
  }

  /* ------------------------------------------------------------------------
     Submit buttons show a spinner and lock while the form posts
     ------------------------------------------------------------------------ */
  document.addEventListener('submit', (event) => {
    const form = event.target;
    if (!(form instanceof HTMLFormElement) || form.hasAttribute('hx-get')) return;
    const button = form.querySelector('[type="submit"]');
    if (button) {
      // Defer so the button's own value is still submitted
      setTimeout(() => button.classList.add('is-loading'), 0);
    }
  });
  // Restore buttons when the page comes back from the bfcache
  window.addEventListener('pageshow', () => {
    document.querySelectorAll('.btn.is-loading').forEach((b) => b.classList.remove('is-loading'));
  });

  /* ------------------------------------------------------------------------
     "/" focuses the first search box on the page
     ------------------------------------------------------------------------ */
  document.addEventListener('keydown', (event) => {
    if (event.key !== '/' || event.metaKey || event.ctrlKey) return;
    const tag = document.activeElement?.tagName;
    if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT') return;
    const search = document.querySelector('[data-hotkey="/"]');
    if (search) {
      event.preventDefault();
      search.focus();
      search.select?.();
    }
  });

  // Keep live-filter URLs tidy: drop empty fields (?min_price=&max_price=…) before htmx sends them
  document.addEventListener('htmx:configRequest', (event) => {
    const params = event.detail.parameters;
    for (const key of Array.from(params.keys ? params.keys() : Object.keys(params))) {
      const value = params.get ? params.get(key) : params[key];
      if (value === '' || (key === 'sort' && value === 'newest')) {
        params.delete ? params.delete(key) : delete params[key];
      }
    }
  });

  function initPage(root = document) {
    initReveal(root);
    initCounters(root);
  }
  document.addEventListener('DOMContentLoaded', () => initPage());
  document.addEventListener('htmx:afterSwap', (e) => initPage(e.detail.target));

  /* ------------------------------------------------------------------------
     Alpine components
     ------------------------------------------------------------------------ */
  document.addEventListener('alpine:init', () => {
    const Alpine = window.Alpine;

    // Toast stack fed by server flash messages
    Alpine.data('toasts', (initial = []) => ({
      items: [],
      init() {
        initial.forEach((t, i) => setTimeout(() => this.push(t.type, t.text), 150 + i * 120));
      },
      push(type, text) {
        const id = Date.now() + Math.random();
        this.items.push({ id, type, text });
        setTimeout(() => this.dismiss(id), 5200);
      },
      dismiss(id) {
        this.items = this.items.filter((t) => t.id !== id);
      },
    }));

    // Password input with show/hide, caps-lock hint and optional strength meter
    Alpine.data('passwordField', () => ({
      show: false,
      caps: false,
      value: '',
      get strength() {
        const v = this.value;
        let score = 0;
        if (v.length >= 8) score++;
        if (v.length >= 12) score++;
        if (/[A-Z]/.test(v) && /[a-z]/.test(v)) score++;
        if (/\d/.test(v) && /[^A-Za-z0-9]/.test(v)) score++;
        return v ? Math.max(1, score) : 0;
      },
      get strengthLabel() {
        return ['', 'Too easy to guess', 'Getting there', 'Solid', 'Strong'][this.strength];
      },
      get strengthColor() {
        return ['var(--rule)', 'var(--danger)', 'var(--butter)', 'var(--moss)', 'var(--moss)'][this.strength];
      },
      checkCaps(e) {
        this.caps = typeof e.getModifierState === 'function' && e.getModifierState('CapsLock');
      },
    }));

    // Multi-image dropzone that keeps a real <input type="file"> in sync
    // `limit` is reactive so the edit form can lower it as existing images are kept.
    Alpine.data('dropzone', (max = 5, maxMb = 5) => ({
      files: [],
      limit: max,
      over: false,
      error: '',
      add(fileList) {
        this.error = '';
        const allowed = ['image/jpeg', 'image/png', 'image/webp'];
        for (const file of Array.from(fileList)) {
          if (this.files.length >= this.limit) {
            this.error = `That's the limit — ${max} images per service.`;
            break;
          }
          if (!allowed.includes(file.type)) {
            this.error = `"${file.name}" isn't a JPG, PNG or WebP.`;
            continue;
          }
          if (file.size > maxMb * 1024 * 1024) {
            this.error = `"${file.name}" is over ${maxMb} MB.`;
            continue;
          }
          this.files.push({ file, url: URL.createObjectURL(file), id: crypto.randomUUID?.() ?? String(Math.random()) });
        }
        this.sync();
      },
      remove(id) {
        const item = this.files.find((f) => f.id === id);
        if (item) URL.revokeObjectURL(item.url);
        this.files = this.files.filter((f) => f.id !== id);
        this.sync();
      },
      drop(e) {
        this.over = false;
        this.add(e.dataTransfer.files);
      },
      sync() {
        const dt = new DataTransfer();
        this.files.forEach((f) => dt.items.add(f.file));
        this.$refs.input.files = dt.files;
      },
    }));

    // Single-image picker with live preview (avatar)
    Alpine.data('imagePicker', (current = '') => ({
      preview: current,
      over: false,
      pick(fileList) {
        const file = fileList?.[0];
        if (!file || !file.type.startsWith('image/')) return;
        this.preview = URL.createObjectURL(file);
        const dt = new DataTransfer();
        dt.items.add(file);
        this.$refs.input.files = dt.files;
      },
    }));

    // Image gallery with thumbnails, arrow keys and a lightbox
    Alpine.data('gallery', (count = 0) => ({
      i: 0,
      open: false,
      count,
      go(n) { this.i = (n + this.count) % this.count; },
      next() { this.go(this.i + 1); },
      prev() { this.go(this.i - 1); },
    }));

    // Interactive 1–5 star picker bound to a hidden input
    Alpine.data('starPicker', (initial = 5) => ({
      value: initial,
      hover: 0,
      words: ['', 'Rough going', 'It was okay', 'Good work', 'Really good', 'Outstanding'],
      get shown() { return this.hover || this.value; },
    }));

    // Character counter for textareas: x-data="charCount(20, 1000)"
    Alpine.data('charCount', (min = 0, max = 0, initial = '') => ({
      text: initial,
      get n() { return this.text.length; },
      get state() {
        if (max && this.n > max) return 'is-bad';
        if (min && this.n < min) return this.n ? 'is-bad' : '';
        return min ? 'is-good' : '';
      },
      get label() {
        if (min && this.n < min) return `${this.n} / ${min} min`;
        return max ? `${this.n} / ${max}` : `${this.n}`;
      },
    }));
  });
})();
