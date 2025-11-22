<?php
// app/core/Autoloader.php

class AutoLoader {
    public static function register() {
        spl_autoload_register(function ($className) {
            // Simple class mapping
            $classMap = [
                'Controller' => __DIR__ . '/Controller.php',
                'AuthController' => __DIR__ . '/../controllers/AuthController.php',
                'DashboardController' => __DIR__ . '/../controllers/DashboardController.php',
                'Request' => __DIR__ . '/Request.php',
                'Response' => __DIR__ . '/Response.php',
                'Session' => __DIR__ . '/Session.php',
            ];
            
            if (isset($classMap[$className])) {
                if (file_exists($classMap[$className])) {
                    require_once $classMap[$className];
                    return true;
                }
            }
            
            return false;
        });
    }
}

AutoLoader::register();