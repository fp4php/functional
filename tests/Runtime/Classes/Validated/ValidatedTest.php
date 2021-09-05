<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Validated;

use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use Fp\Functional\Option\None;
use Fp\Functional\Option\Some;
use Fp\Functional\Validated\Invalid;
use Fp\Functional\Validated\Valid;
use Fp\Functional\Validated\Validated;
use PHPUnit\Framework\TestCase;
use Tests\Mock\FooInput;
use Tests\Mock\FooInputValidator;

use function Fp\Collection\reduce;

final class ValidatedTest extends TestCase
{
    public function testCombine(): void
    {
        $validator = new FooInputValidator();
        $validInput = new FooInput(2, '2021', true);
        $invalidInput = new FooInput(0, '2020', false);

        $valid = $validator->validate($validInput);
        $invalid = $validator->validate($invalidInput);

        $this->assertEquals($validInput, $valid->get());
        $this->assertEquals([
            '"a" must be greater than 0',
            '"a" must be greater than 1',
            '"b" must be greater than 2020',
            '"c" must be true',
        ], $invalid->get());
    }

    public function testFold(): void
    {
        $validator = new FooInputValidator();
        $validInput = new FooInput(2, '2021', true);
        $invalidInput = new FooInput(2, '2020', false);

        $valid = $validator->validate($validInput);
        $invalid = $validator->validate($invalidInput);

        $this->assertEquals($validInput, $valid->get());
        $this->assertEquals([
            '"b" must be greater than 2020',
            '"c" must be true',
        ], $invalid->get());

        $this->assertEquals(
            '"b" must be greater than 2020, "c" must be true',
            $invalid->fold(
                fn(FooInput $input) => $input,
                fn(array $invalidNel) => reduce(
                    $invalidNel,
                    fn(string $acc, string $err) => $acc . ', ' .  $err
                )->getUnsafe(),
            )
        );

        $this->assertEquals(
            $validInput,
            $valid->fold(
                fn(FooInput $input) => $input,
                fn(array $invalidNel) => reduce(
                    $invalidNel,
                    fn(string $acc, string $err) => $acc . ', ' .  $err
                )->getUnsafe(),
            )
        );
    }

    public function testCond(): void
    {
        $this->assertInstanceOf(
            Valid::class,
            Validated::cond(true, 1, 0)
        );

        $this->assertEquals(
            1,
            Validated::cond(true, 1, 0)->get()
        );

        $this->assertInstanceOf(
            Invalid::class,
            Validated::cond(false, 1, 0)
        );

        $this->assertEquals(
            0,
            Validated::cond(false, 1, 0)->get()
        );
    }

    public function testToEither(): void
    {
        $right = Validated::valid(1)->toEither();
        $left = Validated::invalid('err')->toEither();

        $this->assertInstanceOf(Right::class, $right);
        $this->assertInstanceOf(Left::class, $left);

        $this->assertEquals(1, $right->get());
        $this->assertEquals('err', $left->get());
    }

    public function testToOption(): void
    {
        $some = Validated::valid(1)->toOption();
        $none = Validated::invalid('err')->toOption();

        $this->assertInstanceOf(Some::class, $some);
        $this->assertInstanceOf(None::class, $none);

        $this->assertEquals(1, $some->get());
    }
}
