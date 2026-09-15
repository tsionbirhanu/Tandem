<?php
// freelancer-dashboard.php
// Freelancer SaaS Dashboard leveraging Freelancer subclass methods.

require_once 'includes/Database.php';
require_once 'includes/auth.php';

use App\Models\Freelancer;

requireRole('freelancer');

/** @var Freelancer $user */
$user = currentUser();
$dbError = null;

$stats = [
    'active_services'      => 0,
    'pending_requests'     => 0,
    'in_progress_projects' => 0,
    'avg_rating'           => 5.0,
];
$myServices = [];
$incomingRequests = [];

try {
    $pdo = Database::getConnection();

    // Call role-specific methods on Freelancer subclass instance
    $stats['active_services']      = $user->getActiveServicesCount($pdo);
    $stats['pending_requests']     = $user->getPendingRequestsCount($pdo);
    $stats['in_progress_projects'] = $user->getInProgressProjectsCount($pdo);
    $stats['avg_rating']           = $user->getAverageRating($pdo);

    $myServices       = $user->getServices($pdo);
    $incomingRequests = $user->getIncomingRequests($pdo);

} catch (Exception $e) {
    $dbError = "Database Error: Unable to load freelancer metrics. " . $e->getMessage();
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
                <span class="badge badge-success" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;"><?php echo htmlspecialchars($user->getRoleDisplayName(), ENT_QUOTES, 'UTF-8'); ?></span>
                <h3 style="margin-top: var(--space-8); margin-bottom: 0; font-size: var(--text-h5);"><?php echo htmlspecialchars($user->getName(), ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="text-caption" style="margin: 0; color: var(--color-text-muted);"><?php echo htmlspecialchars($user->getEmail(), ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="<?php echo htmlspecialchars($user->getDashboardUrl(), ENT_QUOTES, 'UTF-8'); ?>" class="sidebar-link active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Dashboard Overview
                    </a>
                </li>
                <li>
                    <a href="service-create.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Post New Service
                    </a>
                </li>
                <li>
                    <a href="services.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        Browse Marketplace
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
                <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-4);">Freelancer Dashboard</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Track active service listings, incoming client inquiries, and performance feedback.</p>
            </div>
            <a href="service-create.php" class="btn btn-primary">+ Post Service</a>
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['active_services']; ?></div>
                    <div class="stat-label">Active Services</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(236, 201, 75, 0.2); color: #b7791f;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['pending_requests']; ?></div>
                    <div class="stat-label">Pending Requests</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(72, 187, 120, 0.12); color: var(--color-success);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"></polyline><polyline points="17 6 23 6 23 12"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['in_progress_projects']; ?></div>
                    <div class="stat-label">In-Progress Projects</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(217, 140, 109, 0.15); color: var(--color-accent);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo number_format($stats['avg_rating'], 1); ?> ★</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-24);">
            
            <!-- My Published Services -->
            <div class="sg-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-16);">
                    <h2 style="font-size: var(--text-h5); margin: 0;">My Active Listings</h2>
                    <a href="service-create.php" style="font-size: var(--text-caption); color: var(--color-primary); font-weight: 600;">+ Add New</a>
                </div>

                <?php if (!empty($myServices)): ?>
                    <div style="display: flex; flex-direction: column; gap: var(--space-12);">
                        <?php foreach ($myServices as $svc): ?>
                            <div style="border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-12); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <h4 style="margin: 0; font-size: var(--text-body); font-weight: 600;">
                                        <a href="service-details.php?id=<?php echo (int)$svc['id']; ?>" style="text-decoration: none; color: inherit;">
                                            <?php echo htmlspecialchars($svc['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </h4>
                                    <span class="badge badge-neutral" style="font-size: 0.75rem; margin-top: 4px;">
                                        <?php echo htmlspecialchars($svc['category_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: var(--color-primary);">$<?php echo number_format((float)$svc['price'], 2); ?></div>
                                    <div style="display: flex; gap: var(--space-8); justify-content: flex-end; margin-top: 4px;">
                                        <a href="service-edit.php?id=<?php echo (int)$svc['id']; ?>" style="font-size: var(--text-caption); color: var(--color-primary);">Edit</a>
                                        <a href="service-delete.php?id=<?php echo (int)$svc['id']; ?>" style="font-size: var(--text-caption); color: var(--color-error);">Delete</a>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--color-text-muted); font-size: var(--text-small);">You haven't posted any services yet.</p>
                <?php endif; ?>
            </div>

            <!-- Incoming Client Requests -->
            <div class="sg-card">
                <h2 style="font-size: var(--text-h5); margin-bottom: var(--space-16);">Incoming Client Requests</h2>

                <?php if (!empty($incomingRequests)): ?>
                    <div style="display: flex; flex-direction: column; gap: var(--space-12);">
                        <?php foreach ($incomingRequests as $req): ?>
                            <div style="border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-12);">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 4px;">
                                    <span style="font-weight: 600; font-size: var(--text-small);">
                                        <?php echo htmlspecialchars($req['client_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                    <span class="badge badge-neutral" style="text-transform: capitalize; font-size: 0.75rem;">
                                        <?php echo htmlspecialchars($req['status'], ENT_QUOTES, 'UTF-8'); ?>
                                    </span>
                                </div>
                                <p style="font-size: var(--text-caption); color: var(--color-text-muted); margin: 0 0 6px 0;">
                                    For: <strong><?php echo htmlspecialchars($req['service_title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                </p>
                                <p style="font-size: var(--text-caption); color: var(--color-text-neutral); font-style: italic; margin: 0; background: var(--color-bg-base); padding: var(--space-8); border-radius: var(--radius-sm);">
                                    "<?php echo htmlspecialchars(mb_strimwidth($req['message'], 0, 80, '...'), ENT_QUOTES, 'UTF-8'); ?>"
                                </p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p style="color: var(--color-text-muted); font-size: var(--text-small);">No client requests received yet.</p>
                <?php endif; ?>
            </div>

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
