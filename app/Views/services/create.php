<?php
// app/Views/services/create.php
$pageTitle = 'List a service';
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="wrap">
  <header class="page-head">
    <span class="eyebrow">New listing</span>
    <h1>Put something <em>on the shelf</em>.</h1>
    <p class="muted mb-0">Clients see the title, price and first image in the directory. Everything else is on the details page.</p>
  </header>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <form action="/services/create" method="POST" enctype="multipart/form-data" novalidate>
    <?php $formActions = function () { ?>
      <div class="row row-wrap mt-24" style="justify-content: flex-end; border-top: 1px dashed var(--rule); padding-top: 22px;">
      <a href="/dashboard/freelancer" class="btn btn-ghost">Cancel</a>
      <button type="submit" class="btn btn-accent btn-lg"><span class="spinner"></span>Publish service</button>
      </div>
    <?php }; ?>
    <?php include BASE_PATH . '/app/Views/partials/service-form.php'; ?>
  </form>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
