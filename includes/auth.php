<?php
// includes/auth.php
// Authentication & Session Management Helper Functions using UserFactory and User polymorphic models.

use App\Models\User;
use App\Models\UserFactory;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Ensures the user is logged in. Redirects to login.php with flash error if unauthenticated.
 */
function requireLogin(): void {
    if (!isset($_SESSION['user_id'])) {
        setFlash('error', 'Please log in to access this page.');
        header('Location: /login');
        exit;
    }
}

/**
 * Ensures the user has a specific role or one of an array of allowed roles.
 *
 * @param string|array $allowedRoles
 */
function requireRole($allowedRoles): void {
    requireLogin();
    
    $roles = is_array($allowedRoles) ? $allowedRoles : [$allowedRoles];
    $userRole = $_SESSION['user_role'] ?? '';
    
    if (!in_array($userRole, $roles, true)) {
        setFlash('error', 'You do not have permission to access that resource.');
        redirectUserToDashboard($userRole);
    }
}

/**
 * Checks if a user is currently logged in.
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

/**
 * Returns current logged-in User instance (Client, Freelancer, or Admin) or null.
 */
function currentUser(): ?User {
    if (!isLoggedIn()) {
        return null;
    }
    return UserFactory::createUserFromRow([
        'id'         => $_SESSION['user_id'],
        'name'       => $_SESSION['user_name'] ?? '',
        'email'      => $_SESSION['user_email'] ?? '',
        'role'       => $_SESSION['user_role'] ?? 'client',
        'avatar_url' => $_SESSION['user_avatar'] ?? null,
        'created_at' => $_SESSION['user_created_at'] ?? null,
    ]);
}

/**
 * Sets a session flash message ('success' or 'error').
 */
function setFlash(string $type, string $message): void {
    $_SESSION["flash_{$type}"] = $message;
}

/**
 * Retrieves and consumes a session flash message ('success' or 'error').
 */
function getFlash(string $type): ?string {
    $key = "flash_{$type}";
    if (isset($_SESSION[$key])) {
        $msg = $_SESSION[$key];
        unset($_SESSION[$key]);
        return $msg;
    }
    return null;
}

/**
 * Renders flash message banners if any are set in session.
 */
function renderFlashMessages(): void {
    $success = getFlash('success');
    $error = getFlash('error');
    
    if ($success) {
        echo '<div class="sg-container" style="padding-top: var(--space-16); padding-bottom: 0;">';
        echo '  <div class="alert alert-success" style="margin-bottom: 0;">';
        echo '    <strong>Success!</strong> ' . htmlspecialchars($success, ENT_QUOTES, 'UTF-8');
        echo '  </div>';
        echo '</div>';
    }
    
    if ($error) {
        echo '<div class="sg-container" style="padding-top: var(--space-16); padding-bottom: 0;">';
        echo '  <div class="alert alert-error" style="margin-bottom: 0;">';
        echo '    <strong>Notice:</strong> ' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8');
        echo '  </div>';
        echo '</div>';
    }
}

/**
 * Redirects user to their role-specific dashboard URL using polymorphism.
 */
function redirectUserToDashboard(string $role): void {
    $target = match ($role) {
        'admin'      => '/dashboard/admin',
        'freelancer' => '/dashboard/freelancer',
        default      => '/dashboard/client',
    };
    header("Location: {$target}");
    exit;
}
