<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Container implements ContainerInterface
{
    private $autowire;
    private $loadedDependencies;
    private $configuredDependencies;
    private $parameters;

    public function __construct(array $configuredDependencies, bool $autowire)
    {
        $this->configuredDependencies = $configuredDependencies;
        $this->loadedDependencies = ['config' => $configuredDependencies['config']];
        $this->parameters = $configuredDependencies['parameters'];
        $this->autowire = $autowire;
    }

    public function get($id)
    {
        if (array_key_exists($id, $this->loadedDependencies)) {
            return $this->loadedDependencies[$id];
        }

        if ($this->autowire) {
            $this->setInstanceOf($id);
            return $this->loadedDependencies[$id];
        }

        $this->loadedDependencies[$id] = $this->getService($id);

        return $this->loadedDependencies[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->loadedDependencies)
            || array_key_exists($id, $this->configuredDependencies);
    }

    private function getService(string $id)
    {
        if (is_callable($this->configuredDependencies[$id])) {
            $callableService = $this->configuredDependencies[$id];
            return $callableService($this);
        }

        if (is_object($this->configuredDependencies[$id])) {
            return $this->configuredDependencies[$id];
        }

        throw ServiceNotFoundException::forId($id);
    }

    private function setInstanceOf(string $id): void
    {
        if (array_key_exists($id, $this->configuredDependencies)) {
            if (is_callable($this->configuredDependencies[$id])) {
                $callable = $this->configuredDependencies[$id];
                $this->loadedDependencies[$id] = $callable($this);
                return;
            }

            if (is_string($this->configuredDependencies[$id]) && class_exists($this->configuredDependencies[$id])) {
                $this->loadedDependencies[$id] = $this->getAnInstanceOf($id, $this->configuredDependencies[$id]);
                return;
            }

            if (is_string($this->configuredDependencies[$id]) && $this->has($this->configuredDependencies[$id])) {
                $this->loadedDependencies[$id] = $this->get($this->configuredDependencies[$id]);
                return;
            }
        }

        if (class_exists($id)) {
            $this->loadedDependencies[$id] = $this->getAnInstanceOf($id, $id);
        }
    }

    private function getAnInstanceOf(string $id, string $className)
    {
        $this->parameters[$id] = $this->parameters[$id] ?? [];
        $instance = new ReflectionClass($className);
        if (false === $instance->hasMethod('__construct')) {
            return $instance->newInstance();
        }

        $parameters = (new ReflectionMethod($className, '__construct'))->getParameters();

        $this->parameters[$id] = array_map(function (ReflectionParameter $parameter) use ($id) {
            $type = null === $parameter->getType() ? '' : $parameter->getType()->getName();
            if (array_key_exists($parameter->getName(), $this->parameters[$id])) {
                if (is_array($this->parameters[$id][$parameter->getName()])
                    || $this->parameters[$id][$parameter->getName()] instanceof $type) {
                    return $this->parameters[$id][$parameter->getName()];
                }

                if ($this->has($this->parameters[$id][$parameter->getName()])) {
                    return $this->get($this->parameters[$id][$parameter->getName()]);
                }

                return $this->parameters[$id][$parameter->getName()];
            }

            if ($this->has($type)) {
                return $this->get($type);
            }

            if ($parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            if ('' === $type) {
                throw AutowiringException::withNoType($id, $parameter->getName());
            }

            return $this->getAnInstanceOf($type, $type);
        }, $parameters);

        return $instance->newInstanceArgs($this->parameters[$id]);
    }
}
