-- =============================================================================
-- Tandem Database Seed Data
-- Passwords for all users: Password123!
-- Bcrypt Hash: $2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu
-- =============================================================================

USE `tandem_db`;

-- Disable foreign key checks for safe clean re-insertion
SET FOREIGN_KEY_CHECKS = 0;

TRUNCATE TABLE `notifications`;
TRUNCATE TABLE `reviews`;
TRUNCATE TABLE `messages`;
TRUNCATE TABLE `project_requests`;
TRUNCATE TABLE `service_images`;
TRUNCATE TABLE `services`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `users`;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- 1. USERS (2 Clients, 3 Freelancers, 1 Admin)
-- -----------------------------------------------------------------------------
INSERT INTO `users` (`id`, `name`, `email`, `password_hash`, `role`, `avatar_url`, `created_at`) VALUES
(1, 'Sarah Jenkins', 'sarah.j@acmelabs.io', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'client', 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?w=150', '2026-01-10 09:15:00'),
(2, 'Marcus Vance', 'marcus@vancemedia.co', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'client', 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=150', '2026-01-14 11:30:00'),
(3, 'Elena Rostova', 'elena@rostovadesign.com', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'freelancer', 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=150', '2026-01-05 14:20:00'),
(4, 'David Chen', 'david.chen@devstudio.io', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'freelancer', 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=150', '2026-01-07 16:45:00'),
(5, 'Aaliyah Khan', 'aaliyah@wordsmith.design', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'freelancer', 'https://images.unsplash.com/photo-1517841905240-472988babdf9?w=150', '2026-01-08 10:00:00'),
(6, 'Tandem Admin', 'admin@tandem.network', '$2y$10$yq.AbY1CW/e6AvlaL2ONb.kuORzr6xfTu4k/vojaXCcw/QGUzsJqu', 'admin', 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=150', '2026-01-01 08:00:00');

-- -----------------------------------------------------------------------------
-- 2. CATEGORIES (4 Categories)
-- -----------------------------------------------------------------------------
INSERT INTO `categories` (`id`, `name`, `slug`, `created_at`) VALUES
(1, 'Web & Software Development', 'web-development', '2026-01-01 08:00:00'),
(2, 'UI/UX & Product Design', 'ui-ux-design', '2026-01-01 08:00:00'),
(3, 'Brand Design & Identity', 'brand-design', '2026-01-01 08:00:00'),
(4, 'Content & Copywriting', 'content-writing', '2026-01-01 08:00:00');

-- -----------------------------------------------------------------------------
-- 3. SERVICES (10 Services)
-- -----------------------------------------------------------------------------
INSERT INTO `services` (`id`, `freelancer_id`, `category_id`, `title`, `description`, `price`, `created_at`) VALUES
(1, 4, 1, 'Full-Stack Next.js & React Web Application', 'Custom enterprise web application built with Next.js 14, TypeScript, Tailwind CSS, and optimized database architecture.', 2500.00, '2026-01-12 10:00:00'),
(2, 4, 1, 'Custom PHP & Laravel Backend REST API Development', 'Robust, scalable PHP API endpoints with JWT authentication, rate limiting, automated testing, and OpenAPI documentation.', 1800.00, '2026-01-15 14:30:00'),
(3, 4, 1, 'Web Performance & Core Web Vitals Optimization', 'Comprehensive audit and code refactoring to boost Google Lighthouse scores to 95+ and reduce LCP/CLS metrics.', 750.00, '2026-01-18 09:00:00'),
(4, 3, 2, 'Mobile & Web App UI/UX Design System in Figma', 'Complete atomic design system including responsive component libraries, typography scale, color tokens, and micro-interactions.', 2200.00, '2026-01-10 11:15:00'),
(5, 3, 2, 'SaaS Dashboard UX Audit & Wireframing', 'Heuristic evaluation of existing user flows paired with high-fidelity interactive wireframes for high-conversion SaaS portals.', 1200.00, '2026-01-14 15:45:00'),
(6, 3, 3, 'Complete Brand Identity & Visual Guidelines', 'End-to-end branding package featuring primary/secondary logo marks, typography pairings, color palette, and brand book.', 1950.00, '2026-01-16 13:20:00'),
(7, 5, 4, 'Conversion Copywriting for Landing Pages & Product Launch', 'High-converting sales copy crafted using proven psychological frameworks to double landing page opt-ins and product conversions.', 950.00, '2026-01-11 16:00:00'),
(8, 5, 4, 'Technical Documentation & API Guide Writing', 'Clear, developer-friendly API reference manuals, integration guides, and developer portal documentation.', 1400.00, '2026-01-17 10:30:00'),
(9, 5, 4, 'Brand Voice Strategy & Tagline Messaging Kit', 'Comprehensive brand positioning guide defining tone of voice, key value pillars, target personas, and punchy tagline options.', 850.00, '2026-01-19 12:00:00'),
(10, 3, 3, 'Vector Logo Design & Minimalist Icon Set', 'Custom vector logo suite delivered in SVG/PNG/EPS formats alongside a custom 24-icon set tailored to your product branding.', 650.00, '2026-01-20 17:10:00');

-- -----------------------------------------------------------------------------
-- 4. SERVICE IMAGES
-- -----------------------------------------------------------------------------
INSERT INTO `service_images` (`id`, `service_id`, `image_path`, `sort_order`, `created_at`) VALUES
(1, 1, 'assets/images/services/nextjs-app-hero.jpg', 1, '2026-01-12 10:05:00'),
(2, 1, 'assets/images/services/nextjs-app-dashboard.jpg', 2, '2026-01-12 10:05:00'),
(3, 4, 'assets/images/services/figma-design-system.jpg', 1, '2026-01-10 11:20:00'),
(4, 6, 'assets/images/services/brand-identity-mockup.jpg', 1, '2026-01-16 13:25:00'),
(5, 7, 'assets/images/services/copywriting-hero.jpg', 1, '2026-01-11 16:05:00');

-- -----------------------------------------------------------------------------
-- 5. PROJECT REQUESTS (5 Requests in Various Statuses)
-- -----------------------------------------------------------------------------
INSERT INTO `project_requests` (`id`, `client_id`, `service_id`, `message`, `status`, `created_at`) VALUES
(1, 1, 1, 'Hi David, we are launching an AI analytics SaaS and need a full-stack Next.js dashboard built by next month. Looking forward to connecting!', 'completed', '2026-01-20 09:00:00'),
(2, 1, 4, 'Hi Elena, our mobile app needs a complete redesign in Figma ahead of our Series A funding round. Let us know your availability!', 'in_progress', '2026-01-22 14:15:00'),
(3, 2, 7, 'Hey Aaliyah, Vance Media is launching a new video production service line. We need compelling landing page copy that drives enterprise leads.', 'completed', '2026-01-24 11:00:00'),
(4, 2, 2, 'David, we need a custom Laravel REST API to sync inventory between Shopify and our warehouse database. Can you review our spec?', 'completed', '2026-01-25 16:30:00'),
(5, 2, 5, 'Hi Elena, requesting an audit for our media portal dashboard. We want to streamline the onboarding experience for creators.', 'completed', '2026-01-26 10:20:00');

-- -----------------------------------------------------------------------------
-- 6. MESSAGES (8 Messages across 2 Conversations)
-- Conversation A: Request 1 (Sarah & David)
-- Conversation B: Request 2 (Sarah & Elena)
-- -----------------------------------------------------------------------------
INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `project_request_id`, `body`, `is_read`, `created_at`) VALUES
-- Conversation A (Sarah & David - Request 1)
(1, 1, 4, 1, 'Hi David! We loved your portfolio. Are you open for a fast-turnaround Next.js analytics dashboard?', 1, '2026-01-20 09:05:00'),
(2, 4, 1, 1, 'Hi Sarah! Thanks for reaching out. Yes, I have bandwidth available starting this Thursday. Could you share the wireframes?', 1, '2026-01-20 09:30:00'),
(3, 1, 4, 1, 'Sent over email! We just need the primary chart modules, authentication, and team permissions set up.', 1, '2026-01-20 09:45:00'),
(4, 4, 1, 1, 'Looks great. Scope is clear. I have accepted the project request and started the repository setup.', 1, '2026-01-20 10:15:00'),

-- Conversation B (Sarah & Elena - Request 2)
(5, 1, 3, 2, 'Hi Elena, we need a comprehensive Figma design system for our iOS & Android mobile applications.', 1, '2026-01-22 14:20:00'),
(6, 3, 1, 2, 'Hello Sarah! I would love to work on this. I specialize in scalable atomic design systems in Figma.', 1, '2026-01-22 15:00:00'),
(7, 1, 3, 2, 'Awesome! What is your typical turnaround time for component variants and dark mode tokens?', 1, '2026-01-22 15:30:00'),
(8, 3, 1, 2, 'I can deliver the complete core system in about 10 business days. I will upload preliminary wireframes this Friday!', 0, '2026-01-22 16:10:00');

-- -----------------------------------------------------------------------------
-- 7. REVIEWS (4 Reviews for Completed Project Requests)
-- -----------------------------------------------------------------------------
INSERT INTO `reviews` (`id`, `project_request_id`, `client_id`, `freelancer_id`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 1, 4, 5, 'David executed our Next.js dashboard flawlessly ahead of schedule. Outstanding code quality, responsive communication, and spotless documentation!', '2026-02-05 16:00:00'),
(2, 3, 2, 5, 5, 'Aaliyah is a copywriting genius! The new landing page messaging doubled our lead conversion rate within 48 hours of going live.', '2026-02-10 11:30:00'),
(3, 4, 2, 4, 5, 'Clean, robust Laravel API build. David integrated complex sync logic seamlessly and provided thorough Postman collections.', '2026-02-14 09:45:00'),
(4, 5, 2, 3, 4, 'Elena provided extremely insightful UX wireframes that pinpointed friction in our onboarding flow. Highly recommended designer!', '2026-02-18 14:20:00');

-- -----------------------------------------------------------------------------
-- 8. NOTIFICATIONS (3 Notifications)
-- -----------------------------------------------------------------------------
INSERT INTO `notifications` (`id`, `user_id`, `type`, `payload`, `is_read`, `created_at`) VALUES
(1, 4, 'project_request_received', '{"request_id": 4, "client_name": "Marcus Vance", "service_title": "Custom PHP & Laravel Backend REST API Development"}', 0, '2026-01-25 16:30:00'),
(2, 1, 'project_status_updated', '{"request_id": 2, "freelancer_name": "Elena Rostova", "status": "in_progress"}', 1, '2026-01-22 15:05:00'),
(3, 3, 'new_message', '{"sender_id": 1, "sender_name": "Sarah Jenkins", "preview": "Awesome! What is your typical turnaround time for component variants..."}', 0, '2026-01-22 15:30:00');
