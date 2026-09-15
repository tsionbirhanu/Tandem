<?php
// app/Models/UserFactory.php

namespace App\Models;

/**
 * UserFactory
 * 
 * Factory pattern implementation for instantiating polymorphic User objects.
 * 
 * WHY IS THIS BETTER THAN SCATTERING `if ($role === 'freelancer')` CHECKS?
 * -------------------------------------------------------------------------
 * 1. Open-Closed Principle (SOLID): Adding a new role (e.g., 'Agency' or 'Moderator') 
 *    requires creating a new subclass (e.g., `Agency extends User`) and adding a line 
 *    to this Factory. You NEVER have to hunt down and update 50 `if/else` checks across 
 *    templates, dashboards, and auth handlers.
 * 
 * 2. Encapsulation & Polymorphism: Object behavior like `$user->getDashboardUrl()` or 
 *    `$user->getRoleDisplayName()` is encapsulated inside the respective subclass. 
 *    The calling code simply calls `$user->getDashboardUrl()` without caring what 
 *    concrete role class it is dealing with.
 * 
 * 3. Type Safety & Maintainability: Centralizing instantiation ensures that database 
 *    row data is mapped to clean, strongly typed object properties rather than passing 
 *    raw associative arrays with fragile string keys throughout the application.
 */
class UserFactory {

    /**
     * Creates and returns the appropriate User subclass instance based on database row.
     * 
     * @param array $row Associative array from database table `users`
     * @return User Concrete instance of Client, Freelancer, or Admin
     */
    public static function createUserFromRow(array $row): User {
        $id        = (int)($row['id'] ?? 0);
        $name      = (string)($row['name'] ?? '');
        $email     = (string)($row['email'] ?? '');
        $role      = (string)($row['role'] ?? 'client');
        $avatarUrl = $row['avatar_url'] ?? null;
        $createdAt = $row['created_at'] ?? null;

        return match ($role) {
            'freelancer' => new Freelancer($id, $name, $email, $role, $avatarUrl, $createdAt),
            'admin'      => new Admin($id, $name, $email, $role, $avatarUrl, $createdAt),
            default      => new Client($id, $name, $email, $role, $avatarUrl, $createdAt),
        };
    }

    public static function findRowByEmail(\PDO $pdo, string $email): ?array {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    public static function findUserById(\PDO $pdo, int $id): ?User {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        return $row ? self::createUserFromRow($row) : null;
    }

    public static function emailExists(\PDO $pdo, string $email): bool {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function createUser(\PDO $pdo, array $data): int {
        $stmt = $pdo->prepare("
            INSERT INTO users (name, email, password_hash, role, avatar_url, created_at)
            VALUES (:name, :email, :password_hash, :role, :avatar_url, NOW())
        ");
        $stmt->execute([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'password_hash' => $data['password_hash'],
            'role'          => $data['role'] ?? 'client',
            'avatar_url'    => $data['avatar_url'] ?? null,
        ]);
        return (int)$pdo->lastInsertId();
    }

    public static function updateProfile(\PDO $pdo, int $userId, string $name, string $email, ?string $avatarUrl = null): void {
        if ($avatarUrl !== null) {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email, avatar_url = :avatar_url WHERE id = :id");
            $stmt->execute([
                'name'       => $name,
                'email'      => $email,
                'avatar_url' => $avatarUrl,
                'id'         => $userId,
            ]);
        } else {
            $stmt = $pdo->prepare("UPDATE users SET name = :name, email = :email WHERE id = :id");
            $stmt->execute([
                'name'  => $name,
                'email' => $email,
                'id'    => $userId,
            ]);
        }
    }
}
