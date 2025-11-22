<?php
// app/core/Request.php

declare(strict_types=1);

class Request 
{
    private array $get;
    private array $post;
    private array $files;
    private array $server;
    private array $cookies;
    private array $headers;
    private string $method;
    private string $uri;
    private string $ip;
    private string $userAgent;

    public function __construct() 
    {
        $this->get = $_GET ?? [];
        $this->post = $_POST ?? [];
        $this->files = $_FILES ?? [];
        $this->server = $_SERVER ?? [];
        $this->cookies = $_COOKIE ?? [];
        $this->headers = $this->extractHeaders();
        $this->method = $this->determineMethod();
        $this->uri = $this->determineUri();
        $this->ip = $this->determineIp();
        $this->userAgent = $this->server['HTTP_USER_AGENT'] ?? '';
    }

    /**
     * Extract all HTTP headers
     */
    private function extractHeaders(): array 
    {
        $headers = [];
        
        foreach ($this->server as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', substr($key, 5));
                $headers[$header] = $value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH', 'CONTENT_MD5'])) {
                $header = str_replace('_', '-', $key);
                $headers[$header] = $value;
            }
        }
        
        return $headers;
    }

    /**
     * Determine HTTP method (support for method override)
     */
    private function determineMethod(): string 
    {
        $method = $this->server['REQUEST_METHOD'] ?? 'GET';
        
        // Support for method override via _method parameter
        if ($method === 'POST') {
            $overrideMethod = strtoupper($this->post['_method'] ?? '');
            if (in_array($overrideMethod, ['PUT', 'PATCH', 'DELETE'])) {
                return $overrideMethod;
            }
        }
        
        return $method;
    }

    /**
     * Determine request URI
     */
    private function determineUri(): string 
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        
        // Remove query string
        if (($pos = strpos($uri, '?')) !== false) {
            $uri = substr($uri, 0, $pos);
        }
        
        return rawurldecode($uri);
    }

    /**
     * Determine client IP address
     */
    private function determineIp(): string 
    {
        // Check for forwarded IP first (behind proxy)
        if (!empty($this->server['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(',', $this->server['HTTP_X_FORWARDED_FOR']);
            return trim($ips[0]);
        }
        
        if (!empty($this->server['HTTP_X_REAL_IP'])) {
            return $this->server['HTTP_X_REAL_IP'];
        }
        
        if (!empty($this->server['HTTP_CLIENT_IP'])) {
            return $this->server['HTTP_CLIENT_IP'];
        }
        
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    /**
     * Get all GET parameters
     */
    public function all(): array 
    {
        return array_merge($this->get, $this->post);
    }

    /**
     * Get GET parameter
     */
    public function get(string $key, $default = null) 
    {
        return $this->get[$key] ?? $default;
    }

    /**
     * Get POST parameter
     */
    public function post(string $key, $default = null) 
    {
        return $this->post[$key] ?? $default;
    }

    /**
     * Get input parameter (GET or POST)
     */
    public function input(string $key, $default = null) 
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * Get file upload
     */
    public function file(string $key) 
    {
        return $this->files[$key] ?? null;
    }

    /**
     * Get cookie
     */
    public function cookie(string $key, $default = null) 
    {
        return $this->cookies[$key] ?? $default;
    }

    /**
     * Get header
     */
    public function header(string $key, $default = null) 
    {
        $key = str_replace('_', '-', strtoupper($key));
        return $this->headers[$key] ?? $default;
    }

    /**
     * Get all headers
     */
    public function headers(): array 
    {
        return $this->headers;
    }

    /**
     * Get HTTP method
     */
    public function method(): string 
    {
        return $this->method;
    }

    /**
     * Check if method matches
     */
    public function isMethod(string $method): bool 
    {
        return strtoupper($method) === $this->method;
    }

    /**
     * Check if request is GET
     */
    public function isGet(): bool 
    {
        return $this->isMethod('GET');
    }

    /**
     * Check if request is POST
     */
    public function isPost(): bool 
    {
        return $this->isMethod('POST');
    }

    /**
     * Check if request is PUT
     */
    public function isPut(): bool 
    {
        return $this->isMethod('PUT');
    }

    /**
     * Check if request is PATCH
     */
    public function isPatch(): bool 
    {
        return $this->isMethod('PATCH');
    }

    /**
     * Check if request is DELETE
     */
    public function isDelete(): bool 
    {
        return $this->isMethod('DELETE');
    }

    /**
     * Check if request is AJAX
     */
    public function isAjax(): bool 
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    /**
     * Check if request expects JSON response
     */
    public function expectsJson(): bool 
    {
        $accept = $this->header('Accept') ?? '';
        return str_contains($accept, 'application/json') || $this->isAjax();
    }

    /**
     * Get request URI
     */
    public function uri(): string 
    {
        return $this->uri;
    }

    /**
     * Get request path
     */
    public function path(): string 
    {
        return parse_url($this->uri, PHP_URL_PATH) ?? '/';
    }

    /**
     * Get query string
     */
    public function queryString(): string 
    {
        return $this->server['QUERY_STRING'] ?? '';
    }

    /**
     * Get full URL
     */
    public function fullUrl(): string 
    {
        $scheme = $this->isSecure() ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $uri = $this->uri;
        
        return "{$scheme}://{$host}{$uri}";
    }

    /**
     * Check if request is secure (HTTPS)
     */
    public function isSecure(): bool 
    {
        if (!empty($this->server['HTTPS']) && $this->server['HTTPS'] !== 'off') {
            return true;
        }
        
        if (!empty($this->server['HTTP_X_FORWARDED_PROTO']) && $this->server['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        
        if (!empty($this->server['HTTP_X_FORWARDED_SSL']) && $this->server['HTTP_X_FORWARDED_SSL'] === 'on') {
            return true;
        }
        
        return false;
    }

    /**
     * Get client IP address
     */
    public function ip(): string 
    {
        return $this->ip;
    }

    /**
     * Get user agent
     */
    public function userAgent(): string 
    {
        return $this->userAgent;
    }

    /**
     * Get referrer
     */
    public function referrer(): string 
    {
        return $this->server['HTTP_REFERER'] ?? '';
    }

    /**
     * Get content type
     */
    public function contentType(): string 
    {
        return $this->server['CONTENT_TYPE'] ?? '';
    }

    /**
     * Get content length
     */
    public function contentLength(): int 
    {
        return (int)($this->server['CONTENT_LENGTH'] ?? 0);
    }

    /**
     * Get raw input data
     */
    public function getContent(): string 
    {
        return file_get_contents('php://input') ?? '';
    }

    /**
     * Get JSON input
     */
    public function json($key = null, $default = null) 
    {
        $content = $this->getContent();
        $data = json_decode($content, true) ?? [];
        
        if ($key === null) {
            return $data;
        }
        
        return $data[$key] ?? $default;
    }

    /**
     * Check if has file upload
     */
    public function hasFile(string $key): bool 
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    /**
     * Check if parameter exists
     */
    public function has(string $key): bool 
    {
        return array_key_exists($key, $this->all());
    }

    /**
     * Check if any of parameters exist
     */
    public function hasAny(array $keys): bool 
    {
        foreach ($keys as $key) {
            if ($this->has($key)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if all parameters exist
     */
    public function hasAll(array $keys): bool 
    {
        foreach ($keys as $key) {
            if (!$this->has($key)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Get only specified parameters
     */
    public function only(array $keys): array 
    {
        $all = $this->all();
        return array_intersect_key($all, array_flip($keys));
    }

    /**
     * Get all except specified parameters
     */
    public function except(array $keys): array 
    {
        $all = $this->all();
        return array_diff_key($all, array_flip($keys));
    }

    /**
     * Get boolean value from parameter
     */
    public function boolean(string $key, bool $default = false): bool 
    {
        $value = $this->input($key);
        
        if (is_bool($value)) {
            return $value;
        }
        
        if (is_numeric($value)) {
            return (bool)$value;
        }
        
        $truthy = ['1', 'true', 'on', 'yes', 'y'];
        $falsey = ['0', 'false', 'off', 'no', 'n'];
        
        if (in_array(strtolower($value), $truthy, true)) {
            return true;
        }
        
        if (in_array(strtolower($value), $falsey, true)) {
            return false;
        }
        
        return $default;
    }

    /**
     * Get integer value from parameter
     */
    public function integer(string $key, int $default = 0): int 
    {
        $value = $this->input($key, $default);
        return (int)filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Get float value from parameter
     */
    public function float(string $key, float $default = 0.0): float 
    {
        $value = $this->input($key, $default);
        return (float)filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * Get string value from parameter (sanitized)
     */
    public function string(string $key, string $default = ''): string 
    {
        $value = $this->input($key, $default);
        return trim(htmlspecialchars($value, ENT_QUOTES, 'UTF-8'));
    }

    /**
     * Get email value from parameter (validated)
     */
    public function email(string $key, string $default = ''): string 
    {
        $value = $this->input($key, $default);
        $email = filter_var(trim($value), FILTER_SANITIZE_EMAIL);
        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : $default;
    }

    /**
     * Get URL value from parameter (validated)
     */
    public function url(string $key, string $default = ''): string 
    {
        $value = $this->input($key, $default);
        $url = filter_var(trim($value), FILTER_SANITIZE_URL);
        return filter_var($url, FILTER_VALIDATE_URL) ? $url : $default;
    }

    /**
     * Validate request data against rules
     */
    public function validate(array $rules): array 
    {
        $validator = new Validator($this->all(), $rules);
        return $validator->validate();
    }

    /**
     * Get bearer token from Authorization header
     */
    public function bearerToken(): string 
    {
        $header = $this->header('Authorization') ?? '';
        
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }
        
        return '';
    }

    /**
     * Get CSRF token from request
     */
    public function csrfToken(): string 
    {
        return $this->input('_token', $this->header('X-CSRF-TOKEN'));
    }

    /**
     * Check if request is from specific route
     */
    public function is(string $pattern): bool 
    {
        $pattern = preg_quote($pattern, '#');
        $pattern = str_replace('\*', '.*', $pattern);
        return (bool)preg_match('#^' . $pattern . '$#', $this->path());
    }

    /**
     * Get route segments
     */
    public function segments(): array 
    {
        return array_filter(explode('/', $this->path()));
    }

    /**
     * Get route segment by index
     */
    public function segment(int $index, string $default = '') 
    {
        $segments = $this->segments();
        return $segments[$index] ?? $default;
    }
}

/**
 * Simple validator class
 */
class Validator 
{
    private array $data;
    private array $rules;
    private array $errors = [];

    public function __construct(array $data, array $rules) 
    {
        $this->data = $data;
        $this->rules = $rules;
    }

    public function validate(): array 
    {
        foreach ($this->rules as $field => $rules) {
            $rules = is_array($rules) ? $rules : explode('|', $rules);
            $value = $this->data[$field] ?? null;

            foreach ($rules as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }

        if (!empty($this->errors)) {
            throw new ValidationException('Validation failed', $this->errors);
        }

        return $this->data;
    }

    private function validateRule(string $field, $value, string $rule): void 
    {
        $params = [];
        
        if (str_contains($rule, ':')) {
            [$rule, $param] = explode(':', $rule, 2);
            $params = explode(',', $param);
        }

        $method = 'validate' . ucfirst($rule);
        
        if (method_exists($this, $method)) {
            $this->$method($field, $value, $params);
        }
    }

    private function validateRequired(string $field, $value, array $params): void 
    {
        if ($value === null || $value === '' || (is_array($value) && empty($value))) {
            $this->errors[$field][] = "The {$field} field is required.";
        }
    }

    private function validateEmail(string $field, $value, array $params): void 
    {
        if ($value && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The {$field} must be a valid email address.";
        }
    }

    private function validateNumeric(string $field, $value, array $params): void 
    {
        if ($value && !is_numeric($value)) {
            $this->errors[$field][] = "The {$field} must be a number.";
        }
    }

    private function validateMin(string $field, $value, array $params): void 
    {
        $min = (int)($params[0] ?? 0);
        
        if ($value && strlen((string)$value) < $min) {
            $this->errors[$field][] = "The {$field} must be at least {$min} characters.";
        }
    }

    private function validateMax(string $field, $value, array $params): void 
    {
        $max = (int)($params[0] ?? 0);
        
        if ($value && strlen((string)$value) > $max) {
            $this->errors[$field][] = "The {$field} may not be greater than {$max} characters.";
        }
    }
}

class ValidationException extends Exception 
{
    private array $errors;

    public function __construct(string $message, array $errors = []) 
    {
        parent::__construct($message);
        $this->errors = $errors;
    }

    public function getErrors(): array 
    {
        return $this->errors;
    }
}

/**
 * Helper function to get request instance
 */
function request(): Request 
{
    static $request = null;
    
    if ($request === null) {
        $request = new Request();
    }
    
    return $request;
}