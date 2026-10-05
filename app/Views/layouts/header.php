<?php
// app/Views/layouts/header.php
// Global header: <head>, top navigation and the flash-message toast stack.

$user = currentUser();
$pageTitle = isset($pageTitle) ? $pageTitle . ' · Tandem' : 'Tandem — good work, done together';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <meta name="description" content="Tandem connects clients with independent designers, developers and writers.">
  <meta name="theme-color" content="#ffffff">
  <link rel="icon" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='8' fill='%23e0532f'/%3E%3Ccircle cx='10' cy='19' r='5' fill='none' stroke='%23fff' stroke-width='2.4'/%3E%3Ccircle cx='22' cy='19' r='5' fill='none' stroke='%23fff' stroke-width='2.4'/%3E%3C/svg%3E">

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght,SOFT,WONK@0,9..144,300..700,0..100,0..1;1,9..144,300..700,0..100,0..1&family=Instrument+Sans:ital,wght@0,400..700;1,400..700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/app.css?v=<?= @filemtime(BASE_PATH . '/public/assets/css/app.css') ?>">

  <script src="/assets/js/app.js?v=<?= @filemtime(BASE_PATH . '/public/assets/js/app.js') ?>" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/htmx.org@2.0.4/dist/htmx.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.9/dist/cdn.min.js" defer></script>
</head>
<body>
  <div class="loading-bar" id="loading-bar"></div>

  <header class="nav" x-data="{ mobile: false }" @keydown.escape.window="mobile = false">
    <div class="wrap nav-inner">
      <a href="/" class="logo" aria-label="Tandem home">
        <svg class="logo-mark" viewBox="0 0 38 22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
          <g class="wheel"><circle cx="8" cy="15" r="6"/><path d="M8 9v12M2 15h12" stroke-width="1"/></g>
          <g class="wheel"><circle cx="30" cy="15" r="6"/><path d="M30 9v12M24 15h12" stroke-width="1"/></g>
          <path d="M8 15 L14 5 H25 L30 15 M14 5 L19 15 H8 M19 15 L25 5" stroke="#e0532f"/>
        </svg>
        <span class="logo-word">tandem</span>
      </a>

      <nav class="nav-links" aria-label="Main">
        <a class="nav-link<?= navActive('/services') ?>" href="/services">Browse work</a>
        <a class="nav-link" href="/#how">How it works</a>
        <a class="nav-link<?= navActive('/contact') ?>" href="/contact">Contact</a>
      </nav>

      <div class="nav-actions">
        <?php if ($user): ?>
          <?php if ($user->isFreelancer()): ?>
            <a href="/services/create" class="btn btn-sm hide-sm">
              <svg width="14" height="14" viewBox="0 0 14 14" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path d="M7 2v10M2 7h10"/></svg>
              New service
            </a>
          <?php endif; ?>

          <div class="hide-sm" style="position: relative;" x-data="{ open: false }" @click.outside="open = false" @keydown.escape="open = false">
            <button type="button" class="user-chip" @click="open = !open" :aria-expanded="open" aria-haspopup="menu">
              <?= avatar($user->getAvatarUrl(), $user->getName(), 'sm') ?>
              <span class="small" style="font-weight: 600;"><?= e(explode(' ', $user->getName())[0]) ?></span>
              <svg class="chev" width="10" height="7" viewBox="0 0 10 7" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M1 1.5l4 4 4-4"/></svg>
            </button>
            <div class="menu" role="menu" x-cloak x-show="open" x-transition.origin.top.right.duration.150ms>
              <div class="menu-head">
                <b style="display:block;"><?= e($user->getName()) ?></b>
                <span class="small muted"><?= e($user->getEmail()) ?></span>
              </div>
              <a href="<?= e($user->getDashboardUrl()) ?>" role="menuitem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="1.5" y="1.5" width="5" height="5" rx="1"/><rect x="9.5" y="1.5" width="5" height="5" rx="1"/><rect x="1.5" y="9.5" width="5" height="5" rx="1"/><rect x="9.5" y="9.5" width="5" height="5" rx="1"/></svg>
                Dashboard
              </a>
              <?php if ($user->isFreelancer()): ?>
                <a href="/freelancer/<?= (int)$user->getId() ?>" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="8" cy="5.5" r="3"/><path d="M2.5 14.5c.8-3 3-4.5 5.5-4.5s4.7 1.5 5.5 4.5"/></svg>
                  My public profile
                </a>
              <?php endif; ?>
              <a href="/profile/edit" role="menuitem">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M10.5 2.5l3 3L5 14H2v-3z"/></svg>
                Edit profile
              </a>
              <form action="/logout" method="POST" style="border-top: 1px solid var(--rule); margin-top: 6px; padding-top: 6px;">
                <button type="submit" class="danger" role="menuitem">
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M6 14H3V2h3M10.5 11L14 8l-3.5-3M14 8H6"/></svg>
                  Log out
                </button>
              </form>
            </div>
          </div>
        <?php else: ?>
          <a href="/login" class="nav-link hide-sm">Log in</a>
          <a href="/register" class="btn btn-sm btn-accent hide-sm">Join Tandem</a>
        <?php endif; ?>

        <button type="button" class="btn btn-sm btn-ghost nav-burger" @click="mobile = !mobile" :aria-expanded="mobile" aria-label="Menu">
          <svg x-show="!mobile" width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 6h14M3 10h10M3 14h14"/></svg>
          <svg x-show="mobile" x-cloak width="20" height="20" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M5 5l10 10M15 5L5 15"/></svg>
        </button>
      </div>
    </div>

    <div class="wrap mobile-nav" x-cloak x-show="mobile" x-transition.opacity>
      <a href="/services">Browse work</a>
      <a href="/#how" @click="mobile = false">How it works</a>
      <a href="/contact">Contact</a>
      <?php if ($user): ?>
        <a href="<?= e($user->getDashboardUrl()) ?>">Dashboard</a>
        <?php if ($user->isFreelancer()): ?><a href="/services/create">New service</a><?php endif; ?>
        <a href="/profile/edit">Edit profile</a>
        <form action="/logout" method="POST"><button type="submit" class="btn btn-sm mt-16">Log out</button></form>
      <?php else: ?>
        <a href="/login">Log in</a>
        <a href="/register" style="color: var(--accent-deep); font-weight: 600;">Join Tandem →</a>
      <?php endif; ?>
    </div>
  </header>

  <!-- Flash messages, shown as toasts -->
  <div class="toasts" x-data='toasts(<?= json_encode(flashMessages(), JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP) ?>)' aria-live="polite">
    <template x-for="t in items" :key="t.id">
      <div class="toast" :class="'is-' + t.type"
           x-transition:enter.duration.250ms x-transition:leave.duration.200ms
           x-init="$el.animate([{ transform: 'translateY(16px) rotate(-2deg)', opacity: 0 }, { transform: 'none', opacity: 1 }], { duration: 320, easing: 'cubic-bezier(.2,.8,.2,1)' })">
        <span class="toast-icon" x-text="t.type === 'error' ? '!' : (t.type === 'info' ? 'i' : '✓')"></span>
        <span x-text="t.text"></span>
        <button type="button" @click="dismiss(t.id)" aria-label="Dismiss">×</button>
        <span class="toast-timer"></span>
      </div>
    </template>
  </div>
