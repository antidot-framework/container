<?php

declare(strict_types=1);

namespace Antidot\Container;

class ContainerConfig
{
    /** @var array<mixed> */
    private array $config;

    /**
     * @param array<mixed> $config
     */
    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->config[$id];
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->config);
    }

    public function unset(string $id): void
    {
        unset($this->config[$id]);
    }

    /**
     * @param mixed $param
     */
    public function set(string $id, $param): void
    {
        $this->config[$id] = $param;
    }
}
