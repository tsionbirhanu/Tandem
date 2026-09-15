<?php
// login.php
// Handles user authentication with field-level validation.

$email = '';
$errors = [];
$successMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }
    
    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }
    
    // Process Success (Mock Authentication)
    if (empty($errors)) {
        // In a real app, we'd query the DB and use password_verify()
        // For our mockup, we just simulate a successful login
        $successMessage = "Successfully logged in as {$email}!";
        $email = ''; // clear for display
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 400px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Welcome Back</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Log in to your Tandem account.</p>
        </div>

        <?php if ($successMessage): ?>
            <!-- Success State -->
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> <?php echo htmlspecialchars($successMessage); ?>
            </div>
            
            <div style="text-align: center;">
                <a href="index.php" class="btn btn-primary">Go to Dashboard</a>
            </div>
            
        <?php else: ?>
            <!-- Login Form -->
            <div class="sg-card">
                <form action="login.php" method="POST">
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email Address</label>
                        <input type="email" id="email" name="email" 
                               class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($email); ?>">
                        <?php if (isset($errors['email'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['email']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" 
                               class="form-input <?php echo isset($errors['password']) ? 'form-input-error' : ''; ?>">
                        <?php if (isset($errors['password'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-8);">Log In</button>
                    
                    <div style="text-align: center; margin-top: var(--space-24);">
                        <p class="text-small">Don't have an account? <a href="register.php" style="color: var(--color-primary); font-weight: 500;">Sign up</a></p>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>
