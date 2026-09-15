# Tandem - Premium Freelance Network Platform (MVC)

Tandem is a modern freelance service platform structured according to Model-View-Controller (MVC) software design principles.

---

## Folder Structure & Purpose

```text
Tandem/
├── app/
│   ├── Controllers/       # Handles HTTP requests, orchestrates Models and Views
│   │   ├── AuthController.php        # Authentication (Login, Register, Logout)
│   │   ├── DashboardController.php   # Role-specific SaaS Dashboards (Client, Freelancer, Admin)
│   │   ├── HomeController.php        # Homepage & Featured Listings
│   │   ├── MessageController.php     # Project Requests & Contact Messages
│   │   └── ServiceController.php     # Service CRUD Operations (Directory, Details, Create, Edit, Delete)
│   ├── Helpers/           # Application helper functions
│   │   └── view.php                  # Safe view render($view, $data) helper
│   ├── Models/            # Domain Entities, OOP Hierarchy & PDO Repositories
│   │   ├── User.php                  # Abstract User Base Class
│   │   ├── Client.php                # Client User Subclass (Role-specific queries)
│   │   ├── Freelancer.php            # Freelancer User Subclass (Role-specific queries)
│   │   ├── Admin.php                 # Admin User Subclass (Platform stats & directory)
│   │   ├── UserFactory.php           # Polymorphic User Factory & User DB Queries
│   │   ├── Service.php               # Service CRUD & Search Repository
│   │   ├── Category.php              # Service Categories Repository
│   │   ├── ProjectRequest.php        # Client-Freelancer Project Requests
│   │   ├── Message.php               # System Messaging Repository
│   │   └── Review.php                # Service Rating & Review Repository
│   └── Views/             # Feature-organized plain PHP template views
│       ├── auth/                     # login.php, register.php
│       ├── dashboard/                # client.php, freelancer.php, admin.php
│       ├── errors/                   # 404.php
│       ├── home/                     # index.php
│       ├── layouts/                  # header.php, footer.php
│       ├── messages/                 # contact.php
│       └── services/                 # index.php, details.php, create.php, edit.php, delete.php
├── config/                # Environment & Database Configuration
│   └── database.php                  # PDO Credentials & Connection Settings
├── includes/              # Core Infrastructure & Session Helpers
│   ├── auth.php                      # Session Guard & Flash Banners
│   └── Database.php                  # PDO Singleton Connection Manager
├── public/                # Document Root & Web Assets (Publicly accessible)
│   ├── assets/                       # CSS, Images, Fonts
│   │   └── css/style-guide.css
│   └── index.php                     # Single Entry Point for all HTTP Requests
├── routes/                # Route Definitions & Dispatching
│   └── web.php                       # Lightweight HTTP Router & Route Table
├── .env.example           # Example Environment Variables Configuration
├── schema.sql             # MySQL DDL Database Schema
├── seed.sql               # MySQL Sample Data Seed Script
└── README.md              # Project Architecture Documentation
```

---

## Architectural Summary

1. **Single Entry Point (`public/index.php`)**:
   - All HTTP requests enter through `public/index.php`.
   - Bootstraps session management, database singleton initialization, core helpers, and model/controller files.
   - Forwards request execution to `routes/web.php`.

2. **Router & Web Routes (`routes/web.php`)**:
   - Maps HTTP methods and URI paths (e.g. `GET /services`, `POST /login`, `GET /dashboard/freelancer`) to controller classes and action methods.
   - Returns a 404 error page if a path is not matched.

3. **Controllers (`app/Controllers/`)**:
   - Receives client request data, verifies user authentication and authorization rules, interacts with models, and passes data to views.
   - Controllers never perform direct SQL string concatenations or render raw HTML.

4. **Models (`app/Models/`)**:
   - Contains object-oriented domain entities (abstract `User`, `Client`, `Freelancer`, `Admin`) and database models utilizing PDO prepared statements for database operations.
   - Leverages `UserFactory::createUserFromRow()` to instantiate the correct polymorphic user subclass based on role.

5. **Views (`app/Views/`)**:
   - Contains feature-grouped PHP template files (`auth`, `services`, `dashboard`, `messages`, `home`).
   - Standardized view rendering via `render($view, $data)` in `app/Helpers/view.php`, extracting data into local template variable scope safely.

---

## Local Development Server Setup

To run the application locally:

```bash
php -S localhost:8000 -t public public/index.php
```

Then visit `http://localhost:8000` in your web browser.
