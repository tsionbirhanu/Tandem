<?php
// app/Views/profile/freelancer.php
$pageTitle = $freelancer['name'] ?? 'Freelancer';
include BASE_PATH . '/app/Views/layouts/header.php';

$services    = $services ?? [];
$reviews     = $reviews ?? [];
$reviewCount = (int)($reviewCount ?? 0);
$avg         = $reviewCount ? (float)$avgRating : 0;
$isMe        = isLoggedIn() && (int)($_SESSION['user_id'] ?? 0) === (int)($freelancer['id'] ?? -1);
$firstName   = explode(' ', $freelancer['name'] ?? '')[0];
$startPrice  = $services ? min(array_map(fn($s) => (float)$s['price'], $services)) : 0;
?>

<main>
  <?php if (!empty($dbError)): ?>
    <div class="wrap mt-24"><div class="alert"><?= e($dbError) ?></div></div>
  <?php endif; ?>

  <?php if ($freelancer): ?>
    <section class="profile-hero">
      <div class="wrap profile-hero-inner">
        <?= avatar($freelancer['avatar_url'], $freelancer['name'], 'xl') ?>
        <div>
          <span class="eyebrow">Freelancer</span>
          <h1><?= e($freelancer['name']) ?></h1>
          <div class="row row-wrap small" style="justify-content: inherit;">
            <?php if ($reviewCount): ?>
              <span class="rating-inline"><?= stars($avg) ?> <b><?= number_format($avg, 1) ?></b> <span class="muted">· <?= $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?></span></span>
            <?php else: ?>
              <span class="tag">New on Tandem</span>
            <?php endif; ?>
            <?php if ($isMe): ?>
              <a href="/profile/edit" class="btn btn-sm">Edit profile</a>
            <?php endif; ?>
          </div>
        </div>
        <div class="profile-stats">
          <div><b data-count="<?= count($services) ?>">0</b><span>Services</span></div>
          <div><b data-count="<?= $reviewCount ?>">0</b><span>Reviews</span></div>
          <?php if ($startPrice): ?>
            <div><b><?= money($startPrice) ?></b><span>From</span></div>
          <?php endif; ?>
        </div>
      </div>
    </section>

    <section class="section" style="padding-top: 48px;">
      <div class="wrap">
        <div class="section-head" style="margin-bottom: 24px;">
          <div>
            <span class="eyebrow">On the shelf</span>
            <h2 class="mt-8"><?= e($firstName) ?>'s services</h2>
          </div>
        </div>

        <?php if ($services): ?>
          <div class="cards">
            <?php foreach ($services as $i => $service):
              $service['freelancer_name']   = $freelancer['name'];
              $service['freelancer_avatar'] = $freelancer['avatar_url'];
              $delay = ($i % 3) * 70; ?>
              <?php include BASE_PATH . '/app/Views/partials/service-card.php'; ?>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div class="empty">
            <p class="muted mb-0"><?= $isMe ? 'You haven\'t listed anything yet.' : e($firstName) . ' hasn\'t listed anything yet.' ?></p>
            <?php if ($isMe): ?><a href="/services/create" class="btn btn-accent btn-sm mt-16">List a service</a><?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </section>

    <section class="section" style="padding-top: 0;">
      <div class="wrap"><div style="max-width: 760px;">
        <span class="eyebrow">What clients said</span>
        <h2 class="mt-8 mb-24">Reviews</h2>

        <?php if ($reviews): ?>
          <div class="panel" x-data="{ all: false }">
            <?php foreach ($reviews as $n => $rev): ?>
              <div class="review" <?= $n >= 4 ? 'x-show="all" x-cloak x-transition' : '' ?>>
                <div class="review-head">
                  <?= avatar($rev['client_avatar'] ?? null, $rev['client_name'], 'sm') ?>
                  <div class="meta">
                    <b><?= e($rev['client_name']) ?></b>
                    <?php if (!empty($rev['service_title'])): ?>
                      <a href="/services/<?= (int)$rev['service_id'] ?>" class="small muted"><?= e($rev['service_title']) ?></a>
                    <?php endif; ?>
                  </div>
                  <span style="text-align: right;">
                    <?= stars((float)$rev['rating']) ?><br>
                    <span class="small muted"><?= e(timeAgo($rev['created_at'])) ?></span>
                  </span>
                </div>
                <p><?= e($rev['comment']) ?></p>
              </div>
            <?php endforeach; ?>
            <?php if (count($reviews) > 4): ?>
              <button type="button" class="btn btn-sm mt-16" @click="all = !all" x-text="all ? 'Show fewer' : 'Show all <?= count($reviews) ?>'"></button>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <p class="muted">No reviews yet — they show up after a finished project.</p>
        <?php endif; ?>
      </div></div>
    </section>
  <?php endif; ?>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
