<?php
// app/Models/Freelancer.php

namespace App\Models;

use PDO;

class Freelancer extends User {

    public function getDashboardUrl(): string {
        return 'freelancer-dashboard.php';
    }

    public function getRoleDisplayName(): string {
        return 'Freelancer Workspace';
    }

    /**
     * Role-specific method: Retrieves active service listings posted by this freelancer.
     */
    public function getServices(PDO $db): array {
        $stmt = $db->prepare("SELECT s.*, c.name AS category_name FROM services s JOIN categories c ON s.category_id = c.id WHERE s.freelancer_id = :id ORDER BY s.created_at DESC");
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetchAll();
    }

    /**
     * Role-specific method: Count of active services posted.
     */
    public function getActiveServicesCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM services WHERE freelancer_id = :id");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Count of pending project requests from clients.
     */
    public function getPendingRequestsCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE s.freelancer_id = :id AND pr.status = 'pending'");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Count of in-progress projects.
     */
    public function getInProgressProjectsCount(PDO $db): int {
        $stmt = $db->prepare("SELECT COUNT(*) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE s.freelancer_id = :id AND pr.status = 'in_progress'");
        $stmt->execute([':id' => $this->id]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Role-specific method: Retrieves incoming client inquiries.
     */
    public function getIncomingRequests(PDO $db): array {
        $stmt = $db->prepare("SELECT pr.*, s.title AS service_title, u.name AS client_name, u.email AS client_email 
                              FROM project_requests pr 
                              JOIN services s ON pr.service_id = s.id 
                              JOIN users u ON pr.client_id = u.id 
                              WHERE s.freelancer_id = :id 
                              ORDER BY pr.created_at DESC");
        $stmt->execute([':id' => $this->id]);
        return $stmt->fetchAll();
    }

    /**
     * Role-specific method: Calculates average rating across client reviews.
     */
    public function getAverageRating(PDO $db): float {
        $stmt = $db->prepare("SELECT COALESCE(AVG(rating), 5.0) FROM reviews WHERE freelancer_id = :id");
        $stmt->execute([':id' => $this->id]);
        return (float)$stmt->fetchColumn();
    }
}
