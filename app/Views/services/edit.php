<?php
// app/Views/services/edit.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 600px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="/service/details?id=<?php echo (int)($service['id'] ?? 0); ?>" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
                &larr; Back to Service Details
            </a>
        </div>

        <div style="margin-bottom: var(--space-32);">
            <h1>Edit Service Listing</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Update your service details and pricing.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($service): ?>
            <div class="sg-card">
                <form action="/service/edit" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?php echo (int)$service['id']; ?>">

                    <div class="form-group">
                        <label class="form-label" for="title">Service Title</label>
                        <input type="text" id="title" name="title" 
                               class="form-input <?php echo isset($errors['title']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($errors['title'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['title'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="category_id">Category</label>
                        <select id="category_id" name="category_id" class="form-select <?php echo isset($errors['category_id']) ? 'form-input-error' : ''; ?>">
                            <option value="">-- Select Category --</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo (int)$cat['id']; ?>" <?php echo ($categoryId === (int)$cat['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (isset($errors['category_id'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['category_id'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="price">Starting Price ($ USD)</label>
                        <input type="number" step="0.01" min="1" id="price" name="price" 
                               class="form-input <?php echo isset($errors['price']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($price, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php if (isset($errors['price'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['price'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="summary">Service Summary & Scope</label>
                        <textarea id="summary" name="summary" rows="5" 
                                  class="form-input <?php echo isset($errors['summary']) ? 'form-input-error' : ''; ?>"><?php echo htmlspecialchars($summary, ENT_QUOTES, 'UTF-8'); ?></textarea>
                        <?php if (isset($errors['summary'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['summary'], ENT_QUOTES, 'UTF-8'); ?></div>
                        <?php endif; ?>
                    </div>

                    <div style="display: flex; gap: var(--space-16); margin-top: var(--space-24);">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                        <a href="/service/details?id=<?php echo (int)$service['id']; ?>" class="btn btn-secondary">Cancel</a>
                    </div>

                </form>
            </div>
        <?php endif; ?>

    </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
