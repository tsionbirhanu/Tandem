<?php
// admin-dashboard.php
// Dashboard view for Admin accounts, protected by requireRole('admin').

require_once 'includes/Database.php';
require_once 'includes/auth.php';

requireRole('admin');

$user = currentUser();
$stats = [
    'users' => 0,
    'services' => 0,
    'requests' => 0,
    'reviews' => 0,
];
$dbError = null;

try {
    $pdo = Database::getConnection();
    $stats['users']    = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['services'] = (int)$pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $stats['requests'] = (int)$pdo->query("SELECT COUNT(*) FROM project_requests")->fetchColumn();
    $stats['reviews']  = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
} catch (Exception $e) {
    $dbError = "Unable to load platform statistics right now.";
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-16); margin-bottom: var(--space-32);">
        <div>
            <h1>Admin Control Panel</h1>
            <p class="text-small" style="color: var(--color-text-muted);">
                System Administrator: <strong><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </p>
        </div>
        <div style="display: flex; gap: var(--space-12);">
            <a href="services.php" class="btn btn-secondary">Manage Services</a>
            <a href="logout.php" class="btn btn-secondary">Log Out</a>
        </div>
    </div>

    <?php if ($dbError): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-24);">
            <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <h2 style="font-size: var(--text-h4); margin-bottom: var(--space-16);">Network Metrics Overview</h2>

    <div class="sg-grid-auto" style="grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); margin-bottom: var(--space-32);">
        <div class="sg-card" style="text-align: center; padding: var(--space-24);">
            <h3 style="font-size: var(--text-h1); color: var(--color-primary); margin-bottom: 0;"><?php echo $stats['users']; ?></h3>
            <p class="text-small" style="color: var(--color-text-muted); margin: 0;">Registered Users</p>
        </div>

        <div class="sg-card" style="text-align: center; padding: var(--space-24);">
            <h3 style="font-size: var(--text-h1); color: var(--color-primary); margin-bottom: 0;"><?php echo $stats['services']; ?></h3>
            <p class="text-small" style="color: var(--color-text-muted); margin: 0;">Active Services</p>
        </div>

        <div class="sg-card" style="text-align: center; padding: var(--space-24);">
            <h3 style="font-size: var(--text-h1); color: var(--color-primary); margin-bottom: 0;"><?php echo $stats['requests']; ?></h3>
            <p class="text-small" style="color: var(--color-text-muted); margin: 0;">Project Requests</p>
        </div>

        <div class="sg-card" style="text-align: center; padding: var(--space-24);">
            <h3 style="font-size: var(--text-h1); color: var(--color-primary); margin-bottom: 0;"><?php echo $stats['reviews']; ?></h3>
            <p class="text-small" style="color: var(--color-text-muted); margin: 0;">Client Reviews</p>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
