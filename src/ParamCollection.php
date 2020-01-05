<?php

declare(strict_types=1);

namespace Antidot\Container;

class ParamCollection
{
    private array $parameters;

    public function __construct(array $parameters)
    {
        $this->parameters = $parameters;
    }

    public function set(string $id, $parameters): void
    {
        $this->parameters[$id] = $parameters;
    }

    public function has($id): bool
    {
        return array_key_exists($id, $this->parameters);
    }

    public function get(string $id)
    {
        return $this->parameters[$id];
    }
}
