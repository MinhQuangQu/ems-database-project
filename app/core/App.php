<?php
class App
{
    protected string $controllerName = 'AuthController';
    protected object $controller;  // avoid dynamic property
    protected string $method = 'login';
    protected array $params = [];
    protected array $publicRoutes = [
        'auth' => ['login', 'register', 'logout']
    ];

    public function __construct()
    {
        $url = $this->parseUrl();

        // 1️⃣ Controller
        if (!empty($url[0])) {
            $candidate = ucfirst($url[0]) . 'Controller';
            if (file_exists("../app/controllers/$candidate.php")) {
                $this->controllerName = $candidate;
                unset($url[0]);
            }
        }

        require_once "../app/controllers/{$this->controllerName}.php";
        $this->controller = new $this->controllerName();

        // 2️⃣ Method
        if (!empty($url[1]) && method_exists($this->controller, $url[1])) {
            $this->method = $url[1];
            unset($url[1]);
        } else {
            $this->method = $this->controllerName === 'AuthController' ? 'login' : 'index';
        }

        // 3️⃣ Params
        $this->params = $url ? array_values($url) : [];

        // 4️⃣ Auth check
        if (!$this->isPublicRoute()) {
            if (empty($_SESSION['user'])) {
                header("Location: " . BASE_URL . "/auth/login");
                exit;
            }
        }

        // 5️⃣ Execute controller
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    private function parseUrl(): array
    {
        if (!isset($_GET['url'])) return [];
        $url = rtrim($_GET['url'], '/');
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return explode('/', $url);
    }

    private function isPublicRoute(): bool
    {
        $controller = strtolower(str_replace('Controller', '', $this->controllerName));
        $method = strtolower($this->method);
        return isset($this->publicRoutes[$controller]) && in_array($method, $this->publicRoutes[$controller]);
    }
}
