<?php

namespace WPLazy\Framework;

use WPLazy\Framework\Http\Router;

class Kernel
{
    protected string $namespace;
    protected string $routesFile;

    public function __construct(string $namespace = 'myplugin/v1', string $routesFile = '')
    {
        $this->namespace  = $namespace;
        $this->routesFile = $routesFile ?: __DIR__ . '/../../../../app/routes/api.php';
    }

    public function bootstrap(): void
    {
        add_action('rest_api_init', function () {
            Router::init();

            if (file_exists($this->routesFile)) {
                require $this->routesFile;
            }

            register_rest_route($this->namespace, '/(?P<path>.+)', [
                'methods'  => ['GET', 'POST'],
                'callback' => [$this, 'dispatch'],
                'permission_callback' => '__return_true',
            ]);
        });
    }

    public function dispatch(\WP_REST_Request $request)
    {
        $method = $request->get_method();
        $uri    = '/' . ltrim($request['path'], '/');

        return Router::dispatch($method, $uri);
    }
}
