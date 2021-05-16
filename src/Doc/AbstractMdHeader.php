<?php

declare(strict_types=1);

namespace Doc;

use Stringable;

abstract class AbstractMdHeader
{
    public final function __construct(public string $title) {}

    public abstract static function prefix(): string;

    public static function fromTitle(Stringable $title): static
    {
        return new static((string) $title);
    }
}
