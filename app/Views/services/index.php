<?php
// app/Views/services/index.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-32); flex-wrap: wrap; gap: var(--space-16);">
        <div>
            <h1>Explore Services</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Hand-picked freelance services from top professionals.</p>
        </div>
        
        <?php if (isLoggedIn() && in_array($_SESSION['user_role'] ?? '', ['freelancer', 'admin'], true)): ?>
            <a href="/service/create" class="btn btn-primary">+ Offer New Service</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($dbError)): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-32);">
            <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Search & Filter Control Bar -->
    <div class="sg-card" style="margin-bottom: var(--space-32);">
        <form action="/services" method="GET" style="display: flex; gap: var(--space-16); flex-wrap: wrap; align-items: flex-end;">
            
            <div style="flex: 2; min-width: 240px;">
                <label class="form-label" for="search">Search Keywords</label>
                <input type="text" id="search" name="search" class="form-input" 
                       placeholder="e.g. Logo Design, React, Copywriting..." 
                       value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>">
            </div>

            <div style="flex: 1; min-width: 180px;">
                <label class="form-label" for="category">Category</label>
                <select id="category" name="category" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8'); ?>" 
                                <?php echo ($selectedCategory === $cat['slug']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="display: flex; gap: var(--space-8);">
                <button type="submit" class="btn btn-primary">Filter</button>
                <?php if (!empty($search) || !empty($selectedCategory)): ?>
                    <a href="/services" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </div>

        </form>
    </div>

    <!-- Active Filter Indicator -->
    <?php if (!empty($search) || !empty($selectedCategory)): ?>
        <div style="margin-bottom: var(--space-24); color: var(--color-text-muted);">
            Showing results for 
            <?php if (!empty($search)): ?>
                matching <strong>"<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>"</strong>
            <?php endif; ?>
            <?php if (!empty($selectedCategory)): ?>
                in category <strong>"<?php echo htmlspecialchars($selectedCategory, ENT_QUOTES, 'UTF-8'); ?>"</strong>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Services Grid -->
    <div class="sg-grid-auto">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <a href="/service/details?id=<?php echo (int)$service['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                    <article class="card-listing" style="height: 100%;">
                        <div class="card-image-placeholder"></div>
                        <div class="card-content">
                            <div class="card-badge-row">
                                <span class="badge badge-neutral"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php if (($service['rating'] ?? 0) >= 4.9): ?>
                                    <span class="badge badge-success">Top Rated</span>
                                <?php endif; ?>
                            </div>
                            <h3 class="card-title"><?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="card-desc">By <?php echo htmlspecialchars($service['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="card-footer">
                                <div class="star-rating">
                                    <span class="star filled">★</span>
                                    <span class="rating-text"><?php echo number_format((float)($service['rating'] ?? 0), 1); ?> (<?php echo (int)($service['reviews'] ?? 0); ?>)</span>
                                </div>
                                <div class="card-price">Starting at $<?php echo number_format((float)$service['price'], 2); ?></div>
                            </div>
                        </div>
                    </article>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1 / -1; text-align: center; color: var(--color-text-muted); padding: var(--space-48) 0;">
                <p>No services matched your filter criteria.</p>
                <a href="/services" class="btn btn-secondary" style="margin-top: var(--space-12);">Reset Filters</a>
            </div>
        <?php endif; ?>
    </div>

</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
