<?php

namespace Aimocs\Iis\Flat\Container\Service;
use Aimocs\Iis\Flat\Container\Argument\LiteralArgumentInterface;
use Aimocs\Iis\Flat\Container\ContainerException;
use Psr\Container\ContainerInterface;

class Service implements ServiceInterface
{

    private bool $shared = false;
    private string $alias;
    private array $arguments = [];
    private mixed $concrete;
    private mixed $resolved = null;

    private array $methods = [];

    private array $recursiveCheck = [];

    private ?ContainerInterface $container;
    public function __construct( string $id , string|object $concrete = null , ?object $container = null)
    {
        $concrete = $concrete ?? $id;
        $this->concrete = $concrete;
        $this->alias = $id;
        $this->container= $container;
    }

    public function getAlias():string
    {
        return $this->alias;
        
    }
    public function addArgument($arg)
    {
        $this->arguments[] = $arg;
        return $this;

    }
    public function addArguments(array $args)
    {
        foreach($args as $arg){
            $this->arguments[] = $arg;
        }

        return $this;
    }
    public function setShared(bool $shared = true)
    {
        $this->shared = $shared;
    }
    public function setAlias(string $id)
    {
       $this->alias = $id;
    }
    public function resolve()
    {
        if (null !== $this->resolved && $this->isShared()) {
            return $this->resolved;
        }
        return $this->resolveNew();
    }

    public function resolveNew()
    {
        $concrete = $this->concrete;
        if (is_callable($concrete)) {
            $concrete = $this->resolveCallable($concrete);
        }

        if ($concrete instanceof LiteralArgumentInterface) {
            $this->resolved = $concrete->getValue();
            return $concrete->getValue();
        }

        if (is_string($concrete) && class_exists($concrete)) {
            $concrete = $this->resolveClass($concrete);
        }

        if (is_object($concrete)) {
            $concrete = $this->invokeMethods($concrete);
        }

// recrsive check maybe add later ?
        if (is_string($concrete) && $this->container instanceof ContainerInterface && $this->container->has($concrete)) {
            $concrete = $this->container->get($concrete);
        }
        $this->resolved = $concrete;
        return $concrete;

    }

    public function getConcrete()
    {
        return $this->concrete;
    }

    public function isShared(): bool
    {
        return $this->shared;
    }

    public function setConcrete($concrete)
    {
        $this->concrete = $concrete;
        $this->resolved = null;
        return $this;
    }

    private function resolveCallable(callable $concrete)
    {
        $arguments = $this->resolveArguments($this->arguments);
        return call_user_func_array($concrete, $arguments);
    }

    private function resolveClass(string $concrete)
    {
        $arguments = $this->resolveArguments($this->arguments);
        $reflection = new \ReflectionClass($concrete);
        return $reflection->newInstanceArgs($arguments);
    }
    public function resolveArguments(array $arguments):array
    {
        if($this->container===null){
            // not it
            return [];
        }
        foreach ($arguments as &$arg) {
            if ($arg instanceof LiteralArgumentInterface) {
                $arg = $arg->getValue();
                continue;
            }else{
                $argValue=$arg;
            }
            if (!is_string($argValue)) {
                continue;
            }

            if ($this->container->has($argValue)) {
                try {
                    $arg = $this->container->get($argValue);

                    if ($arg instanceof LiteralArgumentInterface) {
                        $arg = $arg->getValue();
                    }
                    continue;
                } catch (ContainerException $e) {
                }
            }

        }
        return $arguments;
    }


    public function addMethodCall(string $method, array $args = [])
    {
        $this->methods[] = [
            'method'    => $method,
            'arguments' => $args
        ];

        return $this;
    }

    public function addMethodCalls(array $methods = [])
    {
        foreach ($methods as $method => $args) {
            $this->addMethodCall($method, $args);
        }

        return $this;
    }

    private function invokeMethods(object $instance): object
    {
        foreach ($this->methods as $method) {
            $args = $this->resolveArguments($method['arguments']);
            $callable = [$instance, $method['method']];
            call_user_func_array($callable, $args);
        }

        return $instance;
    }
}