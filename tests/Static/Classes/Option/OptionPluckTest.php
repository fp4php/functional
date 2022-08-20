<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\Option;
use Tests\Mock\Foo;

final class OptionPluckTest
{
    /**
     * @param Option<Foo> $option
     * @return Option<int>
     */
    public function pluckObjectProperty(Option $option): Option
    {
        return $option->pluck('a');
    }

    /**
     * @param Option<Foo> $option
     */
    public function pluckUndefinedObjectProperty(Option $option): Option
    {
        /** @psalm-suppress UndefinedPropertyFetch */
        return $option->pluck('undefined');
    }

    /**
     * @param Option<array{a: int}> $option
     * @return Option<int>
     */
    public function pluckArrayProperty(Option $option): Option
    {
        return $option->pluck('a');
    }

    /**
     * @param Option<array{a: int}> $option
     */
    public function pluckUndefinedArrayProperty(Option $option): Option
    {
        /** @psalm-suppress PossiblyUndefinedArrayOffset */
        return $option->pluck('undefined');
    }
}
