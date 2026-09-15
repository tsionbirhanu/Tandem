<?php
// app/Controllers/HomeController.php

namespace App\Controllers;

use Database;
use App\Models\Service;
use Exception;

class HomeController {

    public function index(): void {
        $featuredServices = [];

        try {
            $pdo = Database::getConnection();
            $serviceModel = new Service($pdo);
            $featuredServices = $serviceModel->featured(3);
        } catch (Exception $e) {
            $featuredServices = [];
        }

        render('home/index', [
            'featuredServices' => $featuredServices,
        ]);
    }
}
