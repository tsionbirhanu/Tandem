<?php
// app/Views/profile/edit.php
$pageTitle = 'Edit profile';
include BASE_PATH . '/app/Views/layouts/header.php';
$currentAvatar = assetUrl($user->getAvatarUrl()) ?? '';
?>

<main class="wrap wrap-narrow" style="padding-bottom: 80px;">
  <header class="page-head">
    <a href="<?= e($user->getDashboardUrl()) ?>" class="arrow-link back small muted" style="text-decoration: none;">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M13 8H3M7 4L3 8l4 4"/></svg>
      Dashboard
    </a>
    <h1 class="mt-16">Your <em>profile</em></h1>
    <p class="muted mb-0">This is how you appear to <?= $user->isFreelancer() ? 'clients browsing your services' : 'freelancers you contact' ?>.</p>
  </header>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <form action="/profile/edit" method="POST" enctype="multipart/form-data" novalidate class="panel panel-print"
        x-data="{ name: <?= e(json_encode($name ?? '')) ?> }">

    <div class="field <?= isset($errors['avatar']) ? 'has-error' : '' ?>" x-data="imagePicker(<?= e(json_encode($currentAvatar)) ?>)">
      <span class="label">Photo</span>
      <div class="avatar-edit"
           @dragover.prevent="over = true" @dragleave.prevent="over = false" @drop.prevent="over = false; pick($event.dataTransfer.files)">
        <template x-if="preview">
          <img class="avatar avatar-xl" :src="preview" alt="Your photo" :style="over ? 'outline: 3px dashed var(--accent); outline-offset: 4px' : ''">
        </template>
        <template x-if="!preview">
          <span class="avatar avatar-xl avatar-tint-<?= abs(crc32($user->getName())) % 4 ?>" x-text="(name.trim().split(/\s+/).slice(0,2).map(p => p[0] || '').join('') || '?').toUpperCase()"
                :style="over ? 'outline: 3px dashed var(--accent); outline-offset: 4px' : ''"></span>
        </template>
        <div>
          <label class="btn btn-sm" for="avatar" style="cursor: pointer;">
            <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round"><path d="M8 11V2M4.5 5.5L8 2l3.5 3.5M2 11v3h12v-3"/></svg>
            <span x-text="preview ? 'Change photo' : 'Upload photo'"></span>
          </label>
          <p class="field-help">JPG, PNG or WebP, up to 5 MB. You can also drop an image onto the circle.</p>
          <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" class="sr-only" x-ref="input" @change="pick($event.target.files)">
        </div>
      </div>
      <?= fieldError($errors ?? [], 'avatar') ?>
    </div>

    <hr class="rule-dash">

    <div class="field <?= isset($errors['name']) ? 'has-error' : '' ?>">
      <label class="label" for="name">Name</label>
      <input class="input" type="text" id="name" name="name" x-model="name" autocomplete="name">
      <?= fieldError($errors ?? [], 'name') ?>
    </div>

    <div class="field <?= isset($errors['email']) ? 'has-error' : '' ?>">
      <label class="label" for="email">Email</label>
      <input class="input" type="email" id="email" name="email" value="<?= e($email ?? '') ?>" autocomplete="email">
      <?= fieldError($errors ?? [], 'email') ?>
    </div>

    <div class="row mt-24" style="justify-content: flex-end;">
      <a href="<?= e($user->getDashboardUrl()) ?>" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-ink"><span class="spinner"></span>Save changes</button>
    </div>
  </form>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
