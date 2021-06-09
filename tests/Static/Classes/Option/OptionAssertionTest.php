<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Fp\Functional\Option\None;
use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Tests\PhpBlockTestCase;

final class OptionAssertionTest extends PhpBlockTestCase
{
    public function testIsSomeWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                if ($option->isSome()) {
                    /** @psalm-trace $some */
                    $some = $option;
                } else {
                    /** @psalm-trace $none */
                    $none = $option;
                }
            ',
            'Some<int>',
            'None',
        );
    }

    public function testIsNonEmptyWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                if ($option->isNonEmpty()) {
                    /** @psalm-trace $some */
                    $some = $option;
                } else {
                    /** @psalm-trace $none */
                    $none = $option;
                }
            ',
            'Some<int>',
            'None',
        );
    }

    public function testIsNoneWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                if ($option->isNone()) {
                    /** @psalm-trace $none */
                    $none = $option;
                } else {
                    /** @psalm-trace $some */
                    $some = $option;
                }
            ',
            'None',
            'Some<int>',
        );
    }

    public function testIsEmptyWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                if ($option->isEmpty()) {
                    /** @psalm-trace $none */
                    $none = $option;
                } else {
                    /** @psalm-trace $some */
                    $some = $option;
                }
            ',
            'None',
            'Some<int>',
        );
    }

    public function testIsSomeWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                $option->isSome()
                    ? call_user_func(function() use ($option) {
                        /** @psalm-trace $option */
                        $some = $option;
                    })
                    : call_user_func(function() use ($option) {
                        /** @psalm-trace $none */
                        $none = $option;
                    });
            ',
            'Some<int>',
            'None',
        );
    }

    public function testIsNonEmptyWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                $option->isNonEmpty()
                    ? call_user_func(function() use ($option) {
                        /** @psalm-trace $some */
                        $some = $option;
                    })
                    : call_user_func(function() use ($option) {
                        /** @psalm-trace $none */
                        $none = $option;
                    });
            ',
            'Some<int>',
            'None',
        );
    }

    public function testIsNoneWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                $option->isNone()
                    ? call_user_func(function() use ($option) {
                        /** @psalm-trace $none */
                        $none = $option;
                    })
                    : call_user_func(function() use ($option) {
                        /** @psalm-trace $some */
                        $some = $option;    
                    });
            ',
            'None',
            'Some<int>',
        );
    }

    public function testIsEmptyWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Option\Option;

                /** @var Option<int> */
                $option = Option::none();

                $option->isEmpty()
                    ? call_user_func(function() use ($option) {
                        /** @psalm-trace $none */
                        $none = $option;
                    })
                    : call_user_func(function() use ($option) {
                        /** @psalm-trace $some */
                        $some = $option;    
                    });
            ',
            'None',
            'Some<int>',
        );
    }
}
