<?php

declare(strict_types=1);

namespace Antidot\Container;

class ContainerConfig
{
    private $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function get(string $id)
    {
        return $this->config[$id];
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->config);
    }

    public function unset(string $id): void
    {
        unset($this->config[$id]);
    }

    public function set(string $id, $param): void
    {
        $this->config[$id] = $param;
    }
}
