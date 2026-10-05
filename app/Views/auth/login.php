<?php
// app/Views/auth/login.php
$pageTitle = 'Log in';
include BASE_PATH . '/app/Views/layouts/header.php';

$artHeading = 'Welcome back. <em>Your projects missed you.</em>';
$artQuote   = $artQuote ?? null;
$showDemo   = getenv('APP_ENV') !== 'production';
$demoAccounts = [
    ['Client',     'sarah.j@acmelabs.io'],
    ['Freelancer', 'david.chen@devstudio.io'],
    ['Admin',      'admin@tandem.network'],
];
?>

<main class="auth" style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 16px; background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.06) 100%);">
  <div class="auth-form" style="width: 100%; max-width: 520px;">
    <div class="auth-form-inner contact-form-card" style="border: 1px solid rgba(0,0,0,0.04); background: #fff;" x-data="{ email: <?= e(json_encode($email ?? '')) ?> }">
      <div style="text-align: center; margin-bottom: 24px;">
        <h1 style="font-family: var(--font-display); font-size: 2.2rem; margin: 0 0 4px;">Good to see you.</h1>
        <p class="muted" style="margin: 0;">New here? <a class="link" href="/register" style="font-weight: 600;">Make an account</a></p>
      </div>

      <?php if (isset($errors['login'])): ?>
        <div class="alert" role="alert" style="margin-bottom: 24px; border-radius: var(--r-md);">
          <strong>That didn't work.</strong>&nbsp;<?= e($errors['login']) ?>
        </div>
      <?php endif; ?>

      <form action="/login" method="POST" novalidate x-ref="form">
        <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>" style="margin-bottom: 20px;">
          <label class="label" for="email" style="font-weight: 600;">Email</label>
          <input class="input" type="email" id="email" name="email" x-model="email" autocomplete="email" placeholder="you@example.com" <?= empty($email) ? 'autofocus' : '' ?> style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
          <?= fieldError($errors ?? [], 'email') ?>
        </div>

        <div class="field <?= isset($errors['password']) ? 'has-error' : '' ?>" style="margin-bottom: 32px;" x-data="passwordField()">
          <label class="label" for="password" style="font-weight: 600;">Password</label>
          <div class="input-wrap" style="position: relative;">
            <input class="input" :type="show ? 'text' : 'password'" type="password" id="password" name="password" <?= !empty($email) ? 'autofocus' : '' ?> autocomplete="current-password"
                   placeholder="Your password" @keyup="checkCaps($event)" @keydown="checkCaps($event)" x-ref="pw" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s; width: 100%;">
            <button type="button" class="peek" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted);">
              <svg x-show="!show" width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M1.5 10S5 3.5 10 3.5 18.5 10 18.5 10 15 16.5 10 16.5 1.5 10 1.5 10z"/><circle cx="10" cy="10" r="2.6"/></svg>
              <svg x-show="show" x-cloak width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M3 3l14 14M8.2 4C8.8 3.7 9.4 3.5 10 3.5c5 0 8.5 6.5 8.5 6.5a15 15 0 01-2.6 3.3M5.5 6.2A15 15 0 001.5 10S5 16.5 10 16.5c1.4 0 2.6-.4 3.7-1"/></svg>
            </button>
          </div>
          <p class="caps" x-show="caps" x-cloak style="color: var(--danger); font-size: 0.85rem; margin-top: 4px;">Caps Lock is on</p>
          <?= fieldError($errors ?? [], 'password') ?>
        </div>

        <button type="submit" class="btn btn-ink btn-block btn-lg btn-glow" style="padding: 14px 24px; font-weight: 600; font-size: 1.1rem; border-radius: var(--r-md); width: 100%;">
          <span class="spinner"></span>
          Log in
        </button>
      </form>

      <?php if ($showDemo): ?>
        <div class="demo-logins" style="margin-top: 32px; padding-top: 24px; border-top: 1px solid rgba(0,0,0,0.05);">
          <p class="filter-title" style="margin-bottom: 12px; font-weight: 600;">Demo accounts <span class="mono muted" style="font-weight: normal; margin-left: 8px;">(password: Password123!)</span></p>
          <div style="display: flex; flex-direction: column; gap: 8px;">
          <?php foreach ($demoAccounts as [$role, $demoEmail]): ?>
            <button type="button" class="btn btn-ghost" style="justify-content: space-between; border: 1px solid rgba(0,0,0,0.05); padding: 10px 16px; border-radius: var(--r-md);" @click="email = '<?= e($demoEmail) ?>'; $nextTick(() => { $refs.form.querySelector('#password').value = 'Password123!'; $refs.form.requestSubmit(); })">
              <span style="font-weight: 600;"><?= e($role) ?></span>
              <span class="mono muted" style="font-size: 0.85rem;"><?= e($demoEmail) ?> →</span>
            </button>
          <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

</body>
</html>
