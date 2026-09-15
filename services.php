<?php
// services.php
// This page lists all services and allows filtering by category using $_GET parameters.

require_once 'includes/data.php';
include 'includes/header.php';

// Determine if a specific category was requested in the URL, e.g., ?category=design
// We use the null coalescing operator (??) to set it to null if it's not present.
$activeCategory = $_GET['category'] ?? null;

// Filter the array if an active category is set
$filteredServices = [];
if ($activeCategory && array_key_exists($activeCategory, $categories)) {
    foreach ($services as $service) {
        if ($service['category'] === $activeCategory) {
            $filteredServices[] = $service;
        }
    }
} else {
    // If no valid category is selected, show all services
    $filteredServices = $services;
}
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <h1>All Services</h1>
    
    <!-- Category Filter Pills -->
    <div class="card-badge-row" style="margin-bottom: var(--space-32);">
        <!-- 'All' button -->
        <a href="services.php" class="badge <?php echo !$activeCategory ? 'badge-primary' : 'badge-outline'; ?>" style="text-decoration: none;">
            All
        </a>
        
        <?php foreach ($categories as $key => $label): ?>
            <!-- Category specific buttons -->
            <a href="services.php?category=<?php echo $key; ?>" 
               class="badge <?php echo ($activeCategory === $key) ? 'badge-primary' : 'badge-outline'; ?>" 
               style="text-decoration: none;">
                <?php echo htmlspecialchars($label); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Render the filtered services grid -->
    <div class="sg-grid-auto">
        <?php if (count($filteredServices) > 0): ?>
            <?php foreach ($filteredServices as $service): ?>
                <a href="service-details.php?id=<?php echo $service['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                    <article class="card-listing" style="height: 100%;">
                        <div class="card-image-placeholder"></div>
                        <div class="card-content">
                            <div class="card-badge-row">
                                <span class="badge badge-neutral"><?php echo htmlspecialchars($categories[$service['category']]); ?></span>
                                <?php if ($service['rating'] >= 4.9): ?>
                                    <span class="badge badge-success">Top Rated</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h3>
                            <p class="card-desc"><?php echo htmlspecialchars($service['freelancer_name']); ?></p>
                            <div class="card-footer">
                                <div class="star-rating">
                                    <span class="star filled">★</span>
                                    <span class="rating-text"><?php echo number_format($service['rating'], 1); ?> (<?php echo $service['reviews']; ?>)</span>
                                </div>
                                <div class="card-price">Starting at $<?php echo number_format($service['price']); ?></div>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Skeleton-style empty state -->
            <div style="grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center; gap: var(--space-24);">
                <div style="text-align: center; margin-bottom: var(--space-16);">
                    <h3 class="empty-title">No services found</h3>
                    <p class="empty-desc">Try selecting a different category.</p>
                    <a href="services.php" class="btn btn-primary" style="margin-top: var(--space-16);">Clear Filters</a>
                </div>
                <!-- Faded out skeleton grid as a wireframe background for the empty state -->
                <div class="sg-grid-2" style="width: 100%; opacity: 0.4; pointer-events: none; max-width: 800px;">
                    <div class="skeleton-card">
                        <div class="skeleton skeleton-img"></div>
                        <div class="skeleton-content">
                            <div class="skeleton skeleton-text" style="width: 30%;"></div>
                            <div class="skeleton skeleton-text" style="width: 80%; height: 1.5rem; margin: 0.5rem 0;"></div>
                            <div class="skeleton skeleton-text" style="width: 100%;"></div>
                        </div>
                    </div>
                    <div class="skeleton-card" style="display: none;"> <!-- Hidden on mobile via css, but fine for structural demo -->
                        <div class="skeleton skeleton-img"></div>
                        <div class="skeleton-content">
                            <div class="skeleton skeleton-text" style="width: 30%;"></div>
                            <div class="skeleton skeleton-text" style="width: 80%; height: 1.5rem; margin: 0.5rem 0;"></div>
                            <div class="skeleton skeleton-text" style="width: 100%;"></div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
