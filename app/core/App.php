<?php
// app/core/app.php

declare(strict_types=1);

class App 
{
    private static ?App $instance = null;
    private ?mysqli $connection = null;
    private array $config = [];
    private string $basePath;

    private function __construct(string $basePath = '')
    {
        $this->basePath = $basePath ?: dirname(__DIR__);
        $this->loadConfig();
        $this->initializeSession();
        $this->initializeDatabase();
        $this->setErrorHandling();
    }

    /**
     * Get singleton instance
     */
    public static function getInstance(string $basePath = ''): App
    {
        if (self::$instance === null) {
            self::$instance = new self($basePath);
        }
        return self::$instance;
    }

    /**
     * Load application configuration
     */
    private function loadConfig(): void
    {
        $configPath = $this->basePath . '/config/config.php';
        
        if (file_exists($configPath)) {
            $this->config = require $configPath;
        } else {
            // Default configuration
            $this->config = [
                'database' => [
                    'host' => 'localhost',
                    'username' => 'root',
                    'password' => '',
                    'database' => 'hr_management',
                    'port' => 3306,
                    'charset' => 'utf8mb4'
                ],
                'app' => [
                    'name' => 'HR Management System',
                    'url' => 'http://localhost/hr-system',
                    'timezone' => 'Asia/Ho_Chi_Minh',
                    'debug' => true
                ],
                'session' => [
                    'timeout' => 3600,
                    'name' => 'hr_session'
                ]
            ];
        }

        // Set timezone
        date_default_timezone_set($this->config['app']['timezone'] ?? 'UTC');
    }

    /**
     * Initialize session
     */
    private function initializeSession(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->config['session']['name'] ?? 'hr_session');
            session_set_cookie_params([
                'lifetime' => $this->config['session']['timeout'] ?? 3600,
                'path' => '/',
                'domain' => '',
                'secure' => isset($_SERVER['HTTPS']),
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
        }
    }

    /**
     * Initialize database connection
     */
    private function initializeDatabase(): void
    {
        try {
            $dbConfig = $this->config['database'];
            
            $this->connection = new mysqli(
                $dbConfig['host'],
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['database'],
                $dbConfig['port'] ?? 3306
            );

            if ($this->connection->connect_error) {
                throw new Exception("Database connection failed: " . $this->connection->connect_error);
            }

            // Set charset
            $this->connection->set_charset($dbConfig['charset'] ?? 'utf8mb4');

        } catch (Exception $e) {
            error_log("Database initialization error: " . $e->getMessage());
            
            if ($this->config['app']['debug'] ?? false) {
                die("Database connection error: " . $e->getMessage());
            } else {
                die("System temporarily unavailable. Please try again later.");
            }
        }
    }

    /**
     * Set error handling based on environment
     */
    private function setErrorHandling(): void
    {
        if ($this->config['app']['debug'] ?? false) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
        }
    }

    /**
     * Get database connection
     */
    public function getConnection(): mysqli
    {
        return $this->connection;
    }

    /**
     * Get configuration value
     */
    public function getConfig(?string $key = null)
    {
        if ($key === null) {
            return $this->config;
        }

        $keys = explode('.', $key);
        $value = $this->config;

        foreach ($keys as $k) {
            if (isset($value[$k])) {
                $value = $value[$k];
            } else {
                return null;
            }
        }

        return $value;
    }

    /**
     * Run the application
     */
    public function run(): void
    {
        try {
            $this->routeRequest();
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }

    /**
     * Route the incoming request
     */
    private function routeRequest(): void
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        
        // Remove query string
        $requestUri = strtok($requestUri, '?');
        
        // Remove script path from request URI
        if (strpos($requestUri, $scriptName) === 0) {
            $requestUri = substr($requestUri, strlen($scriptName));
        }

        // Ensure request URI starts with /
        $requestUri = '/' . ltrim($requestUri, '/');

        // Default route
        if ($requestUri === '/') {
            $requestUri = '/dashboard';
        }

        $this->dispatch($requestUri);
    }

    /**
     * Dispatch request to appropriate controller
     */
    private function dispatch(string $uri): void
    {
        $routes = $this->loadRoutes();
        $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        
        // Remove trailing slash
        $uri = rtrim($uri, '/');
        
        // Find matching route
        $matchedRoute = null;
        $params = [];

        foreach ($routes as $route => $routeConfig) {
            $pattern = $this->buildPattern($route);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Check HTTP method
                if (isset($routeConfig[$method])) {
                    $matchedRoute = $routeConfig[$method];
                    array_shift($matches); // Remove full match
                    $params = array_values($matches);
                    break;
                }
            }
        }

        if ($matchedRoute) {
            $this->executeController($matchedRoute, $params);
        } else {
            $this->show404();
        }
    }

    /**
     * Build regex pattern for route matching
     */
    private function buildPattern(string $route): string
    {
        // Convert route parameters to regex patterns
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '([^/]+)', $route);
        return '#^' . $pattern . '$#';
    }

    /**
     * Execute controller action
     */
    private function executeController(array $routeConfig, array $params = []): void
    {
        $controllerName = $routeConfig['controller'] ?? '';
        $action = $routeConfig['action'] ?? 'index';
        
        if (empty($controllerName)) {
            throw new Exception("Controller not specified for route");
        }

        $controllerFile = $this->basePath . '/app/controllers/' . $controllerName . '.php';
        
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: " . $controllerName);
        }

        require_once $controllerFile;

        $fullControllerName = $controllerName;
        if (!class_exists($fullControllerName)) {
            throw new Exception("Controller class not found: " . $fullControllerName);
        }

        $controller = new $fullControllerName($this->connection);
        
        // Check if action exists
        if (!method_exists($controller, $action)) {
            throw new Exception("Action not found: " . $action);
        }

        // Call controller action with parameters
        call_user_func_array([$controller, $action], $params);
    }

    /**
     * Load application routes
     */
    private function loadRoutes(): array
    {
        return [
            '/' => [
                'GET' => [
                    'controller' => 'DashboardController',
                    'action' => 'index'
                ]
            ],
            '/dashboard' => [
                'GET' => [
                    'controller' => 'DashboardController',
                    'action' => 'index'
                ]
            ],
            '/login' => [
                'GET' => [
                    'controller' => 'AuthController',
                    'action' => 'login'
                ],
                'POST' => [
                    'controller' => 'AuthController',
                    'action' => 'authenticate'
                ]
            ],
            '/logout' => [
                'GET' => [
                    'controller' => 'AuthController',
                    'action' => 'logout'
                ]
            ],
            '/employees' => [
                'GET' => [
                    'controller' => 'EmployeeController',
                    'action' => 'index'
                ]
            ],
            '/employees/create' => [
                'GET' => [
                    'controller' => 'EmployeeController',
                    'action' => 'create'
                ],
                'POST' => [
                    'controller' => 'EmployeeController',
                    'action' => 'store'
                ]
            ],
            '/employees/{id}/edit' => [
                'GET' => [
                    'controller' => 'EmployeeController',
                    'action' => 'edit'
                ],
                'POST' => [
                    'controller' => 'EmployeeController',
                    'action' => 'update'
                ]
            ],
            '/employees/{id}/delete' => [
                'POST' => [
                    'controller' => 'EmployeeController',
                    'action' => 'delete'
                ]
            ],
            '/departments' => [
                'GET' => [
                    'controller' => 'DepartmentController',
                    'action' => 'index'
                ]
            ],
            '/departments/create' => [
                'GET' => [
                    'controller' => 'DepartmentController',
                    'action' => 'create'
                ],
                'POST' => [
                    'controller' => 'DepartmentController',
                    'action' => 'store'
                ]
            ],
            '/departments/{id}/edit' => [
                'GET' => [
                    'controller' => 'DepartmentController',
                    'action' => 'edit'
                ],
                'POST' => [
                    'controller' => 'DepartmentController',
                    'action' => 'update'
                ]
            ],
            '/attendance' => [
                'GET' => [
                    'controller' => 'AttendanceController',
                    'action' => 'index'
                ]
            ],
            '/attendance/create' => [
                'GET' => [
                    'controller' => 'AttendanceController',
                    'action' => 'create'
                ],
                'POST' => [
                    'controller' => 'AttendanceController',
                    'action' => 'store'
                ]
            ],
            '/reports' => [
                'GET' => [
                    'controller' => 'ReportController',
                    'action' => 'index'
                ]
            ],
            '/reports/{type}' => [
                'GET' => [
                    'controller' => 'ReportController',
                    'action' => 'report'
                ]
            ]
        ];
    }

    /**
     * Handle 404 Not Found
     */
    private function show404(): void
    {
        http_response_code(404);
        
        $errorView = $this->basePath . '/views/errors/404.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "<h1>404 - Page Not Found</h1>";
            echo "<p>The requested page could not be found.</p>";
        }
        exit;
    }

    /**
     * Handle application exceptions
     */
    private function handleException(Exception $e): void
    {
        error_log("Application error: " . $e->getMessage());
        
        if ($this->config['app']['debug'] ?? false) {
            $this->showErrorPage($e->getMessage(), $e->getTraceAsString());
        } else {
            $this->showErrorPage('An error occurred. Please try again later.');
        }
    }

    /**
     * Show error page
     */
    private function showErrorPage(string $message, string $trace = ''): void
    {
        http_response_code(500);
        
        $errorView = $this->basePath . '/views/errors/500.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo "<h1>500 - Internal Server Error</h1>";
            echo "<p>{$message}</p>";
            if ($trace && ($this->config['app']['debug'] ?? false)) {
                echo "<pre>{$trace}</pre>";
            }
        }
        exit;
    }

    /**
     * Get base path
     */
    public function getBasePath(): string
    {
        return $this->basePath;
    }

    /**
     * Get public path
     */
    public function getPublicPath(): string
    {
        return $this->basePath . '/public';
    }

    /**
     * Get storage path
     */
    public function getStoragePath(): string
    {
        return $this->basePath . '/storage';
    }

    /**
     * Terminate application
     */
    public function terminate(): void
    {
        if ($this->connection) {
            $this->connection->close();
        }
        
        session_write_close();
    }
}

/**
 * Helper function to get app instance
 */
function app(): App
{
    return App::getInstance();
}

// Create global helper functions
if (!function_exists('config')) {
    function config(?string $key = null) {
        return app()->getConfig($key);
    }
}

if (!function_exists('db')) {
    function db(): mysqli {
        return app()->getConnection();
    }
}