<?php

namespace Aimocs\Iis\Flat\Http;

// session change
//use followed\framed\Session\SessionInterface;
class Request {
//    private SessionInterface $session;
//    private mixed $routeHandler;
//    private array $routeHandlerArgs;
    public function __construct(
        public readonly array $getParams,
        public readonly array $postParams,
        public readonly array $cookies,
        public readonly array $files,
        public readonly array $server
    ){

    }

    public static function createFromGlobals():static
    {
        return new static($_GET,$_POST,$_COOKIE,$_FILES,$_SERVER)   ;
    }


    public function input($key):mixed
    {
        return $this->postParams[$key];
    }
    public function getPath():string
    {
        return strtok($this->server["REQUEST_URI"],"?");
    }

    public function getMethod():string
    {
       return $this->server["REQUEST_METHOD"];
    }

}