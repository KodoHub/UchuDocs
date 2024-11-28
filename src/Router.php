<?php

namespace Documentation;

class Router
{
    private $routes = [];
    private $namedRoutes = [];

    /**
     * Register a route with a given method and URL pattern
     *
     * @param string $method HTTP method (GET, POST, etc.)
     * @param string $pattern URL pattern
     * @param callable $handler Route handler (function or controller action)
     * @param string|null $name Optional route name for easier reference
     */
    public function register(string $method, string $pattern, callable $handler, ?string $name = null) {
        $this->routes[$method][$this->compilePattern($pattern)] = $handler;

        // Optionally store a named route
        if ($name) {
            $this->namedRoutes[$name] = $pattern;
        }
    }

    /**
     * Resolve the current request URI and HTTP method to a route
     *
     * @param string $requestUri The current request URI
     * @param string $method The HTTP method (GET, POST, etc.)
     * @return array Route information or null if no match found
     */
    public function resolve(string $requestUri, string $method = 'GET'): ?array {
        // Remove query string
        $path = parse_url($requestUri, PHP_URL_PATH);
        $path = trim($path, '/');

        // Check if the route matches the registered routes
        foreach ($this->routes[$method] as $pattern => $handler) {
            if (preg_match($pattern, $path, $matches)) {
                // Extract the matched parameters from the URL pattern
                array_shift($matches);
                return [
                    'handler' => $handler,
                    'params' => $matches
                ];
            }
        }

        return [
            'handler' => 'error404',
            'params' => []
        ];
    }

    /**
     * Generate a URL from a named route
     *
     * @param string $name Route name
     * @param array $params Parameters to replace in the route
     * @return string Generated URL
     * @throws \Exception If the route does not exist
     */
    public function generateUrl(string $name, array $params = []): string {
        if (!isset($this->namedRoutes[$name])) {
            throw new \Exception("Route with name '$name' not found.");
        }

        // Get the route pattern
        $pattern = $this->namedRoutes[$name];

        // Replace parameters in the route pattern
        foreach ($params as $key => $value) {
            $pattern = str_replace('{' . $key . '}', $value, $pattern);
        }

        return '/' . $pattern;
    }

    /**
     * Compile a URL pattern into a regular expression
     *
     * @param string $pattern URL pattern
     * @return string Compiled regular expression
     */
    private function compilePattern(string $pattern): string {
        // Convert dynamic segments (e.g., {id}) into regular expression
        $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $pattern . '$#';
    }

    /**
     * Handle a 404 error when no route is found
     */
    public function error404() {
        http_response_code(404);
        echo "Page Not Found";
    }

    /**
     * Handle the home route (can be customized as needed)
     */
    public function home() {
        echo "Welcome to the Documentation Home Page!";
    }
}
