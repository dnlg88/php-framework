<?php

namespace Dambo\Framework\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    public function add(string $id, string|object $concrete = null)
    {
        if (is_null($concrete))
        {
            if (!class_exists($id))
            {
                throw new ContainerException("Service $id could not be found");
            }
            $concrete = $id;
        }
        $this->services[$id] = $concrete;
    }
    public function get(string $id)
    {
        if (!$this->has($id))
        {
            if (!class_exists($id))
            {
                throw new ContainerException("Service $id could not be resolved");
            }
            $this->add($id);
        }
        $object = $this->resolve($this->services[$id]);
        return $object;
    }

    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    private function resolve($class): object
    {
        $reflectionClass = new \ReflectionClass($class);
        $constructor = $reflectionClass->getConstructor();
        if (is_null($constructor))
        {
            return $reflectionClass->newInstance();
        }
        $constructorParams = $constructor->getParameters();
        $classDependencies = $this->resolveDependencies($constructorParams);
        $service = $reflectionClass->newInstanceArgs($classDependencies);
        return $service;

    }

    private function resolveDependencies(array $reflectionParameters): array
    {
        $classDependencies = [];
        /**@var \ReflectionParameter $parameter **/
        foreach ($reflectionParameters as $parameter)
        {
            $serviceType = $parameter->getType();
            $service = $this->get($serviceType->getName());
            $classDependencies[] = $service;
        }
        return $classDependencies;
    }
}