<?php

namespace Aimocs\Iis\Flat\Container;

use Aimocs\Iis\Flat\Container\Inflector\Inflector;
use Aimocs\Iis\Flat\Container\Inflector\InflectorInterface;
use Aimocs\Iis\Flat\Container\Service\Service;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];
    private mixed $reflector = null;

    private array $delegates = [];

    private array $inflectors = [];

    public function add(string $id, string|object $concrete = null)
    {
        $serviceObject = new Service($id,$concrete,$this);
        $this->services[] = $serviceObject;
        return $serviceObject;
    }

    public function delegate(ContainerInterface $container)
    {
        $this->delegates[]=$container;
        return $this;
        
    }

    public function inflector(string $type, ?callable $callback = null):InflectorInterface
    {

        $inflector = new Inflector($type, $callback,$this);
        $this->inflectors[] = $inflector;
        return $inflector;
    }
    public function addShared(string $id, string|object $concrete = null)
    {
       $serviceObject =  $this->add($id,$concrete);
       $serviceObject->setShared();
       return $serviceObject;
    }

    public function get(string $id)
    {
        return $this->resolve($id);
    }

    public function getNew($id)
    {
        return $this->resolve($id,true);
    }

    /**
     * @throws ContainerException
     */
    public function resolve(string $id, bool $new = false)
    {
        if($this->has($id)){
            $service = $this->getService($id);
            $resolved = ($new === true) ? $service->resolveNew():$service->resolve();
            //later maybe we add inflector
            return $resolved;
        }
        foreach ($this->delegates as $delegate) {
            if ($delegate->has($id)) {
                $resolved = $delegate->get($id);
//                return $resolved;
                return $this->inflect($resolved);
            }
        }
        throw new ContainerException("ERROR {$id} not managed by container.");
    }

    public function has(string $id): bool
    {
        return $this->getService($id)==null ? false: true;
    }


    public function getService(string $id)
    {
        foreach($this->getIterator() as $service){
            if($service->getAlias() == $id){
                return $service;
            }
        }
        return null;
    }

    public function getIterator():\Generator
    {
        yield from $this->services;
    }

    public function inflect($object)
    {
        foreach($this->getIteratorInflector() as $inflector){

            $type = $inflector->getType();
            dump($type);
            if($object instanceof $type){
                $inflector->inflect($object);
            }

        }
        return $object;
        //  need to check if the inflector is for the passed object so the function name should change and other logic changes
    }
    public function getIteratorInflector(): \Generator
    {
        yield from $this->inflectors;
    }

}
