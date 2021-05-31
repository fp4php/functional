<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Tests\Mock\Foo;
use Tests\PhpBlockTestCase;

final class OptionGetOrElseTest extends PhpBlockTestCase
{
    public function testGetOrElseWithInt(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                /** @var int|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(1);
            ',
            'int'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var 1|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(2);
            ',
            '1|2'
        );
    }

    public function testGetOrElseWithBool(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var bool|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(true);
            ',
            'bool'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var true|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(false);
            ',
            'bool'
        );
    }

    public function testGetOrElseWithFloat(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var float|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(1.1);
            ',
            'float'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var 1.1|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(2.2);
            ',
            'float(1.1)|float(2.2)'
        );
    }

    public function testGetOrElseWithString(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var string|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse("1");
            ',
            'string'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var string|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(\Tests\Mock\Foo::class);
            ',
            'string'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var class-string<\Tests\Mock\Foo>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse("1.1");
            ',
            strtr(
                '"1.1"|class-string<Foo>',
                ['Foo' => Foo::class]
            )
        );
    }

    public function testGetOrElseWithList(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var list<int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var list<int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([1]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([1]);
            ',
            'non-empty-list<int>'
        );
    }

    public function testGetOrElseWithArray(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'array<string, int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([true]);
            ',
            'array<0|string, int|true>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'array<string, int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([1]);
            ',
            'non-empty-array<0|string, int>'
        );
    }

    public function testGetOrElseWithArrayOrList(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var list<bool>|array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(["x"]);
            ',
            'non-empty-array<int|string, "x"|bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int>|null $input */
                $input = null;
                $result = Fp\Functional\Option\Option::fromNullable($input)->getOrElse(fn() => ["x"]);
            ',
            'non-empty-array<int|string, "x"|bool|int>'
        );
    }
}
