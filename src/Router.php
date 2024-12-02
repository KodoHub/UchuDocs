<?php

namespace Documentation;

class Router
{
    private $routes = [];
    private $namedRoutes = [];
    private $notFoundHandler;

    public function __construct(callable $notFoundHandler = null) {
        $this->notFoundHandler = $notFoundHandler ?? [$this, 'default404Handler'];
    }

    /**
     * Register a route with a given method and URL pattern
     */
    public function register(string $method, string $pattern, callable $handler, ?string $name = null): void {
        $method = strtoupper($method);
        $this->routes[$method][$this->compilePattern($pattern)] = $handler;

        if ($name) {
            $this->namedRoutes[$name] = $pattern;
        }
    }

    /**
     * Resolve the current request URI and HTTP method to a route
     */
    public function resolve(string $requestUri, string $method = 'GET'): void {
        $path = trim(parse_url($requestUri, PHP_URL_PATH) ?? '/', '/');
        $method = strtoupper($method);

        if (!isset($this->routes[$method])) {
            ($this->notFoundHandler)();
            return;
        }

        foreach ($this->routes[$method] as $pattern => $handler) {
            if (preg_match($pattern, $path, $matches)) {
                array_shift($matches);
                if (is_callable($handler)) {
                    call_user_func_array($handler, $matches);
                    return;
                } else {
                    throw new \Exception("Route handler for '$pattern' is not callable.");
                }
            }
        }

        ($this->notFoundHandler)();
    }

    /**
     * Generate a URL from a named route
     */
    public function generateUrl(string $name, array $params = []): string {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route with name '$name' not found.");
        }

        $pattern = $this->namedRoutes[$name];
        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }

        // Check if there are any unmatched placeholders
        if (preg_match('/\{[a-zA-Z0-9_]+\}/', $pattern)) {
            throw new \Exception("Missing parameters for route '$name'.");
        }

        return '/' . ltrim($pattern, '/');
    }

    /**
     * Compile a URL pattern into a regular expression
     */
    private function compilePattern(string $pattern): string {
        return '#^' . preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern) . '$#';
    }

    /**
     * Default 404 handler
     */
    private function default404Handler(): void {
        http_response_code(404);
        echo "404 Not Found - The requested page does not exist.";
    }
}
