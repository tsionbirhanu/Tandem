<?php
// register.php
// Handles user registration with field-level validation and segmented toggle styling.

$name = '';
$email = '';
$role = 'client'; // default role
$errors = [];
$successData = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'client';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate Name
    if (empty($name)) {
        $errors['name'] = "Name is required.";
    }
    
    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }
    
    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }
    
    // Validate Confirm Password
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
    
    // Process Success
    if (empty($errors)) {
        // Securely hash the password before "storage"
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Store the data array that would be saved to a database
        $successData = [
            'name' => $name,
            'email' => $email,
            'role' => $role,
            'password_hash' => $hashed_password,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Clear fields for display
        $name = '';
        $email = '';
        $role = 'client';
    }
}

include 'includes/header.php';
?>

<main class="page-main sg-container sg-section" style="padding-top: var(--space-48);">
    <div style="max-width: 480px; margin: 0 auto;">
        
        <div style="text-align: center; margin-bottom: var(--space-32);">
            <h1>Create an Account</h1>
            <p class="text-small" style="color: var(--color-text-muted);">Join Tandem to hire top talent or find premium work.</p>
        </div>

        <?php if ($successData): ?>
            <!-- Success State -->
            <div class="alert alert-success" style="margin-bottom: var(--space-24);">
                <strong>Success!</strong> Your account has been "created".
            </div>
            
            <div class="sg-card">
                <h3 style="margin-bottom: var(--space-16);">Database payload simulation:</h3>
                <pre style="background: var(--color-bg-base); padding: var(--space-16); border-radius: var(--radius-sm); font-size: var(--text-small); overflow-x: auto;"><?php var_dump($successData); ?></pre>
                <div style="margin-top: var(--space-24); text-align: center;">
                    <a href="login.php" class="btn btn-primary">Go to Login</a>
                </div>
            </div>
            
        <?php else: ?>
            <!-- Registration Form -->
            <div class="sg-card">
                <form action="register.php" method="POST">
                    
                    <div class="form-group">
                        <label class="form-label" for="name">Full Name</label>
                        <input type="text" id="name" name="name" 
                               class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                               value="<?php echo htmlspecialchars($name); ?>">
                        <?php if (isset($errors['name'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['name']); ?></div>
                        <?php endif; ?>
                    </div>

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
                        <label class="form-label">I want to join as a:</label>
                        <div class="segmented-control">
                            <input type="radio" id="role_client" name="role" value="client" <?php echo ($role === 'client') ? 'checked' : ''; ?>>
                            <label for="role_client">Client (Hire Talent)</label>
                            
                            <input type="radio" id="role_freelancer" name="role" value="freelancer" <?php echo ($role === 'freelancer') ? 'checked' : ''; ?>>
                            <label for="role_freelancer">Freelancer (Find Work)</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" id="password" name="password" 
                               class="form-input <?php echo isset($errors['password']) ? 'form-input-error' : ''; ?>">
                        <?php if (isset($errors['password'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['password']); ?></div>
                        <?php else: ?>
                            <p class="text-caption" style="margin-top: var(--space-4);">Must be at least 8 characters.</p>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-input <?php echo isset($errors['confirm_password']) ? 'form-input-error' : ''; ?>">
                        <?php if (isset($errors['confirm_password'])): ?>
                            <div class="form-error-msg"><?php echo htmlspecialchars($errors['confirm_password']); ?></div>
                        <?php endif; ?>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: var(--space-8);">Create Account</button>
                    
                    <div style="text-align: center; margin-top: var(--space-24);">
                        <p class="text-small">Already have an account? <a href="login.php" style="color: var(--color-primary); font-weight: 500;">Log in</a></p>
                    </div>
                </form>
            </div>
        <?php endif; ?>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>
