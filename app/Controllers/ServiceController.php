<?php
// app/Controllers/ServiceController.php

namespace App\Controllers;

use Database;
use App\Models\Service;
use App\Models\ServiceImage;
use App\Models\Category;
use App\Models\Review;
use Exception;

class ServiceController {

    public function index(): void {
        $search          = trim($_GET['search'] ?? '');
        $selectedCategory= trim($_GET['category'] ?? '');
        $minPrice        = (isset($_GET['min_price']) && is_numeric($_GET['min_price'])) ? (float)$_GET['min_price'] : null;
        $maxPrice        = (isset($_GET['max_price']) && is_numeric($_GET['max_price'])) ? (float)$_GET['max_price'] : null;
        $minRating       = (isset($_GET['min_rating']) && is_numeric($_GET['min_rating'])) ? (float)$_GET['min_rating'] : null;
        $sort            = trim($_GET['sort'] ?? 'newest');
        $page            = max(1, (int)($_GET['page'] ?? 1));
        $perPage         = 12;

        $categories = [];
        $result = [
            'services'   => [],
            'total'      => 0,
            'page'       => 1,
            'totalPages' => 1,
            'perPage'    => $perPage,
        ];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $categoryModel = new Category($pdo);

            $categories = $categoryModel->all();
            $result = $serviceModel->filter([
                'search'     => $search,
                'category'   => $selectedCategory,
                'min_price'  => $minPrice,
                'max_price'  => $maxPrice,
                'min_rating' => $minRating,
                'sort'       => $sort,
                'page'       => $page,
                'per_page'   => $perPage,
            ]);
        } catch (Exception $e) {
            $dbError = "Database Connection Error: Unable to retrieve services data. " . $e->getMessage();
        }

        // Build active filter chips for UI
        $activeChips = [];
        $queryParams = $_GET;

        if (!empty($search)) {
            $paramsWithoutSearch = $queryParams;
            unset($paramsWithoutSearch['search'], $paramsWithoutSearch['page']);
            $activeChips[] = [
                'key'        => 'search',
                'label'      => 'Search: "' . htmlspecialchars($search, ENT_QUOTES, 'UTF-8') . '"',
                'remove_url' => '/services?' . http_build_query($paramsWithoutSearch),
            ];
        }

        if (!empty($selectedCategory)) {
            $categoryName = $selectedCategory;
            foreach ($categories as $cat) {
                if ($cat['slug'] === $selectedCategory) {
                    $categoryName = $cat['name'];
                    break;
                }
            }
            $paramsWithoutCategory = $queryParams;
            unset($paramsWithoutCategory['category'], $paramsWithoutCategory['page']);
            $activeChips[] = [
                'key'        => 'category',
                'label'      => 'Category: ' . htmlspecialchars($categoryName, ENT_QUOTES, 'UTF-8'),
                'remove_url' => '/services?' . http_build_query($paramsWithoutCategory),
            ];
        }

        if ($minPrice !== null || $maxPrice !== null) {
            $priceLabel = 'Price: ';
            if ($minPrice !== null && $maxPrice !== null) {
                $priceLabel .= '$' . number_format($minPrice, 2) . ' - $' . number_format($maxPrice, 2);
            } elseif ($minPrice !== null) {
                $priceLabel .= 'Min $' . number_format($minPrice, 2);
            } else {
                $priceLabel .= 'Max $' . number_format($maxPrice, 2);
            }
            $paramsWithoutPrice = $queryParams;
            unset($paramsWithoutPrice['min_price'], $paramsWithoutPrice['max_price'], $paramsWithoutPrice['page']);
            $activeChips[] = [
                'key'        => 'price',
                'label'      => htmlspecialchars($priceLabel, ENT_QUOTES, 'UTF-8'),
                'remove_url' => '/services?' . http_build_query($paramsWithoutPrice),
            ];
        }

        if ($minRating !== null && $minRating > 0) {
            $paramsWithoutRating = $queryParams;
            unset($paramsWithoutRating['min_rating'], $paramsWithoutRating['page']);
            $activeChips[] = [
                'key'        => 'min_rating',
                'label'      => 'Rating: ' . number_format($minRating, 1) . '+ ★',
                'remove_url' => '/services?' . http_build_query($paramsWithoutRating),
            ];
        }

        // Helper function to build page link retaining current GET params
        $buildPageUrl = function(int $pageNum) use ($queryParams): string {
            $params = $queryParams;
            $params['page'] = $pageNum;
            return '/services?' . http_build_query($params);
        };

        render('services/index', [
            'services'         => $result['services'],
            'total'            => $result['total'],
            'page'             => $result['page'],
            'totalPages'       => $result['totalPages'],
            'perPage'          => $result['perPage'],
            'categories'       => $categories,
            'search'           => $search,
            'selectedCategory' => $selectedCategory,
            'minPrice'         => $minPrice,
            'maxPrice'         => $maxPrice,
            'minRating'        => $minRating,
            'sort'             => $sort,
            'activeChips'      => $activeChips,
            'buildPageUrl'     => $buildPageUrl,
            'dbError'          => $dbError,
        ]);
    }

    public function show(int|string $id): void {
        $id = (int)$id;

        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $service = null;
        $reviews = [];
        $galleryImages = [];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $reviewModel  = new Review($pdo);
            $imageModel   = new ServiceImage($pdo);

            $service = $serviceModel->find($id);

            if ($service) {
                $reviews       = $reviewModel->getByServiceId($id);
                $galleryImages = $imageModel->getByServiceId($id);
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch service details. " . $e->getMessage();
        }

        if (!$service && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/services/{$id}"]);
            return;
        }

        render('services/details', [
            'service'       => $service,
            'reviews'       => $reviews,
            'galleryImages' => $galleryImages,
            'dbError'       => $dbError,
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

        // Validate Multi-Image Uploads (max 5)
        $uploadedImageFiles = [];
        if (isset($_FILES['service_images']) && is_array($_FILES['service_images']['name'])) {
            $fileCount = count(array_filter($_FILES['service_images']['name']));
            if ($fileCount > 5) {
                $errors['service_images'] = "You can upload a maximum of 5 service gallery images.";
            } else {
                for ($i = 0; $i < count($_FILES['service_images']['name']); $i++) {
                    if ($_FILES['service_images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                        continue;
                    }
                    $singleFile = [
                        'name'     => $_FILES['service_images']['name'][$i],
                        'type'     => $_FILES['service_images']['type'][$i],
                        'tmp_name' => $_FILES['service_images']['tmp_name'][$i],
                        'error'    => $_FILES['service_images']['error'][$i],
                        'size'     => $_FILES['service_images']['size'][$i],
                    ];
                    $uploadResult = uploadImageFile($singleFile, 'services');
                    if ($uploadResult['success']) {
                        $uploadedImageFiles[] = '/' . ltrim($uploadResult['path'], '/');
                    } else {
                        $errors['service_images'] = "Image #" . ($i + 1) . " error: " . $uploadResult['error'];
                        break;
                    }
                }
            }
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

                // Save uploaded gallery images
                $imageModel = new ServiceImage($pdo);
                foreach ($uploadedImageFiles as $sortOrder => $imagePath) {
                    $imageModel->create($serviceId, $imagePath, $sortOrder + 1);
                }

                setFlash('success', 'Service created successfully with gallery images!');
                header("Location: /services/{$serviceId}");
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

    public function showEdit(int|string $id): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)$id;
        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        $categories = [];
        $service = null;
        $galleryImages = [];
        $dbError = null;

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $categoryModel = new Category($pdo);
            $imageModel = new ServiceImage($pdo);

            $service       = $serviceModel->find($id);
            $categories    = $categoryModel->all();
            $galleryImages = $imageModel->getByServiceId($id);
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to fetch service for editing. " . $e->getMessage();
        }

        if (!$service && !$dbError) {
            http_response_code(404);
            render('errors/404', ['path' => "/services/{$id}/edit"]);
            return;
        }

        render('services/edit', [
            'service'       => $service,
            'categories'    => $categories,
            'galleryImages' => $galleryImages,
            'title'         => $service['title'] ?? '',
            'categoryId'    => (int)($service['category_id'] ?? 0),
            'price'         => $service['price'] ?? '',
            'summary'       => $service['summary'] ?? '',
            'errors'        => [],
            'dbError'       => $dbError,
        ]);
    }

    public function edit(int|string $id): void {
        requireRole(['freelancer', 'admin']);

        $id         = (int)$id;
        $title      = trim($_POST['title'] ?? '');
        $categoryId = (int)($_POST['category_id'] ?? 0);
        $price      = trim($_POST['price'] ?? '');
        $summary    = trim($_POST['summary'] ?? '');
        $errors     = [];
        $categories = [];
        $service    = null;
        $galleryImages = [];
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

        // Process Image Deletions if requested
        $deleteImageIds = $_POST['delete_images'] ?? [];

        // Validate New Image Uploads
        $newImageFiles = [];
        if (isset($_FILES['service_images']) && is_array($_FILES['service_images']['name'])) {
            for ($i = 0; $i < count($_FILES['service_images']['name']); $i++) {
                if ($_FILES['service_images']['error'][$i] === UPLOAD_ERR_NO_FILE) {
                    continue;
                }
                $singleFile = [
                    'name'     => $_FILES['service_images']['name'][$i],
                    'type'     => $_FILES['service_images']['type'][$i],
                    'tmp_name' => $_FILES['service_images']['tmp_name'][$i],
                    'error'    => $_FILES['service_images']['error'][$i],
                    'size'     => $_FILES['service_images']['size'][$i],
                ];
                $uploadResult = uploadImageFile($singleFile, 'services');
                if ($uploadResult['success']) {
                    $newImageFiles[] = '/' . ltrim($uploadResult['path'], '/');
                } else {
                    $errors['service_images'] = "Image #" . ($i + 1) . " error: " . $uploadResult['error'];
                    break;
                }
            }
        }

        try {
            $pdo = Database::getConnection();
            $serviceModel  = new Service($pdo);
            $categoryModel = new Category($pdo);
            $imageModel    = new ServiceImage($pdo);

            $categories    = $categoryModel->all();
            $service       = $serviceModel->find($id);
            $galleryImages = $imageModel->getByServiceId($id);

            if (empty($errors)) {
                $serviceModel->update($id, [
                    'title'       => $title,
                    'category_id' => $categoryId,
                    'price'       => (float)$price,
                    'summary'     => $summary,
                ]);

                // Delete specified images
                if (!empty($deleteImageIds)) {
                    foreach ($deleteImageIds as $imgId) {
                        $imageModel->deleteById((int)$imgId);
                    }
                }

                // Add newly uploaded images
                foreach ($newImageFiles as $sortOrder => $imagePath) {
                    $imageModel->create($id, $imagePath, count($galleryImages) + $sortOrder + 1);
                }

                setFlash('success', 'Service updated successfully!');
                header("Location: /services/{$id}");
                exit;
            }
        } catch (Exception $e) {
            $dbError = "Database Error: Unable to update service. " . $e->getMessage();
        }

        render('services/edit', [
            'service'       => $service,
            'categories'    => $categories,
            'galleryImages' => $galleryImages,
            'title'         => $title,
            'categoryId'    => $categoryId,
            'price'         => $price,
            'summary'       => $summary,
            'errors'        => $errors,
            'dbError'       => $dbError,
        ]);
    }

    public function showDelete(int|string $id): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)$id;
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
            render('errors/404', ['path' => "/services/{$id}/delete"]);
            return;
        }

        render('services/delete', [
            'service' => $service,
            'dbError' => $dbError,
        ]);
    }

    public function delete(int|string $id): void {
        requireRole(['freelancer', 'admin']);

        $id = (int)$id;
        $confirm = $_POST['confirm'] ?? '';

        if ($id <= 0) {
            header('Location: /services');
            exit;
        }

        if ($confirm !== 'yes') {
            setFlash('error', 'Deletion cancelled.');
            header("Location: /services/{$id}");
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
            header("Location: /services/{$id}");
            exit;
        }
    }
}
