<?php

namespace Core;

use Core\Middleware\Middleware;

class Router
{
    protected $routes = [];

    public function add($method, $uri, $controller)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'middleware' => null
        ];

        return $this;
    }

    public function get($uri, $controller)
    {
        return $this->add('GET', $uri, $controller);
    }

    public function post($uri, $controller)
    {
        return $this->add('POST', $uri, $controller);
    }

    public function delete($uri, $controller)
    {
        return $this->add('DELETE', $uri, $controller);
    }

    public function patch($uri, $controller)
    {
        return $this->add('PATCH', $uri, $controller);
    }

    public function put($uri, $controller)
    {
        return $this->add('PUT', $uri, $controller);
    }

    public function only($key)
    {
        $this->routes[array_key_last($this->routes)]['middleware'] = $key;
    
        return $this;
    }

    public function route($uri, $method) {
        foreach ($this->routes as $route) {
            // 1. Convert route URI to a regex pattern
            // Example: /post/{id} -> #^/post/([^/]+)$#
            $pattern = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<$1>[^/]+)', $route['uri']);
            $pattern = '#^' . $pattern . '$#'; // Add delimiters and anchors

            // 2. Check if the current request URI matches the pattern
            if (preg_match($pattern, $uri, $matches)) {
                // 3. Check if the method matches
                if ($route['method'] === strtoupper($method)) {
                    // Check middleware
                    Middleware::resolve($route['middleware']);

                    // 4. Extract parameters from the URI
                    // $matches will contain named capture groups, e.g., $matches['id']
                    $params = [];
                    foreach ($matches as $key => $value) {
                        if (is_string($key)) { // Only keep named captures
                            $params[$key] = $value;
                        }
                    }

                    // 5. Make parameters available (e.g., merge into $_GET)
                    // This allows controllers using $_GET['param'] to work without immediate changes
                    $_GET = array_merge($_GET, $params); 

                    // 6. Require the controller
                    return require base_path($route['controller']);
                }
            }
        }

        $this->abort();
    }

    protected function abort($code = 404)
    {
        http_response_code($code);
        require base_path("views/{$code}.php");
        die();
    }
}