<?php

namespace Aimocs\Iis\Flat\Routing;

use Aimocs\Iis\Flat\Http\HttpException;
use Aimocs\Iis\Flat\Http\HttpRequestMethodException;
use Aimocs\Iis\Flat\Http\Request;
use Aimocs\Iis\Flat\Router\Dispatcher;
use Aimocs\Iis\Flat\Router\RouteCollector;
use Psr\Container\ContainerInterface;

class Router implements RouterInterface
{

    private array $routes;
    public function dispatch(Request $request, ContainerInterface $container): array
    {
        $routeInfo = $this->extractRouteInfo($request);

        [$handler, $vars] = $routeInfo;

        if (is_array($handler)) {
            [$controllerId, $method] = $handler;
            $controller = $container->get($controllerId);
            $handler = [$controller, $method];
        }
        return [$handler, $vars];
    }

    private function extractRouteInfo(Request $request)
    {
        // create a dispatcher
        $routeCollector =new  RouteCollector();
        foreach($this->routes as $route){
            $routeCollector->addRoute(...$route);
        }
        $dispatcher = new Dispatcher($routeCollector);

        // Dispatch a URI, to obtain the route info
        $routeInfo = $dispatcher->dispatch($request->getPath(),$request->getMethod());


        switch ($routeInfo[0]){
            case Dispatcher::FOUND:
                return [$routeInfo[1],$routeInfo[2]];
            case Dispatcher::METHOD_NOT_ALLOWED:
                $e = new HttpRequestMethodException("The method is not allowed");
                $e->setStatusCode(405);
                throw $e;
            default:
                $e = new HttpException("Not Found");
                throw $e;
        }

    }

    public function setRoutes(array $routes):void
    {
        $this->routes = $routes;
    }
}