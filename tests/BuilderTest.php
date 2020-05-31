<?php

declare(strict_types=1);

namespace AntidotTest\Container;

use Antidot\Container\Container;
use Antidot\Container\Builder as ContainerBuilder;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use SplStack;

class BuilderTest extends TestCase
{
    public function testItShouldCreateAConfiguredContainerFromFrameworkConfig(): void
    {
        $container = ContainerBuilder::build([
            'services' => [],
            'factories' => [],
            'config' => []
        ]);

        $this->assertInstanceOf(Container::class, $container);
    }

    public function testItShouldThrowInvalidArgumentExceptionWithInvalidSimpleServiceConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContainerBuilder::build([
            'services' => [
                'some.service' => function () {
                }
            ],
            'factories' => [],
            'config' => []
        ]);
    }

    public function testItShouldThrowInvalidArgumentExceptionWithInvalidServiceClassConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContainerBuilder::build([
            'services' => [
                'some.service' => [
                    'class' => function () {
                    }
                ]
            ],
            'factories' => [],
            'config' => []
        ]);
    }

    public function testItShouldThrowInvalidArgumentExceptionWithInvalidServiceConfig(): void
    {
        $this->expectException(InvalidArgumentException::class);
        ContainerBuilder::build([
            'services' => [
                'some.service' => [
                    function () {
                    }
                ]
            ],
            'factories' => [],
            'config' => []
        ]);
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithInvokables(): void
    {
        $container = ContainerBuilder::build([
            'services' => [
                SplStack::class => SplStack::class,
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(SplStack::class, $container->get(SplStack::class));
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithFactories(): void
    {
        $container = ContainerBuilder::build([
            'factories' => [
                'some.test.class' => SomeTestFactory::class,
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(SomeTestClass::class, $container->get('some.test.class'));
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithComplexFactories(): void
    {
        $container = ContainerBuilder::build([
            'factories' => [
                'some.test.class' => [SomeTestFactory::class, 'buzz'],
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(SomeTestClass::class, $container->get('some.test.class'));
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithAnonymousFunctionFactories(): void
    {
        $container = ContainerBuilder::build([
            'factories' => [
                'some.test.class' => function (ContainerInterface $container) {
                    return new SplStack();
                },
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(SplStack::class, $container->get('some.test.class'));
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithConditionals(): void
    {
        $container = ContainerBuilder::build([
            'services' => [
                InvalidArgumentException::class => [
                    'class' => InvalidArgumentException::class,
                    'arguments' => [
                        'message' => 'Oh Oh!!',
                        'code' => 0,
                        'previous' => null,
                    ]
                ],
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(InvalidArgumentException::class, $container->get(InvalidArgumentException::class));
    }

    public function testItShouldCreateAConfiguredContainerFromFrameworkConfigWithAliases(): void
    {
        $container = ContainerBuilder::build([
            'services' => [
                SplStack::class => SplStack::class,
                'some.alias' => SplStack::class,
            ],
            'config' => []
        ], true);

        $this->assertInstanceOf(Container::class, $container);
        $this->assertInstanceOf(SplStack::class, $container->get('some.alias'));
    }
}
