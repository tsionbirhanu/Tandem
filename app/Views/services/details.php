<?php
// app/Views/services/details.php
$pageTitle = $service['title'] ?? 'Service';
include BASE_PATH . '/app/Views/layouts/header.php';

$isOwnerOrAdmin = false;
if (isLoggedIn() && $service) {
    $isOwnerOrAdmin = (int)($_SESSION['user_id'] ?? 0) === (int)$service['freelancer_id']
                   || ($_SESSION['user_role'] ?? '') === 'admin';
}

// Only keep gallery images whose files actually exist
$images = [];
foreach ($galleryImages ?? [] as $img) {
    if ($url = assetUrl($img['image_path'])) {
        $images[] = $url;
    }
}
$reviewCount = count($reviews ?? []);
$avg = $reviewCount ? array_sum(array_column($reviews, 'rating')) / $reviewCount : 0;
$firstName = $service ? explode(' ', $service['freelancer_name'])[0] : '';
?>

<main class="wrap">
  <div class="page-head" style="padding-bottom: 18px;">
    <a href="/services" class="arrow-link back small muted" style="text-decoration: none;">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M13 8H3M7 4L3 8l4 4"/></svg>
      All services
    </a>
  </div>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <?php if ($service): ?>
    <div class="detail">
      <article>
        <div class="row row-wrap mb-16">
          <a class="tag" href="/services?category=<?= e($service['category_slug']) ?>" style="text-decoration: none;"><?= e($service['category_name']) ?></a>
          <span class="mono muted">№ <?= str_pad((string)(int)$service['id'], 3, '0', STR_PAD_LEFT) ?></span>
          <?php if ($isOwnerOrAdmin): ?>
            <span class="grow"></span>
            <a href="/services/<?= (int)$service['id'] ?>/edit" class="btn btn-sm">Edit</a>
            <a href="/services/<?= (int)$service['id'] ?>/delete" class="btn btn-sm btn-ghost" style="color: var(--danger);">Delete</a>
          <?php endif; ?>
        </div>

        <h1><?= e($service['title']) ?></h1>

        <div class="row row-wrap mb-24 small">
          <?php if ($reviewCount): ?>
            <span class="rating-inline"><?= stars($avg) ?> <b><?= number_format($avg, 1) ?></b> <a href="#reviews" class="muted"><?= $reviewCount ?> review<?= $reviewCount === 1 ? '' : 's' ?></a></span>
            <span class="muted">·</span>
          <?php endif; ?>
          <span class="muted">Listed <?= e(date('F Y', strtotime($service['created_at']))) ?></span>
        </div>

        <!-- Gallery -->
        <div x-data="gallery(<?= max(1, count($images)) ?>)"
             @keydown.arrow-right.window="if (!$event.target.closest('input,textarea')) next()"
             @keydown.arrow-left.window="if (!$event.target.closest('input,textarea')) prev()"
             @keydown.escape.window="open = false">
          <div class="gallery-main" @click="<?= $images ? 'open = true' : '' ?>">
            <?php if ($images): ?>
              <?php foreach ($images as $n => $src): ?>
                <img src="<?= e($src) ?>" alt="<?= e($service['title']) ?> — image <?= $n + 1 ?>" x-show="i === <?= $n ?>" <?= $n ? 'x-cloak' : '' ?>
                     x-transition:enter.opacity.duration.250ms>
              <?php endforeach; ?>
              <?php if (count($images) > 1): ?>
                <button type="button" class="gallery-nav prev" @click.stop="prev()" aria-label="Previous image">←</button>
                <button type="button" class="gallery-nav next" @click.stop="next()" aria-label="Next image">→</button>
                <span class="gallery-count" x-text="(i + 1) + ' / ' + count"></span>
              <?php endif; ?>
            <?php else: ?>
              <?= coverArt($service) ?>
            <?php endif; ?>
          </div>

          <?php if (count($images) > 1): ?>
            <div class="gallery-thumbs">
              <?php foreach ($images as $n => $src): ?>
                <button type="button" @click="go(<?= $n ?>)" :class="{ 'is-on': i === <?= $n ?> }" aria-label="Show image <?= $n + 1 ?>">
                  <img src="<?= e($src) ?>" alt="" loading="lazy">
                </button>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <?php if ($images): ?>
            <template x-teleport="body">
              <div class="lightbox" x-show="open" x-cloak x-transition.opacity @click.self="open = false">
                <?php foreach ($images as $n => $src): ?>
                  <img src="<?= e($src) ?>" alt="" x-show="i === <?= $n ?>">
                <?php endforeach; ?>
                <button type="button" class="btn btn-sm close" @click="open = false">Close ✕</button>
              </div>
            </template>
          <?php endif; ?>
        </div>

        <!-- Description -->
        <section class="mt-32">
          <span class="eyebrow">What you get</span>
          <div class="prose mt-16<?= mb_strlen($service['description']) > 280 ? ' has-dropcap' : '' ?>">
            <?php foreach (preg_split("/\n\s*\n/", trim($service['description'])) as $para): ?>
              <p><?= nl2br(e($para)) ?></p>
            <?php endforeach; ?>
          </div>
        </section>

        <hr class="rule-dash">

        <!-- Reviews -->
        <section id="reviews" x-data="{ all: false }">
          <div class="panel-title">
            <h2 style="font-size: 1.8rem;">Reviews</h2>
          </div>

          <?php if ($reviewCount): ?>
            <div class="rating-summary">
              <span class="big"><?= number_format($avg, 1) ?></span>
              <span><?= stars($avg, 'lg') ?><br><span class="small muted">from <?= $reviewCount ?> finished project<?= $reviewCount === 1 ? '' : 's' ?></span></span>
            </div>
            <?php foreach ($reviews as $n => $review): ?>
              <div class="review" <?= $n >= 3 ? 'x-show="all" x-cloak x-transition' : '' ?>>
                <div class="review-head">
                  <?= avatar($review['client_avatar'] ?? null, $review['client_name'], 'sm') ?>
                  <div class="meta">
                    <b><?= e($review['client_name']) ?></b>
                    <span class="small muted"><?= e(timeAgo($review['created_at'])) ?></span>
                  </div>
                  <?= stars((float)$review['rating']) ?>
                </div>
                <p><?= e($review['comment']) ?></p>
              </div>
            <?php endforeach; ?>
            <?php if ($reviewCount > 3): ?>
              <button type="button" class="btn btn-sm mt-16" @click="all = !all" x-text="all ? 'Show fewer' : 'Show all <?= $reviewCount ?> reviews'"></button>
            <?php endif; ?>
          <?php else: ?>
            <div class="empty" style="padding: 32px;">
              <p class="mb-0 muted">No reviews yet. Reviews appear here once a project with <?= e($firstName) ?> is finished.</p>
            </div>
          <?php endif; ?>
        </section>
      </article>

      <!-- Sidebar -->
      <aside>
        <div class="buy-box stack">
          <div class="panel panel-print">
            <span class="eyebrow no-dash">Starting at</span>
            <span class="price"><?= money($service['price']) ?></span>
            <span class="small muted">Fixed price. Final scope is agreed with <?= e($firstName) ?> before work starts.</span>

            <ul class="facts">
              <li><span>Category</span><span><?= e($service['category_name']) ?></span></li>
              <li><span>Rating</span><span><?= $reviewCount ? number_format($avg, 1) . ' / 5' : 'No reviews yet' ?></span></li>
              <li><span>Updated</span><span><?= e(timeAgo($service['updated_at'] ?? $service['created_at'])) ?></span></li>
            </ul>

            <?php if ($isOwnerOrAdmin): ?>
              <a href="/services/<?= (int)$service['id'] ?>/edit" class="btn btn-block">Edit this listing</a>
            <?php elseif (!empty($openRequest)): ?>
              <div class="alert alert-info mb-0" style="flex-direction: column; gap: 8px;">
                <span>You've already sent a request for this.</span>
                <span class="row between" style="width: 100%;">
                  <?= statusPill($openRequest['status']) ?>
                  <a href="/dashboard/client" class="small link">Track it →</a>
                </span>
              </div>
            <?php elseif (($_SESSION['user_role'] ?? '') === 'client'): ?>
              <div x-data="{ open: window.location.hash === '#request' }" id="request">
                <button type="button" class="btn btn-accent btn-block btn-lg" x-show="!open" @click="open = true; $nextTick(() => $refs.brief.focus())">
                  Request this service
                  <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M3 8h10M9 4l4 4-4 4"/></svg>
                </button>
                <form action="/services/<?= (int)$service['id'] ?>/request" method="POST" x-show="open" x-cloak x-transition
                      x-data="charCount(20, 2000)">
                  <label class="label" for="brief">Your brief for <?= e($firstName) ?> <span class="counter" :class="state" x-text="label"></span></label>
                  <textarea class="textarea" id="brief" name="message" rows="5" x-ref="brief" x-model="text"
                            placeholder="What do you need, by when, and anything <?= e($firstName) ?> should know?"></textarea>
                  <div class="row mt-16">
                    <button type="button" class="btn btn-ghost btn-sm" @click="open = false">Cancel</button>
                    <button type="submit" class="btn btn-accent grow" :disabled="n < 20 || n > 2000"><span class="spinner"></span>Send request</button>
                  </div>
                </form>
              </div>
              <p class="small muted mt-16 mb-0" style="text-align: center;">Nothing is charged. <?= e($firstName) ?> accepts or declines first.</p>
            <?php elseif (isLoggedIn()): ?>
              <p class="small muted mb-0" style="text-align: center;">Log in with a <b>client</b> account to request this service.</p>
            <?php else: ?>
              <a href="/login" class="btn btn-accent btn-block btn-lg">Log in to request</a>
              <p class="small muted mt-16 mb-0" style="text-align: center;">New here? <a class="link" href="/register">Make an account</a></p>
            <?php endif; ?>
          </div>

          <a class="seller" href="/freelancer/<?= (int)$service['freelancer_id'] ?>">
            <?= avatar($service['freelancer_avatar'] ?? null, $service['freelancer_name'], 'lg') ?>
            <span class="grow">
              <span class="small muted">Made by</span>
              <b style="display: block; font-family: var(--font-display); font-size: 1.2rem; font-weight: 500;"><?= e($service['freelancer_name']) ?></b>
              <span class="small link" style="text-decoration-thickness: 1.5px;">See profile</span>
            </span>
          </a>

          <?php if (!empty($moreServices)): ?>
            <div class="panel panel-soft">
              <p class="filter-title">More from <?= e($firstName) ?></p>
              <?php foreach ($moreServices as $other): ?>
                <a href="/services/<?= (int)$other['id'] ?>" class="row between" style="text-decoration: none; padding: 10px 0; border-top: 1px dashed var(--rule); gap: 16px;">
                  <span class="small" style="font-weight: 600; line-height: 1.35;"><?= e($other['title']) ?></span>
                  <span class="mono"><?= money($other['price']) ?></span>
                </a>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>
        </div>
      </aside>
    </div>
  <?php endif; ?>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
