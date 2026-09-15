<?php
// service-details.php
// This page shows a specific service based on the ?id= parameter.

require_once 'includes/data.php';

// Get the ID from the URL and convert it to an integer for safety
$serviceId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Search our mock database for the service with this ID
$activeService = null;
foreach ($services as $service) {
    if ($service['id'] === $serviceId) {
        $activeService = $service;
        break; // Stop searching once found
    }
}

// If no service is found with that ID, we can handle the error.
if (!$activeService) {
    include 'includes/header.php';
    echo '<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">';
    echo '<div class="alert alert-error"><strong>Error:</strong> Service not found.</div>';
    echo '<a href="services.php" class="btn btn-secondary">Back to Services</a>';
    echo '</main>';
    include 'includes/footer.php';
    exit; // Stop execution of the rest of the file
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="margin-bottom: var(--space-24);">
        <a href="services.php" style="color: var(--color-text-muted); text-decoration: none;">&larr; Back to all services</a>
    </div>

    <div class="sg-grid-2">
        <!-- Main Service Content -->
        <div>
            <div class="card-image-placeholder" style="height: 300px; margin-bottom: var(--space-24);"></div>
            <div class="card-badge-row" style="margin-bottom: var(--space-16);">
                <span class="badge badge-primary"><?php echo htmlspecialchars($categories[$activeService['category']]); ?></span>
            </div>
            <h1 style="margin-bottom: var(--space-16);"><?php echo htmlspecialchars($activeService['title']); ?></h1>
            <div class="star-rating" style="margin-bottom: var(--space-24);">
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="star filled">★</span>
                <span class="rating-text" style="font-size: var(--text-body);"><?php echo number_format($activeService['rating'], 1); ?> (<?php echo $activeService['reviews']; ?> reviews)</span>
            </div>
            
            <h3>About this service</h3>
            <p style="font-size: var(--text-body); line-height: 1.8; color: var(--color-text-neutral);">
                <?php echo nl2br(htmlspecialchars($activeService['description'])); ?>
            </p>
        </div>

        <!-- Sidebar Profile Card & CTA -->
        <div>
            <article class="card-profile" style="position: sticky; top: var(--space-24);">
                <div class="profile-header">
                    <div class="avatar-lg"></div>
                    <div class="profile-info">
                        <h3 class="profile-name"><?php echo htmlspecialchars($activeService['freelancer_name']); ?></h3>
                        <p class="profile-title">Top Rated Professional</p>
                    </div>
                </div>
                
                <div style="border-top: 1px solid var(--color-border); margin: var(--space-16) 0; padding-top: var(--space-16);">
                    <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-24);">
                        <span style="font-weight: 500;">Starting price</span>
                        <span class="card-price" style="font-size: var(--text-h5);">$<?php echo number_format($activeService['price']); ?></span>
                    </div>
                    
                    <a href="contact.php?service_id=<?php echo $activeService['id']; ?>" class="btn btn-primary" style="width: 100%;">Request Proposal</a>
                </div>
            </article>
        </div>
    </div>

</main>

<?php include 'includes/footer.php'; ?>
