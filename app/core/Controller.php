<?php

declare(strict_types=1);

class Controller
{
    protected ?mysqli $conn = null;
    protected array $config = [];

    public function __construct(?mysqli $conn = null)
    {
        // Khởi tạo session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'use_strict_mode' => true
            ]);
        }

        if ($conn !== null) {
            $this->conn = $conn;
        }

        // Load configuration (nếu có)
        $this->loadConfig();
    }

    /**
     * Set database connection
     */
    public function setConnection(mysqli $conn): void
    {
        $this->conn = $conn;
    }

    /**
     * Load configuration
     */
    protected function loadConfig(): void
    {
        $configPath = __DIR__ . '/../../config/config.php';
        if (file_exists($configPath)) {
            $this->config = require $configPath;
        } else {
            $this->config = [];
        }
    }

    /**
     * Render view với data
     */
    protected function renderView(string $viewPath, array $data = []): string
    {
        // Convert dot notation to file path (nếu cần)
        $viewFile = __DIR__ . '/../../resource/views/' . str_replace('.', '/', $viewPath) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: {$viewFile}");
        }

        // Extract data to variables
        extract($data);
        
        // Start output buffering
        ob_start();
        include $viewFile;
        return ob_get_clean();
    }

    /**
     * Redirect to URL
     */
    protected function redirect(string $url, int $statusCode = 302): void
    {
        header("Location: {$url}", true, $statusCode);
        exit();
    }

    /**
     * Check if user is logged in (cho admin)
     */
    protected function isLoggedIn(): bool
    {
        return isset($_SESSION['admin_id'], $_SESSION['authenticated']) && 
               $_SESSION['authenticated'] === true;
    }

    /**
     * Check if request is POST
     */
    protected function isPostRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    /**
     * Check if request is GET
     */
    protected function isGetRequest(): bool
    {
        return $_SERVER['REQUEST_METHOD'] === 'GET';
    }

    /**
     * Get POST data (đã được làm sạch)
     */
    protected function getPostData(): array
    {
        $data = [];
        foreach ($_POST as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Get GET data (đã được làm sạch)
     */
    protected function getQueryParams(): array
    {
        $data = [];
        foreach ($_GET as $key => $value) {
            if (is_string($value)) {
                $data[$key] = trim($value);
            } else {
                $data[$key] = $value;
            }
        }
        return $data;
    }

    /**
     * Set flash message
     */
    protected function setFlashMessage(string $type, string $message): void
    {
        $_SESSION['flash_messages'][$type] = $message;
    }

    /**
     * Get flash message
     */
    protected function getFlashMessage(string $type): ?string
    {
        $message = $_SESSION['flash_messages'][$type] ?? null;
        unset($_SESSION['flash_messages'][$type]);
        return $message;
    }

    /**
     * Validate CSRF token
     */
    protected function validateCsrfToken(): bool
    {
        $token = $_POST['csrf_token'] ?? $_GET['csrf_token'] ?? '';
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Add CSRF token to form
     */
    protected function addCsrfToken(): string
    {
        $token = $this->generateCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Check authorization (cho admin system)
     */
    protected function hasPermission(string $permission): bool
    {
        $userPermissions = $_SESSION['admin_permissions'] ?? [];
        return in_array($permission, $userPermissions, true);
    }

    /**
     * Return JSON response
     */
    protected function jsonResponse(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit();
    }

    /**
     * Handle exception
     */
    protected function handleException(Exception $e, string $message = 'An error occurred'): void
    {
        error_log("Controller error: " . $e->getMessage());
        
        if ($this->isAjaxRequest()) {
            $this->jsonResponse([
                'success' => false,
                'message' => $message,
                'error' => $this->config['debug'] ?? false ? $e->getMessage() : null
            ], 500);
        } else {
            $this->setFlashMessage('error', $message);
            $this->redirect('/error');
        }
    }

    /**
     * Check if request is AJAX
     */
    protected function isAjaxRequest(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Get current user info (cho admin)
     */
    protected function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'admin_id' => $_SESSION['admin_id'],
            'username' => $_SESSION['username'] ?? null,
            'full_name' => $_SESSION['full_name'] ?? null
        ];
    }

    /**
     * Require authentication
     */
    protected function requireAuth(): void
    {
        if (!$this->isLoggedIn()) {
            $this->setFlashMessage('error', 'Please login to access this page.');
            $this->redirect('/login');
        }
    }

    /**
     * Require POST method
     */
    protected function requirePost(): void
    {
        if (!$this->isPostRequest()) {
            $this->handleException(new Exception('Method not allowed'), 'Method not allowed');
        }
    }

    /**
     * Sanitize input data
     */
    protected function sanitizeInput($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitizeInput'], $data);
        }
        
        return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validate required fields
     */
    protected function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $errors[$field] = "The {$field} field is required.";
            }
        }
        
        return $errors;
    }

    /**
     * Get pagination parameters
     */
    protected function getPaginationParams(int $defaultPerPage = 15): array
    {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = max(1, (int)($_GET['per_page'] ?? $defaultPerPage));
        
        return [
            'page' => $page,
            'per_page' => $perPage,
            'offset' => ($page - 1) * $perPage
        ];
    }
}