<?php
// app/Views/auth/login.php
include BASE_PATH . '/app/Views/layouts/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 420px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Welcome Back</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Log in to your Tandem account.</p>
        </div>

        <?php if (isset($errors['login'])): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Login Failed:</strong> <?php echo htmlspecialchars($errors['login'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="sg-card">
            <form action="/login" method="POST" novalidate>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. sarah.j@acmelabs.io">
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" 
                           class="form-input <?php echo isset($errors['password']) ? 'form-input-error' : ''; ?>"
                           placeholder="Enter your password">
                    <?php if (isset($errors['password'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['password'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-8);">Log In</button>
                
                <div style="text-align: center; margin-top: var(--space-24);">
                    <p class="text-small" style="color: var(--color-text-muted);">
                        Don't have an account? <a href="/register" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Sign up</a>
                    </p>
                </div>
            </form>
        </div>
        
    </div>
</main>

<?php include BASE_PATH . '/app/Views/layouts/footer.php'; ?>
