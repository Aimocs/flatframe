<?php

namespace Aimocs\Iis\Flat\Container\Service;

interface ServiceInterface
{
    public function addArgument($arg);
    public function addArguments(array $args);
    public function getAlias(): string;
    public function getConcrete();
    public function isShared(): bool;
    public function resolve();
    public function setAlias(string $id);
    public function setConcrete($concrete);
    public function setShared(bool $shared);

}