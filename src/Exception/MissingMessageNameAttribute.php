<?php

declare(strict_types=1);

namespace StephBug\ApiDev\Exception;

class MissingMessageNameAttribute extends RuntimeException
{
    public static function withKeys(string $attribute, string $busType): self
    {
        return new self(sprintf('Missing attribute name %s for bus %s', $attribute, $busType));
    }
}