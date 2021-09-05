<?php

declare(strict_types=1);

namespace Tests\Mock;

use Fp\Functional\Semigroup\LhsSemigroup;
use Fp\Functional\Semigroup\NonEmptyListSemigroup;
use Fp\Functional\Semigroup\Semigroup;
use Fp\Functional\Semigroup\ValidatedSemigroup;
use Fp\Functional\Validated\Validated;

/**
 * @internal
 */
class FooInputValidator
{
    /**
     * @var Semigroup<Validated<non-empty-list<string>, FooInput>>
     */
    private Semigroup $semigroup;

    public function __construct()
    {
        /** @var Semigroup<FooInput> $lhsSemigroup */
        $lhsSemigroup = new LhsSemigroup();

        /** @var Semigroup<non-empty-list<string>> $nonEmptyListSemigroup */
        $nonEmptyListSemigroup = new NonEmptyListSemigroup();

        $this->semigroup = new ValidatedSemigroup(
            $lhsSemigroup,
            $nonEmptyListSemigroup
        );
    }

    /**
     * @psalm-return Validated<non-empty-list<string>, FooInput>
     */
    private function validateA(FooInput $input): Validated {
        return $this->semigroup->combineAll([
            Validated::cond($input->a > 0, $input, ['"a" must be greater than 0']),
            Validated::cond($input->a > 1, $input, ['"a" must be greater than 1']),
            Validated::cond($input->a % 2 === 0, $input, ['"a" must be even']),
        ]);
    }

    /**
     * @psalm-return Validated<non-empty-list<string>, FooInput>
     */
    private function validateB(FooInput $input): Validated {
        return $this->semigroup->combineAll([
            Validated::cond($input->b !== '', $input, ['empty string is not allowed for "b"']),
            Validated::cond($input->b > '2020', $input, ['"b" must be greater than 2020']),
        ]);
    }

    /**
     * @psalm-return Validated<non-empty-list<string>, FooInput>
     */
    private function validateC(FooInput $input): Validated {
        return $this->semigroup->combineAll([
            Validated::cond($input->c === true, $input, ['"c" must be true']),
        ]);
    }

    /**
     * @psalm-return Validated<non-empty-list<string>, FooInput>
     */
    public function validate(FooInput $input): Validated
    {
        return $this->semigroup->combineAll([
            $this->validateA($input),
            $this->validateB($input),
            $this->validateC($input),
        ]);
    }
}
