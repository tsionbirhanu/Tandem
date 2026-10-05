<?php
// app/Views/dashboard/freelancer.php
$pageTitle = 'Your studio';
include BASE_PATH . '/app/Views/layouts/header.php';

$incomingRequests = $incomingRequests ?? [];
$pending  = array_values(array_filter($incomingRequests, fn($r) => $r['status'] === 'pending'));
$openWork = array_values(array_filter($incomingRequests, fn($r) => in_array($r['status'], ['accepted', 'in_progress'], true)));
$firstName = explode(' ', $user->getName())[0];
$avg = (float)($stats['average_rating'] ?? 0);
?>

<div class="dash">
  <?php include BASE_PATH . '/app/Views/partials/dashboard-nav.php'; ?>

  <main class="dash-main">
    <header class="dash-head">
      <div>
        <span class="eyebrow">Freelancer studio</span>
        <h1>Hey <em><?= e($firstName) ?></em> — <?= count($pending) ? count($pending) . ' new ' . (count($pending) === 1 ? 'request' : 'requests') . '.' : 'all caught up.' ?></h1>
      </div>
      <div class="row">
        <a href="/freelancer/<?= (int)$user->getId() ?>" class="btn">View public profile</a>
        <a href="/services/create" class="btn btn-accent">New service</a>
      </div>
    </header>

    <?php if (!empty($dbError)): ?>
      <div class="alert"><?= e($dbError) ?></div>
    <?php endif; ?>

    <section class="stats" aria-label="Summary">
      <div class="stat">
        <span class="stat-label">Listed</span>
        <div class="stat-value" data-count="<?= (int)$stats['active_services'] ?>">0</div>
        <span class="stat-note">services</span>
      </div>
      <div class="stat <?= $stats['pending_requests'] ? 'is-hot' : '' ?>">
        <span class="stat-label">Waiting on you</span>
        <div class="stat-value" data-count="<?= (int)$stats['pending_requests'] ?>">0</div>
        <span class="stat-note">new requests</span>
      </div>
      <div class="stat">
        <span class="stat-label">On the bench</span>
        <div class="stat-value" data-count="<?= (int)$stats['in_progress'] ?>">0</div>
        <span class="stat-note">in progress</span>
      </div>
      <div class="stat">
        <span class="stat-label">Rating</span>
        <div class="stat-value"><span data-count="<?= $avg ?>" data-decimals="1">0</span><span style="color: var(--accent); font-size: .6em;"> ★</span></div>
        <span class="stat-note">average from clients</span>
      </div>
    </section>

    <div class="dash-grid">
      <!-- Services -->
      <section class="panel" x-data="{ confirmId: null, confirmTitle: '' }">
        <div class="panel-title">
          <h2 style="font-size: 1.6rem;">Your services</h2>
          <a href="/services/create" class="small link">+ Add one</a>
        </div>

        <?php if (!empty($myServices)): ?>
          <div class="table-wrap">
            <table class="table">
              <thead><tr><th>Service</th><th>Price</th><th></th></tr></thead>
              <tbody>
                <?php foreach ($myServices as $service): ?>
                  <tr>
                    <td>
                      <a class="t-title" href="/services/<?= (int)$service['id'] ?>"><?= e($service['title']) ?></a>
                      <span class="small muted"><?= e($service['category_name']) ?> · listed <?= e(timeAgo($service['created_at'])) ?></span>
                    </td>
                    <td class="mono"><?= money($service['price']) ?></td>
                    <td>
                      <div class="t-actions">
                        <a href="/services/<?= (int)$service['id'] ?>/edit" class="btn btn-sm">Edit</a>
                        <button type="button" class="btn btn-sm btn-ghost" style="color: var(--danger);"
                                @click="confirmId = <?= (int)$service['id'] ?>; confirmTitle = <?= e(json_encode($service['title'])) ?>">Delete</button>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <div class="empty">
            <h3>Nothing listed yet</h3>
            <p class="muted">A clear title, an honest price and a couple of images go a long way.</p>
            <a href="/services/create" class="btn btn-accent btn-sm mt-8">List your first service</a>
          </div>
        <?php endif; ?>

        <!-- Delete confirmation -->
        <template x-teleport="body">
          <div class="modal-back" x-show="confirmId" x-cloak x-transition.opacity @click.self="confirmId = null" @keydown.escape.window="confirmId = null">
            <div class="panel panel-print modal" x-show="confirmId" x-transition.scale.95>
              <span class="eyebrow" style="color: var(--danger);">Delete service</span>
              <h3 class="mt-16">Take down “<span x-text="confirmTitle"></span>”?</h3>
              <p class="muted">It disappears from the directory straight away. Past requests keep their history.</p>
              <form method="POST" :action="'/services/' + confirmId + '/delete'" class="row mt-24" style="justify-content: flex-end;">
                <input type="hidden" name="confirm" value="yes">
                <input type="hidden" name="id" :value="confirmId">
                <button type="button" class="btn" @click="confirmId = null">Keep it</button>
                <button type="submit" class="btn btn-danger"><span class="spinner"></span>Delete</button>
              </form>
            </div>
          </div>
        </template>
      </section>

      <!-- Requests -->
      <section class="panel" x-data="{ tab: '<?= $pending ? 'pending' : 'all' ?>' }">
        <div class="panel-title">
          <h2 style="font-size: 1.6rem;">Requests</h2>
        </div>
        <?php if ($incomingRequests): ?>
          <div class="tabs" role="tablist" style="margin-bottom: 6px;">
            <button type="button" class="tab" :class="{ 'is-on': tab === 'pending' }" @click="tab = 'pending'">New<span class="count"><?= count($pending) ?></span></button>
            <button type="button" class="tab" :class="{ 'is-on': tab === 'open' }" @click="tab = 'open'">Open<span class="count"><?= count($openWork) ?></span></button>
            <button type="button" class="tab" :class="{ 'is-on': tab === 'all' }" @click="tab = 'all'">All<span class="count"><?= count($incomingRequests) ?></span></button>
          </div>
          <?php foreach ($incomingRequests as $req):
            $group = $req['status'] === 'pending' ? 'pending' : (in_array($req['status'], ['accepted', 'in_progress'], true) ? 'open' : 'done'); ?>
            <div class="inbox-item" x-show="tab === 'all' || tab === '<?= $group ?>'" x-transition.opacity>
              <?= avatar($req['client_avatar'] ?? null, $req['client_name'], 'sm') ?>
              <div class="grow">
                <div class="row between" style="gap: 8px;">
                  <b class="small"><?= e($req['client_name']) ?></b>
                  <span class="small muted"><?= e(timeAgo($req['created_at'])) ?></span>
                </div>
                <p><?= e($req['message']) ?></p>
                <div class="row row-wrap" style="gap: 8px;">
                  <?= statusPill($req['status']) ?>
                  <a href="/services/<?= (int)$req['service_id'] ?>" class="small muted" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 220px;"><?= e($req['service_title']) ?></a>
                </div>
                <div class="row row-wrap mt-8" style="gap: 6px;">
                  <?php $party = 'freelancer'; include BASE_PATH . '/app/Views/partials/request-actions.php'; ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
          <p class="small muted mt-16 mb-0" x-show="tab === 'pending' && <?= count($pending) ?> === 0">Nothing new. Nice.</p>
          <p class="small muted mt-16 mb-0" x-show="tab === 'open' && <?= count($openWork) ?> === 0" x-cloak>No open projects right now.</p>
        <?php else: ?>
          <p class="muted mb-0">No requests yet. They'll land here when a client gets in touch about one of your services.</p>
        <?php endif; ?>
      </section>
    </div>
  </main>
</div>

</body>
</html>
