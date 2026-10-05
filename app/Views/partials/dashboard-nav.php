<?php
// app/Views/partials/dashboard-nav.php
// Sidebar (desktop) and scrolling pill bar (mobile) for the dashboards. Expects $user.

$icons = [
    'home'    => '<path d="M2.5 8L9 2.5 15.5 8v7.5h-4.5V11H7v4.5H2.5z"/>',
    'search'  => '<circle cx="8" cy="8" r="5"/><path d="M12 12l4 4"/>',
    'plus'    => '<path d="M9 3v12M3 9h12"/>',
    'user'    => '<circle cx="9" cy="6" r="3.2"/><path d="M3 16c.8-3.2 3.2-4.8 6-4.8s5.2 1.6 6 4.8"/>',
    'pen'     => '<path d="M11.5 3l3.5 3.5L6 15.5H2.5V12z"/>',
    'mail'    => '<rect x="2" y="4" width="14" height="10" rx="1.5"/><path d="M2.5 5l6.5 5 6.5-5"/>',
];

$links = [['Overview', $user->getDashboardUrl(), 'home']];
if ($user->isFreelancer()) {
    $links[] = ['New service', '/services/create', 'plus'];
    $links[] = ['Public profile', '/freelancer/' . (int)$user->getId(), 'user'];
}
$links[] = ['Browse work', '/services', 'search'];
$links[] = ['Edit profile', '/profile/edit', 'pen'];
$links[] = ['Contact', '/contact', 'mail'];

$current = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
$renderLinks = function () use ($links, $icons, $current) {
    foreach ($links as [$label, $href, $icon]) {
        $active = $href === $current ? ' is-active' : '';
        echo '<a class="dash-link' . $active . '" href="' . e($href) . '">'
           . '<svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $icons[$icon] . '</svg>'
           . e($label) . '</a>';
    }
};
?>
<nav class="mobile-dash-nav" aria-label="Dashboard"><?php $renderLinks(); ?></nav>

<aside class="dash-side" aria-label="Dashboard">
  <div class="dash-who">
    <?= avatar($user->getAvatarUrl(), $user->getName(), 'md') ?>
    <span>
      <b><?= e($user->getName()) ?></b>
      <span class="small muted"><?= e(ucfirst($user->getRole())) ?></span>
    </span>
  </div>
  <?php $renderLinks(); ?>
  <span class="spacer"></span>
  <form action="/logout" method="POST">
    <button type="submit" class="dash-link" style="border: 0; background: none; width: 100%; cursor: pointer; color: var(--danger);">
      <svg width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"><path d="M7 15.5H3v-13h4M11.5 12.5L15 9l-3.5-3.5M15 9H7"/></svg>
      Log out
    </button>
  </form>
</aside>
