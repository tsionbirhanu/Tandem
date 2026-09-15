<?php
// app/Views/auth/register.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 480px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Create an Account</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Join Tandem to hire top talent or find premium work.</p>
        </div>

        <?php if (isset($errors['global'])): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Error:</strong> <?php echo htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="sg-card">
            <form action="/register" method="POST" novalidate>
                
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" 
                           class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($name ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. Jane Doe">
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. jane@example.com">
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">I want to join as a:</label>
                    <div class="segmented-control">
                        <input type="radio" id="role_client" name="role" value="client" <?php echo (($role ?? 'client') === 'client') ? 'checked' : ''; ?>>
                        <label for="role_client">Client (Hire Talent)</label>
                        
                        <input type="radio" id="role_freelancer" name="role" value="freelancer" <?php echo (($role ?? 'client') === 'freelancer') ? 'checked' : ''; ?>>
                        <label for="role_freelancer">Freelancer (Find Work)</label>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           class="form-input <?php echo isset($errors['password']) ? 'form-input-error' : ''; ?>"
                           placeholder="Create a strong password">
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php else: ?>
                        <p class="text-caption" style="margin-top: var(--space-4);">Must be at least 8 characters.</p>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" 
                           class="form-input <?php echo isset($errors['confirm_password']) ? 'form-input-error' : ''; ?>"
                           placeholder="Re-enter your password">
                    <?php if (isset($errors['confirm_password'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['confirm_password'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-8);">Create Account</button>
                
                <div style="text-align: center; margin-top: var(--space-24);">
                    <p class="text-small" style="color: var(--color-text-muted);">
                        Already have an account? <a href="/login" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Log in</a>
                    </p>
                </div>
            </form>
        </div>
        
    </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
