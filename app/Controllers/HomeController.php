<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

use Database;
use App\Models\Service;
use App\Models\Category;
use App\Models\Review;
use Exception;

class HomeController {

    public function index(): void {
        $featuredServices = [];
        $categories       = [];
        $latestReviews    = [];
        $serviceCount     = 0;

        try {
            $pdo = Database::getConnection();
            $serviceModel     = new Service($pdo);
            $featuredServices = $serviceModel->featured(6);
            $serviceCount     = $serviceModel->count();
            $categories       = (new Category($pdo))->allWithCounts();
            $latestReviews    = (new Review($pdo))->latest(3);
        } catch (Exception $e) {
            // The homepage still renders (with empty sections) if the database is unreachable
        }

        render('home/index', [
            'featuredServices' => $featuredServices,
            'categories'       => $categories,
            'latestReviews'    => $latestReviews,
            'serviceCount'     => $serviceCount,
        ]);
    }
}
