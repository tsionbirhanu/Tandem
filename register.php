<?php
// register.php
// User registration handler using User model.

require_once 'includes/Database.php';
require_once 'includes/auth.php';

use App\Models\User;

if (isLoggedIn()) {
    redirectUserToDashboard($_SESSION['user_role'] ?? 'client');
}

$name = '';
$email = '';
$role = 'client';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? 'client';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Validate Name
    if (empty($name)) {
        $errors['name'] = "Full name is required.";
    } elseif (mb_strlen($name) < 2) {
        $errors['name'] = "Name must be at least 2 characters long.";
    }
    
    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } else {
        try {
            $pdo = Database::getConnection();
            $userModel = new User($pdo);

            if ($userModel->emailExists($email)) {
                $errors['email'] = "This email address is already registered.";
            }
        } catch (Exception $e) {
            $errors['global'] = "Database Connection Error: Unable to verify email uniqueness.";
        }
    }
    
    // Validate Role
    if (!in_array($role, ['client', 'freelancer'], true)) {
        $role = 'client';
    }

    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    } elseif (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters.";
    }
    
    // Validate Password Confirmation
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }
    
    // Register User via User model
    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();
            $userModel = new User($pdo);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $newUserId = $userModel->create([
                'name'          => $name,
                'email'         => $email,
                'password_hash' => $hashed_password,
                'role'          => $role,
            ]);
            
            session_regenerate_id(true);
            $_SESSION['user_id']    = $newUserId;
            $_SESSION['user_name']  = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role']  = $role;
            
            setFlash('success', "Account created successfully! Welcome to Tandem, {$name}.");
            redirectUserToDashboard($role);

        } catch (Exception $e) {
            $errors['global'] = "Registration failed: " . $e->getMessage();
        }
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

        <?php if (isset($errors['global'])): ?>
            <div class="alert alert-error" style="margin-bottom: var(--space-24);">
                <strong>Error:</strong> <?php echo htmlspecialchars($errors['global'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <div class="sg-card">
            <form action="register.php" method="POST" novalidate>
                
                <div class="form-group">
                    <label class="form-label" for="name">Full Name</label>
                    <input type="text" id="name" name="name" 
                           class="form-input <?php echo isset($errors['name']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. Jane Doe">
                    <?php if (isset($errors['name'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8'); ?></div>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
                           placeholder="e.g. jane@example.com">
                    <?php if (isset($errors['email'])): ?>
                        <div class="form-error-msg"><?php echo htmlspecialchars($errors['email'], ENT_QUOTES, 'UTF-8'); ?></div>
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
                        Already have an account? <a href="login.php" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Log in</a>
                    </p>
                </div>
            </form>
        </div>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>
