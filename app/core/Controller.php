<?php
class Controller
{
    protected PDO $conn;
    protected array $config;

    public function __construct()
    {
        // Load config
        $configPath = __DIR__ . '/../config/config.php';
        if (!file_exists($configPath)) die("Config file not found: $configPath");
        $this->config = require $configPath;

        // Load DB (database.php must NOT start session)
        $dbPath = __DIR__ . '/../config/database.php';
        if (!file_exists($dbPath)) die("Database config not found: $dbPath");

        $this->conn = require $dbPath;
        if (!$this->conn instanceof PDO) {
            die("Database connection is invalid!");
        }
    }

    // =====================
    // Model loader
    // =====================
    public function model($model)
    {
        $file = "../app/models/" . $model . ".php";
        if (!file_exists($file)) die("Model $model not found!");
        require_once $file;
        return new $model($this->conn);
    }

    // =====================
    // View loader
    // =====================
    public function view($view, $data = [])
    {
        extract($data);
        require __DIR__ . "/../views/$view.php";
    }

    // =====================
    // Redirect helper
    // =====================
    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }

    // =====================
    // Login check (middleware)
    // =====================
    protected function requireLogin()
    {
        if (empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . "/auth/login");
        }
    }

    // =====================
    // Safe sanitize
    // =====================
    protected function sanitize($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'sanitize'], $data);
        }
        return htmlspecialchars(trim(strip_tags($data)));
    }

    // =====================
    // FLASH MESSAGE
    // KHÔNG BAO GIỜ ĐƯỢC session_start() ở đây
    // =====================
    protected function flash(string $key, string $message)
    {
        $_SESSION['flash_messages'][$key] = $message;
    }

    protected function getFlash(string $key)
    {
        if (!isset($_SESSION['flash_messages'][$key])) {
            return null;
        }
        $msg = $_SESSION['flash_messages'][$key];
        unset($_SESSION['flash_messages'][$key]);
        return $msg;
    }

    // =====================
    // POST safe
    // =====================
    protected function getPostData(): array
    {
        $clean = [];
        foreach ($_POST as $k => $v) {
            $clean[$k] = htmlspecialchars(trim(strip_tags($v)));
        }
        return $clean;
    }
}
