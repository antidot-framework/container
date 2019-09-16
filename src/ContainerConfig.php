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
}
