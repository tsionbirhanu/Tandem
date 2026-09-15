<?php
// app/Models/Admin.php

namespace App\Models;

use PDO;

class Admin extends User {

    public function getDashboardUrl(): string {
        return '/dashboard/admin';
    }

    public function getRoleDisplayName(): string {
        return 'System Administrator';
    }

    /**
     * Role-specific method: Retrieves system-wide overview statistics.
     */
    public function getPlatformStats(PDO $db): array {
        return [
            'users'    => (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
            'services' => (int)$db->query("SELECT COUNT(*) FROM services")->fetchColumn(),
            'requests' => (int)$db->query("SELECT COUNT(*) FROM project_requests")->fetchColumn(),
            'reviews'  => (int)$db->query("SELECT COUNT(*) FROM reviews")->fetchColumn(),
        ];
    }

    /**
     * Role-specific method: Retrieves complete user directory.
     */
    public function getAllUsers(PDO $db): array {
        return $db->query("SELECT id, name, email, role, created_at FROM users ORDER BY id ASC")->fetchAll();
    }
}
