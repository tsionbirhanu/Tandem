<?php
// client-dashboard.php
// Client SaaS Dashboard refactored to use ProjectRequest and Review models.

require_once 'includes/Database.php';
require_once 'includes/auth.php';

use App\Models\ProjectRequest;
use App\Models\Review;

requireRole('client');

$user = currentUser();
$dbError = null;

$stats = [
    'active_requests'    => 0,
    'completed_projects' => 0,
    'reviews_given'      => 0,
    'total_spent'        => 0.00,
];
$requests = [];

try {
    $pdo = Database::getConnection();

    $requestModel = new ProjectRequest($pdo);
    $reviewModel  = new Review($pdo);

    $stats['active_requests']    = $requestModel->countActiveByClient($user['id']);
    $stats['completed_projects'] = $requestModel->countCompletedByClient($user['id']);
    $stats['reviews_given']      = $reviewModel->countByClient($user['id']);
    $stats['total_spent']        = $requestModel->totalSpentByClient($user['id']);

    $requests = $requestModel->findByClient($user['id']);

} catch (Exception $e) {
    $dbError = "Database Error: Unable to fetch dashboard metrics. " . $e->getMessage();
}

include 'includes/header.php';
?>

<!-- Mobile Navigation Bar -->
<div class="mobile-toggle-bar">
    <span style="font-weight: 600; font-family: var(--font-heading); color: var(--color-primary);">Client Portal</span>
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
                <span class="badge badge-primary" style="text-transform: uppercase; font-size: 0.7rem; letter-spacing: 0.05em;">Client Workspace</span>
                <h3 style="margin-top: var(--space-8); margin-bottom: 0; font-size: var(--text-h5);"><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                <p class="text-caption" style="margin: 0; color: var(--color-text-muted);"><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></p>
            </div>

            <ul class="sidebar-menu">
                <li>
                    <a href="client-dashboard.php" class="sidebar-link active">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                        Dashboard Overview
                    </a>
                </li>
                <li>
                    <a href="services.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                        Browse Services
                    </a>
                </li>
                <li>
                    <a href="contact.php" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
                        Send Inquiry
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
                <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-4);">Client Dashboard</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Manage your active requests, hired freelancers, and project milestones.</p>
            </div>
            <a href="services.php" class="btn btn-primary">+ Hire Freelancer</a>
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
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['active_requests']; ?></div>
                    <div class="stat-label">Active Requests</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(72, 187, 120, 0.12); color: var(--color-success);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['completed_projects']; ?></div>
                    <div class="stat-label">Completed Projects</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(217, 140, 109, 0.15); color: var(--color-accent);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo $stats['reviews_given']; ?></div>
                    <div class="stat-label">Reviews Given</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(45, 55, 72, 0.08); color: var(--color-text-neutral);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"></line><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <div>
                    <div class="stat-value">$<?php echo number_format($stats['total_spent'], 0); ?></div>
                    <div class="stat-label">Total Investment</div>
                </div>
            </div>
        </div>

        <!-- Project Requests Table -->
        <div class="sg-card">
            <h2 style="font-size: var(--text-h4); margin-bottom: var(--space-16);">My Project Requests</h2>

            <?php if (!empty($requests)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: var(--text-small);">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--color-border); text-align: left; color: var(--color-text-muted);">
                                <th style="padding: var(--space-12);">Service Title</th>
                                <th style="padding: var(--space-12);">Category</th>
                                <th style="padding: var(--space-12);">Freelancer</th>
                                <th style="padding: var(--space-12);">Price</th>
                                <th style="padding: var(--space-12);">Status</th>
                                <th style="padding: var(--space-12);">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: var(--space-12); font-weight: 600;">
                                        <a href="service-details.php?id=<?php echo (int)$req['service_id']; ?>" style="color: inherit; text-decoration: none;">
                                            <?php echo htmlspecialchars($req['service_title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <span class="badge badge-neutral"><?php echo htmlspecialchars($req['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <?php echo htmlspecialchars($req['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?>
                                    </td>
                                    <td style="padding: var(--space-12); font-weight: 600; color: var(--color-primary);">
                                        $<?php echo number_format((float)$req['price'], 2); ?>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <?php
                                            $badgeClass = 'badge-neutral';
                                            if ($req['status'] === 'completed') $badgeClass = 'badge-success';
                                            if ($req['status'] === 'in_progress' || $req['status'] === 'accepted') $badgeClass = 'badge-primary';
                                        ?>
                                        <span class="badge <?php echo $badgeClass; ?>" style="text-transform: capitalize;">
                                            <?php echo htmlspecialchars(str_replace('_', ' ', $req['status']), ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td style="padding: var(--space-12); color: var(--color-text-muted);">
                                        <?php echo date('M j, Y', strtotime($req['created_at'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: var(--space-32) 0; color: var(--color-text-muted);">
                    <p>You haven't requested any services yet.</p>
                    <a href="services.php" class="btn btn-primary" style="margin-top: var(--space-12);">Browse Services Marketplace</a>
                </div>
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
