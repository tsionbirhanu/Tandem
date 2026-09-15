<?php
// admin-dashboard.php
// Admin Panel leveraging Admin subclass methods.

require_once 'includes/Database.php';
require_once 'includes/auth.php';

use App\Models\Admin;

requireRole('admin');

/** @var Admin $user */
$user = currentUser();
$dbError = null;

$stats = [
    'users'    => 0,
    'services' => 0,
    'requests' => 0,
    'reviews'  => 0,
];
$allUsers = [];

try {
    $pdo = Database::getConnection();

    // Call role-specific methods on Admin subclass instance
    $stats    = $user->getPlatformStats($pdo);
    $allUsers = $user->getAllUsers($pdo);

} catch (Exception $e) {
    $dbError = "Database Error: Unable to fetch system administration data. " . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- Mobile Navigation Bar -->
<div class="mobile-toggle-bar">
    <span style="font-weight: 600; font-family: var(--font-heading); color: var(--color-primary);"><?php echo htmlspecialchars($user->getRoleDisplayName(), ENT_QUOTES, 'UTF-8'); ?></span>
    <button type="button" id="sidebarToggleBtn" class="btn btn-secondary" style="padding: var(--space-8) var(--space-12);">
        ☰ Menu
    </button>
</div>

<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="dashboard-container">
    
    <!-- Left Sidebar Navigation -->
    <aside class="dashboard-sidebar" id="dashboardSidebar">
        <div>
            <div class="dashboard-sidebar-header">
                <span class="badge badge-primary" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em; background-color: var(--color-primary-dark);"><?php echo htmlspecialchars($user->getRoleDisplayName(), ENT_QUOTES, 'UTF-8'); ?></span>
                <h3 style="margin-top: var(--space-8); margin-bottom: 0; font-size: var(--text-h5);"><?php echo htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="text-caption" style="margin: 0; color: var(--color-text-muted);"><?php echo htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo htmlspecialchars($user->getDashboardUrl(), ENT_QUOTES, 'UTF-8'); ?>" class="sidebar-link active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        System Overview
                    </a>
                </li>
                <li>
                    <a href="services.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        Manage Services
                    </a>
                </li>
                <li>
                    <a href="service-create.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Create Listing
                    </a>
                </li>
            </ul>
        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: var(--space-16);">
            <a href="logout.php" class="sidebar-link" style="color: var(--color-error);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Log Out
            </a>
        </div>
    </aside>

    <!-- Main SaaS Content Area -->
    <main class="dashboard-main-content">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-32); flex-wrap: wrap; gap: var(--space-16);">
            <div>
                <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-4);">Admin Console</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Platform statistics, user accounts, and content moderation.</p>
            </div>
            <a href="services.php" class="btn btn-secondary">Services Directory</a>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- Stats Summary Row -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(44, 95, 93, 0.12); color: var(--color-primary);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['users']; ?></div>
                    <div class="stat-label">Total Users</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(72, 187, 120, 0.12); color: var(--color-success);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['services']; ?></div>
                    <div class="stat-label">Total Services</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(236, 201, 75, 0.2); color: #b7791f;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['requests']; ?></div>
                    <div class="stat-label">Project Requests</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(217, 140, 109, 0.15); color: var(--color-accent);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['reviews']; ?></div>
                    <div class="stat-label">Platform Reviews</div>
                </div>
            </div>
        </div>

        <!-- Users Table -->
        <div class="sg-card" style="margin-bottom: var(--space-32);">
            <h2 style="font-size: var(--text-h4); margin-bottom: var(--space-16);">Registered User Directory</h2>

            <?php if (!empty($allUsers)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: var(--text-small);">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--color-border); text-align: left; color: var(--color-text-muted);">
                                <th style="padding: var(--space-12);">ID</th>
                                <th style="padding: var(--space-12);">Name</th>
                                <th style="padding: var(--space-12);">Email</th>
                                <th style="padding: var(--space-12);">Role</th>
                                <th style="padding: var(--space-12);">Joined Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allUsers as $u): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: var(--space-12); font-weight: 600; color: var(--color-text-muted);">
                                        #<?php echo (int)$u['id']; ?>
                                    </td>
                                    <td style="padding: var(--space-12); font-weight: 600;">
                                        <?php echo htmlspecialchars($u['name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <?php echo htmlspecialchars($u['email'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <?php
                                            $roleBadge = 'badge-neutral';
                                            if ($u['role'] === 'admin') $roleBadge = 'badge-primary';
                                            if ($u['role'] === 'freelancer') $roleBadge = 'badge-success';
                                        ?>
                                        <span class="badge <?php echo $roleBadge; ?>" style="text-transform: uppercase; font-size: 0.7rem;">
                                            <?php echo htmlspecialchars($u['role'], ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="padding: var(--space-12); color: var(--color-text-muted);">
                                        <?php echo date('M j, Y', strtotime($u['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="color: var(--color-text-muted);">No users found.</p>
            <?php endif; ?>
        </div>

    </main>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('sidebarToggleBtn');
    const sidebar = document.getElementById('dashboardSidebar');
    const overlay = document.getElementById('sidebarOverlay');

    if (btn && sidebar && overlay) {
        btn.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        });

        overlay.addEventListener('click', function() {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>
