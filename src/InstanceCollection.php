<?php

declare(strict_types=1);

namespace Antidot\Container;

class InstanceCollection
{
    /** @var array<mixed>  */
    private array $instances;

    public function __construct()
    {
        $this->instances = [];
    }

    /**
     * @param mixed $instance
     */
    public function set(string $id, $instance): void
    {
        $this->instances[$id] = $instance;
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->instances);
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->instances[$id];
    }
}
