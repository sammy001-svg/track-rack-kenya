<?php
namespace App\Core;

/**
 * Pattern router. Routes are registered as "/shop/{slug}" where each
 * {placeholder} becomes a named capture passed to the action in order.
 */
class Router
{
    private array $routes = ['GET' => [], 'POST' => []];
    private $notFoundHandler = null;

    public function get(string $pattern, $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    private function add(string $method, string $pattern, $handler, array $middleware): void
    {
        $this->routes[$method][] = [
            'regex'      => $this->compile($pattern),
            'handler'    => $handler,
            'middleware' => $middleware,
        ];
    }

    public function notFound(callable $handler): void
    {
        $this->notFoundHandler = $handler;
    }

    private function compile(string $pattern): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $pattern);
        return '#^' . rtrim($regex, '/') . '/?$#';
    }

    public function dispatch(string $method, string $uri): void
    {
        $method = strtoupper($method) === 'POST' ? 'POST' : 'GET';
        $path   = '/' . trim(parse_url($uri, PHP_URL_PATH) ?? '/', '/');

        foreach ($this->routes[$method] as $route) {
            if (!preg_match($route['regex'], $path, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $params[] = urldecode($value);
                }
            }

            foreach ($route['middleware'] as $middleware) {
                // A middleware that returns false has already sent its response.
                if ($middleware() === false) {
                    return;
                }
            }

            $this->invoke($route['handler'], $params);
            return;
        }

        if ($this->notFoundHandler !== null) {
            ($this->notFoundHandler)();
            return;
        }

        http_response_code(404);
        echo 'Not Found';
    }

    private function invoke($handler, array $params): void
    {
        if (is_callable($handler)) {
            $handler(...$params);
            return;
        }

        // "App\Controllers\ShopController@index"
        [$class, $action] = explode('@', $handler, 2);
        $controller = new $class();
        $controller->$action(...$params);
    }
}
