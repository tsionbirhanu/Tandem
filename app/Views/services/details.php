<?php
// app/Views/services/details.php
include BASE_PATH . '/app/Views/layouts/header.php';

$isOwnerOrAdmin = false;
if (isLoggedIn() && $service) {
    $currentUserId = $_SESSION['user_id'] ?? 0;
    $currentUserRole = $_SESSION['user_role'] ?? '';
    if ($currentUserId === (int)$service['freelancer_id'] || $currentUserRole === 'admin') {
        $isOwnerOrAdmin = true;
    }
}
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    
    <div style="margin-bottom: var(--space-24);">
        <a href="/services" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
            &larr; Back to Services Directory
        </a>
    </div>

    <?php if ($dbError): ?>
        <div class="alert alert-error" style="margin-bottom: var(--space-24);">
            <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
        </div>
    <?php endif; ?>

    <?php if ($service): ?>
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-32);">
            
            <!-- Left Column: Service Overview & Reviews -->
            <div>
                <div class="sg-card" style="margin-bottom: var(--space-32);">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: var(--space-12); margin-bottom: var(--space-16);">
                        <span class="badge badge-primary"><?php echo htmlspecialchars($service['category_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                        
                        <?php if ($isOwnerOrAdmin): ?>
                            <div style="display: flex; gap: var(--space-8);">
                                <a href="/services/<?php echo (int)$service['id']; ?>/edit" class="btn btn-secondary" style="padding: var(--space-6) var(--space-12); font-size: 0.85rem;">
                                    Edit Service
                                </a>
                                <a href="/services/<?php echo (int)$service['id']; ?>/delete" class="btn btn-secondary" style="padding: var(--space-6) var(--space-12); font-size: 0.85rem; color: var(--color-error); border-color: var(--color-error);">
                                    Delete
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>

                    <h1 style="font-size: var(--text-h2); margin-bottom: var(--space-16);">
                        <?php echo htmlspecialchars($service['title'], ENT_QUOTES, 'UTF-8'); ?>
                    </h1>

                    <!-- Freelancer Meta -->
                    <div style="display: flex; align-items: center; gap: var(--space-12); margin-bottom: var(--space-24); padding-bottom: var(--space-16); border-bottom: 1px solid var(--color-border);">
                        <div style="width: 44px; height: 44px; border-radius: 50%; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.1rem;">
                            <?php echo strtoupper(substr($service['freelancer_name'], 0, 1)); ?>
                        </div>
                        <div>
                            <div style="font-weight: 600; color: var(--color-text-neutral);">
                                <?php echo htmlspecialchars($service['freelancer_name'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                            <div class="text-caption" style="color: var(--color-text-muted);">
                                <?php echo htmlspecialchars($service['freelancer_email'], ENT_QUOTES, 'UTF-8'); ?>
                            </div>
                        </div>
                    </div>

                    <h3>Service Overview</h3>
                    <div style="line-height: 1.7; color: var(--color-text-neutral); margin-bottom: var(--space-24);">
                        <?php echo nl2br(htmlspecialchars($service['summary'], ENT_QUOTES, 'UTF-8')); ?>
                    </div>
                </div>

                <!-- Client Reviews Section -->
                <div class="sg-card">
                    <h3 style="margin-bottom: var(--space-24);">Client Reviews</h3>
                    <?php if (!empty($reviews)): ?>
                        <div style="display: flex; flex-direction: column; gap: var(--space-16);">
                            <?php foreach ($reviews as $review): ?>
                                <div style="padding-bottom: var(--space-16); border-bottom: 1px solid var(--color-border);">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
                                        <div style="font-weight: 600; color: var(--color-text-neutral);">
                                            <?php echo htmlspecialchars($review['client_name'], ENT_QUOTES, 'UTF-8'); ?>
                                        </div>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <span class="star <?php echo ($i <= $review['rating']) ? 'filled' : ''; ?>">★</span>
                                            <?php endfor; ?>
                                        </div>
                                    </div>
                                    <p style="margin: 0; color: var(--color-text-muted); font-size: var(--text-small);">
                                        <?php echo htmlspecialchars($review['comment'], ENT_QUOTES, 'UTF-8'); ?>
                                    </p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--color-text-muted);">No reviews yet for this service offer.</p>
                    <?php endif; ?>
                </div>

            </div>

            <!-- Right Column: Order CTA Sidebar -->
            <div>
                <div class="sg-card" style="position: sticky; top: var(--space-32);">
                    <div style="font-size: var(--text-h2); font-weight: 700; color: var(--color-primary); margin-bottom: var(--space-8);">
                        $<?php echo number_format((float)$service['price'], 2); ?>
                    </div>
                    <div style="color: var(--color-text-muted); font-size: var(--text-small); margin-bottom: var(--space-24);">
                        Upfront fixed project rate
                    </div>

                    <div style="display: flex; flex-direction: column; gap: var(--space-12);">
                        <a href="/contact" class="btn btn-primary" style="width: 100%; text-align: center;">
                            Contact Freelancer
                        </a>
                        <a href="/services" class="btn btn-secondary" style="width: 100%; text-align: center;">
                            Explore More Services
                        </a>
                    </div>
                </div>
            </div>

        </div>
    <?php endif; ?>

</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
