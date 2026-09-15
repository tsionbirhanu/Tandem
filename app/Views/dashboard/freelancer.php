<?php
// app/Views/dashboard/freelancer.php
include BASE_PATH . '/app/Views/layouts/header.php';
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
                        Overview
                    </a>
                </li>
                <li>
                    <a href="/services/create" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        Create Service Listing
                    </a>
                </li>
                <li>
                    <a href="/profile/edit" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                        Edit Profile
                    </a>
                </li>
                <li>
                    <a href="/services" class="sidebar-link">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path></svg>
                        All Services Directory
                    </a>
                </li>
            </ul>
        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: var(--space-16);">
            <a href="/logout" class="sidebar-link" style="color: var(--color-error);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1="21" y1="12" x2="9" y2="12"></line></svg>
                Log Out
            </a>
        </div>
    </aside>

    <!-- Main SaaS Content Area -->
    <main class="dashboard-main-content">
        
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-32); flex-wrap: wrap; gap: var(--space-16);">
            <div>
                <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-4);">Freelancer Workspace</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Track your offered services, client requests, and client rating metrics.</p>
            </div>
            <a href="/services/create" class="btn btn-primary">+ Create New Service</a>
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
                    <div class="stat-value"><?php echo (int)($stats['active_services'] ?? 0); ?></div>
                    <div class="stat-label">Active Services</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(236, 201, 75, 0.2); color: #b7791f;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo (int)($stats['pending_requests'] ?? 0); ?></div>
                    <div class="stat-label">Pending Requests</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(72, 187, 120, 0.12); color: var(--color-success);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo (int)($stats['in_progress'] ?? 0); ?></div>
                    <div class="stat-label">In-Progress Projects</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon-wrapper" style="background-color: rgba(217, 140, 109, 0.15); color: var(--color-accent);">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <div>
                    <div class="stat-value"><?php echo number_format((float)($stats['average_rating'] ?? 0), 1); ?> ★</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        </div>

        <!-- My Services Table -->
        <div class="sg-card" style="margin-bottom: var(--space-32);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-16);">
                <h2 style="font-size: var(--text-h4); margin: 0;">My Active Listings</h2>
                <a href="/service/create" class="btn btn-secondary" style="padding: var(--space-6) var(--space-12); font-size: 0.85rem;">+ Add New</a>
            </div>

            <?php if (!empty($myServices)): ?>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: var(--text-small);">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--color-border); text-align: left; color: var(--color-text-muted);">
                                <th style="padding: var(--space-12);">Title</th>
                                <th style="padding: var(--space-12);">Category</th>
                                <th style="padding: var(--space-12);">Price</th>
                                <th style="padding: var(--space-12);">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($myServices as $service): ?>
                                <tr style="border-bottom: 1px solid var(--color-border);">
                                    <td style="padding: var(--space-12); font-weight: 600;">
                                        <a href="/services/<?php echo (int)$service['id']; ?>" style="color: var(--color-primary); text-decoration: none;">
                                            <?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>
                                        </a>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <span class="badge badge-neutral"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td style="padding: var(--space-12); font-weight: 600; color: var(--color-primary);">
                                        $<?php echo number_format((float)$service['price'], 2); ?>
                                    </td>
                                    <td style="padding: var(--space-12);">
                                        <div style="display: flex; gap: var(--space-8);">
                                            <a href="/services/<?php echo (int)$service['id']; ?>/edit" class="btn btn-secondary" style="padding: var(--space-4) var(--space-8); font-size: 0.8rem;">Edit</a>
                                            <a href="/services/<?php echo (int)$service['id']; ?>/delete" class="btn btn-secondary" style="padding: var(--space-4) var(--space-8); font-size: 0.8rem; color: var(--color-error); border-color: var(--color-error);">Delete</a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div style="text-align: center; padding: var(--space-32) 0; color: var(--color-text-muted);">
                    <p>You haven't listed any services yet.</p>
                    <a href="/service/create" class="btn btn-primary" style="margin-top: var(--space-12);">Create Your First Service</a>
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

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
