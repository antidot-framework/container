<?php

declare(strict_types=1);

namespace Antidot\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionMethod;

class Builder
{
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
            'delegators' => $dependencies['delegators'] ?? [],
        ];

        foreach ($dependencies['factories'] ?? [] as $name => $factory) {
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

                throw new InvalidArgumentException('Invalid factory type given.');
            };
        }

        foreach ($dependencies['services'] ?? [] as $name => $service) {
            $this->assertValidService($service);
            if (is_array($service)) {
                $containerConfig['parameters'][$name] = $service['arguments'] ?? [];
                $containerConfig[$name] = $service['class'];
            }

            if (is_string($service)) {
                $containerConfig[$name] = $service;
            }
        }

        return new ContainerConfig($containerConfig);
    }

    private function assertValidService($service): void
    {
        if (is_array($service)) {
            if (false === array_key_exists('class', $service)) {
                throw new InvalidArgumentException(
                    'Invalid Container Configuration, "class" parameter is required for configurable dependencies.'
                );
            }

            $service = $service['class'];
        }

        if (false === is_string($service)) {
            throw new InvalidArgumentException(
                'Invalid Container Configuration, Service "class", Simple or autowired dependencies must have a string value.'
            );
        }
    }
}
