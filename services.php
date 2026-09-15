<?php
// services.php
// Lists all services from the database with category filtering and database error handling.

require_once 'includes/Database.php';

$dbError = null;
$categories = [];
$services = [];
$activeCategorySlug = $_GET['category'] ?? null;
$flashMsg = $_GET['msg'] ?? null;

try {
    $pdo = Database::getConnection();

    // 1. Fetch all categories for filter navigation
    $catStmt = $pdo->query("SELECT id, name, slug FROM categories ORDER BY name ASC");
    $categories = $catStmt->fetchAll();

    // 2. Fetch services (filtered by category slug if provided) using prepared statements
    if (!empty($activeCategorySlug)) {
        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       c.slug AS category_slug, 
                       u.name AS freelancer_name, 
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                WHERE c.slug = :slug 
                GROUP BY s.id 
                ORDER BY s.created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':slug' => $activeCategorySlug]);
    } else {
        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       c.slug AS category_slug, 
                       u.name AS freelancer_name, 
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                GROUP BY s.id 
                ORDER BY s.created_at DESC";
        $stmt = $pdo->query($sql);
    }
    $services = $stmt->fetchAll();

} catch (Exception $e) {
    $dbError = "Database Connection Error: Unable to retrieve services at this time. Please verify that your MySQL server is running.";
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <?php if ($dbError): ?>
        <!-- Styled Database Error Card -->
        <div class="sg-card" style="max-width: 650px; margin: 0 auto; text-align: center; padding: var(--space-48) var(--space-32);">
            <div style="width: 64px; height: 64px; background-color: rgba(245, 101, 101, 0.12); color: var(--color-error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-24);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h2 style="font-size: var(--text-h3); margin-bottom: var(--space-12);">Service Temporarily Unavailable</h2>
            <p style="color: var(--color-text-muted); font-size: var(--text-body); margin-bottom: var(--space-24);">
                <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <a href="services.php" class="btn btn-secondary">Retry Connection</a>
        </div>
    <?php else: ?>

        <!-- Action Header & Navigation -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: var(--space-16); margin-bottom: var(--space-24);">
            <div>
                <h1 style="margin-bottom: var(--space-4);">All Services</h1>
                <p class="text-small" style="color: var(--color-text-muted);">Explore top freelance services across development, design, and content.</p>
            </div>
            <a href="service-create.php" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: var(--space-8);">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Post New Service
            </a>
        </div>

        <?php if ($flashMsg === 'deleted'): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> Service has been deleted.
            </div>
        <?php elseif ($flashMsg === 'created'): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> Your service has been created and published.
            </div>
        <?php endif; ?>

        <!-- Category Filter Pills -->
        <div class="card-badge-row" style="margin-bottom: var(--space-32); display: flex; flex-wrap: wrap; gap: var(--space-8);">
            <a href="services.php" 
               class="badge <?php echo empty($activeCategorySlug) ? 'badge-primary' : 'badge-outline'; ?>" 
               style="text-decoration: none; padding: var(--space-8) var(--space-16);">
                All Categories
            </a>
            
            <?php foreach ($categories as $cat): ?>
                <a href="services.php?category=<?php echo urlencode($cat['slug']); ?>" 
                   class="badge <?php echo ($activeCategorySlug === $cat['slug']) ? 'badge-primary' : 'badge-outline'; ?>" 
                   style="text-decoration: none; padding: var(--space-8) var(--space-16);">
                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Services Grid -->
        <div class="sg-grid-auto">
            <?php if (count($services) > 0): ?>
                <?php foreach ($services as $service): ?>
                    <article class="card-listing" style="height: 100%; display: flex; flex-direction: column; justify-content: space-between;">
                        <div>
                            <div class="card-image-placeholder"></div>
                            <div class="card-content" style="padding-top: var(--space-16);">
                                <div class="card-badge-row" style="margin-bottom: var(--space-8);">
                                    <span class="badge badge-neutral"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    <?php if ($service['rating'] >= 4.9): ?>
                                        <span class="badge badge-success">Top Rated</span>
                                    <?php endif; ?>
                                </div>
                                <h3 class="card-title" style="margin-bottom: var(--space-8);">
                                    <a href="service-details.php?id=<?php echo (int)$service['id']; ?>" style="text-decoration: none; color: inherit;">
                                        <?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>
                                    </a>
                                </h3>
                                <p class="card-desc" style="color: var(--color-text-muted); font-size: var(--text-small); margin-bottom: var(--space-16);">
                                    By <?php echo htmlspecialchars($service['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?>
                                </p>
                            </div>
                        </div>

                        <div class="card-footer" style="margin-top: var(--space-16); border-top: 1px solid var(--color-border); padding-top: var(--space-12); display: flex; align-items: center; justify-content: space-between;">
                            <div class="star-rating">
                                <span class="star filled">★</span>
                                <span class="rating-text"><?php echo number_format((float)$service['rating'], 1); ?> (<?php echo (int)$service['reviews']; ?>)</span>
                            </div>
                            <div class="card-price" style="font-weight: 600; color: var(--color-primary);">
                                $<?php echo number_format((float)$service['price'], 2); ?>
                            </div>
                        </div>

                        <div style="display: flex; gap: var(--space-8); margin-top: var(--space-16);">
                            <a href="service-details.php?id=<?php echo (int)$service['id']; ?>" class="btn btn-secondary" style="flex: 1; padding: var(--space-8); font-size: var(--text-small);">View Details</a>
                            <a href="service-edit.php?id=<?php echo (int)$service['id']; ?>" class="btn btn-secondary" style="padding: var(--space-8); font-size: var(--text-small);" title="Edit Service">Edit</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Empty State -->
                <div style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; gap: var(--space-24); padding: var(--space-48) 0;">
                    <div style="text-align: center;">
                        <h3 class="empty-title">No services found</h3>
                        <p class="empty-desc" style="color: var(--color-text-muted);">No services match your selected category filter.</p>
                        <a href="services.php" class="btn btn-primary" style="margin-top: var(--space-16);">Clear Filter</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>
