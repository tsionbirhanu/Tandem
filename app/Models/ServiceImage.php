<?php
// app/Models/ServiceImage.php

namespace App\Models;

use PDO;

class ServiceImage {

    public function __construct(private PDO $db) {}

    /**
     * Retrieves all gallery images for a given service ordered by sort_order.
     */
    public function getByServiceId(int $serviceId): array {
        $stmt = $this->db->prepare("
            SELECT * FROM service_images 
            WHERE service_id = :service_id 
            ORDER BY sort_order ASC, id ASC
        ");
        $stmt->execute([':service_id' => $serviceId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Inserts a new image record for a service.
     */
    public function create(int $serviceId, string $imagePath, int $sortOrder = 0): int {
        $stmt = $this->db->prepare("
            INSERT INTO service_images (service_id, image_path, sort_order, created_at)
            VALUES (:service_id, :image_path, :sort_order, NOW())
        ");
        $stmt->execute([
            ':service_id' => $serviceId,
            ':image_path' => $imagePath,
            ':sort_order' => $sortOrder,
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Deletes a specific service image by ID.
     */
    public function deleteById(int $imageId): void {
        $stmt = $this->db->prepare("DELETE FROM service_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
    }

    /**
     * Deletes all images for a given service ID.
     */
    public function deleteByServiceId(int $serviceId): void {
        $stmt = $this->db->prepare("DELETE FROM service_images WHERE service_id = :service_id");
        $stmt->execute([':service_id' => $serviceId]);
    }
}
