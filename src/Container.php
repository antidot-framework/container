<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private bool $autowire;
    private InstanceCollection $loadedDependencies;
    private ContainerConfig $configuredDependencies;
    private InstanceResolver $resolver;

    public function __construct(ContainerConfig $configuredDependencies, bool $autowire)
    {
        $this->configuredDependencies = $configuredDependencies->has('delegators')
            ? (new MarshalDelegatorsConfig())($configuredDependencies)
            : $configuredDependencies;
        $this->loadedDependencies = new InstanceCollection();
        $this->loadedDependencies->set('config', $configuredDependencies->get('config'));
        $this->autowire = $autowire;
        $this->resolver = new InstanceResolver($this->configuredDependencies, $this->loadedDependencies, $this);
    }

    public function get(string $id)
    {
        if ($this->loadedDependencies->has($id)) {
            return $this->loadedDependencies->get($id);
        }

        if ($this->autowire) {
            $this->resolver->setInstanceOf($id);
            return $this->loadedDependencies->get($id);
        }

        $this->loadedDependencies->set($id, $this->getService($id));

        return $this->loadedDependencies->get($id);
    }

    public function has(string $id): bool
    {
        return $this->loadedDependencies->has($id)
            || $this->configuredDependencies->has($id);
    }

    private function getService(string $id): object
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
}
