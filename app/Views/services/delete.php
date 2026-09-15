<?php
// app/Views/services/delete.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 500px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="/services/<?php echo (int)($service['id'] ?? 0); ?>" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
                &larr; Back to Service Details
            </a>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($service): ?>
            <div class="sg-card" style="border: 2px solid var(--color-error); border-radius: var(--radius-lg);">
                <div style="text-align: center; margin-bottom: var(--space-24);">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background-color: rgba(229, 62, 62, 0.1); color: var(--color-error); display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-16) auto;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"></path><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                    </div>
                    <h2>Confirm Service Deletion</h2>
                    <p class="text-small" style="color: var(--color-text-muted);">
                        Are you sure you want to permanently delete <strong>"<?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>"</strong>?
                    </p>
                </div>

                <div class="alert alert-error" style="margin-bottom: var(--space-24); font-size: var(--text-small);">
                    <strong>Warning:</strong> This action cannot be undone.
                </div>

                <form action="/services/<?php echo (int)$service['id']; ?>/delete" method="POST">
                    <input type="hidden" name="id" value="<?php echo (int)$service['id']; ?>">
                    <input type="hidden" name="confirm" value="yes">

                    <div style="display: flex; gap: var(--space-12);">
                        <button type="submit" class="btn btn-primary" style="flex: 1; background-color: var(--color-error); border-color: var(--color-error);">
                            Yes, Delete Service
                        </button>
                        <a href="/services/<?php echo (int)$service['id']; ?>" class="btn btn-secondary" style="flex: 1; text-align: center;">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
