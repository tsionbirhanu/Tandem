<?php
// logout.php
// Clears session data and logs the user out.

require_once 'includes/auth.php';

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();

// Start new session to store logout notification message
session_start();
setFlash('success', 'You have logged out successfully.');

header('Location: login.php');
exit;
