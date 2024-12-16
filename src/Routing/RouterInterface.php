<?php

namespace Aimocs\Iis\Flat\Routing;

use Aimocs\Iis\Flat\Http\Request;
use Psr\Container\ContainerInterface;

interface RouterInterface
{

    public function dispatch(Request $request,ContainerInterface $container);

    public function setRoutes(array $routes):void;

}