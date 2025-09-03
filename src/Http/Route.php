<?php
namespace WPLazy\Framework\Http;


class Route
{
    public string $method;
    public string $uri;
    public $action;
    public array $middleware = [];

    public function __construct(string $method, string $uri, $action)
    {
        $this->method = strtoupper($method);
        $this->uri = $uri;
        $this->action = $action;
    }

    public function middleware(string $middleware): self
    {
        $this->middleware[] = $middleware;
        return $this;
    }

}
