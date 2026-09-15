<?php
// app/Controllers/DashboardController.php

namespace App\Controllers;

use Database;
use App\Models\Client;
use App\Models\Freelancer;
use App\Models\Admin;
use Exception;

class DashboardController {

    public function client(): void {
        requireRole('client');

        /** @var Client $user */
        $user = currentUser();
        $dbError = null;

        $stats = [
            'active_requests'    => 0,
            'completed_projects' => 0,
            'reviews_written'    => 0,
            'total_spent'        => 0.0,
        ];
        $projectRequests = [];

        try {
            $pdo = Database::getConnection();

            $stats['active_requests']    = $user->getActiveRequestsCount($pdo);
            $stats['completed_projects'] = $user->getCompletedProjectsCount($pdo);
            $stats['reviews_written']    = $user->getReviewsCount($pdo);
            $stats['total_spent']        = $user->getTotalSpent($pdo);

            $projectRequests = $user->getProjectRequests($pdo);

        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch dashboard data. " . $e->getMessage();
        }

        render('dashboard/client', [
            'user'            => $user,
            'stats'           => $stats,
            'projectRequests' => $projectRequests,
            'dbError'         => $dbError,
        ]);
    }

    public function freelancer(): void {
        requireRole('freelancer');

        /** @var Freelancer $user */
        $user = currentUser();
        $dbError = null;

        $stats = [
            'active_services'    => 0,
            'pending_requests'   => 0,
            'in_progress'        => 0,
            'average_rating'     => 0.0,
        ];
        $myServices = [];
        $incomingRequests = [];

        try {
            $pdo = Database::getConnection();

            $stats['active_services']  = $user->getActiveServicesCount($pdo);
            $stats['pending_requests'] = $user->getPendingRequestsCount($pdo);
            $stats['in_progress']      = $user->getInProgressProjectsCount($pdo);
            $stats['average_rating']   = $user->getAverageRating($pdo);

            $myServices       = $user->getServices($pdo);
            $incomingRequests = $user->getIncomingRequests($pdo);

        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch dashboard data. " . $e->getMessage();
        }

        render('dashboard/freelancer', [
            'user'             => $user,
            'stats'            => $stats,
            'myServices'       => $myServices,
            'incomingRequests' => $incomingRequests,
            'dbError'          => $dbError,
        ]);
    }

    public function admin(): void {
        requireRole('admin');

        /** @var Admin $user */
        $user = currentUser();
        $dbError = null;

        $stats = [
            'users'    => 0,
            'services' => 0,
            'requests' => 0,
            'reviews'  => 0,
        ];
        $allUsers = [];

        try {
            $pdo = Database::getConnection();

            $stats    = $user->getPlatformStats($pdo);
            $allUsers = $user->getAllUsers($pdo);

        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch system administration data. " . $e->getMessage();
        }

        render('dashboard/admin', [
            'user'     => $user,
            'stats'    => $stats,
            'allUsers' => $allUsers,
            'dbError'  => $dbError,
        ]);
    }
}
