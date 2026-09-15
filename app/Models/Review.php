<?php
// app/Models/Review.php

namespace App\Models;

use PDO;

class Review {
    public function __construct(private PDO $db) {}

    /**
     * Finds a review by ID.
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM reviews WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $rev = $stmt->fetch();
        return $rev ?: null;
    }

    /**
     * Creates a new review record.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO reviews (project_request_id, client_id, freelancer_id, rating, comment, created_at) VALUES (:project_request_id, :client_id, :freelancer_id, :rating, :comment, NOW())");
        $stmt->execute([
            ':project_request_id' => $data['project_request_id'],
            ':client_id'          => $data['client_id'],
            ':freelancer_id'      => $data['freelancer_id'],
            ':rating'             => (int)$data['rating'],
            ':comment'            => $data['comment'] ?? null,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Returns total count of reviews in the system.
     */
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    }

    /**
     * Returns count of reviews submitted by a client.
     */
    public function countByClient(int $clientId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM reviews WHERE client_id = :id");
        $stmt->execute([':id' => $clientId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Returns average rating score for a freelancer.
     */
    public function averageRatingByFreelancer(int $freelancerId): float {
        $stmt = $this->db->prepare("SELECT COALESCE(AVG(rating), 5.0) FROM reviews WHERE freelancer_id = :id");
        $stmt->execute([':id' => $freelancerId]);
        return (float)$stmt->fetchColumn();
    }

    /**
     * Returns list of reviews for a given service ID.
     */
    public function getByServiceId(int $serviceId): array {
        $stmt = $this->db->prepare("
            SELECT r.*, u.name AS client_name 
            FROM reviews r
            JOIN project_requests pr ON pr.id = r.project_request_id
            JOIN users u ON u.id = r.client_id
            WHERE pr.service_id = :service_id
            ORDER BY r.created_at DESC
        ");
        $stmt->execute([':service_id' => $serviceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
