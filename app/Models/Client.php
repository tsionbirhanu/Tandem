<?php
// app/Models/Client.php

namespace App\Models;

use PDO;

class Client extends User {

    public function getDashboardUrl(): string {
        return 'client-dashboard.php';
    }

    public function getRoleDisplayName(): string {
        return 'Client Workspace';
    }

    /**
     * Role-specific method: Retrieves all project requests submitted by this client.
     */
    public function getProjectRequests(PDO $db): array {
        $stmt = $db->prepare("SELECT pr.*, s.title AS service_title, s.price, c.name AS category_name, u.name AS freelancer_name 
                              FROM project_requests pr 
                              JOIN services s ON pr.service_id = s.id 
                              JOIN categories c ON s.category_id = c.id
                              JOIN users u ON s.freelancer_id = u.id 
                              WHERE pr.client_id = :client_id 
                              ORDER BY pr.created_at DESC");
        $stmt->execute([':client_id' => $this->id]);
        return $stmt->fetchAll();
    }

    /**
     * Role-specific method: Count of active requests ('pending', 'accepted', 'in_progress').
     */
    public function getActiveRequestsCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM project_requests WHERE client_id = :id AND status IN ('pending', 'accepted', 'in_progress')");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Count of completed projects.
     */
    public function getCompletedProjectsCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM project_requests WHERE client_id = :id AND status = 'completed'");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Count of reviews submitted.
     */
    public function getReviewsCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM reviews WHERE client_id = :id");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Total investment spent on completed projects.
     */
    public function getTotalSpent(PDO $db): float {
        $stmt = $db->prepare("SELECT COALESCE(SUM(s.price), 0) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE pr.client_id = :id AND pr.status = 'completed'");
        $stmt->execute([':id' => $this->id]);
        return (float)$stmt->fetchColumn();
    }
}
