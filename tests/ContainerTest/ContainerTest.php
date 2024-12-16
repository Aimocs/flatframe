<?php

namespace Aimocs\Iis\Tests\ContainerTest;

use Aimocs\Iis\Flat\Container\Container;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    public function test_a_service_can_be_retrieved_from_the_container()
    {
        //setup
        $sometihng="afdsad";
        $container = new Container();
        // id string, concrete class name string | object
        $container->add('dependant-class',CTDependantClass::class);
        $container->addShared('callable',function () use ($sometihng){return $sometihng;});
        $container->add(CTDependencyClass::class)->addArguments(["yosomeon","NO DISIPLIN"]);
        //hastest
        foreach($container->getIterator() as $yo){
            dump($yo);
        }


    }
    /*
    public function test_a_ContainerException_is_thrown_when_the_service_does_not_exist()
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $container->add('medontexist');
    }
    public function test_a_service_that_will_check_if_service_is_in_services_array(){

        $container = new Container();
        $container->add('dependant-class',CTDependantClass::class);
        $this->assertTrue($container->has('dependant-class'));
        $this->assertFalse($container->has('medontexist'));
    }
    public function test_services_can_be_recursively_autowired()
    {
        $container = new Container();

        $container->add('dependant-class', CTDependantClass::class);

        $dependantService = $container->get('dependant-class');

        $this->assertInstanceOf(CTDependencyClass::class, $dependantService->getDependency());
    }
    */
}