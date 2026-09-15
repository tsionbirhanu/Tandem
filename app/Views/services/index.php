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
            <a href="/services/create" class="btn btn-primary">+ Offer New Service</a>
        <?php endif; ?>
    </div>

    <?php if (!empty($dbError)): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-32);">
            <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <!-- Search & Filter Control Bar -->
    <div class="sg-card" style="margin-bottom: var(--space-32); padding: var(--space-24);">
        <form action="/services" method="GET" style="display: flex; flex-direction: column; gap: var(--space-20);">
            
            <!-- Top Search Bar & Sort Row -->
            <div style="display: flex; gap: var(--space-16); flex-wrap: wrap; align-items: flex-end;">
                
                <div style="flex: 2; min-width: 260px;">
                    <label class="form-label" for="search">Search Services</label>
                    <input type="text" id="search" name="search" class="form-input" 
                           placeholder="Search by service title or keywords..." 
                           value="<?php echo htmlspecialchars($search ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>

                <div style="flex: 1; min-width: 180px;">
                    <label class="form-label" for="category">Category</label>
                    <select id="category" name="category" class="form-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['slug'], ENT_QUOTES, 'UTF-8'); ?>" 
                                    <?php echo (($selectedCategory ?? '') === $cat['slug']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div style="flex: 1; min-width: 180px;">
                    <label class="form-label" for="sort">Sort By</label>
                    <select id="sort" name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="newest" <?php echo (($sort ?? 'newest') === 'newest') ? 'selected' : ''; ?>>Newest Arrivals</option>
                        <option value="price_asc" <?php echo (($sort ?? '') === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo (($sort ?? '') === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                        <option value="rating_desc" <?php echo (($sort ?? '') === 'rating_desc') ? 'selected' : ''; ?>>Top Rated</option>
                    </select>
                </div>

            </div>

            <!-- Detailed Price Range & Rating Filter Row -->
            <div style="display: flex; gap: var(--space-16); flex-wrap: wrap; align-items: flex-end; border-top: 1px dashed var(--color-border); padding-top: var(--space-16);">
                
                <div style="flex: 1; min-width: 140px;">
                    <label class="form-label" for="min_price">Min Price ($)</label>
                    <input type="number" id="min_price" name="min_price" min="0" step="10" class="form-input" 
                           placeholder="e.g. 50" 
                           value="<?php echo ($minPrice !== null) ? htmlspecialchars($minPrice, ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>

                <div style="flex: 1; min-width: 140px;">
                    <label class="form-label" for="max_price">Max Price ($)</label>
                    <input type="number" id="max_price" name="max_price" min="0" step="10" class="form-input" 
                           placeholder="e.g. 2000" 
                           value="<?php echo ($maxPrice !== null) ? htmlspecialchars($maxPrice, ENT_QUOTES, 'UTF-8') : ''; ?>">
                </div>

                <div style="flex: 1; min-width: 160px;">
                    <label class="form-label" for="min_rating">Minimum Rating</label>
                    <select id="min_rating" name="min_rating" class="form-select">
                        <option value="">Any Rating</option>
                        <option value="4.5" <?php echo (($minRating ?? 0) == 4.5) ? 'selected' : ''; ?>>4.5+ Stars ★★★★★</option>
                        <option value="4.0" <?php echo (($minRating ?? 0) == 4.0) ? 'selected' : ''; ?>>4.0+ Stars ★★★★☆</option>
                        <option value="3.0" <?php echo (($minRating ?? 0) == 3.0) ? 'selected' : ''; ?>>3.0+ Stars ★★★☆☆</option>
                    </select>
                </div>

                <div style="display: flex; gap: var(--space-8); min-width: 180px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Apply Filters</button>
                    <a href="/services" class="btn btn-secondary">Clear</a>
                </div>

            </div>

        </form>
    </div>

    <!-- Active Removable Filter Chips Bar -->
    <?php if (!empty($activeChips)): ?>
        <div style="display: flex; align-items: center; gap: var(--space-12); flex-wrap: wrap; margin-bottom: var(--space-24); background-color: rgba(44, 95, 93, 0.05); padding: var(--space-12) var(--space-16); border-radius: var(--radius-sm); border: 1px solid rgba(44, 95, 93, 0.15);">
            <span style="font-size: var(--text-caption); font-weight: 600; text-transform: uppercase; color: var(--color-primary); letter-spacing: 0.05em;">Active Filters:</span>
            
            <?php foreach ($activeChips as $chip): ?>
                <a href="<?php echo htmlspecialchars($chip['remove_url'], ENT_QUOTES, 'UTF-8'); ?>" 
                   style="text-decoration: none; display: inline-flex; align-items: center; gap: 6px; background-color: #fff; border: 1px solid var(--color-primary); color: var(--color-primary); padding: 4px 10px; border-radius: 16px; font-size: 0.82rem; font-weight: 500; transition: all 0.2s ease;">
                    <span><?php echo $chip['label']; ?></span>
                    <span style="font-weight: 700; font-size: 1rem; line-height: 1;">&times;</span>
                </a>
            <?php endforeach; ?>

            <a href="/services" style="font-size: 0.82rem; color: var(--color-error); text-decoration: underline; margin-left: auto;">
                Reset All Filters
            </a>
        </div>
    <?php endif; ?>

    <!-- Results Count Meta Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-20); color: var(--color-text-muted); font-size: var(--text-small);">
        <div>
            Showing <strong><?php echo count($services); ?></strong> of <strong><?php echo (int)($total ?? count($services)); ?></strong> available service offers
            <?php if (($totalPages ?? 1) > 1): ?>
                (Page <?php echo (int)($page ?? 1); ?> of <?php echo (int)$totalPages; ?>)
            <?php endif; ?>
        </div>
    </div>

    <!-- Services Grid -->
    <div class="sg-grid-auto">
        <?php if (!empty($services)): ?>
            <?php foreach ($services as $service): ?>
                <a href="/services/<?php echo (int)$service['id']; ?>" style="text-decoration: none; color: inherit; display: block;">
                    <article class="card-listing" style="height: 100%;">
                        
                        <!-- Primary Image or Card Placeholder -->
                        <?php if (!empty($service['primary_image'])): ?>
                            <div style="height: 160px; overflow: hidden; border-radius: var(--radius-sm) var(--radius-sm) 0 0; background-color: #f7fafc;">
                                <img src="<?php echo htmlspecialchars($service['primary_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     alt="<?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="card-image-placeholder"></div>
                        <?php endif; ?>

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
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin-bottom: var(--space-16); color: var(--color-text-muted);"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                <h3>No Services Found</h3>
                <p>No services matched your current filter criteria.</p>
                <a href="/services" class="btn btn-secondary" style="margin-top: var(--space-12);">Reset All Filters</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Pagination Bar -->
    <?php if (($totalPages ?? 1) > 1): ?>
        <div style="display: flex; justify-content: center; align-items: center; gap: var(--space-8); margin-top: var(--space-48);">
            
            <!-- Previous Page Button -->
            <?php if ($page > 1): ?>
                <a href="<?php echo htmlspecialchars($buildPageUrl($page - 1), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary" style="padding: var(--space-8) var(--space-16);">
                    &larr; Previous
                </a>
            <?php else: ?>
                <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: var(--space-8) var(--space-16);">
                    &larr; Previous
                </span>
            <?php endif; ?>

            <!-- Page Numbers -->
            <div style="display: flex; gap: 4px;">
                <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                    <?php if ($p === $page): ?>
                        <span class="btn btn-primary" style="padding: var(--space-8) var(--space-12); font-weight: 600;">
                            <?php echo $p; ?>
                        </span>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($buildPageUrl($p), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary" style="padding: var(--space-8) var(--space-12);">
                            <?php echo $p; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
            </div>

            <!-- Next Page Button -->
            <?php if ($page < $totalPages): ?>
                <a href="<?php echo htmlspecialchars($buildPageUrl($page + 1), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary" style="padding: var(--space-8) var(--space-16);">
                    Next &rarr;
                </a>
            <?php else: ?>
                <span class="btn btn-secondary" style="opacity: 0.5; cursor: not-allowed; padding: var(--space-8) var(--space-16);">
                    Next &rarr;
                </span>
            <?php endif; ?>

        </div>
    <?php endif; ?>

</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
