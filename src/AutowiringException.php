<?php

declare(strict_types=1);

namespace Antidot\Container;

use Psr\Container\ContainerExceptionInterface;
use RuntimeException;

class AutowiringException extends RuntimeException implements ContainerExceptionInterface
{
    public static function withNoType(string $id, string $parameter): self
    {
        return new self(sprintf('Cannot autowire untyped parameter $%s in service %s.', $parameter, $id));
    }
}
