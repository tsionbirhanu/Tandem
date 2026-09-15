<?php
// app/Views/profile/freelancer.php
$pageTitle = htmlspecialchars($freelancer['name'] ?? 'Freelancer Profile') . " - Tandem";
include BASE_PATH . '/app/Views/layouts/header.php';

/**
 * Format timestamp into relative human-readable time (e.g. "3 days ago")
 */
function formatRelativeTime($datetime): string {
    if (!$datetime) return 'Recently';
    $time = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    if (!$time) return 'Recently';

    $diff = time() - $time;
    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}

$ratingAvg = number_format((float)($freelancer['rating_avg'] ?? 5.0), 1);
$reviewCount = (int)($freelancer['review_count'] ?? 0);
?>

<div class="container my-5">
    <!-- Profile Hero Card -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
        <div class="bg-primary text-white p-4 p-md-5 position-relative" style="background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);">
            <div class="row align-items-center g-4">
                <div class="col-auto">
                    <?php if (!empty($freelancer['avatar_url'])): ?>
                        <img src="<?= htmlspecialchars($freelancer['avatar_url']) ?>" alt="<?= htmlspecialchars($freelancer['name']) ?>" class="rounded-circle border border-4 border-white shadow-sm object-fit-cover" width="110" height="110">
                    <?php else: ?>
                        <div class="rounded-circle border border-4 border-white shadow-sm bg-white text-primary d-flex align-items-center justify-content-center fw-bold fs-1" style="width: 110px; height: 110px;">
                            <?= strtoupper(substr($freelancer['name'] ?? 'F', 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col">
                    <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                        <span class="badge bg-white text-primary rounded-pill px-3 py-1 fw-semibold text-uppercase tracking-wider small">
                            Verified Freelancer
                        </span>
                        <?php if ($reviewCount > 0): ?>
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold small">
                                ★ <?= $ratingAvg ?> (<?= $reviewCount ?> <?= $reviewCount === 1 ? 'review' : 'reviews' ?>)
                            </span>
                        <?php else: ?>
                            <span class="badge bg-secondary-subtle text-white rounded-pill px-3 py-1 fw-normal small">
                                New Freelancer
                            </span>
                        <?php endif; ?>
                    </div>

                    <h1 class="fw-bold mb-1 display-6"><?= htmlspecialchars($freelancer['name'] ?? 'Freelancer') ?></h1>
                    <p class="fs-5 text-white-50 mb-0">
                        <?= htmlspecialchars($freelancer['title'] ?? 'Professional Service Provider') ?>
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5 bg-white">
            <h5 class="fw-bold text-dark mb-3">About the Freelancer</h5>
            <p class="text-secondary mb-0 leading-relaxed fs-6">
                <?= !empty($freelancer['bio']) ? nl2br(htmlspecialchars($freelancer['bio'])) : 'No biography provided yet.' ?>
            </p>
        </div>
    </div>

    <!-- Main Content: Services & Reviews -->
    <div class="row g-4">
        <!-- Offered Services Column -->
        <div class="col-lg-7">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Services Offered 
                    <span class="badge bg-light text-dark rounded-pill border fs-6 ms-2"><?= count($services) ?></span>
                </h4>
            </div>

            <?php if (empty($services)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <div class="text-muted py-4">This freelancer has not published any services yet.</div>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($services as $service): ?>
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 service-hover-card">
                                <div class="row g-0 align-items-center">
                                    <?php if (!empty($service['image_url'])): ?>
                                        <div class="col-md-4">
                                            <img src="<?= htmlspecialchars($service['image_url']) ?>" class="img-fluid rounded-start h-100 object-fit-cover w-100" style="min-height: 140px;" alt="<?= htmlspecialchars($service['title']) ?>">
                                        </div>
                                    <?php endif; ?>
                                    <div class="<?= !empty($service['image_url']) ? 'col-md-8' : 'col-12' ?>">
                                        <div class="card-body p-4">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <span class="badge bg-primary-subtle text-primary fw-semibold rounded-pill px-2 py-1 small">
                                                    <?= htmlspecialchars($service['category_name'] ?? 'General') ?>
                                                </span>
                                                <span class="fw-bold text-success fs-5">$<?= number_format((float)$service['price'], 2) ?></span>
                                            </div>
                                            <h5 class="card-title fw-bold text-dark mb-2">
                                                <a href="/services/<?= (int)$service['id'] ?>" class="text-decoration-none text-dark hover-primary">
                                                    <?= htmlspecialchars($service['title']) ?>
                                                </a>
                                            </h5>
                                            <p class="card-text text-muted small text-truncate mb-3" style="max-width: 90%;">
                                                <?= htmlspecialchars($service['description'] ?? '') ?>
                                            </p>
                                            <a href="/services/<?= (int)$service['id'] ?>" class="btn btn-outline-primary btn-sm rounded-3 fw-semibold">
                                                View Details &rarr;
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Client Reviews Column -->
        <div class="col-lg-5">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h4 class="fw-bold text-dark mb-0">
                    Client Reviews
                    <span class="badge bg-light text-dark rounded-pill border fs-6 ms-2"><?= count($reviews) ?></span>
                </h4>
            </div>

            <?php if (empty($reviews)): ?>
                <div class="card border-0 shadow-sm rounded-4 p-4 text-center">
                    <div class="text-muted py-4">No reviews yet for this freelancer.</div>
                </div>
            <?php else: ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($reviews as $rev): ?>
                        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($rev['client_avatar'])): ?>
                                        <img src="<?= htmlspecialchars($rev['client_avatar']) ?>" alt="Client" class="rounded-circle object-fit-cover" width="36" height="36">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-light text-dark fw-bold d-flex align-items-center justify-content-center border" style="width: 36px; height: 36px;">
                                            <?= strtoupper(substr($rev['client_name'] ?? 'C', 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <div class="fw-semibold text-dark mb-0 lh-sm"><?= htmlspecialchars($rev['client_name'] ?? 'Verified Client') ?></div>
                                        <small class="text-muted" style="font-size: 0.75rem;"><?= formatRelativeTime($rev['created_at']) ?></small>
                                    </div>
                                </div>
                                <div class="text-warning fs-6">
                                    <?php
                                    $r = (int)($rev['rating'] ?? 5);
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo $i <= $r ? '★' : '☆';
                                    }
                                    ?>
                                </div>
                            </div>

                            <?php if (!empty($rev['service_title'])): ?>
                                <div class="mb-2">
                                    <small class="text-muted font-monospace" style="font-size: 0.75rem;">Service:</small>
                                    <span class="small fw-semibold text-primary"><?= htmlspecialchars($rev['service_title']) ?></span>
                                </div>
                            <?php endif; ?>

                            <p class="text-secondary small mb-0 leading-normal">
                                "<?= htmlspecialchars($rev['comment']) ?>"
                            </p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.service-hover-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.service-hover-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}
.hover-primary:hover {
    color: #0d6efd !important;
}
</style>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
