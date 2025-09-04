<?php
namespace WPLazy\Framework\Http;

class Route
{
    public string $method;
    public string $uri;
    public $action;
    public array $middlewares = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri    = '/' . ltrim($uri, '/');
        $this->action = $action;
    }

    public function middleware($middlewares): self
    {
        $this->middlewares = array_merge(
            $this->middlewares,
            is_array($middlewares) ? $middlewares : [$middlewares]
        );
        return $this;
    }
}
