<?php

namespace Dambo\Framework\Tests;

use Dambo\Framework\Container\Container;
use Dambo\Framework\Container\ContainerException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ContainerTest extends TestCase
{
    #[Test]
    public function a_service_can_be_retrieved_from_the_container()
    {
        $container = new Container();
        $container->add('dependant-class', DependantClass::class);
        $this->assertInstanceOf(DependantClass::class, $container->get('dependant-class'));
    }
    #[Test]
    public function a_container_exception_is_thrown_if_a_service_cannot_be_found()
    {
        $container = new Container();
        $this->expectException(ContainerException::class);
        $container->add('1234');
    }
    #[Test]
    public function can_check_if_container_has_a_service()
    {
        $container = new Container();
        $container->add('dependant-class', DependantClass::class);
        $this->assertTrue($container->has('dependant-class'));
        $this->assertFalse($container->has('non-existent-class'));
    }
    #[Test]
    public function services_can_be_recursively_autowired()
    {
        $container = new Container();
        $container->add('dependant-service', DependantClass::class);
        $dependantService = $container->get('dependant-service');
        $this->assertInstanceOf(DependencyClass::class, $dependantService->getDependency());
    }



}