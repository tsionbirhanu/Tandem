<?php
// app/Views/reviews/create.php
// Expects: $projectRequest, $rating, $comment, $errors, $dbError (from ReviewController).
$pageTitle = 'Leave a review';
include BASE_PATH . '/app/Views/layouts/header.php';

$req = $projectRequest ?? null;
$freelancerFirst = $req ? explode(' ', $req['freelancer_name'] ?? 'them')[0] : 'them';
?>

<main class="wrap wrap-narrow" style="padding-bottom: 96px;">
  <header class="page-head">
    <a href="/dashboard/client" class="arrow-link back small muted" style="text-decoration: none;">
      <svg width="14" height="14" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><path d="M13 8H3M7 4L3 8l4 4"/></svg>
      Your projects
    </a>
    <h1 class="mt-16">How did it go with <em><?= e($freelancerFirst) ?></em>?</h1>
    <p class="muted mb-0">Your review is public and shows on <?= e($freelancerFirst) ?>'s profile. Be honest and specific — it helps the next client most.</p>
  </header>

  <?php if (!empty($dbError)): ?>
    <div class="alert"><?= e($dbError) ?></div>
  <?php endif; ?>

  <?php if ($req): ?>
    <div class="seller mb-24" style="border-style: solid; background: var(--card);">
      <span class="tag"><?= e($req['category_name'] ?? 'Project') ?></span>
      <span class="grow small"><b><?= e($req['service_title'] ?? 'Project') ?></b></span>
      <?php if (!empty($req['price'])): ?><span class="mono"><?= money($req['price']) ?></span><?php endif; ?>
    </div>

    <form action="/requests/<?= (int)$req['id'] ?>/review" method="POST" novalidate class="panel panel-print"
          x-data="starPicker(<?= (int)($rating ?? 5) ?>)">
      <div class="field <?= isset($errors['rating']) ? 'has-error' : '' ?>">
        <span class="label">Your rating</span>
        <div class="row row-wrap" style="gap: 18px;">
          <div class="star-picker" role="radiogroup" aria-label="Rating" @mouseleave="hover = 0">
            <template x-for="n in 5" :key="n">
              <button type="button" role="radio" :aria-checked="value === n" :aria-label="n + ' star' + (n > 1 ? 's' : '')"
                      :class="{ 'is-on': n <= shown }" @mouseenter="hover = n" @focus="hover = n" @blur="hover = 0" @click="value = n">
                <svg viewBox="0 0 20 20"><path d="M10 1.8l2.5 5.3 5.7.7-4.2 3.9 1.1 5.7L10 14.6l-5.1 2.8 1.1-5.7L1.8 7.8l5.7-.7z"/></svg>
              </button>
            </template>
          </div>
          <span class="star-word" x-text="words[shown]"></span>
        </div>
        <input type="hidden" name="rating" :value="value" value="<?= (int)($rating ?? 5) ?>">
        <?= fieldError($errors ?? [], 'rating') ?>
      </div>

      <div class="field <?= isset($errors['comment']) ? 'has-error' : '' ?>" x-data="charCount(10, 1000, <?= e(json_encode($comment ?? '')) ?>)">
        <label class="label" for="comment">What was it like? <span class="counter" :class="state" x-text="label"></span></label>
        <textarea class="textarea" id="comment" name="comment" rows="6" x-model="text"
                  placeholder="Communication, quality, timing — what would you tell a friend about to hire <?= e($freelancerFirst) ?>?"><?= e($comment ?? '') ?></textarea>
        <?= fieldError($errors ?? [], 'comment') ?>
      </div>

      <div class="row" style="justify-content: flex-end;">
        <a href="/dashboard/client" class="btn btn-ghost">Later</a>
        <button type="submit" class="btn btn-accent btn-lg"><span class="spinner"></span>Publish review</button>
      </div>
    </form>
  <?php endif; ?>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
