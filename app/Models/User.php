<?php
// app/Models/User.php

namespace App\Models;

use PDO;

class User {
    public function __construct(private PDO $db) {}

    /**
     * Finds a user by ID.
     */
    public function find(int $id): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, role, avatar_url, created_at FROM users WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Finds a user by email (including password_hash for authentication).
     */
    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT id, name, email, password_hash, role, avatar_url, created_at FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Checks if an email address is already registered.
     */
    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Creates a new user record and returns the new user ID.
     */
    public function create(array $data): int {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, role, created_at) VALUES (:name, :email, :password_hash, :role, NOW())");
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':password_hash' => $data['password_hash'],
            ':role'          => $data['role'] ?? 'client',
        ]);
        return (int)$this->db->lastInsertId();
    }

    /**
     * Retrieves all users (for admin panel).
     */
    public function all(): array {
        $stmt = $this->db->query("SELECT id, name, email, role, created_at FROM users ORDER BY id ASC");
        return $stmt->fetchAll();
    }

    /**
     * Retrieves users filtered by role.
     */
    public function findByRole(string $role): array {
        $stmt = $this->db->prepare("SELECT id, name, email FROM users WHERE role = :role ORDER BY name ASC");
        $stmt->execute([':role' => $role]);
        return $stmt->fetchAll();
    }

    /**
     * Returns total count of registered users.
     */
    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    }
}
