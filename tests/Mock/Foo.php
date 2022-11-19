<?php

declare(strict_types=1);

namespace Tests\Mock;

use Fp\Collections\HashContract;
use Fp\Functional\Either\Either;
use Fp\Functional\Option\Option;

/**
 * @internal
 */
class Foo implements HashContract
{
    public function __construct(public int $a, public bool $b = true, public bool $c = true)
    {
    }

    public static function create(int $a, bool $b = true, bool $c = true): Foo
    {
        return new Foo($a, $b, $c);
    }

    /**
     * @return Option<Foo>
     */
    public static function createOption(int $a, bool $b = true, bool $c = true): Option
    {
       return 0 === $a ? Option::none() : Option::some(self::create($a, $b, $c));
    }

    /**
     * @return Either<string, Foo>
     */
    public static function createEither(int $a, bool $b = true, bool $c = true): Either
    {
       return 0 === $a ? Either::left('$a is invalid') : Either::right(self::create($a, $b, $c));
    }

    public function equals(mixed $that): bool
    {
        return $that instanceof self
            && $this->a === $that->a
            && $this->b === $that->b;
    }

    public function hashCode(): string
    {
        return md5(implode(',', [$this->a, $this->b]));
    }
}
