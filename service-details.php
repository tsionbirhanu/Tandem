<?php
// service-details.php
// Displays full details for a single service queried dynamically from database using PDO prepared statements.

require_once 'includes/Database.php';

$serviceId = (int)($_GET['id'] ?? 0);
$service = null;
$serviceImages = [];
$dbError = null;
$flashMsg = $_GET['msg'] ?? null;

try {
    $pdo = Database::getConnection();

    // Fetch service details using prepared statements
    $sql = "SELECT s.*, 
                   c.name AS category_name, 
                   c.slug AS category_slug, 
                   u.name AS freelancer_name, 
                   u.email AS freelancer_email, 
                   u.avatar_url AS freelancer_avatar,
                   COALESCE(AVG(r.rating), 5.0) AS rating, 
                   COUNT(r.id) AS reviews 
            FROM services s 
            JOIN categories c ON s.category_id = c.id 
            JOIN users u ON s.freelancer_id = u.id 
            LEFT JOIN project_requests pr ON pr.service_id = s.id 
            LEFT JOIN reviews r ON r.project_request_id = pr.id 
            WHERE s.id = :id 
            GROUP BY s.id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id' => $serviceId]);
    $service = $stmt->fetch();

    if ($service) {
        // Fetch gallery images
        $imgStmt = $pdo->prepare("SELECT * FROM service_images WHERE service_id = :service_id ORDER BY sort_order ASC");
        $imgStmt->execute([':service_id' => $serviceId]);
        $serviceImages = $imgStmt->fetchAll();
    }

} catch (Exception $e) {
    $dbError = "Database Connection Error: Unable to load service details right now.";
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">

    <?php if ($dbError): ?>
        <div class="sg-card" style="max-width: 600px; margin: 0 auto; text-align: center; padding: var(--space-48) var(--space-32);">
            <div style="width: 64px; height: 64px; background-color: rgba(245, 101, 101, 0.12); color: var(--color-error); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto var(--space-24);">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"></circle>
                    <line x1="12" y1="8" x2="12" y2="12"></line>
                    <line x1="12" y1="16" x2="12.01" y2="16"></line>
                </svg>
            </div>
            <h2 style="font-size: var(--text-h3); margin-bottom: var(--space-12);">System Error</h2>
            <p style="color: var(--color-text-muted); margin-bottom: var(--space-24);"><?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?></p>
            <a href="services.php" class="btn btn-secondary">Back to Services</a>
        </div>
    <?php elseif (!$service): ?>
        <div class="sg-card" style="max-width: 600px; margin: 0 auto; text-align: center; padding: var(--space-48) var(--space-32);">
            <h2 style="font-size: var(--text-h3); margin-bottom: var(--space-12);">Service Not Found</h2>
            <p style="color: var(--color-text-muted); margin-bottom: var(--space-24);">The service listing you are looking for does not exist or has been removed.</p>
            <a href="services.php" class="btn btn-primary">Browse All Services</a>
        </div>
    <?php else: ?>

        <div style="margin-bottom: var(--space-24); display: flex; justify-content: space-between; align-items: center;">
            <a href="services.php" style="color: var(--color-text-muted); text-decoration: none; display: inline-flex; align-items: center; gap: var(--space-8);">
                &larr; Back to all services
            </a>
            
            <div style="display: flex; gap: var(--space-12);">
                <a href="service-edit.php?id=<?php echo (int)$service['id']; ?>" class="btn btn-secondary">Edit Service</a>
                <a href="service-delete.php?id=<?php echo (int)$service['id']; ?>" class="btn btn-danger">Delete Service</a>
            </div>
        </div>

        <?php if ($flashMsg === 'updated'): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> Service details updated successfully.
            </div>
        <?php elseif ($flashMsg === 'created'): ?>
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> Service created successfully.
            </div>
        <?php endif; ?>

        <div class="sg-grid-2" style="grid-template-columns: 2fr 1fr;">
            <!-- Main Service Content -->
            <div>
                <div class="card-image-placeholder" style="height: 320px; margin-bottom: var(--space-24); border-radius: var(--radius-md);"></div>
                
                <div class="card-badge-row" style="margin-bottom: var(--space-16);">
                    <span class="badge badge-primary"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                    <?php if ($service['rating'] >= 4.9): ?>
                        <span class="badge badge-success">Top Rated</span>
                    <?php endif; ?>
                </div>

                <h1 style="margin-bottom: var(--space-16); font-size: var(--text-h2);"><?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?></h1>
                
                <div class="star-rating" style="margin-bottom: var(--space-24); display: flex; align-items: center; gap: var(--space-8);">
                    <span class="star filled" style="color: #f59e0b; font-size: 1.25rem;">★</span>
                    <span class="rating-text" style="font-size: var(--text-body); font-weight: 600; color: var(--color-text-neutral);">
                        <?php echo number_format((float)$service['rating'], 1); ?>
                    </span>
                    <span style="color: var(--color-text-muted);">
                        (<?php echo (int)$service['reviews']; ?> client reviews)
                    </span>
                </div>
                
                <div style="border-top: 1px solid var(--color-border); border-bottom: 1px solid var(--color-border); padding: var(--space-24) 0; margin-bottom: var(--space-32);">
                    <h3 style="margin-bottom: var(--space-12);">About this service</h3>
                    <p style="font-size: var(--text-body); line-height: 1.8; color: var(--color-text-neutral); white-space: pre-wrap;">
                        <?php echo htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8'); ?>
                    </p>
                </div>

                <?php if (!empty($serviceImages)): ?>
                    <h3 style="margin-bottom: var(--space-16);">Service Gallery</h3>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: var(--space-16); margin-bottom: var(--space-32);">
                        <?php foreach ($serviceImages as $img): ?>
                            <div style="background-color: var(--color-bg-base); border: 1px solid var(--color-border); border-radius: var(--radius-sm); height: 120px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <span style="color: var(--color-text-muted); font-size: var(--text-small);"><?php echo htmlspecialchars($img['image_path'], ENT_QUOTES, 'UTF-8'); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Sidebar Profile & CTA Card -->
            <div>
                <article class="card-listing" style="position: sticky; top: var(--space-24); padding: var(--space-24);">
                    <div style="display: flex; align-items: center; gap: var(--space-16); margin-bottom: var(--space-20);">
                        <div style="width: 56px; height: 56px; background-color: var(--color-primary); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.25rem;">
                            <?php echo strtoupper(substr($service['freelancer_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <h3 class="profile-name" style="margin: 0; font-size: var(--text-h5);"><?php echo htmlspecialchars($service['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="profile-title" style="margin: 0; font-size: var(--text-small); color: var(--color-text-muted);">Verified Freelancer</p>
                        </div>
                    </div>
                    
                    <div style="border-top: 1px solid var(--color-border); margin: var(--space-16) 0; padding-top: var(--space-16);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-24);">
                            <span style="font-weight: 500; color: var(--color-text-muted);">Starting price</span>
                            <span class="card-price" style="font-size: var(--text-h4); color: var(--color-primary); font-weight: 700;">
                                $<?php echo number_format((float)$service['price'], 2); ?>
                            </span>
                        </div>
                        
                        <a href="contact.php?service_id=<?php echo (int)$service['id']; ?>" class="btn btn-primary" style="width: 100%; text-align: center;">Request Proposal</a>
                    </div>
                </article>
            </div>
        </div>

    <?php endif; ?>

</main>

<?php include 'includes/footer.php'; ?>
