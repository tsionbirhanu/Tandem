<?php
// app/Views/dashboard/admin.php
$pageTitle = 'Admin';
include BASE_PATH . '/app/Views/layouts/header.php';

$usersJson = array_map(fn($u) => [
    'id'      => (int)$u['id'],
    'name'    => $u['name'],
    'email'   => $u['email'],
    'role'    => $u['role'],
    'joined'  => date('M j, Y', strtotime($u['created_at'])),
    'ts'      => strtotime($u['created_at']),
    'initials'=> initials($u['name']),
    'tint'    => abs(crc32($u['name'])) % 4,
], $allUsers ?? []);
$roleCounts = array_count_values(array_column($allUsers ?? [], 'role'));
?>

<div class="dash">
  <?php include BASE_PATH . '/app/Views/partials/dashboard-nav.php'; ?>

  <main class="dash-main">
    <header class="dash-head">
      <div>
        <span class="eyebrow">Admin</span>
        <h1>The whole <em>workshop</em>.</h1>
      </div>
      <a href="/services" class="btn">Open directory</a>
    </header>

    <?php if (!empty($dbError)): ?>
      <div class="alert"><?= e($dbError) ?></div>
    <?php endif; ?>

    <section class="stats" aria-label="Platform totals">
      <div class="stat"><span class="stat-label">People</span><div class="stat-value" data-count="<?= (int)$stats['users'] ?>">0</div><span class="stat-note"><?= (int)($roleCounts['freelancer'] ?? 0) ?> freelancers · <?= (int)($roleCounts['client'] ?? 0) ?> clients</span></div>
      <div class="stat"><span class="stat-label">Services</span><div class="stat-value" data-count="<?= (int)$stats['services'] ?>">0</div><span class="stat-note">listed</span></div>
      <div class="stat"><span class="stat-label">Requests</span><div class="stat-value" data-count="<?= (int)$stats['requests'] ?>">0</div><span class="stat-note">all time</span></div>
      <div class="stat"><span class="stat-label">Reviews</span><div class="stat-value" data-count="<?= (int)$stats['reviews'] ?>">0</div><span class="stat-note">published</span></div>
    </section>

    <section class="panel"
             x-data='{
               users: <?= json_encode($usersJson, JSON_HEX_APOS | JSON_HEX_TAG | JSON_HEX_AMP) ?>,
               q: "", role: "all", sortKey: "id", dir: 1,
               get rows() {
                 const q = this.q.toLowerCase().trim();
                 return this.users
                   .filter(u => this.role === "all" || u.role === this.role)
                   .filter(u => !q || u.name.toLowerCase().includes(q) || u.email.toLowerCase().includes(q))
                   .sort((a, b) => (a[this.sortKey] > b[this.sortKey] ? 1 : a[this.sortKey] < b[this.sortKey] ? -1 : 0) * this.dir);
               },
               sortBy(k) { this.dir = this.sortKey === k ? -this.dir : 1; this.sortKey = k; }
             }'>
      <div class="panel-title row-wrap">
        <h2 style="font-size: 1.6rem;">Members</h2>
        <span class="small muted" x-text="rows.length + ' of ' + users.length"></span>
      </div>

      <div class="row row-wrap mb-16">
        <div class="search-bar grow" style="margin: 0; box-shadow: none; min-width: 220px;">
          <svg width="16" height="16" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="9" cy="9" r="6"/><path d="M13.5 13.5L18 18"/></svg>
          <input type="search" x-model="q" placeholder="Search by name or email" aria-label="Search members" data-hotkey="/">
        </div>
        <div class="chips">
          <?php foreach (['all' => 'Everyone', 'client' => 'Clients', 'freelancer' => 'Freelancers', 'admin' => 'Admins'] as $key => $label): ?>
            <label class="chip"><input type="radio" value="<?= $key ?>" x-model="role"><span><?= $label ?></span></label>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="table-wrap">
        <table class="table">
          <thead>
            <tr>
              <th><button class="th-sort" @click="sortBy('id')">#<span x-show="sortKey === 'id'" x-text="dir > 0 ? '↑' : '↓'"></span></button></th>
              <th><button class="th-sort" @click="sortBy('name')">Name<span x-show="sortKey === 'name'" x-text="dir > 0 ? '↑' : '↓'"></span></button></th>
              <th>Role</th>
              <th><button class="th-sort" @click="sortBy('ts')">Joined<span x-show="sortKey === 'ts'" x-text="dir > 0 ? '↑' : '↓'"></span></button></th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <template x-for="u in rows" :key="u.id">
              <tr>
                <td class="mono muted" x-text="u.id"></td>
                <td>
                  <div class="row" style="gap: 10px;">
                    <span class="avatar avatar-sm" :class="'avatar-tint-' + u.tint" x-text="u.initials"></span>
                    <span><b class="small" style="display: block;" x-text="u.name"></b><span class="small muted" x-text="u.email"></span></span>
                  </div>
                </td>
                <td><span class="tag" :class="{ 'tag-moss': u.role === 'freelancer', 'tag-accent': u.role === 'admin' }" x-text="u.role"></span></td>
                <td class="small muted" x-text="u.joined"></td>
                <td><div class="t-actions"><a class="btn btn-sm" x-show="u.role === 'freelancer'" :href="'/freelancer/' + u.id">Profile</a></div></td>
              </tr>
            </template>
          </tbody>
        </table>
        <p class="muted small mt-16" x-show="rows.length === 0" x-cloak>Nobody matches that search.</p>
      </div>
    </section>
  </main>
</div>

</body>
</html>
