<?php

namespace WPLazy\Framework\Http;

class Router
{
    protected static RouteCollection $routes;

    public static function init(): void
    {
        self::$routes = new RouteCollection();
    }

    public static function get(string $uri, $action): Route
    {
        $route = new Route('GET', $uri, $action);
        self::$routes->add($route);
        return $route;
    }

    public static function post(string $uri, $action): Route
    {
        $route = new Route('POST', $uri, $action);
        self::$routes->add($route);
        return $route;
    }

    public static function dispatch(string $method, string $uri)
    {
        foreach (self::$routes->all() as $route) {
            if ($route->method === strtoupper($method) && $route->uri === $uri) {
                // Middleware pipeline
                foreach ($route->middleware as $middlewareClass) {
                    $middleware = new $middlewareClass();
                    if (method_exists($middleware, 'handle')) {
                        $middleware->handle();
                    }
                }

                [$controller, $action] = $route->action;
                $response = (new $controller())->$action();

                return rest_ensure_response($response);
            }
        }

        return new \WP_Error('not_found', 'Route not found', ['status' => 404]);
    }
}
