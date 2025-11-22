<?php
// app/core/Session.php

declare(strict_types=1);

class Session 
{
    private static bool $initialized = false;
    private static array $config = [
        'name' => 'HR_SESSION',
        'lifetime' => 7200, // 2 hours
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Strict'
    ];

    /**
     * Initialize session with security settings
     */
    public static function start(array $config = []): void 
    {
        if (self::$initialized) {
            return;
        }

        // Merge custom config
        self::$config = array_merge(self::$config, $config);

        // Set session name
        session_name(self::$config['name']);

        // Set session cookie parameters
        session_set_cookie_params([
            'lifetime' => self::$config['lifetime'],
            'path' => self::$config['path'],
            'domain' => self::$config['domain'],
            'secure' => self::$config['secure'],
            'httponly' => self::$config['httponly'],
            'samesite' => self::$config['samesite']
        ]);

        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Regenerate session ID to prevent fixation attacks
        if (empty($_SESSION['_initialized'])) {
            session_regenerate_id(true);
            $_SESSION['_initialized'] = true;
            $_SESSION['_created'] = time();
        }

        // Check session expiration
        self::checkExpiration();

        // Clear old flash data from previous request
        self::clearOldFlashData();

        self::$initialized = true;
    }

    /**
     * Check if session has expired
     */
    private static function checkExpiration(): void 
    {
        $lastActivity = $_SESSION['_last_activity'] ?? $_SESSION['_created'] ?? time();
        $currentTime = time();

        // Update last activity time
        $_SESSION['_last_activity'] = $currentTime;

        // Check if session has expired
        if (($currentTime - $lastActivity) > self::$config['lifetime']) {
            self::destroy();
            throw new SessionExpiredException('Session has expired');
        }
    }

    /**
     * Clear old flash data from previous request
     */
    private static function clearOldFlashData(): void 
    {
        if (isset($_SESSION['_flash_old'])) {
            unset($_SESSION['_flash_old']);
        }
        
        // Move current flash data to old
        if (isset($_SESSION['_flash'])) {
            $_SESSION['_flash_old'] = $_SESSION['_flash'];
            unset($_SESSION['_flash']);
        }
    }

    /**
     * Set session value
     */
    public static function set(string $key, $value): void 
    {
        self::ensureStarted();
        $_SESSION[$key] = $value;
    }

    /**
     * Get session value
     */
    public static function get(string $key, $default = null) 
    {
        self::ensureStarted();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if session key exists
     */
    public static function has(string $key): bool 
    {
        self::ensureStarted();
        return isset($_SESSION[$key]);
    }

    /**
     * Remove session value
     */
    public static function remove(string $key): void 
    {
        self::ensureStarted();
        unset($_SESSION[$key]);
    }

    /**
     * Get all session data
     */
    public static function all(): array 
    {
        self::ensureStarted();
        return $_SESSION;
    }

    /**
     * Set multiple session values
     */
    public static function put(array $data): void 
    {
        self::ensureStarted();
        foreach ($data as $key => $value) {
            $_SESSION[$key] = $value;
        }
    }

    /**
     * Flash data to session for next request
     */
    public static function flash(string $key, $value): void 
    {
        self::ensureStarted();
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Get flashed data and remove it
     */
    public static function getFlash(string $key, $default = null) 
    {
        self::ensureStarted();
        $value = $_SESSION['_flash_old'][$key] ?? $default;
        self::removeFlash($key);
        return $value;
    }

    /**
     * Check if flashed data exists
     */
    public static function hasFlash(string $key): bool 
    {
        self::ensureStarted();
        return isset($_SESSION['_flash_old'][$key]);
    }

    /**
     * Remove flashed data
     */
    public static function removeFlash(string $key): void 
    {
        self::ensureStarted();
        unset($_SESSION['_flash_old'][$key]);
    }

    /**
     * Keep flashed data for next request
     */
    public static function reflash(): void 
    {
        self::ensureStarted();
        if (isset($_SESSION['_flash_old'])) {
            $_SESSION['_flash'] = array_merge($_SESSION['_flash'] ?? [], $_SESSION['_flash_old']);
        }
    }

    /**
     * Keep specific flashed data for next request
     */
    public static function keep(array $keys): void 
    {
        self::ensureStarted();
        foreach ($keys as $key) {
            if (isset($_SESSION['_flash_old'][$key])) {
                $_SESSION['_flash'][$key] = $_SESSION['_flash_old'][$key];
            }
        }
    }

    /**
     * Get and remove all flashed data
     */
    public static function getFlashed(): array 
    {
        self::ensureStarted();
        $flashed = $_SESSION['_flash_old'] ?? [];
        unset($_SESSION['_flash_old']);
        return $flashed;
    }

    /**
     * Flash input data for form repopulation
     */
    public static function flashInput(array $input): void 
    {
        self::ensureStarted();
        foreach ($input as $key => $value) {
            self::flash("old_$key", $value);
        }
    }

    /**
     * Get old input value
     */
    public static function old(string $key, $default = null) 
    {
        return self::getFlash("old_$key", $default);
    }

    /**
     * Set user authentication
     */
    public static function login(array $user): void 
    {
        self::ensureStarted();
        $_SESSION['user'] = $user;
        $_SESSION['authenticated'] = true;
        $_SESSION['login_time'] = time();
        
        // Regenerate session ID after login for security
        session_regenerate_id(true);
    }

    /**
     * Check if user is authenticated
     */
    public static function isAuthenticated(): bool 
    {
        self::ensureStarted();
        return ($_SESSION['authenticated'] ?? false) === true;
    }

    /**
     * Get authenticated user data
     */
    public static function user(?string $key = null) 
    {
        self::ensureStarted();
        if ($key === null) {
            return $_SESSION['user'] ?? null;
        }
        return $_SESSION['user'][$key] ?? null;
    }

    /**
     * Get user ID
     */
    public static function userId() 
    {
        return self::user('id') ?? self::user('user_id') ?? self::user('admin_id');
    }

    /**
     * Logout user
     */
    public static function logout(): void 
    {
        self::ensureStarted();
        
        // Clear all session data
        $_SESSION = [];

        // Delete session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        // Destroy session
        session_destroy();
        self::$initialized = false;
    }

    /**
     * Regenerate session ID
     */
    public static function regenerate(): void 
    {
        self::ensureStarted();
        session_regenerate_id(true);
    }

    /**
     * Get session ID
     */
    public static function getId(): string 
    {
        self::ensureStarted();
        return session_id();
    }

    /**
     * Get session name
     */
    public static function getName(): string 
    {
        return session_name();
    }

    /**
     * Get session status
     */
    public static function status(): int 
    {
        return session_status();
    }

    /**
     * Get session creation time
     */
    public static function getCreatedTime(): int 
    {
        self::ensureStarted();
        return $_SESSION['_created'] ?? time();
    }

    /**
     * Get last activity time
     */
    public static function getLastActivity(): int 
    {
        self::ensureStarted();
        return $_SESSION['_last_activity'] ?? time();
    }

    /**
     * Get session lifetime
     */
    public static function getLifetime(): int 
    {
        return self::$config['lifetime'];
    }

    /**
     * Get remaining session time
     */
    public static function getRemainingTime(): int 
    {
        self::ensureStarted();
        $lastActivity = self::getLastActivity();
        return max(0, self::$config['lifetime'] - (time() - $lastActivity));
    }

    /**
     * Set session lifetime
     */
    public static function setLifetime(int $seconds): void 
    {
        self::$config['lifetime'] = $seconds;
        
        // Update session cookie
        if (self::$initialized) {
            session_set_cookie_params([
                'lifetime' => $seconds,
                'path' => self::$config['path'],
                'domain' => self::$config['domain'],
                'secure' => self::$config['secure'],
                'httponly' => self::$config['httponly'],
                'samesite' => self::$config['samesite']
            ]);
        }
    }

    /**
     * Add CSRF token to session
     */
    public static function csrfToken(): string 
    {
        self::ensureStarted();
        
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['_csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken(string $token): bool 
    {
        self::ensureStarted();
        return isset($_SESSION['_csrf_token']) && hash_equals($_SESSION['_csrf_token'], $token);
    }

    /**
     * Add message to session
     */
    public static function addMessage(string $type, string $message): void 
    {
        self::ensureStarted();
        $_SESSION['_messages'][] = [
            'type' => $type,
            'message' => $message,
            'time' => time()
        ];
    }

    /**
     * Get all messages
     */
    public static function getMessages(): array 
    {
        self::ensureStarted();
        $messages = $_SESSION['_messages'] ?? [];
        unset($_SESSION['_messages']);
        return $messages;
    }

    /**
     * Get messages by type
     */
    public static function getMessagesByType(string $type): array 
    {
        $messages = self::getMessages();
        return array_filter($messages, function($message) use ($type) {
            return $message['type'] === $type;
        });
    }

    /**
     * Check if session has messages
     */
    public static function hasMessages(): bool 
    {
        self::ensureStarted();
        return !empty($_SESSION['_messages']);
    }

    /**
     * Clear all session data except specified keys
     */
    public static function clearExcept(array $keys): void 
    {
        self::ensureStarted();
        $preserved = [];
        
        foreach ($keys as $key) {
            if (isset($_SESSION[$key])) {
                $preserved[$key] = $_SESSION[$key];
            }
        }
        
        $_SESSION = $preserved;
    }

    /**
     * Destroy session completely
     */
    public static function destroy(): void 
    {
        if (self::$initialized) {
            // Clear all session data
            $_SESSION = [];

            // Delete session cookie
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(
                    session_name(),
                    '',
                    time() - 42000,
                    $params["path"],
                    $params["domain"],
                    $params["secure"],
                    $params["httponly"]
                );
            }

            // Destroy session
            session_destroy();
            self::$initialized = false;
        }
    }

    /**
     * Ensure session is started
     */
    private static function ensureStarted(): void 
    {
        if (!self::$initialized) {
            self::start();
        }
    }

    /**
     * Get session configuration
     */
    public static function getConfig(): array 
    {
        return self::$config;
    }
}

/**
 * Session expired exception
 */
class SessionExpiredException extends Exception 
{
    public function __construct(string $message = "Session has expired", int $code = 0, ?Throwable $previous = null) 
    {
        parent::__construct($message, $code, $previous);
    }
}

/**
 * Helper functions
 */

if (!function_exists('session')) {
    /**
     * Session helper function
     */
    function session(?string $key = null, $value = null) 
    {
        if ($key === null) {
            return Session::all();
        }
        
        if ($value === null) {
            return Session::get($key);
        }
        
        Session::set($key, $value);
        return $value;
    }
}

if (!function_exists('auth')) {
    /**
     * Authentication helper function
     */
    function auth(): bool 
    {
        return Session::isAuthenticated();
    }
}

if (!function_exists('user')) {
    /**
     * User data helper function
     */
    function user(?string $key = null) 
    {
        return Session::user($key);
    }
}

if (!function_exists('csrf_token')) {
    /**
     * CSRF token helper function
     */
    function csrf_token(): string 
    {
        return Session::csrfToken();
    }
}

if (!function_exists('flash')) {
    /**
     * Flash data helper function
     */
    function flash(string $key, $value): void 
    {
        Session::flash($key, $value);
    }
}

if (!function_exists('old')) {
    /**
     * Get old input value (for form repopulation)
     */
    function old(string $key, $default = null) 
    {
        return Session::old($key, $default);
    }
}