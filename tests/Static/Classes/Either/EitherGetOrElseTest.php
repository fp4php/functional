<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Tests\Mock\Foo;
use Tests\PhpBlockTestCase;

final class EitherGetOrElseTest extends PhpBlockTestCase
{
    public function testGetOrElseWithInt(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                /** @var int $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(1);
            ',
            'int'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var 1 $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(2);
            ',
            '1|2'
        );
    }

    public function testGetOrElseWithBool(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var bool $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(true);
            ',
            'bool'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var true $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(false);
            ',
            'bool'
        );
    }

    public function testGetOrElseWithFloat(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var float $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(1.1);
            ',
            'float'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var 1.1 $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(2.2);
            ',
            'float(1.1)|float(2.2)'
        );
    }

    public function testGetOrElseWithString(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var string $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse("1");
            ',
            'string'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var string $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(\Tests\Mock\Foo::class);
            ',
            'string'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var class-string<\Tests\Mock\Foo> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse("1.1");
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
                /** @var list<int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var list<int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([1]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'list<int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([1]);
            ',
            'non-empty-list<int>'
        );
    }

    public function testGetOrElseWithArray(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'array<string, int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([true]);
            ',
            'array<0|string, int|true>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'array<string, int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([1]);
            ',
            'non-empty-array<0|string, int>'
        );
    }

    public function testGetOrElseWithArrayOrList(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var list<bool>|array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse([]);
            ',
            'array<int|string, bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(["x"]);
            ',
            'non-empty-array<int|string, "x"|bool|int>'
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input */
                $input = null;
                $result = Fp\Functional\Either\Either::right($input)->getOrElse(fn() => ["x"]);
            ',
            'non-empty-array<int|string, "x"|bool|int>'
        );
    }
}
