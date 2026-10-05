<?php
// app/Views/messages/contact.php
$pageTitle = 'Contact';
include BASE_PATH . '/app/Views/layouts/header.php';

// Prefill from the logged-in account on first visit
$me = currentUser();
if ($me && empty($errors) && empty($isSuccess)) {
    $name  = $name  ?: $me->getName();
    $email = $email ?: $me->getEmail();
}
?>

<section style="position: relative; width: 100%; height: 320px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 48px; border-bottom: 1px solid rgba(0,0,0,0.1);">
  <img src="/assets/img/contact-hero.jpg" alt="Abstract Background" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover; z-index: -1; filter: brightness(0.6);">
  <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.2), rgba(0,0,0,0.8)); z-index: -1;"></div>
  <div style="text-align: center; z-index: 1;">
    <h1 style="color: #fff; font-family: var(--font-display); font-size: 3.5rem; text-shadow: 0 12px 32px rgba(0,0,0,0.6); margin: 0;">We're here to help</h1>
    <p style="color: rgba(255,255,255,0.8); font-size: 1.2rem; margin-top: 12px;">Reach out and let's create something great together.</p>
  </div>
</section>

<main class="wrap" style="padding-bottom: 96px;">
  <div class="detail contact-grid">
    <div>
      <span class="eyebrow">Contact</span>
      <h1 class="mt-16">Say <em>hello</em>.</h1>
      <p class="lead">Questions about a service, a project idea you're not sure how to scope, or something that's not working — write it here and a real person reads it.</p>

      <ul class="facts" style="max-width: 360px; list-style: none; padding: 0;">
        <li class="contact-fact"><span>Usually replies</span><span style="float: right; font-weight: 600;">within a day</span></li>
        <li class="contact-fact"><span>Best for</span><span style="float: right; font-weight: 600;">scoping &amp; support</span></li>
        <li class="contact-fact"><span>Not for</span><span style="float: right; font-weight: 600;">payments (yet)</span></li>
      </ul>

      <svg width="180" height="90" viewBox="0 0 180 90" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="hide-sm" aria-hidden="true" style="margin-top: 28px;">
        <path class="arrow-path" d="M6 70 C 40 20, 90 90, 130 34" stroke-dasharray="3 7"/>
        <path d="M130 34 l26 -18 l-8 30 l-7 -9 z" fill="#e0532f" stroke="#1d1b17" style="animation: float-in 0.8s 1.5s both;" />
      </svg>
    </div>

    <div>
      <?php if (!empty($isSuccess)): ?>
        <div class="panel contact-form-card reveal" style="background: var(--moss-wash); border: 1px solid rgba(0,0,0,0.05);">
          <span class="eyebrow" style="color: var(--moss);">Sent successfully</span>
          <h2 class="mt-16" style="font-size: 2.2rem; font-family: var(--font-display);">Thanks, <?= e($submittedData['name']) ?>.</h2>
          <p>Your message is in. We'll reply to <b><?= e($submittedData['email']) ?></b> shortly.</p>
          <blockquote style="margin: 24px 0 0; padding: 18px 24px; border-left: 4px solid var(--moss); background: rgba(255,255,255,0.6); border-radius: 0 var(--r-md) var(--r-md) 0; box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);" class="small">
            <i>"<?= nl2br(e($submittedData['message'])) ?>"</i>
          </blockquote>
          <div class="row mt-32" style="gap: 16px;">
            <a href="/contact" class="btn btn-glow" style="background: var(--ink); color: #fff;">Send another</a>
            <a href="/services" class="btn btn-ghost" style="border: 1px solid var(--rule);">Back to browsing</a>
          </div>
        </div>
      <?php else: ?>
        <form action="/contact" method="POST" novalidate class="contact-form-card reveal" style="border: 1px solid rgba(0,0,0,0.04);">
          <div style="margin-bottom: 24px;">
            <h3 style="font-family: var(--font-display); font-size: 1.4rem; margin-bottom: 8px;">Let's get started</h3>
            <p class="muted small" style="margin: 0;">Fill out the form below and we'll be in touch as soon as possible.</p>
          </div>
          <div class="row row-wrap" style="gap: 16px; align-items: flex-start; margin-bottom: 16px;">
            <div class="field grow <?= isset($errors['name']) ? 'has-error' : '' ?>" style="min-width: 200px;">
              <label class="label" for="name" style="font-weight: 600;">Name</label>
              <input class="input" type="text" id="name" name="name" value="<?= e($name ?? '') ?>" autocomplete="name" placeholder="Jane Doe" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
              <?= fieldError($errors ?? [], 'name') ?>
            </div>
            <div class="field grow <?= isset($errors['email']) ? 'has-error' : '' ?>" style="min-width: 200px;">
              <label class="label" for="email" style="font-weight: 600;">Email</label>
              <input class="input" type="email" id="email" name="email" value="<?= e($email ?? '') ?>" autocomplete="email" placeholder="jane@example.com" style="padding: 12px 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s;">
              <?= fieldError($errors ?? [], 'email') ?>
            </div>
          </div>

          <div class="field <?= isset($errors['message']) ? 'has-error' : '' ?>" x-data="charCount(20, 1000, <?= e(json_encode($message ?? '')) ?>)">
            <label class="label" for="message" style="font-weight: 600; display: flex; justify-content: space-between;">Message <span class="counter" :class="state" x-text="label" style="font-weight: normal; font-size: 0.85rem;"></span></label>
            <textarea class="textarea" id="message" name="message" rows="7" x-model="text"
                      placeholder="What are you working on? A rough budget and timeline helps." style="padding: 16px; border-radius: var(--r-md); transition: border-color 0.2s, box-shadow 0.2s; line-height: 1.5; resize: vertical;"></textarea>
            <?= fieldError($errors ?? [], 'message') ?>
          </div>

          <button type="submit" class="btn btn-accent btn-lg btn-glow" style="width: 100%; justify-content: center; margin-top: 8px; font-weight: 600; font-size: 1.1rem; padding: 14px 24px; border-radius: var(--r-md); transition: transform 0.2s, background 0.2s;">
            <span class="spinner"></span>Send message
          </button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
