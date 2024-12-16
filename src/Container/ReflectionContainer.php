<?php

namespace Aimocs\Iis\Flat\Container;

use Psr\Container\ContainerInterface;

class ReflectionContainer implements ContainerInterface
{

    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    private function resolve($class): object
    {

        $reflectionClass = new \ReflectionClass($class);

        $constructor = $reflectionClass->getConstructor();

        if (null == $constructor) {
            return $reflectionClass->newInstance();
        }

        $constructorParams = $constructor->getParameters();

        $classDependencies = $this->resolveClassDependencies($constructorParams);

        $service = $reflectionClass->newInstanceArgs($classDependencies);

        return $service;
    }

    private function resolveClassDependencies(array $constructorParams): array
    {

        $classDependencies = [];

        foreach ($constructorParams as $param) {

            $serviceType = $param->getType();
            $service = $this->container->get($serviceType->getName());

            $classDependencies[] = $service;
        }
        return $classDependencies;
    }

    public function get(string $id)
    {
        return $this->resolve($id);
    }

    public function has(string $id): bool
    {
        return class_exists($id);
    }

}