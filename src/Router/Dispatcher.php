<?php

namespace Aimocs\Iis\Flat\Router;

class Dispatcher
{
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;
    private RouteCollector $routeCollector;
    public function __construct(RouteCollector $routeCollector )
    {
        $this->routeCollector = $routeCollector;
    }
    private function parser(string $template, string $url):?array
    {
        $parser = new RouteParser();
        return $parser->parse($template,$url);
    }
    public function dispatch(string $path,string $method):array
    {
        $foundFlag = false;
        $result = [];
        // check if any routes in the routeCollector match with the arguments

        // return array
        foreach($this->routeCollector->getRoutes() as $route){
            $info = $this->parser($route->path, $path);
            if($info!== null){
                $foundFlag = true;
            }

            if($foundFlag) {
               if($method === $route->httpMethod) {

                   // adding status code
                   $result[]=self::FOUND;
                   // adding controller/handler
                   $result[]=$route->handler;
                   // adding arguments for controller/handler
                   $result[]=$info['arguments'];

                   return $result;
               }else{
                   $result[]=self::METHOD_NOT_ALLOWED;
                   return $result;
               }
            }
        }
        $result[]=self::NOT_FOUND;
        return $result;

    }

}