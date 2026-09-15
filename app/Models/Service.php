<?php
// app/Models/Service.php

namespace App\Models;

use PDO;

class Service {
    public function __construct(private PDO $db) {}

    /**
     * Retrieves all services with optional category slug filtering.
     */
    public function all(?string $categorySlug = null): array {
        if (!empty($categorySlug)) {
            return $this->findByCategory($categorySlug);
        }

        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       c.slug AS category_slug, 
                       u.name AS freelancer_name, 
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                GROUP BY s.id 
                ORDER BY s.created_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    /**
     * Retrieves top-rated featured services for the homepage.
     */
    public function featured(int $limit = 3): array {
        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       u.name AS freelancer_name, 
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                GROUP BY s.id 
                ORDER BY rating DESC, s.created_at DESC 
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Finds a single service by ID with freelancer and category metadata.
     */
    public function find(int $id): ?array {
        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       c.slug AS category_slug, 
                       u.name AS freelancer_name, 
                       u.email AS freelancer_email, 
                       u.avatar_url AS freelancer_avatar,
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                WHERE s.id = :id 
                GROUP BY s.id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        $service = $stmt->fetch();
        return $service ?: null;
    }

    /**
     * Retrieves services by category slug.
     */
    public function findByCategory(string $slug): array {
        $sql = "SELECT s.*, 
                       c.name AS category_name, 
                       c.slug AS category_slug, 
                       u.name AS freelancer_name, 
                       COALESCE(AVG(r.rating), 5.0) AS rating, 
                       COUNT(r.id) AS reviews 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                JOIN users u ON s.freelancer_id = u.id 
                LEFT JOIN project_requests pr ON pr.service_id = s.id 
                LEFT JOIN reviews r ON r.project_request_id = pr.id 
                WHERE c.slug = :slug 
                GROUP BY s.id 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetchAll();
    }

    /**
     * Retrieves all services created by a specific freelancer.
     */
    public function findByFreelancer(int $freelancerId): array {
        $sql = "SELECT s.*, c.name AS category_name 
                FROM services s 
                JOIN categories c ON s.category_id = c.id 
                WHERE s.freelancer_id = :freelancer_id 
                ORDER BY s.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':freelancer_id' => $freelancerId]);
        return $stmt->fetchAll();
    }

    /**
     * Creates a new service record.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO services (freelancer_id, category_id, title, description, price, created_at) VALUES (:freelancer_id, :category_id, :title, :description, :price, NOW())");
        $stmt->execute([
            ':freelancer_id' => $data['freelancer_id'],
            ':category_id'   => $data['category_id'],
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':price'         => (float)$data['price'],
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Updates an existing service record.
     */
    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("UPDATE services SET freelancer_id = :freelancer_id, category_id = :category_id, title = :title, description = :description, price = :price WHERE id = :id");
        return $stmt->execute([
            ':freelancer_id' => $data['freelancer_id'],
            ':category_id'   => $data['category_id'],
            ':title'         => $data['title'],
            ':description'   => $data['description'],
            ':price'         => (float)$data['price'],
            ':id'            => $id,
        ]);
    }

    /**
     * Deletes a service record.
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM services WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Returns total count of services.
     */
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    }

    /**
     * Returns count of active services by freelancer ID.
     */
    public function countByFreelancer(int $freelancerId): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM services WHERE freelancer_id = :id");
        $stmt->execute([':id' => $freelancerId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Retrieves gallery images for a service.
     */
    public function getImages(int $serviceId): array {
        $stmt = $this->db->prepare("SELECT * FROM service_images WHERE service_id = :service_id ORDER BY sort_order ASC");
        $stmt->execute([':service_id' => $serviceId]);
        return $stmt->fetchAll();
    }

    /**
     * Adds a gallery image record to a service.
     */
    public function addImage(int $serviceId, string $imagePath, int $sortOrder = 1): bool {
        $stmt = $this->db->prepare("INSERT INTO service_images (service_id, image_path, sort_order) VALUES (:service_id, :image_path, :sort_order)");
        return $stmt->execute([
            ':service_id' => $serviceId,
            ':image_path' => $imagePath,
            ':sort_order' => $sortOrder,
        ]);
    }
}
