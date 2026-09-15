-- =============================================================================
-- Tandem Database Schema (MySQL 8.0+)
-- =============================================================================

CREATE DATABASE IF NOT EXISTS `tandem_db` 
  DEFAULT CHARACTER SET utf8mb4 
  DEFAULT COLLATE utf8mb4_unicode_ci;

USE `tandem_db`;

-- Disable foreign key checks during creation for safe execution order
SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables if they exist
DROP TABLE IF EXISTS `notifications`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `project_requests`;
DROP TABLE IF EXISTS `service_images`;
DROP TABLE IF EXISTS `services`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 1. USERS
-- Stores account information for clients, freelancers, and admins.
-- -----------------------------------------------------------------------------
CREATE TABLE `users` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(255) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('client', 'freelancer', 'admin') NOT NULL DEFAULT 'client',
  `avatar_url` VARCHAR(512) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Constraints & Indexes
  CONSTRAINT `uk_users_email` UNIQUE (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 2. CATEGORIES
-- Taxonomy for grouping marketplace services.
-- -----------------------------------------------------------------------------
CREATE TABLE `categories` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Constraints & Indexes
  CONSTRAINT `uk_categories_slug` UNIQUE (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 3. SERVICES
-- Gig or service listings posted by freelancers.
-- -----------------------------------------------------------------------------
CREATE TABLE `services` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `category_id` BIGINT UNSIGNED NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `description` TEXT NOT NULL,
  `price` DECIMAL(10, 2) NOT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Indexes for fast filtering by category and freelancer lookup
  INDEX `idx_services_freelancer_id` (`freelancer_id`),
  INDEX `idx_services_category_id` (`category_id`),
  INDEX `idx_services_price` (`price`),
  
  -- Foreign Key Relationships:
  -- 1. If a freelancer user is deleted, automatically delete their published services (CASCADE).
  CONSTRAINT `fk_services_freelancer` 
    FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
  -- 2. Prevent deleting a category if services are actively assigned to it (RESTRICT).
  CONSTRAINT `fk_services_category` 
    FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) 
    ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 4. SERVICE_IMAGES
-- Gallery images associated with a service listing.
-- -----------------------------------------------------------------------------
CREATE TABLE `service_images` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` BIGINT UNSIGNED NOT NULL,
  `image_path` VARCHAR(512) NOT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Indexes
  INDEX `idx_service_images_service_id` (`service_id`),
  
  -- Foreign Key Relationships:
  -- Deleting a service cascades to remove all of its associated image records.
  CONSTRAINT `fk_service_images_service` 
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 5. PROJECT_REQUESTS
-- Client inquiries and project requests tied to a service.
-- -----------------------------------------------------------------------------
CREATE TABLE `project_requests` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `service_id` BIGINT UNSIGNED DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('pending', 'accepted', 'rejected', 'in_progress', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  -- Indexes for user dashboards and status filtering
  INDEX `idx_project_requests_client_id` (`client_id`),
  INDEX `idx_project_requests_service_id` (`service_id`),
  INDEX `idx_project_requests_status` (`status`),
  
  -- Foreign Key Relationships:
  -- 1. Deleting a client user removes their project requests (CASCADE).
  CONSTRAINT `fk_project_requests_client` 
    FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
  -- 2. If a service listing is deleted, preserve historical project request records by setting service_id to NULL (SET NULL).
  CONSTRAINT `fk_project_requests_service` 
    FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 6. MESSAGES
-- Direct messages between users, optionally tied to a project request.
-- -----------------------------------------------------------------------------
CREATE TABLE `messages` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `sender_id` BIGINT UNSIGNED NOT NULL,
  `receiver_id` BIGINT UNSIGNED NOT NULL,
  `project_request_id` BIGINT UNSIGNED DEFAULT NULL,
  `body` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Indexes for inbox threads, unread badge counters, and project conversations
  INDEX `idx_messages_sender_id` (`sender_id`),
  INDEX `idx_messages_receiver_id` (`receiver_id`),
  INDEX `idx_messages_project_request_id` (`project_request_id`),
  INDEX `idx_messages_unread` (`receiver_id`, `is_read`),
  
  -- Foreign Key Relationships:
  -- 1. Deleting sender or receiver user cascades and cleans up message threads (CASCADE).
  CONSTRAINT `fk_messages_sender` 
    FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_messages_receiver` 
    FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
  -- 2. If a project request is deleted, retain conversation history by setting project_request_id to NULL (SET NULL).
  CONSTRAINT `fk_messages_project_request` 
    FOREIGN KEY (`project_request_id`) REFERENCES `project_requests` (`id`) 
    ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 7. REVIEWS
-- Feedback and 1-5 star ratings given by clients for completed project requests.
-- -----------------------------------------------------------------------------
CREATE TABLE `reviews` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `project_request_id` BIGINT UNSIGNED NOT NULL,
  `client_id` BIGINT UNSIGNED NOT NULL,
  `freelancer_id` BIGINT UNSIGNED NOT NULL,
  `rating` TINYINT UNSIGNED NOT NULL,
  `comment` TEXT DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Constraints & Indexes
  CONSTRAINT `uk_reviews_project_request` UNIQUE (`project_request_id`),
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
  INDEX `idx_reviews_client_id` (`client_id`),
  INDEX `idx_reviews_freelancer_id` (`freelancer_id`),
  INDEX `idx_reviews_rating` (`rating`),
  
  -- Foreign Key Relationships:
  -- 1. Deleting a project request removes the review (CASCADE).
  CONSTRAINT `fk_reviews_project_request` 
    FOREIGN KEY (`project_request_id`) REFERENCES `project_requests` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
    
  -- 2. Deleting either client or freelancer cascades to delete review history (CASCADE).
  CONSTRAINT `fk_reviews_client` 
    FOREIGN KEY (`client_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_freelancer` 
    FOREIGN KEY (`freelancer_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- -----------------------------------------------------------------------------
-- 8. NOTIFICATIONS
-- In-app notifications sent to users with structured JSON payloads.
-- -----------------------------------------------------------------------------
CREATE TABLE `notifications` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `type` VARCHAR(50) NOT NULL,
  `payload` JSON NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  
  -- Indexes for retrieving user notifications and unread counts
  INDEX `idx_notifications_user_unread` (`user_id`, `is_read`, `created_at`),
  
  -- Foreign Key Relationships:
  -- Deleting a user removes all their notification records (CASCADE).
  CONSTRAINT `fk_notifications_user` 
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) 
    ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
