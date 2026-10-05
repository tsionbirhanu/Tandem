<?php
// app/Views/auth/register.php
$pageTitle = 'Join Tandem';
include BASE_PATH . '/app/Views/layouts/header.php';

$artHeading = 'Hire someone good, <em>or be someone good to hire.</em>';
$artQuote   = $artQuote ?? null;
$role       = $role ?? 'client';
?>

<main class="auth" style="display: flex; align-items: center; justify-content: center; min-height: calc(100vh - 120px); padding: 16px; background: linear-gradient(135deg, rgba(0,0,0,0.02) 0%, rgba(0,0,0,0.06) 100%);">
  <div class="auth-form" style="width: 100%; max-width: 520px;">
    <div class="auth-form-inner contact-form-card" style="border: 1px solid rgba(0,0,0,0.04); background: #fff;">
      <div style="text-align: center; margin-bottom: 24px;">
        <span class="eyebrow" style="color: var(--accent);">Join Tandem</span>
        <h1 style="font-family: var(--font-display); font-size: 2.2rem; margin: 0 0 4px;">Make an account.</h1>
        <p class="muted" style="margin: 0;">Already have one? <a class="link" href="/login" style="font-weight: 600;">Log in</a>.</p>
      </div>

      <?php if (isset($errors['global'])): ?>
        <div class="alert" role="alert" style="margin-bottom: 24px; border-radius: var(--r-md);"><?= e($errors['global']) ?></div>
      <?php endif; ?>

      <form action="/register" method="POST" novalidate x-data="{ role: '<?= e($role) ?>', pw: '', confirm: '' }">
        <fieldset class="field" style="border: 0; padding: 0; margin: 0 0 32px;">
          <legend class="label" style="font-weight: 600; margin-bottom: 12px; text-align: center; width: 100%;">I'm here to…</legend>
          <div class="choices" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
            <label class="choice" style="margin: 0; transition: transform 0.2s; border-radius: var(--r-md);">
              <input type="radio" name="role" value="client" x-model="role" <?= $role === 'client' ? 'checked' : '' ?>>
              <span class="choice-body" style="height: 100%; padding: 20px 16px; text-align: center; border-radius: var(--r-md); background: rgba(0,0,0,0.02);">
                <span class="choice-tick"></span>
                <svg width="32" height="32" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true" style="margin: 0 auto; color: var(--accent);"><circle cx="12" cy="12" r="7"/><path d="M17 17l7 7"/></svg>
                <span class="choice-title mt-12" style="font-size: 1.1rem; font-weight: 600;">Hire</span>
                <span class="choice-sub" style="font-size: 0.85rem;">Find someone for a project</span>
              </span>
            </label>
            <label class="choice" style="margin: 0; transition: transform 0.2s; border-radius: var(--r-md);">
              <input type="radio" name="role" value="freelancer" x-model="role" <?= $role === 'freelancer' ? 'checked' : '' ?>>
              <span class="choice-body" style="height: 100%; padding: 20px 16px; text-align: center; border-radius: var(--r-md); background: rgba(0,0,0,0.02);">
                <span class="choice-tick"></span>
                <svg width="32" height="32" viewBox="0 0 28 28" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="margin: 0 auto; color: var(--accent);"><path d="M5 23l3-9L19 3l6 6-11 11z"/><path d="M16 6l6 6"/></svg>
                <span class="choice-title mt-12" style="font-size: 1.1rem; font-weight: 600;">Work</span>
                <span class="choice-sub" style="font-size: 0.85rem;">Offer my skills as a freelancer</span>
              </span>
            </label>
          </div>
        </fieldset>

        <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>" style="margin-bottom: 20px;">
          <label class="label" for="name" style="font-weight: 600;">Your name</label>
          <input class="input" type="text" id="name" name="name" value="<?= e($name ?? '') ?>" autocomplete="name" placeholder="Jane Doe" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
          <?= fieldError($errors ?? [], 'name') ?>
        </div>

        <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>" style="margin-bottom: 20px;">
          <label class="label" for="email" style="font-weight: 600;">Email</label>
          <input class="input" type="email" id="email" name="email" value="<?= e($email ?? '') ?>" autocomplete="email" placeholder="you@example.com" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
          <?= fieldError($errors ?? [], 'email') ?>
        </div>

        <div class="field <?= isset($errors['password']) ? 'has-error' : '' ?>" style="margin-bottom: 20px;" x-data="passwordField()" x-effect="value = pw">
          <label class="label" for="password" style="font-weight: 600;">Password <span class="hint" x-text="strengthLabel"></span></label>
          <div class="input-wrap" style="position: relative;">
            <input class="input" :type="show ? 'text' : 'password'" type="password" id="password" name="password" autocomplete="new-password"
                   placeholder="At least 8 characters" x-model="pw" @keyup="checkCaps($event)" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s; width: 100%;">
            <button type="button" class="peek" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--muted);">
              <svg width="18" height="18" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M1.5 10S5 3.5 10 3.5 18.5 10 18.5 10 15 16.5 10 16.5 1.5 10 1.5 10z"/><circle cx="10" cy="10" r="2.6"/></svg>
            </button>
          </div>
          <div class="strength" aria-hidden="true" style="margin-top: 8px;">
            <template x-for="n in 4"><i :style="n <= strength ? 'background:' + strengthColor : ''"></i></template>
          </div>
          <p class="caps" x-show="caps" x-cloak style="color: var(--danger); font-size: 0.85rem; margin-top: 4px;">Caps Lock is on</p>
          <?= fieldError($errors ?? [], 'password') ?>
        </div>

        <div class="field <?= isset($errors['confirm_password']) ? 'has-error' : '' ?>" style="margin-bottom: 32px;">
          <label class="label" for="confirm_password" style="font-weight: 600; display: flex; justify-content: space-between;">
            Confirm password
            <span class="hint" x-show="confirm.length > 0" x-cloak
                  :style="confirm === pw ? 'color: var(--moss)' : 'color: var(--danger)'"
                  x-text="confirm === pw ? '✓ matches' : 'doesn\'t match yet'"></span>
          </label>
          <input class="input" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" placeholder="Type it again" x-model="confirm" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
          <?= fieldError($errors ?? [], 'confirm_password') ?>
        </div>

        <button type="submit" class="btn btn-accent btn-block btn-lg btn-glow" style="padding: 14px 24px; font-weight: 600; font-size: 1.1rem; border-radius: var(--r-md); width: 100%;">
          <span class="spinner"></span>
          <span x-text="role === 'freelancer' ? 'Create account & start listing' : 'Create account'">Create account</span>
        </button>
      </form>
    </div>
  </div>
</main>

</body>
</html>
