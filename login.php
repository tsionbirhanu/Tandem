<?php
// login.php
// User authentication handler using User model.

require_once 'includes/Database.php';
require_once 'includes/auth.php';

use App\Models\User;

if (isLoggedIn()) {
    redirectUserToDashboard($_SESSION['user_role'] ?? 'client');
}

$email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Validate Email
    if (empty($email)) {
        $errors['email'] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    }
    
    // Validate Password
    if (empty($password)) {
        $errors['password'] = "Password is required.";
    }

    // Authenticate via User model
    if (empty($errors)) {
        try {
            $pdo = Database::getConnection();
            $userModel = new User($pdo);
            $user = $userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);

                $_SESSION['user_id']    = (int)$user['id'];
                $_SESSION['user_name']  = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role']  = $user['role'];

                setFlash('success', "Welcome back, {$user['name']}!");
                redirectUserToDashboard($user['role']);
            } else {
                $errors['login'] = "Invalid email address or password.";
            }

        } catch (Exception $e) {
            $errors['login'] = "Database Connection Error: Unable to complete authentication. " . $e->getMessage();
        }
    }
}

include 'includes/header.php';
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
            <form action="login.php" method="POST" novalidate>
                
                <div class="form-group">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" id="email" name="email" 
                           class="form-input <?php echo isset($errors['email']) ? 'form-input-error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>"
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
                        Don't have an account? <a href="register.php" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Sign up</a>
                    </p>
                </div>
            </form>
        </div>
        
    </div>
</main>

<?php include 'includes/footer.php'; ?>
