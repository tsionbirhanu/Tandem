<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use Database;
use App\Models\UserFactory;
use App\Models\Review;
use Exception;

class AuthController {

    /**
     * A recent client review to show beside the login/register form (null if unavailable).
     */
    private function randomQuote(): ?array {
        try {
            $reviews = (new Review(Database::getConnection()))->latest(5);
            return $reviews ? $reviews[array_rand($reviews)] : null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function showLogin(): void {
        if (isLoggedIn()) {
            $userRole = $_SESSION['user_role'] ?? 'client';
            redirectUserToDashboard($userRole);
        }

        // Email carried over from a just-completed registration (shown once)
        $email = $_SESSION['login_prefill_email'] ?? '';
        unset($_SESSION['login_prefill_email']);

        render('auth/login', [
            'artQuote' => $this->randomQuote(),
            'email'  => $email,
            'errors' => [],
        ]);
    }

    public function login(): void {
        if (isLoggedIn()) {
            $userRole = $_SESSION['user_role'] ?? 'client';
            redirectUserToDashboard($userRole);
        }

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $errors   = [];

        if (empty($email)) {
            $errors['email'] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address.";
        }

        if (empty($password)) {
            $errors['password'] = "Password is required.";
        }

        if (empty($errors)) {
            try {
                $pdo = Database::getConnection();
                $userRow = UserFactory::findRowByEmail($pdo, $email);

                if ($userRow && password_verify($password, $userRow['password_hash'])) {
                    session_regenerate_id(true);

                    $user = UserFactory::createUserFromRow($userRow);

                    $_SESSION['user_id']    = $user->getId();
                    $_SESSION['user_name']  = $user->getName();
                    $_SESSION['user_email'] = $user->getEmail();
                    $_SESSION['user_role']  = $user->getRole();
                    $_SESSION['user_avatar']     = $user->getAvatarUrl();
                    $_SESSION['user_created_at'] = $user->getCreatedAt();

                    setFlash('success', "Welcome back, {$user->getName()}!");
                    redirectUserToDashboard($user->getRole());
                } else {
                    $errors['login'] = "Invalid email address or password.";
                }
            } catch (Exception $e) {
                $errors['login'] = "Database Connection Error: Unable to complete authentication. " . $e->getMessage();
            }
        }

        render('auth/login', [
            'artQuote' => $this->randomQuote(),
            'email'  => $email,
            'errors' => $errors,
        ]);
    }

    public function showRegister(): void {
        if (isLoggedIn()) {
            $userRole = $_SESSION['user_role'] ?? 'client';
            redirectUserToDashboard($userRole);
        }

        render('auth/register', [
            'artQuote' => $this->randomQuote(),
            'name'   => '',
            'email'  => '',
            'role'   => 'client',
            'errors' => [],
        ]);
    }

    public function register(): void {
        if (isLoggedIn()) {
            $userRole = $_SESSION['user_role'] ?? 'client';
            redirectUserToDashboard($userRole);
        }

        $name             = trim($_POST['name'] ?? '');
        $email            = trim($_POST['email'] ?? '');
        $role             = $_POST['role'] ?? 'client';
        $password         = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        $errors           = [];

        if (empty($name)) {
            $errors['name'] = "Full name is required.";
        } elseif (mb_strlen($name) < 2) {
            $errors['name'] = "Name must be at least 2 characters long.";
        }

        if (empty($email)) {
            $errors['email'] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address.";
        } else {
            try {
                $pdo = Database::getConnection();
                if (UserFactory::emailExists($pdo, $email)) {
                    $errors['email'] = "This email address is already registered.";
                }
            } catch (Exception $e) {
                $errors['global'] = "Database Connection Error: Unable to verify email uniqueness.";
            }
        }

        if (!in_array($role, ['client', 'freelancer'], true)) {
            $role = 'client';
        }

        if (empty($password)) {
            $errors['password'] = "Password is required.";
        } elseif (strlen($password) < 8) {
            $errors['password'] = "Password must be at least 8 characters.";
        }

        if ($password !== $confirm_password) {
            $errors['confirm_password'] = "Passwords do not match.";
        }

        if (empty($errors)) {
            try {
                $pdo = Database::getConnection();
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                UserFactory::createUser($pdo, [
                    'name'          => $name,
                    'email'         => $email,
                    'password_hash' => $hashed_password,
                    'role'          => $role,
                ]);

                // Not logged in automatically: send the new user to the login page
                // with their email prefilled; logging in takes them to their dashboard.
                $_SESSION['login_prefill_email'] = $email;
                setFlash('success', "Account created! Log in to get started, {$name}.");
                header('Location: /login');
                exit;
            } catch (Exception $e) {
                $errors['global'] = "Registration failed: " . $e->getMessage();
            }
        }

        render('auth/register', [
            'artQuote' => $this->randomQuote(),
            'name'   => $name,
            'email'  => $email,
            'role'   => $role,
            'errors' => $errors,
        ]);
    }

    public function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

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

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        setFlash('success', 'You have been successfully logged out.');
        header('Location: /login');
        exit;
    }
}
