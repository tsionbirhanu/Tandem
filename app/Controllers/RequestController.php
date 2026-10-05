<?php
// app/Controllers/RequestController.php
// Hiring flow: clients send project requests for a service; freelancers accept,
// decline, start and complete them; clients may cancel before work starts.

namespace App\Controllers;

use Database;
use App\Models\Message;
use App\Models\ProjectRequest;
use App\Models\Service;
use Exception;

class RequestController {

    /**
     * POST /services/{id}/request — a client sends a brief to the service's freelancer.
     */
    public function create(int|string $serviceId): void {
        requireRole('client');

        $serviceId = (int)$serviceId;
        $clientId  = (int)$_SESSION['user_id'];
        $brief     = trim($_POST['message'] ?? '');

        if (mb_strlen($brief) < 20) {
            setFlash('error', 'Please describe your project in at least 20 characters.');
            header("Location: /services/{$serviceId}#request");
            exit;
        }
        if (mb_strlen($brief) > 2000) {
            setFlash('error', 'Please keep your brief under 2000 characters.');
            header("Location: /services/{$serviceId}#request");
            exit;
        }

        try {
            $pdo          = Database::getConnection();
            $service      = (new Service($pdo))->find($serviceId);
            $requestModel = new ProjectRequest($pdo);

            if (!$service) {
                setFlash('error', 'That service no longer exists.');
                header('Location: /services');
                exit;
            }

            if ($requestModel->findOpenForClientAndService($clientId, $serviceId)) {
                setFlash('notice', 'You already have an open request for this service — check your dashboard.');
                header('Location: /dashboard/client');
                exit;
            }

            $pdo->beginTransaction();
            $requestId = $requestModel->create([
                'client_id'  => $clientId,
                'service_id' => $serviceId,
                'message'    => $brief,
            ]);
            // The brief also opens the conversation thread for this request
            (new Message($pdo))->create([
                'sender_id'          => $clientId,
                'receiver_id'        => (int)$service['freelancer_id'],
                'project_request_id' => $requestId,
                'body'               => $brief,
            ]);
            $pdo->commit();

            $first = explode(' ', $service['freelancer_name'])[0];
            setFlash('success', "Request sent! {$first} will accept or decline it from their dashboard.");
            header('Location: /dashboard/client');
            exit;
        } catch (Exception $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            setFlash('error', 'Could not send your request. Please try again.');
            header("Location: /services/{$serviceId}");
            exit;
        }
    }

    /**
     * POST /requests/{id}/status — moves a request through its lifecycle.
     * Expects an `action` field: accept | reject | start | complete | cancel.
     */
    public function updateStatus(int|string $requestId): void {
        requireRole(['client', 'freelancer']);

        $requestId = (int)$requestId;
        $userId    = (int)$_SESSION['user_id'];
        $role      = $_SESSION['user_role'];
        $action    = $_POST['action'] ?? '';
        $back      = $role === 'freelancer' ? '/dashboard/freelancer' : '/dashboard/client';

        try {
            $pdo          = Database::getConnection();
            $requestModel = new ProjectRequest($pdo);
            $request      = $requestModel->find($requestId);

            // Only the two parties of the request may act on it
            $isOwner = $request && (
                ($role === 'client'     && (int)$request['client_id'] === $userId) ||
                ($role === 'freelancer' && (int)$request['freelancer_id'] === $userId)
            );
            if (!$isOwner) {
                setFlash('error', 'Request not found or access denied.');
                header("Location: {$back}");
                exit;
            }

            $newStatus = ProjectRequest::nextStatus($request['status'], $action, $role);
            if ($newStatus === null) {
                setFlash('error', 'That action is not available for this request right now.');
                header("Location: {$back}");
                exit;
            }

            $requestModel->updateStatus($requestId, $newStatus);

            $messages = [
                'accepted'    => 'Request accepted. Start work when you\'re ready.',
                'rejected'    => 'Request declined.',
                'in_progress' => 'Marked as in progress.',
                'completed'   => 'Project marked complete — the client can now leave a review.',
                'cancelled'   => 'Request cancelled.',
            ];
            setFlash('success', $messages[$newStatus]);
        } catch (Exception $e) {
            setFlash('error', 'Could not update the request. Please try again.');
        }

        header("Location: {$back}");
        exit;
    }
}
