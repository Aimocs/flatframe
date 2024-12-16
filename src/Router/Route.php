<?php

namespace Aimocs\Iis\Flat\Router;

class Route {
    public function __construct(
        public string $httpMethod,
        public string $path,
        public mixed $handler,
        public ?string $parent = null // Group name or identifier !!!ALSO NOT IN USE CURRENTLY!!!
    ) {}
}