<?php
// app/Views/reviews/create.php
$pageTitle = "Leave a Review - Tandem";
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<div class="container my-5" style="max-width: 680px;">
    <div class="mb-4">
        <a href="/dashboard/client" class="text-decoration-none text-muted small fw-semibold">
            &larr; Back to Client Dashboard
        </a>
    </div>

    <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="bg-primary text-white p-4 p-md-5 bg-gradient">
            <span class="badge bg-white text-primary rounded-pill px-3 py-2 fw-semibold mb-2 text-uppercase tracking-wider">
                Completed Project
            </span>
            <h2 class="fw-bold mb-1">Leave a Review</h2>
            <p class="mb-0 text-white-50">
                Share your experience working with <strong><?= htmlspecialchars($request['freelancer_name'] ?? 'the freelancer') ?></strong>
            </p>
        </div>

        <div class="card-body p-4 p-md-5 bg-white">
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger rounded-3 mb-4 d-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor" class="bi bi-exclamation-triangle-fill flex-shrink-0" viewBox="0 0 16 16">
                        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                    </svg>
                    <div><?= htmlspecialchars($error) ?></div>
                </div>
            <?php endif; ?>

            <!-- Project Details Summary Box -->
            <div class="p-3 mb-4 rounded-3 bg-light border border-light-subtle">
                <div class="row align-items-center g-3">
                    <div class="col-sm-8">
                        <small class="text-muted d-block text-uppercase font-monospace fw-bold" style="font-size: 0.75rem;">Service Title</small>
                        <h6 class="fw-bold text-dark mb-0 text-truncate"><?= htmlspecialchars($request['service_title'] ?? 'Project Service') ?></h6>
                    </div>
                    <div class="col-sm-4 text-sm-end">
                        <small class="text-muted d-block text-uppercase font-monospace fw-bold" style="font-size: 0.75rem;">Freelancer</small>
                        <span class="fw-semibold text-primary"><?= htmlspecialchars($request['freelancer_name'] ?? 'Freelancer') ?></span>
                    </div>
                </div>
            </div>

            <form action="/requests/<?= (int)$request['id'] ?>/review" method="POST" id="review-form">
                <!-- Star Rating Widget -->
                <div class="mb-4 text-center">
                    <label class="form-label fw-bold d-block text-dark fs-5 mb-2">Overall Rating</label>
                    <p class="text-muted small mb-3">Click to rate your satisfaction with this service</p>

                    <input type="hidden" name="rating" id="rating-input" value="5" required>

                    <div class="d-inline-flex flex-row-reverse justify-content-center gap-1 star-widget py-2 px-3 rounded-pill bg-light border">
                        <button type="button" class="star-btn border-0 bg-transparent p-1 fs-2 text-warning lh-1" data-value="5" title="5 Stars - Excellent">★</button>
                        <button type="button" class="star-btn border-0 bg-transparent p-1 fs-2 text-warning lh-1" data-value="4" title="4 Stars - Very Good">★</button>
                        <button type="button" class="star-btn border-0 bg-transparent p-1 fs-2 text-warning lh-1" data-value="3" title="3 Stars - Average">★</button>
                        <button type="button" class="star-btn border-0 bg-transparent p-1 fs-2 text-warning lh-1" data-value="2" title="2 Stars - Poor">★</button>
                        <button type="button" class="star-btn border-0 bg-transparent p-1 fs-2 text-warning lh-1" data-value="1" title="1 Star - Terrible">★</button>
                    </div>

                    <div class="mt-2">
                        <span id="rating-label" class="badge bg-warning-subtle text-warning-emphasis fs-6 rounded-pill px-3 py-1 fw-semibold">
                            5 Stars - Excellent
                        </span>
                    </div>
                </div>

                <!-- Written Review Comment -->
                <div class="mb-4">
                    <label for="comment" class="form-label fw-bold text-dark fs-6">Written Review</label>
                    <textarea 
                        name="comment" 
                        id="comment" 
                        class="form-control form-control-lg rounded-3 fs-6" 
                        rows="5" 
                        placeholder="Write a few sentences about what went well, communication, quality of work, and timeliness..."
                        required
                        minlength="5"
                    ><?= htmlspecialchars($_POST['comment'] ?? '') ?></textarea>
                    <div class="form-text text-muted d-flex justify-content-between">
                        <span>Minimum 5 characters. Be honest and constructive.</span>
                        <span id="char-count">0 characters</span>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-grid gap-2 d-md-flex justify-content-md-end pt-3">
                    <a href="/dashboard/client" class="btn btn-light btn-lg px-4 rounded-3 fw-semibold">Cancel</a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 rounded-3 fw-bold shadow-sm">
                        Submit Review &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.star-widget {
    user-select: none;
    transition: all 0.2s ease;
}
.star-btn {
    cursor: pointer;
    transition: transform 0.15s cubic-bezier(0.175, 0.885, 0.32, 1.275), color 0.15s ease;
    opacity: 0.35;
}
.star-btn.active,
.star-btn:hover,
.star-btn:hover ~ .star-btn {
    opacity: 1 !important;
    transform: scale(1.15);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const starBtns = Array.from(document.querySelectorAll('.star-btn'));
    const ratingInput = document.getElementById('rating-input');
    const ratingLabel = document.getElementById('rating-label');
    const commentTextarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');

    const labels = {
        1: '1 Star - Terrible',
        2: '2 Stars - Poor',
        3: '3 Stars - Average',
        4: '4 Stars - Very Good',
        5: '5 Stars - Excellent'
    };

    function setRating(value) {
        ratingInput.value = value;
        ratingLabel.textContent = labels[value] || (value + ' Stars');
        
        starBtns.forEach(btn => {
            const val = parseInt(btn.getAttribute('data-value'));
            if (val <= value) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }

    // Initialize with current input value (default 5)
    setRating(parseInt(ratingInput.value) || 5);

    starBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const val = parseInt(this.getAttribute('data-value'));
            setRating(val);
        });

        btn.addEventListener('mouseenter', function () {
            const val = parseInt(this.getAttribute('data-value'));
            ratingLabel.textContent = labels[val] || (val + ' Stars');
        });

        btn.addEventListener('mouseleave', function () {
            const currentVal = parseInt(ratingInput.value) || 5;
            ratingLabel.textContent = labels[currentVal] || (currentVal + ' Stars');
        });
    });

    if (commentTextarea && charCount) {
        function updateCharCount() {
            charCount.textContent = commentTextarea.value.length + ' characters';
        }
        commentTextarea.addEventListener('input', updateCharCount);
        updateCharCount();
    }
});
</script>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
