<?php
// app/Controllers/MessageController.php

namespace App\Controllers;

use Database;
use App\Models\Message;
use Exception;

class MessageController {

    public function showContact(): void {
        render('messages/contact', [
            'name'          => '',
            'email'         => '',
            'message'       => '',
            'errors'        => [],
            'isSuccess'     => false,
            'submittedData' => [],
        ]);
    }

    public function submitContact(): void {
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $errors  = [];
        $isSuccess = false;
        $submittedData = [];

        if (empty($name)) {
            $errors['name'] = "Name is required.";
        } elseif (mb_strlen($name) < 2) {
            $errors['name'] = "Name must be at least 2 characters long.";
        }

        if (empty($email)) {
            $errors['email'] = "Email address is required.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = "Please enter a valid email address.";
        }

        if (empty($message)) {
            $errors['message'] = "Project details are required.";
        } elseif (mb_strlen($message) < 20) {
            $errors['message'] = "Message must be at least 20 characters long.";
        } elseif (mb_strlen($message) > 1000) {
            $errors['message'] = "Message cannot exceed 1000 characters.";
        }

        if (empty($errors)) {
            // Messages need a sender account, so only logged-in users' inquiries are stored
            // (delivered to the platform admin's inbox).
            if (isLoggedIn()) {
                try {
                    $pdo = Database::getConnection();
                    $adminId = (int)$pdo->query("SELECT id FROM users WHERE role = 'admin' ORDER BY id LIMIT 1")->fetchColumn();
                    if ($adminId > 0) {
                        (new Message($pdo))->create([
                            'sender_id'   => (int)$_SESSION['user_id'],
                            'receiver_id' => $adminId,
                            'body'        => "Contact form inquiry from {$name} ({$email}): {$message}",
                        ]);
                    }
                } catch (Exception $e) {
                    error_log('Contact form message could not be stored: ' . $e->getMessage());
                }
            }

            $isSuccess = true;
            $submittedData = [
                'name'         => $name,
                'email'        => $email,
                'message'      => $message,
                'submitted_at' => date('F j, Y, g:i a'),
            ];

            $name    = '';
            $email   = '';
            $message = '';
        }

        render('messages/contact', [
            'name'          => $name,
            'email'         => $email,
            'message'       => $message,
            'errors'        => $errors,
            'isSuccess'     => $isSuccess,
            'submittedData' => $submittedData,
        ]);
    }
}
