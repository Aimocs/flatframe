<?php

namespace Aimocs\Iis\Flat\Router;

class RouteCollector {
    private array $routes = [];
    private array $groups = [];

    public function addRoute(string $httpMethod, string $path, mixed $handler, ?string $parent = null): void {
        $this->routes[] = new Route($httpMethod, $path, $handler, $parent);
    }

    public function group(string $prefix, callable $callback): void {
        $this->groups[] = $prefix;

        $currentGroup = $prefix;

        $callback($this);

        array_pop($this->groups);
    }
    public function getRoutes(): \Generator {
        yield from $this->routes;
    }
//    public function getRoutes(): array {
//        $finalRoutes = [];
//
//        foreach ($this->routes as $route) {
//            $path = $route->path;
//
//            // If the route has a parent (group), prepend the group prefix
//            if ($route->parent !== null) {
//                $path = $this->getGroupPrefix($route->parent) . $path;
//            }
//
//            $finalRoutes[] = new Route($route->httpMethod, $path, $route->handler);
//        }
//
//        return $finalRoutes;
//    }

    private function getGroupPrefix(string $groupName): string {
        return $groupName;
    }
}
