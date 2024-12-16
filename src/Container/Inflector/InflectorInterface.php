<?php

namespace Aimocs\Iis\Flat\Container\Inflector;
interface InflectorInterface
{
    public function getType(): string;
    public function inflect(object $object): void;
    public function invokeMethod(string $name, array $args): InflectorInterface;
    public function invokeMethods(array $methods): InflectorInterface;

}