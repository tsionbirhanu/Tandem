<?php
// app/Views/profile/edit.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 520px; margin: 0 auto;">
        
        <div style="margin-bottom: var(--space-24);">
            <a href="<?php echo htmlspecialchars($user->getDashboardUrl(), ENT_QUOTES, 'UTF-8'); ?>" style="color: var(--color-primary); text-decoration: none; font-weight: 500;">
                &larr; Back to Dashboard
            </a>
        </div>

        <div style="margin-bottom: var(--space-32); text-align: center;">
            <h1>Edit Account Profile</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Update your display name, email, and avatar photo.</p>
        </div>

        <?php if ($dbError): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Database Error:</strong> <?php echo htmlspecialchars($dbError, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="sg-card">
            <form action="/profile/edit" method="POST" enctype="multipart/form-data" novalidate>
                
                <!-- Profile Avatar Preview & Upload -->
                <div class="form-group" style="text-align: center; margin-bottom: var(--space-32);">
                    <div style="position: relative; width: 100px; height: 100px; margin: 0 auto var(--space-16) auto;">
                        <?php if ($user->getAvatarUrl()): ?>
                            <img id="avatar-preview-img" src="<?php echo htmlspecialchars($user->getAvatarUrl(), ENT_QUOTES, 'UTF-8'); ?>" 
                                 alt="Avatar" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary);">
                        <?php else: ?>
                            <div id="avatar-preview-fallback" style="width: 100px; height: 100px; border-radius: 50%; background-color: var(--color-primary); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; font-weight: 700; border: 3px solid var(--color-primary);">
                                <?php echo strtoupper(substr($user->getName(), 0, 1)); ?>
                            </div>
                            <img id="avatar-preview-img" src="" alt="Avatar" style="display: none; width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 3px solid var(--color-primary);">
                        <?php endif; ?>
                    </div>

                    <label class="btn btn-secondary" for="avatar" style="cursor: pointer; display: inline-block; font-size: 0.85rem; padding: var(--space-6) var(--space-16);">
                        📷 Change Profile Photo
                    </label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp" style="display: none;">
                    
                    <p class="text-caption" style="margin-top: var(--space-8); color: var(--color-text-muted);">
                        JPG, PNG, or WebP. Max file size: 5MB.
                    </p>
                    
                    <?php if (isset($errors['avatar'])): ?>
                        <div class="form-error-msg" style="margin-top: var(--space-8); text-align: center;"><?php echo htmlspecialchars($errors['avatar'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" 
                           class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div style="display: flex; gap: var(--space-16); margin-top: var(--space-24);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">Save Changes</button>
                    <a href="<?php echo htmlspecialchars($user->getDashboardUrl(), ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-secondary">Cancel</a>
                </div>

            </form>
        </div>

    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const avatarInput = document.getElementById('avatar');
    const avatarImg = document.getElementById('avatar-preview-img');
    const avatarFallback = document.getElementById('avatar-preview-fallback');

    if (avatarInput) {
        avatarInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                if (file.size > 5242880) {
                    alert('File is too large! Maximum allowed size is 5MB.');
                    this.value = '';
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarImg) {
                        avatarImg.src = e.target.result;
                        avatarImg.style.display = 'block';
                    }
                    if (avatarFallback) {
                        avatarFallback.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
});
</script>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
