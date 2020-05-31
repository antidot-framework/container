<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;
use ReflectionMethod;

/**
 * Class ContainerBuilder
 * @deprecated This class will be removed in 1.0.0 version
 */
class ContainerBuilder
{
    private function __construct()
    {
    }

    public static function build(array $dependencies, bool $autowire = false): ContainerInterface
    {
        $self = new self();

        return new Container(
            $self->parseConfigFor($dependencies),
            $autowire
        );
    }

    private function parseConfigFor(array $dependencies): ContainerConfig
    {
        $containerConfig = [
            'config' => $dependencies,
            'parameters' => [],
            'delegators' => $dependencies['dependencies']['delegators'] ?? [],
        ];

        foreach ($dependencies['dependencies']['invokables'] ?? [] as $name => $invokable) {
            $containerConfig[$name] = $invokable;
        }
        foreach ($dependencies['dependencies']['aliases'] ?? [] as $name => $service) {
            $containerConfig[$name] = $service;
        }
        foreach ($dependencies['dependencies']['factories'] ?? [] as $name => $factory) {
            $containerConfig[$name] = static function (ContainerInterface $container) use ($factory) {
                if (is_array($factory)) {
                    $class = array_shift($factory);
                    $instance = new $class();
                    $method = new ReflectionMethod($class, '__invoke');
                    return $method->invokeArgs($instance, array_merge([$container], $factory));
                }

                if (is_callable($factory)) {
                    return $factory($container);
                }

                if (class_exists($factory)) {
                    return (new $factory())($container);
                }

                throw new \InvalidArgumentException('Invalid factory type given.');
            };
        }

        foreach ($dependencies['dependencies']['conditionals'] ?? [] as $name => $conditional) {
            $containerConfig['parameters'][$name] = $conditional['arguments'];
            $containerConfig[$name] = $conditional['class'];
        }

        return new ContainerConfig($containerConfig);
    }
}
