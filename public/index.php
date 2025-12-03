<?php
declare(strict_types=1);

use Dotenv\Dotenv;

$rootPath = dirname(__DIR__);

require_once $rootPath . '/vendor/autoload.php';

if (class_exists(Dotenv::class) && file_exists($rootPath . '/.env')) {
    Dotenv::createImmutable($rootPath)->safeLoad();
}

$config = require $rootPath . '/app/config/config.php';

date_default_timezone_set($config['timezone'] ?? 'UTC');
ini_set('display_errors', !empty($config['debug']) ? '1' : '0');
error_reporting(E_ALL);

if (!defined('BASE_PATH')) define('BASE_PATH', $rootPath);
if (!defined('BASE_URL'))  define('BASE_URL', $config['base_url'] ?? '/');

// ======================
// SESSION – CHUẨN
// ======================
if (!empty($config['session_name'])) session_name($config['session_name']);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => $config['session_lifetime'] ?? 0,
        'path'     => parse_url(BASE_URL, PHP_URL_PATH) ?: '/', // ✔ fix path
        'secure'   => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

// ======================
// Autoload core + helpers
// ======================
require_once $rootPath . '/app/core/Model.php';
require_once $rootPath . '/app/core/Controller.php';

spl_autoload_register(function (string $class) use ($rootPath) {
    $dirs = [
        '/app/core/',
        '/app/controllers/',
        '/app/models/',
        '/app/services/',
        '/app/middleware/',
        '/app/validators/',
    ];
    foreach ($dirs as $dir) {
        $file = $rootPath . $dir . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

foreach (glob($rootPath . '/app/helpers/*.php') ?: [] as $helper) {
    require_once $helper;
}

// ======================
// URL routing
// ======================
$requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$basePath   = rtrim(BASE_URL, '/');
if ($basePath !== '' && strpos($requestUri, $basePath) === 0) {
    $requestUri = substr($requestUri, strlen($basePath));
}
$requestUri = trim($requestUri, '/');
if ($requestUri === '') $requestUri = 'dashboard/index';
$_GET['url'] = $requestUri;

// ======================
// Run App
// ======================
require_once $rootPath . '/app/core/App.php';
new App();
