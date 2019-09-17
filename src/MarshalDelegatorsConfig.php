<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;
use function is_callable;

/**
 *
 * Class MarshalDelegatorsConfig
 * @package Antidot\Container
 */
class MarshalDelegatorsConfig
{
    public function __invoke(ContainerInterface $container, ContainerConfig $dependencies): ContainerConfig
    {
        foreach ($dependencies->get('delegators') as $service => $delegatorNames) {
            $factory = $this->delegateFactories($container, $dependencies, $service);
            if (!is_callable($factory)) {
                continue;
            }
            $dependencies->set(ContainerDelegatorFactory::class, static function () use ($delegatorNames, $factory) {
                return new ContainerDelegatorFactory($delegatorNames, $factory);
            });
            $dependencies->set(
                $service,
                static function (ContainerInterface $container) use ($service) {
                    $callable = $container->get(ContainerDelegatorFactory::class);
                    return $callable($container, $service);
                }
            );
        }

        return $dependencies;
    }

    private function delegateFactories(
        ContainerInterface $container,
        ContainerConfig $dependencies,
        string $service
    ): ?callable {
        if (false === $dependencies->has($service)) {
            return null;
        }
        // Marshal from factory
        $serviceFactory = $dependencies->get($service);
        return static function () use ($service, $serviceFactory, $container) {
            return is_callable($serviceFactory)
                ? $serviceFactory($container, $service)
                : (new $serviceFactory())($container, $service);
        };
    }
}
