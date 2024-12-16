<?php

namespace Aimocs\Iis\Flat\Container\Inflector;

use Aimocs\Iis\Flat\Container\Argument\LiteralArgumentInterface;
use Aimocs\Iis\Flat\Container\ContainerException;
use Psr\Container\ContainerInterface;

class Inflector implements InflectorInterface
{

    private string $type;

    private mixed  $callback;

    private array $methods=[];

    private ContainerInterface $container;
    public function __construct(string $type, ?callable $callback = null ,?ContainerInterface $container=null)
    {
        $this->type = $type;
        $this->callback = $callback;
        $this->container=$container;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function inflect(object $object): void
    {
        foreach ($this->methods as $method => $args) {
            $args = $this->resolveArguments($args);
            $callable = [$object, $method];
            call_user_func_array($callable, $args);
        }
    }

    public function invokeMethod(string $name, array $args): InflectorInterface
    {
        $this->methods[$name] = $args;
        return $this;
    }

    public function invokeMethods(array $methods): InflectorInterface
    {
        foreach ($methods as $name => $args) {
            $this->invokeMethod($name, $args);
        }
        return $this;
    }

    public function resolveArguments(array $arguments):array
    {
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

}