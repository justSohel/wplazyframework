<?php

namespace WPLazy\Framework\Http;

class Router
{
    /** @var Route[] */
    protected static array $routes = [];

    public static function add(string $method, string $uri, $action): Route
    {
        $route = new Route($method, $uri, $action);
        self::$routes[] = $route;
        return $route;
    }

    // Helpers for HTTP verbs
    public static function get(string $uri, $action): Route     { return self::add('GET', $uri, $action); }
    public static function post(string $uri, $action): Route    { return self::add('POST', $uri, $action); }
    public static function put(string $uri, $action): Route     { return self::add('PUT', $uri, $action); }
    public static function patch(string $uri, $action): Route   { return self::add('PATCH', $uri, $action); }
    public static function delete(string $uri, $action): Route  { return self::add('DELETE', $uri, $action); }

    public static function register(string $namespace): void
    {
        // Match any path, let dispatch handle it
        register_rest_route($namespace, '/(?P<path>.+)', [
            'methods'             => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
            'callback'            => [self::class, 'dispatch'],
            'permission_callback' => '__return_true',
        ]);
    }

    public static function dispatch(\WP_REST_Request $request)
    {
        $method = $request->get_method();
        $uri    = '/' . ltrim($request['path'], '/');

        foreach (self::$routes as $route) {
            if ($route->method === $method && $route->uri === $uri) {

                // Middleware pipeline
                foreach ($route->middlewares as $mwClass) {
                    $mw = new $mwClass();
                    if (method_exists($mw, 'handle')) {
                        $result = $mw->handle($request);
                        if ($result !== true) {
                            return rest_ensure_response($result);
                        }
                    }
                }

                // Controller or closure dispatch
                if (is_array($route->action)) {
                    [$controller, $method] = $route->action;
                    $instance = new $controller();
                    return rest_ensure_response($instance->$method($request));
                }

                return rest_ensure_response(call_user_func($route->action, $request));
            }
        }

        return new \WP_Error('not_found', 'Route not found', ['status' => 404]);
    }
}