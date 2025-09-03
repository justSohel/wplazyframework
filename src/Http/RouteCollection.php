<?php

namespace WPLazy\Framework\Http;

class RouteCollection
{
    /** @var Route[] */
    protected array $routes = [];

    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    public function all(): array
    {
        return $this->routes;
    }
}