<?php


namespace Antidot\Container;

use InvalidArgumentException;
use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends InvalidArgumentException implements NotFoundExceptionInterface
{
    public static function forId(string $id): self
    {
        return new self(sprintf('Servie with id %s not found in container config.', $id));
    }
}
