<?php
// app/Views/dashboard/client.php
$pageTitle = 'Your projects';
include BASE_PATH . '/app/Views/layouts/header.php';

$active = ['pending', 'accepted', 'in_progress'];
$counts = ['all' => count($projectRequests), 'active' => 0, 'completed' => 0, 'closed' => 0];
foreach ($projectRequests as $req) {
    if (in_array($req['status'], $active, true))  $counts['active']++;
    elseif ($req['status'] === 'completed')       $counts['completed']++;
    else                                          $counts['closed']++;
}
$toReview = array_filter($projectRequests, fn($r) => $r['status'] === 'completed' && empty($r['has_reviewed']));
$firstName = explode(' ', $user->getName())[0];
$hour = (int)date('G');
$greeting = $hour < 12 ? 'Morning' : ($hour < 18 ? 'Afternoon' : 'Evening');
?>

<div class="dash">
  <?php include BASE_PATH . '/app/Views/partials/dashboard-nav.php'; ?>

  <main class="dash-main">
    <header class="dash-head">
      <div>
        <span class="eyebrow">Client dashboard</span>
        <h1><?= $greeting ?>, <em><?= e($firstName) ?></em>.</h1>
      </div>
      <a href="/services" class="btn btn-accent">Find someone new</a>
    </header>

    <?php if (!empty($dbError)): ?>
      <div class="alert"><?= e($dbError) ?></div>
    <?php endif; ?>

    <?php if ($toReview): $next = reset($toReview); ?>
      <div class="alert alert-info reveal" style="align-items: center;">
        <span style="font-size: 1.4rem;" aria-hidden="true">✎</span>
        <span class="grow">
          <strong><?= count($toReview) ?> finished project<?= count($toReview) === 1 ? '' : 's' ?> waiting for your review.</strong>
          It takes a minute and really helps <?= e(explode(' ', $next['freelancer_name'])[0]) ?>.
        </span>
        <a href="/requests/<?= (int)$next['id'] ?>/review" class="btn btn-sm">Write review</a>
      </div>
    <?php endif; ?>

    <section class="stats" aria-label="Summary">
      <div class="stat <?= $stats['active_requests'] ? 'is-hot' : '' ?>">
        <span class="stat-label">In motion</span>
        <div class="stat-value" data-count="<?= (int)$stats['active_requests'] ?>">0</div>
        <span class="stat-note">requests open</span>
      </div>
      <div class="stat">
        <span class="stat-label">Finished</span>
        <div class="stat-value" data-count="<?= (int)$stats['completed_projects'] ?>">0</div>
        <span class="stat-note">projects</span>
      </div>
      <div class="stat">
        <span class="stat-label">Reviews</span>
        <div class="stat-value" data-count="<?= (int)$stats['reviews_written'] ?>">0</div>
        <span class="stat-note">written by you</span>
      </div>
      <div class="stat">
        <span class="stat-label">Invested</span>
        <div class="stat-value" data-count="<?= (float)$stats['total_spent'] ?>" data-prefix="$">$0</div>
        <span class="stat-note">on finished work</span>
      </div>
    </section>

    <section class="panel" x-data="{ tab: 'all' }">
      <div class="panel-title">
        <h2 style="font-size: 1.6rem;">Your requests</h2>
      </div>

      <?php if ($projectRequests): ?>
        <div class="tabs" role="tablist">
          <?php foreach (['all' => 'All', 'active' => 'Active', 'completed' => 'Finished', 'closed' => 'Closed'] as $key => $label): ?>
            <?php if ($key === 'all' || $counts[$key] > 0): ?>
              <button type="button" role="tab" class="tab" :class="{ 'is-on': tab === '<?= $key ?>' }" :aria-selected="tab === '<?= $key ?>'" @click="tab = '<?= $key ?>'">
                <?= $label ?><span class="count"><?= $counts[$key] ?></span>
              </button>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr><th>Project</th><th>Freelancer</th><th>Status</th><th>Sent</th><th></th></tr>
            </thead>
            <tbody>
              <?php foreach ($projectRequests as $req):
                $group = in_array($req['status'], $active, true) ? 'active' : ($req['status'] === 'completed' ? 'completed' : 'closed'); ?>
                <tr x-show="tab === 'all' || tab === '<?= $group ?>'" x-transition.opacity>
                  <td>
                    <a class="t-title" href="/services/<?= (int)$req['service_id'] ?>"><?= e($req['service_title']) ?></a>
                    <span class="small muted"><?= e($req['category_name']) ?> · <?= money($req['price']) ?></span>
                  </td>
                  <td>
                    <a href="/freelancer/<?= (int)$req['freelancer_id'] ?>" class="row" style="gap: 8px; text-decoration: none;">
                      <?= avatar($req['freelancer_avatar'] ?? null, $req['freelancer_name'], 'xs') ?>
                      <span class="small"><?= e($req['freelancer_name']) ?></span>
                    </a>
                  </td>
                  <td><?= statusPill($req['status']) ?></td>
                  <td class="small muted" title="<?= e(date('M j, Y g:ia', strtotime($req['created_at']))) ?>"><?= e(timeAgo($req['created_at'])) ?></td>
                  <td>
                    <div class="t-actions">
                      <?php if ($req['status'] === 'completed' && empty($req['has_reviewed'])): ?>
                        <a href="/requests/<?= (int)$req['id'] ?>/review" class="btn btn-sm btn-accent">Review</a>
                      <?php elseif ($req['status'] === 'completed'): ?>
                        <span class="small muted">Reviewed ✓</span>
                      <?php endif; ?>
                      <?php $party = 'client'; include BASE_PATH . '/app/Views/partials/request-actions.php'; ?>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <div class="empty">
          <svg width="72" height="56" viewBox="0 0 72 56" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" aria-hidden="true"><rect x="6" y="10" width="60" height="40" rx="4"/><path d="M6 20h60" /><path d="M18 32h22M18 40h14" stroke="#e0532f"/></svg>
          <h3>No requests yet</h3>
          <p class="muted">When you contact a freelancer about a service, it'll show up here.</p>
          <a href="/services" class="btn btn-sm mt-8">Browse work</a>
        </div>
      <?php endif; ?>
    </section>
  </main>
</div>

</body>
</html>
