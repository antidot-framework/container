<?php

namespace AntidotTest\Container;

use Psr\Container\ContainerInterface;

class SomeTestFactory
{
    public function __invoke(ContainerInterface $container, string $foo = 'bar'): SomeTestClass
    {
        return $container->get(SomeTestClass::class);
    }
}
