<?php
// app/Views/errors/404.php
$pageTitle = 'Not found';
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="wrap lost">
  <span class="lost-no" aria-hidden="true"><span>4</span><span>0</span><span>4</span></span>
  <h1 style="font-size: clamp(1.8rem, 4vw, 2.6rem);">This page took a different turn.</h1>
  <p class="muted">Nothing lives at <span class="mono" style="background: var(--card); padding: 2px 8px; border: 1px solid var(--rule); border-radius: 4px;"><?= e($path ?? '') ?></span> — it may have been moved or taken down.</p>
  <div class="row mt-24" style="justify-content: center;">
    <a href="/" class="btn">Home</a>
    <a href="/services" class="btn btn-accent">Browse work</a>
  </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
