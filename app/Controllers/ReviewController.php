<?php
// app/Controllers/ReviewController.php

namespace App\Controllers;

use Database;
use App\Models\ProjectRequest;
use App\Models\Review;
use Exception;

class ReviewController {

    public function showCreate(int|string $requestId): void {
        requireRole('client');

        $requestId = (int)$requestId;
        if ($requestId <= 0) {
            header('Location: /dashboard/client');
            exit;
        }

        $clientId = $_SESSION['user_id'] ?? 0;
        $projectRequest = null;
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $requestModel = new ProjectRequest($pdo);
            $reviewModel  = new Review($pdo);

            $projectRequest = $requestModel->find($requestId);

            // Access control checks
            if (!$projectRequest || (int)$projectRequest['client_id'] !== $clientId) {
                setFlash('error', 'Project request not found or access denied.');
                header('Location: /dashboard/client');
                exit;
            }

            if ($projectRequest['status'] !== 'completed') {
                setFlash('error', 'Reviews can only be submitted for completed project requests.');
                header('Location: /dashboard/client');
                exit;
            }

            if ($reviewModel->hasReviewed($requestId)) {
                setFlash('notice', 'You have already submitted a review for this project request.');
                header('Location: /dashboard/client');
                exit;
            }

        } catch (Exception $e) {
            $dbError = "Database Error: Unable to verify project request. " . $e->getMessage();
        }

        render('reviews/create', [
            'projectRequest' => $projectRequest,
            'rating'         => 5,
            'comment'        => '',
            'errors'         => [],
            'dbError'        => $dbError,
        ]);
    }

    public function create(int|string $requestId): void {
        requireRole('client');

        $requestId = (int)$requestId;
        if ($requestId <= 0) {
            header('Location: /dashboard/client');
            exit;
        }

        $clientId = $_SESSION['user_id'] ?? 0;
        $rating   = (int)($_POST['rating'] ?? 5);
        $comment  = trim($_POST['comment'] ?? '');
        $errors   = [];
        $projectRequest = null;
        $dbError  = null;

        if ($rating < 1 || $rating > 5) {
            $errors['rating'] = "Please select a star rating between 1 and 5.";
        }

        if (empty($comment)) {
            $errors['comment'] = "Please provide feedback in your review comment.";
        } elseif (mb_strlen($comment) < 10) {
            $errors['comment'] = "Review comment must be at least 10 characters long.";
        }

        try {
            $pdo = Database::getConnection();
            $requestModel = new ProjectRequest($pdo);
            $reviewModel  = new Review($pdo);

            $projectRequest = $requestModel->find($requestId);

            if (!$projectRequest || (int)$projectRequest['client_id'] !== $clientId || $projectRequest['status'] !== 'completed') {
                setFlash('error', 'Invalid project request for review.');
                header('Location: /dashboard/client');
                exit;
            }

            if ($reviewModel->hasReviewed($requestId)) {
                setFlash('notice', 'You have already submitted a review for this project request.');
                header('Location: /dashboard/client');
                exit;
            }

            if (empty($errors)) {
                // Determine freelancer_id from service or request
                $freelancerId = (int)($projectRequest['freelancer_id'] ?? 0);
                if ($freelancerId <= 0 && !empty($projectRequest['service_id'])) {
                    $stmt = $pdo->prepare("SELECT freelancer_id FROM services WHERE id = :sid");
                    $stmt->execute([':sid' => $projectRequest['service_id']]);
                    $freelancerId = (int)$stmt->fetchColumn();
                }

                $reviewModel->create([
                    'project_request_id' => $requestId,
                    'client_id'          => $clientId,
                    'freelancer_id'      => $freelancerId,
                    'rating'             => $rating,
                    'comment'            => $comment,
                ]);

                setFlash('success', 'Thank you! Your review has been published successfully.');
                header('Location: /dashboard/client');
                exit;
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to submit review. " . $e->getMessage();
        }

        render('reviews/create', [
            'projectRequest' => $projectRequest,
            'rating'         => $rating,
            'comment'        => $comment,
            'errors'         => $errors,
            'dbError'        => $dbError,
        ]);
    }
}
