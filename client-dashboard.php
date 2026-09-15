<?php
// client-dashboard.php
// Dashboard view for Client accounts, protected by requireRole('client').

require_once 'includes/Database.php';
require_once 'includes/auth.php';

requireRole('client');

$user = currentUser();
$requests = [];
$dbError = null;

try {
    $pdo = Database::getConnection();
    $stmt = $pdo->prepare("SELECT pr.*, s.title AS service_title, u.name AS freelancer_name 
                           FROM project_requests pr 
                           JOIN services s ON pr.service_id = s.id 
                           JOIN users u ON s.freelancer_id = u.id 
                           WHERE pr.client_id = :client_id 
                           ORDER BY pr.created_at DESC");
    $stmt->execute([':client_id' => $user['id']]);
    $requests = $stmt->fetchAll();
} catch (Exception $e) {
    $dbError = "Unable to load dashboard data right now.";
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-16); margin-bottom: var(--space-32);">
        <div>
            <h1>Client Dashboard</h1>
            <p class="text-small" style="color: var(--color-text-muted);">
                Welcome back, <strong><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></strong> (Client Account)
            </p>
        </div>
        <div style="display: flex; gap: var(--space-12);">
            <a href="services.php" class="btn btn-primary">Find Services</a>
            <a href="logout.php" class="btn btn-secondary">Log Out</a>
        </div>
    </div>

    <?php if ($dbError): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-24);">
            <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <h2 style="font-size: var(--text-h4); margin-bottom: var(--space-16);">My Project Requests</h2>

    <div class="sg-card">
        <?php if (!empty($requests)): ?>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: var(--text-small);">
                    <thead>
                        <tr style="border-bottom: 2px solid var(--color-border); text-align: left;">
                            <th style="padding: var(--space-12);">Service Requested</th>
                            <th style="padding: var(--space-12);">Freelancer</th>
                            <th style="padding: var(--space-12);">Status</th>
                            <th style="padding: var(--space-12);">Date Submitted</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr style="border-bottom: 1px solid var(--color-border);">
                                <td style="padding: var(--space-12); font-weight: 500;">
                                    <?php echo htmlspecialchars($req['service_title'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td style="padding: var(--space-12);">
                                    <?php echo htmlspecialchars($req['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </td>
                                <td style="padding: var(--space-12);">
                                    <span class="badge badge-neutral" style="text-transform: capitalize;">
                                        <?php echo htmlspecialchars($req['status'], ENT_QUOTES, 'UTF-8'); ?>
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
            <div style="text-align: center; padding: var(--space-24) 0; color: var(--color-text-muted);">
                <p>You haven't submitted any project requests yet.</p>
                <a href="services.php" class="btn btn-primary" style="margin-top: var(--space-12);">Explore Marketplace</a>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
