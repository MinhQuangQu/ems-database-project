<?php
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/EmployeeController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
;

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Department routes
if ($path === '/department') {
    $controller = new DepartmentController();
    $controller->index();

} elseif ($path === '/department/create') {
    $controller = new DepartmentController();
    $controller->create();

} elseif (preg_match('#^/department/edit/(\d+)$#', $path, $matches)) {
    $controller = new DepartmentController();
    $controller->edit($matches[1]);

} elseif (preg_match('#^/department/delete/(\d+)$#', $path, $matches)) {
    $controller = new DepartmentController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->delete($matches[1]);
    }

// Employee routes
} elseif ($path === '/employee') {
    $controller = new EmployeeController();
    $controller->index();

} elseif ($path === '/employee/create') {
    $controller = new EmployeeController();
    $controller->create();

// Auth routes
} elseif ($path === '/auth/login') {
    $controller = new AuthController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->authenticate();
    } else {
        $controller->login();
    }

} elseif ($path === '/auth/register') {
    $controller = new AuthController();
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller->store();
    } else {
        $controller->register();
    }

} elseif ($path === '/auth/logout') {
    $controller = new AuthController();
    $controller->logout();

} else {
    http_response_code(404);
    echo "404 Not Found";
}
