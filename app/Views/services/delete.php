<?php
// app/Views/services/delete.php
$pageTitle = 'Delete service';
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="wrap wrap-tight" style="padding: 72px var(--gutter) 120px;">
  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <?php if ($service): ?>
    <div class="panel panel-print" x-data="{ sure: false }">
      <span class="eyebrow" style="color: var(--danger);">Delete service</span>
      <h2 class="mt-16" style="font-size: 1.9rem;">Take down “<?= e($service['title']) ?>”?</h2>
      <p class="muted">It disappears from the directory straight away and can't be brought back. Past project requests keep their history.</p>

      <label class="row small mt-24" style="cursor: pointer;">
        <input type="checkbox" x-model="sure" style="width: 18px; height: 18px; accent-color: var(--danger);">
        Yes, I understand this can't be undone.
      </label>

      <form action="/services/<?= (int)$service['id'] ?>/delete" method="POST" class="row mt-24" style="justify-content: flex-end;">
        <input type="hidden" name="id" value="<?= (int)$service['id'] ?>">
        <input type="hidden" name="confirm" value="yes">
        <a href="/services/<?= (int)$service['id'] ?>" class="btn">Keep it</a>
        <button type="submit" class="btn btn-danger" :disabled="!sure"><span class="spinner"></span>Delete for good</button>
      </form>
    </div>
  <?php endif; ?>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
