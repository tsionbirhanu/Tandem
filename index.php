<?php
// index.php
// Homepage of Tandem featuring top-rated service listings queried via Service model.

require_once 'includes/Database.php';

use App\Models\Service;

$featuredServices = [];

try {
    $pdo = Database::getConnection();
    $serviceModel = new Service($pdo);
    $featuredServices = $serviceModel->featured(3);
} catch (Exception $e) {
    $featuredServices = [];
}

include 'includes/header.php';
?>

<main class="page-main">
    <!-- Hero Section -->
    <section class="sg-header" style="text-align: center; padding: var(--space-64) 0;">
        <div class="sg-container hero-animated">
            <h1 style="font-size: var(--text-h1);">Find premium talent for your next project.</h1>
            <p style="font-size: var(--text-h5); color: var(--color-text-muted); max-width: 600px; margin: 0 auto var(--space-32) auto;">
                Tandem is a curated network of top-tier designers, developers, and writers.
            </p>
            <a href="services.php" class="btn btn-primary">Browse All Services</a>
        </div>
    </section>

    <!-- Featured Services Section -->
    <section class="sg-container sg-section">
        <h2 style="text-align: center;">Featured Services</h2>
        <div class="sg-grid-auto" style="margin-top: var(--space-32);">
            <?php if (!empty($featuredServices)): ?>
                <?php foreach ($featuredServices as $service): ?>
                    <a href="service-details.php?id=<?php echo (int)$service['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                        <article class="card-listing" style="height: 100%;">
                            <div class="card-image-placeholder"></div>
                            <div class="card-content">
                                <div class="card-badge-row">
                                    <span class="badge badge-neutral"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if ($service['rating'] >= 4.9): ?>
                                        <span class="badge badge-success">Top Rated</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="card-title"><?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                                <p class="card-desc">By <?php echo htmlspecialchars($service['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                                <div class="card-footer">
                                    <div class="star-rating">
                                        <span class="star filled">★</span>
                                        <span class="rating-text"><?php echo number_format((float)$service['rating'], 1); ?> (<?php echo (int)$service['reviews']; ?>)</span>
                                    </div>
                                    <div class="card-price">Starting at $<?php echo number_format((float)$service['price'], 2); ?></div>
                                </div>
                            </div>
                        </article>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: var(--space-32) 0;">
                    <p>Discover our wide selection of services available on Tandem.</p>
                    <a href="services.php" class="btn btn-secondary" style="margin-top: var(--space-12);">View Services Directory</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
