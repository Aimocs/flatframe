<?php

namespace Aimocs\Iis\Tests\ContainerTest;

class CTDependantClass
{

    public function __construct(private CTDependencyClass $depen)
    {
    }
    public function check(){
        return $this->depen;
    }
}