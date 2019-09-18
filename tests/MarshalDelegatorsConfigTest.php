<?php

declare(strict_types=1);

namespace AntidotTest\Container;

use Antidot\Container\Container;
use Antidot\Container\ContainerConfig;
use Antidot\Container\ContainerDelegatorFactory;
use Antidot\Container\MarshalDelegatorsConfig;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class MarshalDelegatorsConfigTest extends TestCase
{
    public function testItShouldPrepareContainerConfigFromMarshalledDelegatorFactories(): void
    {
        $config = new ContainerConfig([
            'config' => [],
            'parameters' => [],
            'some.service' => \SplStack::class,
            'delegators' => [
                'some.service' => [
                    function (ContainerInterface $container, string $name, callable $callback): \SplStack {
                        /** @var \SplStack $stack */
                        $stack = $callback();
                        $stack->push('Hello World!!!');

                        return $stack;
                    }
                ]
            ],
        ]);

        $marshallDelegatorConfig = new MarshalDelegatorsConfig();
        $marshalledConfig = $marshallDelegatorConfig($config);
        $this->assertTrue($marshalledConfig->has('some.service'));
        $this->assertIsCallable($marshalledConfig->get('some.service'));
        $this->assertTrue($marshalledConfig->has('delegators'));
        $delegators = $marshalledConfig->get('delegators');
        $this->assertArrayHasKey('some.service', $delegators);
        $this->assertIsCallable($delegators['some.service'][0]);
        $this->assertTrue($marshalledConfig->has(ContainerDelegatorFactory::class));
        $this->assertIsCallable($marshalledConfig->get(ContainerDelegatorFactory::class));
    }
}
