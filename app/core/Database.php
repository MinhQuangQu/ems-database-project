<?php
// app/core/Route.php

declare(strict_types=1);

class Route 
{
    private static array $routes = [];
    private static array $namedRoutes = [];
    private static array $middleware = [];
    private static array $groupStack = [];
    private static string $baseNamespace = 'App\\Controllers\\';

    /**
     * Add GET route
     */
    public static function get(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute('GET', $uri, $action, $name);
    }

    /**
     * Add POST route
     */
    public static function post(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute('POST', $uri, $action, $name);
    }

    /**
     * Add PUT route
     */
    public static function put(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute('PUT', $uri, $action, $name);
    }

    /**
     * Add PATCH route
     */
    public static function patch(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute('PATCH', $uri, $action, $name);
    }

    /**
     * Add DELETE route
     */
    public static function delete(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute('DELETE', $uri, $action, $name);
    }

    /**
     * Add route for any HTTP method
     */
    public static function any(?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], $uri, $action, $name);
    }

    /**
     * Add route for multiple HTTP methods
     */
    public static function match(array $methods, ?string $uri, $action, ?string $name = null): void 
    {
        self::addRoute($methods, $uri, $action, $name);
    }

    /**
     * Add resource routes (RESTful)
     */
    public static function resource(?string $uri, ?string $controller, ?string $name = null): void 
    {
        $name = $name ?: str_replace('/', '.', trim($uri, '/'));
        
        self::get($uri, "{$controller}@index", "{$name}.index");
        self::get("{$uri}/create", "{$controller}@create", "{$name}.create");
        self::post($uri, "{$controller}@store", "{$name}.store");
        self::get("{$uri}/{id}", "{$controller}@show", "{$name}.show");
        self::get("{$uri}/{id}/edit", "{$controller}@edit", "{$name}.edit");
        self::put("{$uri}/{id}", "{$controller}@update", "{$name}.update");
        self::patch("{$uri}/{id}", "{$controller}@update", "{$name}.update");
        self::delete("{$uri}/{id}", "{$controller}@destroy", "{$name}.destroy");
    }

    /**
     * Add API resource routes (without create/edit views)
     */
    public static function apiResource(?string $uri, ?string $controller, ?string $name = null): void 
    {
        $name = $name ?: str_replace('/', '.', trim($uri, '/'));
        
        self::get($uri, "{$controller}@index", "{$name}.index");
        self::post($uri, "{$controller}@store", "{$name}.store");
        self::get("{$uri}/{id}", "{$controller}@show", "{$name}.show");
        self::put("{$uri}/{id}", "{$controller}@update", "{$name}.update");
        self::patch("{$uri}/{id}", "{$controller}@update", "{$name}.update");
        self::delete("{$uri}/{id}", "{$controller}@destroy", "{$name}.destroy");
    }

    /**
     * Add route with middleware
     */
    public static function middleware(array $middleware, callable $callback): void 
    {
        self::$groupStack[] = ['middleware' => $middleware];
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    /**
     * Add route with prefix
     */
    public static function prefix(string $prefix, callable $callback): void 
    {
        self::$groupStack[] = ['prefix' => $prefix];
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    /**
     * Add route with namespace
     */
    public static function namespace(string $namespace, callable $callback): void 
    {
        self::$groupStack[] = ['namespace' => $namespace];
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    /**
     * Add route group with multiple attributes
     */
    public static function group(array $attributes, callable $callback): void 
    {
        self::$groupStack[] = $attributes;
        call_user_func($callback);
        array_pop(self::$groupStack);
    }

    /**
     * Add a route to the collection
     */
    private static function addRoute($methods, string $uri, $action, ?string $name): void 
    {
        $methods = (array)$methods;
        $uri = self::applyGroupPrefix($uri);
        $action = self::applyGroupNamespace($action);
        $middleware = self::getGroupMiddleware();

        foreach ($methods as $method) {
            $route = [
                'method' => strtoupper($method),
                'uri' => $uri,
                'action' => $action,
                'middleware' => $middleware,
                'name' => $name
            ];

            self::$routes[] = $route;

            // Store named route
            if ($name) {
                self::$namedRoutes[$name] = $route;
            }
        }
    }

    /**
     * Apply group prefix to URI
     */
    private static function applyGroupPrefix(string $uri): string 
    {
        $prefix = '';
        
        foreach (self::$groupStack as $group) {
            if (isset($group['prefix'])) {
                $prefix = rtrim($group['prefix'], '/') . '/' . ltrim($prefix, '/');
            }
        }
        
        return $prefix . $uri;
    }

    /**
     * Apply group namespace to action
     */
    private static function applyGroupNamespace($action) 
    {
        if (is_string($action)) {
            $namespace = '';
            
            foreach (self::$groupStack as $group) {
                if (isset($group['namespace'])) {
                    $namespace = rtrim($group['namespace'], '\\') . '\\' . ltrim($namespace, '\\');
                }
            }
            
            if ($namespace && strpos($action, '\\') !== 0) {
                $action = $namespace . $action;
            }
        }
        
        return $action;
    }

    /**
     * Get middleware from group stack
     */
    private static function getGroupMiddleware(): array 
    {
        $middleware = [];
        
        foreach (self::$groupStack as $group) {
            if (isset($group['middleware'])) {
                $middleware = array_merge($middleware, (array)$group['middleware']);
            }
        }
        
        return $middleware;
    }

    /**
     * Match request to route
     */
    public static function matchRoute(Request $request): ?array 
    {
        $method = $request->method();
        $uri = $request->path();

        foreach (self::$routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = self::convertToRegex($route['uri']);
            
            if (preg_match($pattern, $uri, $matches)) {
                // Extract parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (is_string($key)) {
                        $params[$key] = $value;
                    }
                }

                return [
                    'route' => $route,
                    'params' => $params
                ];
            }
        }

        return null;
    }

    /**
     * Convert route URI to regex pattern
     */
    private static function convertToRegex(string $uri): string 
    {
        // Escape forward slashes
        $pattern = preg_quote($uri, '#');
        
        // Replace route parameters with regex patterns
        $pattern = preg_replace('/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\\\}/', '(?P<$1>[^/]+)', $pattern);
        
        // Replace optional parameters
        $pattern = preg_replace('/\\\{([a-zA-Z_][a-zA-Z0-9_]*)\?\\\}/', '(?P<$1>[^/]*)?', $pattern);
        
        return '#^' . $pattern . '$#';
    }

    /**
     * Dispatch the route
     */
    public static function dispatch(Request $request): Response 
    {
        $match = self::matchRoute($request);
        
        if (!$match) {
            return Response::notFound('Page not found');
        }

        $route = $match['route'];
        $params = $match['params'];

        // Apply middleware
        if (!empty($route['middleware'])) {
            $middlewareResult = self::applyMiddleware($route['middleware'], $request);
            if ($middlewareResult instanceof Response) {
                return $middlewareResult;
            }
        }

        // Execute route action
        return self::executeAction($route['action'], $params, $request);
    }

    /**
     * Apply middleware to request
     */
    private static function applyMiddleware(array $middleware, Request $request): ?Response 
    {
        foreach ($middleware as $middlewareClass) {
            if (!class_exists($middlewareClass)) {
                throw new Exception("Middleware class not found: {$middlewareClass}");
            }

            $middlewareInstance = new $middlewareClass();
            
            if (!method_exists($middlewareInstance, 'handle')) {
                throw new Exception("Middleware {$middlewareClass} must have handle method");
            }

            $result = $middlewareInstance->handle($request, function ($request) {
                return null;
            });

            if ($result instanceof Response) {
                return $result;
            }
        }

        return null;
    }

    /**
     * Execute route action
     */
    private static function executeAction($action, array $params, Request $request): Response 
    {
        if (is_callable($action)) {
            return call_user_func_array($action, array_merge([$request], array_values($params)));
        }

        if (is_string($action)) {
            return self::executeControllerAction($action, $params, $request);
        }

        throw new Exception("Invalid route action");
    }

    /**
     * Execute controller action
     */
    private static function executeControllerAction(string $action, array $params, Request $request): Response 
    {
        // Parse controller@action syntax
        if (strpos($action, '@') === false) {
            throw new Exception("Invalid controller action format: {$action}");
        }

        list($controller, $method) = explode('@', $action, 2);
        
        // Ensure controller has namespace
        if (strpos($controller, '\\') !== 0) {
            $controller = self::$baseNamespace . $controller;
        }

        // Load controller file
        $controllerFile = self::getControllerFile($controller);
        if (!file_exists($controllerFile)) {
            throw new Exception("Controller file not found: {$controllerFile}");
        }

        require_once $controllerFile;

        if (!class_exists($controller)) {
            throw new Exception("Controller class not found: {$controller}");
        }

        // Create controller instance
        $controllerInstance = new $controller();
        
        if (!method_exists($controllerInstance, $method)) {
            throw new Exception("Controller method not found: {$controller}@{$method}");
        }

        // Execute controller method
        return call_user_func_array(
            [$controllerInstance, $method], 
            array_merge(array_values($params), [$request])
        );
    }

    /**
     * Get controller file path
     */
    private static function getControllerFile(string $controller): string 
    {
        $relativePath = str_replace('App\\Controllers\\', '', $controller);
        $relativePath = str_replace('\\', '/', $relativePath);
        
        return __DIR__ . '/../controllers/' . $relativePath . '.php';
    }

    /**
     * Generate URL for named route
     */
    public static function url(string $name, array $params = []): string 
    {
        if (!isset(self::$namedRoutes[$name])) {
            throw new Exception("Named route not found: {$name}");
        }

        $route = self::$namedRoutes[$name];
        $uri = $route['uri'];

        // Replace route parameters
        foreach ($params as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        // Remove optional parameters that weren't provided
        $uri = preg_replace('/\/\{[^}]+\?\}/', '', $uri);

        return $uri;
    }

    /**
     * Redirect to named route
     */
    public static function redirectToRoute(string $name, array $params = [], int $status = 302): Response 
    {
        $url = self::url($name, $params);
        return Response::redirectTo($url, $status);
    }

    /**
     * Get all registered routes
     */
    public static function getRoutes(): array 
    {
        return self::$routes;
    }

    /**
     * Get all named routes
     */
    public static function getNamedRoutes(): array 
    {
        return self::$namedRoutes;
    }

    /**
     * Clear all routes (for testing)
     */
    public static function clear(): void 
    {
        self::$routes = [];
        self::$namedRoutes = [];
        self::$groupStack = [];
    }

    /**
     * Load routes from file
     */
    public static function loadRoutes(string $routesFile): void 
    {
        if (!file_exists($routesFile)) {
            throw new Exception("Routes file not found: {$routesFile}");
        }

        require $routesFile;
    }
}

/**
 * Route helper functions
 */

if (!function_exists('route')) {
    function route(string $name, array $params = []): string 
    {
        return Route::url($name, $params);
    }
}

if (!function_exists('redirect_to_route')) {
    function redirect_to_route(string $name, array $params = [], int $status = 302): Response 
    {
        return Route::redirectToRoute($name, $params, $status);
    }
}