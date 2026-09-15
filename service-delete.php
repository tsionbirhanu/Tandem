<?php
// service-delete.php
// Handles service deletion using Service model.

require_once 'includes/Database.php';

use App\Models\Service;

$serviceId = (int)($_REQUEST['id'] ?? 0);
$service = null;
$error = null;

try {
    $pdo = Database::getConnection();
    $serviceModel = new Service($pdo);

    $service = $serviceModel->find($serviceId);

    if (!$service) {
        $error = "Service listing not found.";
    }

} catch (Exception $e) {
    $error = "Database Connection Error: Unable to perform request.";
}

// Perform Deletion on POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $service && !$error) {
    try {
        $serviceModel->delete($serviceId);

        header("Location: services.php?msg=deleted");
        exit;
    } catch (Exception $e) {
        $error = "Failed to delete service: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 550px; margin: 0 auto;">
        
        <?php if ($error): ?>
            <div class="sg-card" style="text-align: center; padding: var(--space-48) var(--space-32);">
                <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                    <strong>Error:</strong> <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <a href="services.php" class="btn btn-secondary">Back to Services</a>
            </div>
        <?php else: ?>
            
            <div class="sg-card" style="text-align: center; padding: var(--space-48) var(--space-32);">
                <div style="width: 64px; height: 64px; background-color: rgba(245, 101, 101, 0.12); color: var(--color-error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-24);">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </div>

                <h1 style="font-size: var(--text-h3); margin-bottom: var(--space-12);">Confirm Deletion</h1>
                
                <p style="color: var(--color-text-muted); margin-bottom: var(--space-24); line-height: 1.6;">
                    Are you sure you want to permanently delete <strong>"<?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>"</strong>?
                </p>

                <div class="alert alert-error" style="margin-bottom: var(--space-32); text-align: left; font-size: var(--text-small);">
                    <strong>Warning:</strong> This action cannot be undone. Associated gallery images will be removed from the database.
                </div>

                <form action="service-delete.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo (int)$service['id']; ?>">
                    <div style="display: flex; gap: var(--space-16); justify-content: center;">
                        <button type="submit" class="btn btn-danger" style="flex: 1;">Yes, Delete Service</button>
                        <a href="service-details.php?id=<?php echo (int)$service['id']; ?>" class="btn btn-secondary" style="flex: 1;">Cancel</a>
                    </div>
                </form>
            </div>

        <?php endif; ?>

    </div>
</main>

<?php include 'includes/footer.php'; ?>
