<?php
// app/Views/partials/service-card.php
// Expects $service (row from Service::filter() / featured()) and optional $delay (ms) for the reveal stagger.
$cardRating  = (float)($service['rating'] ?? 0);
$cardReviews = (int)($service['reviews'] ?? 0);
?>
<a href="/services/<?= (int)$service['id'] ?>" class="card reveal" data-delay="<?= (int)($delay ?? 0) ?>">
  <?php if ($cardReviews > 0 && $cardRating >= 4.9): ?>
    <span class="tag tag-butter tag-top">★ Loved</span>
  <?php endif; ?>
  <?= serviceCover($service) ?>
  <div class="card-body">
    <h3 class="card-title"><?= e($service['title']) ?></h3>
    <div class="card-by">
      <?= avatar($service['freelancer_avatar'] ?? null, $service['freelancer_name'], 'xs') ?>
      <span><?= e($service['freelancer_name']) ?></span>
    </div>
    <div class="card-foot">
      <span class="rating-inline">
        <?php if ($cardReviews > 0): ?>
          <?= stars($cardRating) ?> <b><?= number_format($cardRating, 1) ?></b> <span class="muted">(<?= $cardReviews ?>)</span>
        <?php else: ?>
          <span class="tag">New</span>
        <?php endif; ?>
      </span>
      <span class="price"><small>from</small><?= money($service['price']) ?></span>
    </div>
  </div>
</a>
