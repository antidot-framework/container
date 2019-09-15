<?php

declare(strict_types=1);

namespace AntidotTest\Container;

use Antidot\Container\AutowiringException;
use Antidot\Container\Container;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use SplObjectStorage;
use SplQueue;
use SplStack;

class ContainerTest extends TestCase
{
    public function testItShouldHaveConfiguredInstancesWithAutowiringEnabled(): void
    {
        $container = new Container([
            'config' => [],
            'parameters' => [],
            'some.service' => SplObjectStorage::class,
            'some.other.service' => SplQueue::class,
        ], true);

        $this->assertEquals([], $container->get('config'));
        $testService = $container->get('some.service');
        $this->assertInstanceOf(SplObjectStorage::class, $testService);
        $this->assertEquals($testService, $container->get('some.service'));
        $this->assertInstanceOf(SplQueue::class, $container->get('some.other.service'));
    }

    public function testItShouldHaveConfiguredInstancesFromCallablesWithOutAutowiringEnabled(): void
    {
        $container = new Container([
            'config' => [],
            'parameters' => [],
            'some.service' => function (ContainerInterface $container) {
                $this->assertInstanceOf(Container::class, $container);
                return new SplObjectStorage();
            },
            'some.other.service' => new SplQueue(),
            'some.other.type.service' => new class
            {
                public function __invoke(ContainerInterface $container)
                {
                    return new SplStack();
                }

            },
        ], false);

        $this->assertEquals([], $container->get('config'));
        $this->assertInstanceOf(SplObjectStorage::class, $container->get('some.service'));
        $this->assertInstanceOf(SplQueue::class, $container->get('some.other.service'));
        $this->assertInstanceOf(SplStack::class, $container->get('some.other.type.service'));
    }

    public function testItShouldKnowAllConfiguredAndLoadedServices(): void
    {
        $container = new Container([
            'config' => [],
            'parameters' => [],
            'some.service' => function (ContainerInterface $container) {
                $this->assertInstanceOf(Container::class, $container);
                return new SplObjectStorage();
            },
        ], false);

        $this->assertEquals([], $container->get('config'));
        $this->assertTrue($container->has('some.service'));
        $this->assertFalse($container->has('some.other.service'));
    }

    public function testItShouldDistinctBetweenFactoryAndService(): void
    {
        $container = new Container([
            'config' => [],
            'parameters' => [
                'some.other.service' => [
                    'queue' => 'some.other.type.service',
                    'bar' => 'buzz',
                    'bazz' => ['Hello World']
                ],
            ],
            'some.service' => function (ContainerInterface $container) {
                $this->assertInstanceOf(Container::class, $container);
                return new SplObjectStorage();
            },
            'some.other.service' => SomeTestClass::class,
            'some.other.type.service' => SplQueue::class,
            'some.other.service.class' => SplQueue::class,
            'some.alias' => 'some.other.type.service',
            SplStack::class => SplStack::class,
        ], true);

        $container->get('some.alias');
        $this->assertEquals([], $container->get('config'));
        $this->assertInstanceOf(SplObjectStorage::class, $container->get('some.service'));
        $this->assertInstanceOf(SplQueue::class, $container->get('some.other.type.service'));
        $testService = $container->get('some.other.service');
        $this->assertInstanceOf(SomeTestClass::class, $testService);
        $this->assertInstanceOf(SplQueue::class, $testService->getQueue());
        $this->assertInstanceOf(SplStack::class, $testService->getStack());
        $this->assertSame($testService->getQueue(), $container->get('some.other.type.service'));
        $this->assertSame($testService->getQueue(), $container->get('some.alias'));
        $this->assertNotSame($testService->getQueue(), $container->get('some.other.service.class'));
        $someTestClass2 = $container->get(SomeTestClass::class);
        $this->assertNotSame($testService, $someTestClass2);
        $this->assertSame($testService->getStack(), $someTestClass2->getStack());
    }

    public function testItShouldThrowExceptionWhenAreNotConfiguredInstancesOrCallablesWithOutAutowiringEnabled(): void
    {
        $this->expectException(NotFoundExceptionInterface::class);

        $container = new Container([
            'config' => [],
            'parameters' => [],
            'some.service' => SplObjectStorage::class,
        ], false);

        $container->get('some.service');
    }

    public function testItShouldThrowExceptionWhenWithAutowiringEnabled(): void
    {
        $this->expectException(AutowiringException::class);

        $container = new Container([
            'config' => [],
            'parameters' => [],
        ], true);

        $container->get(InvalidArgumentException::class);
    }
}
