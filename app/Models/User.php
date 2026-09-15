<?php
// app/Models/User.php

namespace App\Models;

/**
 * Abstract Base User Class
 * 
 * Demonstrates Object-Oriented Polymorphism and Single Responsibility.
 * Role-specific logic is encapsulated in concrete subclasses (Client, Freelancer, Admin)
 * rather than scattering `if ($role === 'freelancer')` conditionals across the codebase.
 */
abstract class User {
    public function __construct(
        protected int $id,
        protected string $name,
        protected string $email,
        protected string $role,
        protected ?string $avatarUrl = null,
        protected ?string $createdAt = null
    ) {}

    // Getters
    public function getId(): int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function getEmail(): string { return $this->email; }
    public function getRole(): string { return $this->role; }
    public function getAvatarUrl(): ?string { return $this->avatarUrl; }
    public function getCreatedAt(): ?string { return $this->createdAt; }

    // Role checks
    public function isClient(): bool { return $this->role === 'client'; }
    public function isFreelancer(): bool { return $this->role === 'freelancer'; }
    public function isAdmin(): bool { return $this->role === 'admin'; }

    /**
     * Abstract method enforced on all user types to determine their role dashboard URL.
     */
    abstract public function getDashboardUrl(): string;

    /**
     * Abstract method enforcing human-readable display title for user roles.
     */
    abstract public function getRoleDisplayName(): string;
}
