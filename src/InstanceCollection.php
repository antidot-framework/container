<?php

declare(strict_types=1);

namespace Antidot\Container;

class InstanceCollection
{
    private array $instances;

    public function __construct()
    {
        $this->instances = [];
    }

    public function set(string $id, $instance): void
    {
        $this->instances[$id] = $instance;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    public function get(string $id)
    {
        return $this->instances[$id];
    }
}
