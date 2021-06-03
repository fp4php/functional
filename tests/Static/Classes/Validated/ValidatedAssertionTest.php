<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Validated;

use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Tests\PhpBlockTestCase;

final class ValidatedAssertionTest extends PhpBlockTestCase
{
    public function testIsValidWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Validated\Validated;

                /** @var Validated<string, int> */
                $validated = Validated::invalid("err");

                if ($validated->isValid()) {
                    /** @psalm-trace $valid */
                    $valid = $validated;
                } else {
                    /** @psalm-trace $invalid */
                    $invalid = $validated;
                }
            ',
            Valid::class . '<int>',
            Invalid::class . '<string>',
        );
    }

    public function testIsInvalidWithIfStatement(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Validated\Validated;

                /** @var Validated<string, int> */
                $validated = Validated::invalid("err");

                if ($validated->isInvalid()) {
                    /** @psalm-trace $invalid */
                    $invalid = $validated;
                } else {
                    /** @psalm-trace $valid */
                    $valid = $validated;
                }
            ',
            Invalid::class . '<string>',
            Valid::class . '<int>',
        );
    }

    public function testIsValidWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Validated\Validated;

                /** @var Validated<string, int> */
                $validated = Validated::invalid("err");

                $validated->isValid()
                    ? call_user_func(function() use ($validated) {
                        /** @psalm-trace $valid */
                        $valid = $validated;
                    })
                    : call_user_func(function() use ($validated) {
                        /** @psalm-trace $invalid */
                        $invalid = $validated;
                    });
            ',
            Valid::class . '<int>',
            Invalid::class . '<string>',
        );
    }

    public function testIsInvalidWithTernaryOperator(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Validated\Validated;

                /** @var Validated<string, int> */
                $validated = Validated::invalid("err");

                $validated->isInvalid()
                    ? call_user_func(function() use ($validated) {
                        /** @psalm-trace $invalid */
                        $invalid = $validated;
                    })
                    : call_user_func(function() use ($validated) {
                        /** @psalm-trace $valid */
                        $valid = $validated;
                    });
            ',
            Invalid::class . '<string>',
            Valid::class . '<int>',
        );
    }
}
