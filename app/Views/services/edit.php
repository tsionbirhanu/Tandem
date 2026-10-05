<?php
// app/Views/services/edit.php
$pageTitle = 'Edit service';
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="wrap">
  <header class="page-head">
    <a href="/services/<?= (int)$service['id'] ?>" class="arrow-link back small muted" style="text-decoration: none;">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M13 8H3M7 4L3 8l4 4"/></svg>
      Back to listing
    </a>
    <h1 class="mt-16">Edit <em>listing</em></h1>
    <p class="muted mb-0">Changes go live as soon as you save.</p>
  </header>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <form action="/services/<?= (int)$service['id'] ?>/edit" method="POST" enctype="multipart/form-data" novalidate>
    <input type="hidden" name="id" value="<?= (int)$service['id'] ?>">
    <?php $formActions = function () use ($service) { ?>
      <div class="row row-wrap mt-24" style="justify-content: flex-end; border-top: 1px dashed var(--rule); padding-top: 22px;">
      <a href="/services/<?= (int)$service['id'] ?>/delete" class="btn btn-ghost" style="color: var(--danger); margin-right: auto;">Delete service</a>
      <a href="/services/<?= (int)$service['id'] ?>" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-ink btn-lg"><span class="spinner"></span>Save changes</button>
      </div>
    <?php }; ?>
    <?php include BASE_PATH . '/app/Views/partials/service-form.php'; ?>
  </form>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
