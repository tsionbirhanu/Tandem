<?php
// app/Controllers/AuthController.php

namespace App\Controllers;

use Database;
use App\Models\UserFactory;
use Exception;

class AuthController {

    public function showLogin(): void {
        if (isLoggedIn()) {
            $userRole = $_SESSION['user_role'] ?? 'client';
            redirectUserToDashboard($userRole);
        }

        render('auth/login', [
            'email'  => '',
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

                $newUserId = UserFactory::createUser($pdo, [
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

        render('auth/register', [
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
