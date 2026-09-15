<?php
// app/Models/Category.php

namespace App\Models;

use PDO;

class Category {
    public function __construct(private PDO $db) {}

    /**
     * Retrieves all categories ordered by name.
     */
    public function all(): array {
        $stmt = $this->db->query("SELECT id, name, slug FROM categories ORDER BY name ASC");
        return $stmt->fetchAll();
    }

    /**
     * Finds a category by ID.
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }

    /**
     * Finds a category by slug.
     */
    public function findBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT id, name, slug FROM categories WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        $cat = $stmt->fetch();
        return $cat ?: null;
    }
}
