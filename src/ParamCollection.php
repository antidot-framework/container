<?php

declare(strict_types=1);

namespace Antidot\Container;

class ParamCollection
{
    /** @var array<mixed> */
    private array $parameters;

    /**
     * @param array<mixed> $parameters
     */
    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    /**
     * @param mixed $parameters
     */
    public function set(string $id, $parameters): void
    {
        $this->parameters[$id] = $parameters;
    }

    /**
     * @param int|string $id
     * @return bool
     */
    public function has($id): bool
    {
        return array_key_exists($id, $this->parameters);
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        return $this->parameters[$id];
    }
}
