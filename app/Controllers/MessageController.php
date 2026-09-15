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
            try {
                $pdo = Database::getConnection();
                $messageModel = new Message($pdo);
                $messageModel->create([
                    'sender_id'   => $_SESSION['user_id'] ?? null,
                    'receiver_id' => 1,
                    'content'     => "Contact form inquiry from {$name} ({$email}): {$message}",
                ]);
            } catch (Exception $e) {
                // Ignore or log error silently for contact form persistence
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
