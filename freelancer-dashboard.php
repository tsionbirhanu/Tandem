<?php
// freelancer-dashboard.php
// Dashboard view for Freelancer accounts, protected by requireRole('freelancer').

require_once 'includes/Database.php';
require_once 'includes/auth.php';

requireRole('freelancer');

$user = currentUser();
$myServices = [];
$incomingRequests = [];
$dbError = null;

try {
    $pdo = Database::getConnection();
    
    // Fetch freelancer's services
    $svcStmt = $pdo->prepare("SELECT s.*, c.name AS category_name FROM services s JOIN categories c ON s.category_id = c.id WHERE s.freelancer_id = :freelancer_id ORDER BY s.created_at DESC");
    $svcStmt->execute([':freelancer_id' => $user['id']]);
    $myServices = $svcStmt->fetchAll();

    // Fetch incoming project requests
    $reqStmt = $pdo->prepare("SELECT pr.*, s.title AS service_title, u.name AS client_name 
                              FROM project_requests pr 
                              JOIN services s ON pr.service_id = s.id 
                              JOIN users u ON pr.client_id = u.id 
                              WHERE s.freelancer_id = :freelancer_id 
                              ORDER BY pr.created_at DESC");
    $reqStmt->execute([':freelancer_id' => $user['id']]);
    $incomingRequests = $reqStmt->fetchAll();

} catch (Exception $e) {
    $dbError = "Unable to load dashboard data right now.";
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-16); margin-bottom: var(--space-32);">
        <div>
            <h1>Freelancer Dashboard</h1>
            <p class="text-small" style="color: var(--color-text-muted);">
                Welcome back, <strong><?php echo htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8'); ?></strong> (Freelancer Account)
            </p>
        </div>
        <div style="display: flex; gap: var(--space-12);">
            <a href="service-create.php" class="btn btn-primary">+ Post New Service</a>
            <a href="logout.php" class="btn btn-secondary">Log Out</a>
        </div>
    </div>

    <?php if ($dbError): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-24);">
            <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-24);">
        <!-- Active Services -->
        <div class="sg-card">
            <h2 style="font-size: var(--text-h5); margin-bottom: var(--space-16);">My Published Services</h2>
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
                                <a href="service-edit.php?id=<?php echo (int)$svc['id']; ?>" style="font-size: var(--text-caption); color: var(--color-text-muted);">Edit</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--color-text-muted); font-size: var(--text-small);">You have not published any service listings yet.</p>
                <a href="service-create.php" class="btn btn-secondary" style="margin-top: var(--space-12);">Create First Service</a>
            <?php endif; ?>
        </div>

        <!-- Incoming Client Requests -->
        <div class="sg-card">
            <h2 style="font-size: var(--text-h5); margin-bottom: var(--space-16);">Incoming Client Requests</h2>
            <?php if (!empty($incomingRequests)): ?>
                <div style="display: flex; flex-direction: column; gap: var(--space-12);">
                    <?php foreach ($incomingRequests as $req): ?>
                        <div style="border-bottom: 1px solid var(--color-border); padding-bottom: var(--space-12);">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <strong><?php echo htmlspecialchars($req['client_name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <span class="badge badge-neutral" style="text-transform: capitalize; font-size: 0.75rem;">
                                    <?php echo htmlspecialchars($req['status'], ENT_QUOTES, 'UTF-8'); ?>
                                </span>
                            </div>
                            <p style="font-size: var(--text-small); color: var(--color-text-muted); margin: 4px 0 0 0;">
                                Service: <?php echo htmlspecialchars($req['service_title'], ENT_QUOTES, 'UTF-8'); ?>
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: var(--color-text-muted); font-size: var(--text-small);">No incoming requests at this moment.</p>
            <?php endif; ?>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
