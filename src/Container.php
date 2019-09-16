<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private $autowire;
    private $loadedDependencies;
    private $configuredDependencies;
    /** @var InstanceResolver */
    private $resolver;

    public function __construct(array $configuredDependencies, bool $autowire)
    {
        $this->configuredDependencies = new ContainerConfig($configuredDependencies);
        $this->loadedDependencies = new InstanceCollection();
        $this->loadedDependencies->set('config', $configuredDependencies['config']);
        $this->autowire = $autowire;
    }

    public function get($id)
    {
        if ($this->loadedDependencies->has($id)) {
            return $this->loadedDependencies->get($id);
        }

        if ($this->autowire) {
            $this->setInstanceResolver();
            $this->resolver->setInstanceOf($id);
            return $this->loadedDependencies->get($id);
        }

        $this->loadedDependencies->set($id, $this->getService($id));

        return $this->loadedDependencies->get($id);
    }

    public function has($id): bool
    {
        return $this->loadedDependencies->has($id)
            || $this->configuredDependencies->has($id);
    }

    private function getService(string $id)
    {
        if (is_callable($this->configuredDependencies->get($id))) {
            $callableService = $this->configuredDependencies->get($id);
            return $callableService($this);
        }

        if (is_object($this->configuredDependencies->get($id))) {
            return $this->configuredDependencies->get($id);
        }

        throw ServiceNotFoundException::forId($id);
    }

    private function setInstanceResolver(): void
    {
        if (null === $this->resolver) {
            $this->resolver = new InstanceResolver($this->configuredDependencies, $this->loadedDependencies, $this);
        }
    }
}
