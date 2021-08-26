<?php

declare(strict_types=1);

namespace Doc;

abstract class AbstractMdHeader
{
    public final function __construct(public string $title) {}

    public abstract static function prefix(): string;

    public static function fromTitle(string $title): static
    {
        return new static($title);
    }
}
