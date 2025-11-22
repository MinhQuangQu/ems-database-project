<?php

declare(strict_types=1);

class Response 
{
    private int $statusCode = 200;
    private array $headers = [];
    private $content = '';
    private array $cookies = [];
    private bool $sent = false;

    public function __construct($content = '', int $status = 200, array $headers = []) 
    {
        $this->setContent($content);
        $this->setStatusCode($status);
        $this->setHeaders($headers);
    }

    /**
     * Set HTTP status code
     */
    public function setStatusCode(int $code): self 
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Set response content
     */
    public function setContent($content): self 
    {
        if (is_array($content) || is_object($content)) {
            $this->setJsonContentType();
            $this->content = json_encode($content, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } else {
            $this->content = (string)$content;
        }
        
        return $this;
    }

    /**
     * Set JSON response
     */
    public function json($data, int $status = 200): self 
    {
        $this->setStatusCode($status);
        $this->setJsonContentType();
        $this->setContent($data);
        
        return $this;
    }

    /**
     * Set JSON error response
     */
/**
 * Set JSON error response
 */
public function jsonError($data, int $status = 400, array $additional = []): self 
{
    $response = [
        'success' => false,
        'status' => $status
    ];
    
    // Xử lý cả string và array/object
    if (is_string($data)) {
        $response['message'] = $data;
    } else {
        $response['data'] = $data;
    }
    
    if (!empty($additional)) {
        $response = array_merge($response, $additional);
    }
    
    return $this->json($response, $status);
}

    /**
     * Set JSON success response
     */
    public function jsonSuccess($data = null, string $message = '', int $status = 200): self 
    {
        $response = [
            'success' => true,
            'message' => $message,
            'status' => $status
        ];
        
        if ($data !== null) {
            $response['data'] = $data;
        }
        
        return $this->json($response, $status);
    }

    /**
     * Set HTML content type
     */
    public function setHtmlContentType(): self 
    {
        return $this->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Set JSON content type
     */
    public function setJsonContentType(): self 
    {
        return $this->header('Content-Type', 'application/json');
    }

    /**
     * Set a response header
     */
    public function header(string $name, string $value, bool $replace = true): self 
    {
        $name = $this->normalizeHeaderName($name);
        
        if ($replace || !isset($this->headers[$name])) {
            $this->headers[$name] = $value;
        }
        
        return $this;
    }

    /**
     * Set multiple headers
     */
    public function setHeaders(array $headers): self 
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }
        
        return $this;
    }

public static function notFound($message = 'Page not found'): self 
{
    $response = new self();
    
    if (is_array($message) || is_object($message)) {
        return $response->jsonError($message, 404);
    }
    
    $response->setStatusCode(404);
    $response->setContent($message);
    $response->setHtmlContentType();
    
    return $response;
}

public static function serverError($message = 'Internal Server Error'): self 
{
    $response = new self();
    
    if (is_array($message) || is_object($message)) {
        return $response->jsonError($message, 500);
    }
    
    $response->setStatusCode(500);
    $response->setContent($message);
    $response->setHtmlContentType();
    
    return $response;
}

/**
 * Create a 403 Forbidden response
 */
public static function forbidden($message = 'Access Forbidden'): self 
{
    $response = new self();
    
    if (is_array($message) || is_object($message)) {
        return $response->jsonError($message, 403);
    }
    
    $response->setStatusCode(403);
    $response->setContent($message);
    $response->setHtmlContentType();
    
    return $response;
}

/**
 * Create a 401 Unauthorized response
 */
public static function unauthorized($message = 'Unauthorized'): self 
{
    $response = new self();
    
    if (is_array($message) || is_object($message)) {
        return $response->jsonError($message, 401);
    }
    
    $response->setStatusCode(401);
    $response->setContent($message);
    $response->setHtmlContentType();
    
    return $response;
}

/**
 * Create a 400 Bad Request response
 */
public static function badRequest($message = 'Bad Request'): self 
{
    $response = new self();
    
    if (is_array($message) || is_object($message)) {
        return $response->jsonError($message, 400);
    }
    
    $response->setStatusCode(400);
    $response->setContent($message);
    $response->setHtmlContentType();
    
    return $response;
}

/**
 * Create a 200 OK response
 */
public static function ok($content = ''): self 
{
    $response = new self($content);
    $response->setStatusCode(200);
    
    return $response;
}

/**
 * Create a 201 Created response
 */
public static function created($content = ''): self 
{
    $response = new self($content);
    $response->setStatusCode(201);
    
    return $response;
}

    /**
     * Send the response
     */
    public function send(): self 
    {
        if ($this->sent) {
            return $this;
        }

        // Set status code
        http_response_code($this->statusCode);

        // Set headers
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}", true);
        }

        // Send content
        echo $this->content;

        $this->sent = true;
        
        return $this;
    }

    /**
     * Normalize header name
     */
    private function normalizeHeaderName(string $name): string 
    {
        return str_replace(' ', '-', ucwords(str_replace('-', ' ', $name)));
    }

    /**
     * Static method to create redirect response
     */
    public static function redirectTo(string $url, int $status = 302): self 
    {
        return (new self())->redirect($url, $status);
    }

    /**
     * Set redirect response
     */
    public function redirect(string $url, int $status = 302): self 
    {
        $this->setStatusCode($status);
        $this->header('Location', $url);
        
        // Add minimal content for browsers that don't follow redirects
        $this->content = sprintf('<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta http-equiv="refresh" content="0;url=%1$s" />
        <title>Redirecting to %1$s</title>
    </head>
    <body>
        Redirecting to <a href="%1$s">%1$s</a>.
    </body>
</html>', htmlspecialchars($url, ENT_QUOTES, 'UTF-8'));

        return $this;
    }
}