<?php

namespace Aimocs\Iis\Flat\Container\Argument;

class LiteralArgument implements LiteralArgumentInterface
{
    private mixed $value ;
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

}