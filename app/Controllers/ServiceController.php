<?php
// app/Controllers/ServiceController.php

namespace App\Controllers;

use Database;
use App\Models\Service;
use App\Models\Category;
use App\Models\Review;
use Exception;

class ServiceController {

    public function index(): void {
        $search = trim($_GET['search'] ?? '');
        $selectedCategory = trim($_GET['category'] ?? '');

        $services = [];
        $categories = [];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $categoryModel = new Category($pdo);

            $categories = $categoryModel->all();

            if (!empty($search)) {
                $services = $serviceModel->search($search);
            } elseif (!empty($selectedCategory)) {
                $services = $serviceModel->findByCategorySlug($selectedCategory);
            } else {
                $services = $serviceModel->all();
            }
        } catch (Exception $e) {
            $dbError = "Database Connection Error: Unable to retrieve services data. " . $e->getMessage();
        }

        render('services/index', [
            'services'         => $services,
            'categories'       => $categories,
            'search'           => $search,
            'selectedCategory' => $selectedCategory,
            'dbError'          => $dbError,
        ]);
    }

    public function show(): void {
        $id = (int)($_GET['id'] ?? 0);

        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $service = null;
        $reviews = [];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $reviewModel  = new Review($pdo);

            $service = $serviceModel->find($id);

            if ($service) {
                $reviews = $reviewModel->getByServiceId($id);
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch service details. " . $e->getMessage();
        }

        if (!$service && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/service/details?id={$id}"]);
            return;
        }

        render('services/details', [
            'service' => $service,
            'reviews' => $reviews,
            'dbError' => $dbError,
        ]);
    }

    public function showCreate(): void {
        requireRole(['freelancer', 'admin']);

        $categories = [];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $categoryModel = new Category($pdo);
            $categories = $categoryModel->all();
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to load categories. " . $e->getMessage();
        }

        render('services/create', [
            'categories' => $categories,
            'title'      => '',
            'categoryId' => 0,
            'price'      => '',
            'summary'    => '',
            'errors'     => [],
            'dbError'    => $dbError,
        ]);
    }

    public function create(): void {
        requireRole(['freelancer', 'admin']);

        $title      = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price      = trim($_POST['price'] ?? '');
        $summary    = trim($_POST['summary'] ?? '');
        $errors     = [];
        $categories = [];
        $dbError    = null;

        if (empty($title)) {
            $errors['title'] = "Service title is required.";
        }
        if ($categoryId <= 0) {
            $errors['category_id'] = "Please select a category.";
        }
        if (empty($price) || !is_numeric($price) || (float)$price <= 0) {
            $errors['price'] = "Please enter a valid price greater than zero.";
        }
        if (empty($summary)) {
            $errors['summary'] = "Service summary is required.";
        }

        try {
            $pdo = Database::getConnection();
            $categoryModel = new Category($pdo);
            $categories = $categoryModel->all();

            if (empty($errors)) {
                $serviceModel = new Service($pdo);
                $serviceId = $serviceModel->create([
                    'title'         => $title,
                    'category_id'   => $categoryId,
                    'price'         => (float)$price,
                    'summary'       => $summary,
                    'freelancer_id' => $_SESSION['user_id'] ?? 1,
                ]);

                setFlash('success', 'Service created successfully!');
                header("Location: /service/details?id={$serviceId}");
                exit;
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to save service. " . $e->getMessage();
        }

        render('services/create', [
            'categories' => $categories,
            'title'      => $title,
            'categoryId' => $categoryId,
            'price'      => $price,
            'summary'    => $summary,
            'errors'     => $errors,
            'dbError'    => $dbError,
        ]);
    }

    public function showEdit(): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $categories = [];
        $service = null;
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $categoryModel = new Category($pdo);

            $service = $serviceModel->find($id);
            $categories = $categoryModel->all();
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch service for editing. " . $e->getMessage();
        }

        if (!$service && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/service/edit?id={$id}"]);
            return;
        }

        render('services/edit', [
            'service'    => $service,
            'categories' => $categories,
            'title'      => $service['title'] ?? '',
            'categoryId' => (int)($service['category_id'] ?? 0),
            'price'      => $service['price'] ?? '',
            'summary'    => $service['summary'] ?? '',
            'errors'     => [],
            'dbError'    => $dbError,
        ]);
    }

    public function edit(): void {
        requireRole(['freelancer', 'admin']);

        $id         = (int)($_POST['id'] ?? 0);
        $title      = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price      = trim($_POST['price'] ?? '');
        $summary    = trim($_POST['summary'] ?? '');
        $errors     = [];
        $categories = [];
        $service    = null;
        $dbError    = null;

        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        if (empty($title)) {
            $errors['title'] = "Service title is required.";
        }
        if ($categoryId <= 0) {
            $errors['category_id'] = "Please select a category.";
        }
        if (empty($price) || !is_numeric($price) || (float)$price <= 0) {
            $errors['price'] = "Please enter a valid price greater than zero.";
        }
        if (empty($summary)) {
            $errors['summary'] = "Service summary is required.";
        }

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $categoryModel = new Category($pdo);

            $categories = $categoryModel->all();
            $service = $serviceModel->find($id);

            if (empty($errors)) {
                $serviceModel->update($id, [
                    'title'       => $title,
                    'category_id' => $categoryId,
                    'price'       => (float)$price,
                    'summary'     => $summary,
                ]);

                setFlash('success', 'Service updated successfully!');
                header("Location: /service/details?id={$id}");
                exit;
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to update service. " . $e->getMessage();
        }

        render('services/edit', [
            'service'    => $service,
            'categories' => $categories,
            'title'      => $title,
            'categoryId' => $categoryId,
            'price'      => $price,
            'summary'    => $summary,
            'errors'     => $errors,
            'dbError'    => $dbError,
        ]);
    }

    public function showDelete(): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $service = null;
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $service = $serviceModel->find($id);
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch service for deletion. " . $e->getMessage();
        }

        if (!$service && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/service/delete?id={$id}"]);
            return;
        }

        render('services/delete', [
            'service' => $service,
            'dbError' => $dbError,
        ]);
    }

    public function delete(): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)($_POST['id'] ?? 0);
        $confirm = $_POST['confirm'] ?? '';

        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        if ($confirm !== 'yes') {
            setFlash('error', 'Deletion cancelled.');
            header("Location: /service/details?id={$id}");
            exit;
        }

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $serviceModel->delete($id);

            setFlash('success', 'Service deleted successfully.');
            header('Location: /services');
            exit;
        } catch (Exception $e) {
            setFlash('error', 'Failed to delete service: ' . $e->getMessage());
            header("Location: /service/details?id={$id}");
            exit;
        }
    }
}
