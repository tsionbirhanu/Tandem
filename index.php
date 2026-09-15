<?php
// index.php
// The homepage of the Tandem application.

// Include our data first so we have access to the $services array.
// 'require_once' ensures the file is included exactly once, preventing errors.
require_once 'includes/data.php';

// Include the header which outputs the start of our HTML and the navigation bar.
include 'includes/header.php';
?>

<main class="page-main">
    <!-- Hero Section -->
    <section class="sg-header" style="text-align: center; padding: var(--space-64) 0;">
        <div class="sg-container">
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
            <?php
            // We slice the array to get only the first 3 items for our featured section.
            // This is equivalent to `LIMIT 3` in a database query.
            $featured = array_slice($services, 0, 3);
            
            // Loop through the featured array and render a card for each service.
            foreach ($featured as $service):
            ?>
                <!-- Service Card Wrapper. We wrap the card in an anchor tag to make it clickable. -->
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
        </div>
    </section>
</main>

<?php 
// Include the footer to close out the HTML page properly.
include 'includes/footer.php'; 
?>
