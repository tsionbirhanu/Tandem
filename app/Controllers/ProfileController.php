<?php
// app/Controllers/ProfileController.php

namespace App\Controllers;

use Database;
use App\Models\UserFactory;
use App\Models\Service;
use App\Models\Review;
use Exception;

class ProfileController {

    public function showEdit(): void {
        requireLogin();

        $user = currentUser();
        $dbError = null;

        render('profile/edit', [
            'user'    => $user,
            'name'    => $user->getName(),
            'email'   => $user->getEmail(),
            'errors'  => [],
            'dbError' => $dbError,
        ]);
    }

    public function edit(): void {
        requireLogin();

        $user   = currentUser();
        $userId = $user->getId();
        $name   = trim($_POST['name'] ?? '');
        $email  = trim($_POST['email'] ?? '');
        $errors = [];
        $dbError = null;
        $avatarPath = null;

        if (empty($name)) {
            $errors['name'] = "Full name is required.";
        } elseif (mb_strlen($name) < 2) {
            $errors['name'] = "Name must be at least 2 characters long.";
        }

        if (empty($email)) {
            $errors['email'] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address.";
        }

        // Handle Avatar File Upload if provided
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = uploadImageFile($_FILES['avatar'], 'avatars');
            if ($uploadResult['success']) {
                $avatarPath = '/' . ltrim($uploadResult['path'], '/');
            } else {
                $errors['avatar'] = $uploadResult['error'];
            }
        }

        if (empty($errors)) {
            try {
                $pdo = Database::getConnection();

                // Check email uniqueness if modified
                if (strtolower($email) !== strtolower($user->getEmail()) && UserFactory::emailExists($pdo, $email)) {
                    $errors['email'] = "This email address is already in use by another account.";
                } else {
                    UserFactory::updateProfile($pdo, $userId, $name, $email, $avatarPath);

                    // Update active session data
                    $_SESSION['user_name']  = $name;
                    $_SESSION['user_email'] = $email;
                    if ($avatarPath !== null) {
                        $_SESSION['user_avatar'] = $avatarPath;
                    }

                    setFlash('success', 'Profile updated successfully!');
                    header('Location: ' . $user->getDashboardUrl());
                    exit;
                }

            } catch (Exception $e) {
                $dbError = "Database Error: Unable to update profile. " . $e->getMessage();
            }
        }

        render('profile/edit', [
            'user'    => $user,
            'name'    => $name,
            'email'   => $email,
            'errors'  => $errors,
            'dbError' => $dbError,
        ]);
    }

    public function showFreelancer(int|string $id): void {
        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $freelancer = null;
        $services   = [];
        $reviews    = [];
        $avgRating  = 5.0;
        $reviewCount= 0;
        $dbError    = null;

        try {
            $pdo = Database::getConnection();
            $freelancer   = UserFactory::findUserById($pdo, $id);
            $serviceModel = new Service($pdo);
            $reviewModel  = new Review($pdo);

            if ($freelancer && ($freelancer->isFreelancer() || $freelancer->isAdmin())) {
                $services    = $serviceModel->findByFreelancer($id);
                $reviews     = $reviewModel->getByFreelancerId($id);
                $reviewCount = count($reviews);
                if ($reviewCount > 0) {
                    $totalScore = array_sum(array_column($reviews, 'rating'));
                    $avgRating  = $totalScore / $reviewCount;
                }

                $freelancerData = [
                    'id'           => $freelancer->getId(),
                    'name'         => $freelancer->getName(),
                    'email'        => $freelancer->getEmail(),
                    'avatar_url'   => $freelancer->getAvatarUrl(),
                    'role'         => $freelancer->getRole(),
                    'rating_avg'   => $avgRating,
                    'review_count' => $reviewCount,
                ];
            } else {
                $freelancerData = null;
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to load freelancer profile. " . $e->getMessage();
        }

        if (empty($freelancerData) && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/freelancer/{$id}"]);
            return;
        }

        render('profile/freelancer', [
            'freelancer'  => $freelancerData,
            'services'    => $services,
            'reviews'     => $reviews,
            'avgRating'   => $avgRating,
            'reviewCount' => $reviewCount,
            'dbError'     => $dbError,
        ]);
    }
}
