<?php
// app/Views/errors/404.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-64); text-align: center;">
    <div style="max-width: 500px; margin: 0 auto;">
        <h1 style="font-size: 4rem; color: var(--color-primary); margin-bottom: var(--space-16);">404</h1>
        <h2>Page Not Found</h2>
        <p style="color: var(--color-text-muted); margin-bottom: var(--space-32);">
            The page you requested (<code><?php echo htmlspecialchars($path ?? '', ENT_QUOTES, 'UTF-8'); ?></code>) could not be found.
        </p>
        <a href="/" class="btn btn-primary">Return to Home</a>
    </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
