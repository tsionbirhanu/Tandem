<?php
// app/Models/ProjectRequest.php

namespace App\Models;

use PDO;

class ProjectRequest {
    public function __construct(private PDO $db) {}

    /**
     * Finds a project request by ID.
     */
    public function find(int $id): ?array {
        $sql = "SELECT pr.*, s.title AS service_title, s.price, c.name AS category_name, 
                       cl.name AS client_name, cl.email AS client_email,
                       fl.name AS freelancer_name, fl.email AS freelancer_email
                FROM project_requests pr 
                LEFT JOIN services s ON pr.service_id = s.id 
                LEFT JOIN categories c ON s.category_id = c.id
                LEFT JOIN users cl ON pr.client_id = cl.id 
                LEFT JOIN users fl ON s.freelancer_id = fl.id 
                WHERE pr.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $req = $stmt->fetch();
        return $req ?: null;
    }

    /**
     * Finds all project requests submitted by a specific client.
     */
    public function findByClient(int $clientId): array {
        $sql = "SELECT pr.*, s.title AS service_title, s.price, c.name AS category_name, u.name AS freelancer_name 
                FROM project_requests pr 
                JOIN services s ON pr.service_id = s.id 
                JOIN categories c ON s.category_id = c.id
                JOIN users u ON s.freelancer_id = u.id 
                WHERE pr.client_id = :client_id 
                ORDER BY pr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':client_id' => $clientId]);
        return $stmt->fetchAll();
    }

    /**
     * Finds incoming project requests for a freelancer.
     */
    public function findByFreelancer(int $freelancerId): array {
        $sql = "SELECT pr.*, s.title AS service_title, u.name AS client_name, u.email AS client_email 
                FROM project_requests pr 
                JOIN services s ON pr.service_id = s.id 
                JOIN users u ON pr.client_id = u.id 
                WHERE s.freelancer_id = :freelancer_id 
                ORDER BY pr.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':freelancer_id' => $freelancerId]);
        return $stmt->fetchAll();
    }

    /**
     * Creates a new project request record.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO project_requests (client_id, service_id, message, status, created_at) VALUES (:client_id, :service_id, :message, :status, NOW())");
        $stmt->execute([
            ':client_id' => $data['client_id'],
            ':service_id' => $data['service_id'] ?? null,
            ':message'    => $data['message'],
            ':status'     => $data['status'] ?? 'pending',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Returns total count of all project requests in the platform.
     */
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM project_requests")->fetchColumn();
    }

    /**
     * Returns active project requests for a client ('pending', 'accepted', 'in_progress').
     */
    public function countActiveByClient(int $clientId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_requests WHERE client_id = :id AND status IN ('pending', 'accepted', 'in_progress')");
        $stmt->execute([':id' => $clientId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Returns completed project requests count for a client.
     */
    public function countCompletedByClient(int $clientId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_requests WHERE client_id = :id AND status = 'completed'");
        $stmt->execute([':id' => $clientId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Returns count of pending requests for a freelancer.
     */
    public function countPendingByFreelancer(int $freelancerId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE s.freelancer_id = :id AND pr.status = 'pending'");
        $stmt->execute([':id' => $freelancerId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Returns count of in-progress requests for a freelancer.
     */
    public function countInProgressByFreelancer(int $freelancerId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE s.freelancer_id = :id AND pr.status = 'in_progress'");
        $stmt->execute([':id' => $freelancerId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Calculates total investment spent by a client on completed projects.
     */
    public function totalSpentByClient(int $clientId): float {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(s.price), 0) FROM project_requests pr JOIN services s ON pr.service_id = s.id WHERE pr.client_id = :id AND pr.status = 'completed'");
        $stmt->execute([':id' => $clientId]);
        return (float)$stmt->fetchColumn();
    }
}
