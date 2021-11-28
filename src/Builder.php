<?php

declare(strict_types=1);

namespace Antidot\Container;

use InvalidArgumentException;
use Psr\Container\ContainerInterface;
use ReflectionMethod;

use function array_key_exists;
use function array_merge;
use function sprintf;

class Builder
{
    /**
     * @param array<mixed> $dependencies
     */
    public static function build(array $dependencies, bool $autowire = false): ContainerInterface
    {
        $self = new self();

        return new Container(
            $self->parseConfigFor($dependencies),
            $autowire
        );
    }

    /**
     * @param array<mixed> $dependencies
     */
    private function parseConfigFor(array $dependencies): ContainerConfig
    {
        $containerConfig = [
            'config' => $dependencies,
            'parameters' => [],
            'delegators' => $dependencies['delegators'] ?? [],
        ];

        $factories = array_merge(
            $dependencies['factories'] ?? [],
            $dependencies['dependencies']['factories'] ?? []
        );

        foreach ($factories as $name => $factory) {
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
                    /** @var callable $callable */
                    $callable = new $factory();
                    return $callable($container);
                }

                throw new InvalidArgumentException('Invalid factory type given.');
            };
        }

        $services = array_merge(
            $dependencies['services'] ?? [],
            $dependencies['invokables'] ?? [],
            $dependencies['dependencies']['invokables'] ?? []
        );

        foreach ($services as $name => $service) {
            $this->assertValidService($service);
            if (is_array($service)) {
                $containerConfig['parameters'][$name] = $service['arguments'] ?? [];
                $containerConfig[$name] = $service['class'];
            }

            if (is_string($service)) {
                $containerConfig[$name] = $service;
            }
        }

        $aliases = array_merge(
            $dependencies['aliases'] ?? [],
            $dependencies['dependencies']['aliases'] ?? []
        );
        foreach ($aliases as $alias => $service) {
            $this->assertValidAlias($service, $containerConfig);
            $containerConfig[$alias] = $service;
        }

        return new ContainerConfig($containerConfig);
    }

    /**
     * @param mixed $service
     */
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
                'Invalid Container Configuration, Service "class", Simple or autowired dependencies must have'
                . ' a string value.'
            );
        }
    }

    /**
     * @param string $service
     * @param array<int|string, string> $containerConfig
     */
    private function assertValidAlias(string $service, array $containerConfig): void
    {
        if (false === array_key_exists($service, $containerConfig)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid Alias "%s" given, be sure that the service already exists.',
                $service
            ));
        }
    }
}
