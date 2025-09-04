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
            if ($this->routesFile && file_exists($this->routesFile)) {
                require $this->routesFile;
            }

            Router::register($this->namespace);
        });
    }
}
