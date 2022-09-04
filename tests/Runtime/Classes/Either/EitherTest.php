<?php

declare(strict_types=1);

namespace Tests\Runtime\Classes\Either;

use Exception;
use Fp\Collections\ArrayList;
use Fp\Collections\LinkedList;
use Fp\Collections\LinkedListBuffer;
use Fp\Functional\Either\Either;
use Fp\Functional\Either\Left;
use Fp\Functional\Either\Right;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use Tests\Mock\Foo;

final class EitherTest extends TestCase
{
    public function testToString(): void
    {
        $this->assertEquals('Left(42)', (string) Either::left(42));
        $this->assertEquals('Right(42)', (string) Either::right(42));
        $this->assertEquals('Left(42)', Either::left(42)->toString());
        $this->assertEquals('Right(42)', Either::right(42)->toString());
        $this->assertEquals('Right([])', Either::right([]));
        $this->assertEquals("Right([1, 2, 3])", Either::right([1, 2, 3]));
        $this->assertEquals("Right(['t' => 1])", Either::right(['t' => 1]));
    }

    public function testCreation(): void
    {
        $this->assertInstanceOf(Right::class, Either::right(1));
        $this->assertEquals(1, Either::right(1)->get());
        $this->assertInstanceOf(Left::class, Either::left('err'));
        $this->assertEquals('err', Either::left('err')->get());
    }

    public function testMap(): void
    {
        $right = Either::right(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $left = Either::left(1)
            ->map(fn(int $s) => $s + 1)
            ->map(fn(int $s) => $s + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(1, $left->get());
    }

    public function testMapN(): void
    {
        $right = Either::right([1, true, false])->mapN(Foo::create(...));
        $this->assertEquals(Either::right(new Foo(1, true, false)), $right);
    }

    public function testFlatMap(): void
    {
        $getRight = function(): Either {
            /** @psalm-var Either<string, int> $e */
            $e = Either::right(1);

            return $e;
        };

        $right = $getRight()
            ->flatMap(fn(int $r) => Either::right($r + 1))
            ->flatMap(fn(int $r) => Either::right($r + 1));

        $left = $getRight()
            ->flatMap(fn(int $r) => Either::right($r + 1))
            ->flatMap(function(int $r) {
                /** @psalm-var Either<string, int> $e */
                $e = Either::left('error');

                return $e;
            })
            ->flatMap(fn(int $r) => Either::right($r + 1));

        $this->assertEquals(3, $right->get());
        $this->assertEquals('error', $left->get());
    }

    public function testFlatMapN(): void
    {
        $right = Either::right([1, true, false])->flatMapN(Foo::createEither(...));
        $left = Either::right([0, true, false])->flatMapN(Foo::createEither(...));

        $this->assertEquals(Either::right(new Foo(1, true, false)), $right);
        $this->assertEquals(Either::left('$a is invalid'), $left);
    }

    public function testTap(): void
    {
        $right = Either::right([1, true, false])
            ->tap(function(array $tuple) {
                $this->assertEquals(1, $tuple[0]);
                $this->assertEquals(true, $tuple[1]);
                $this->assertEquals(false, $tuple[2]);
            });

        $left = Either::right([1, true, false])
            ->flatMap(function() {
                /** @var Either<string, array{int, bool, bool}> */
                return Either::left('error');
            })
            ->tap(function(array $tuple) {
                $this->assertEquals(1, $tuple[0]);
                $this->assertEquals(true, $tuple[1]);
                $this->assertEquals(false, $tuple[2]);
            });

        $this->assertEquals(Either::right([1, true, false]), $right);
        $this->assertEquals(Either::left('error'), $left);
    }

    public function testTapLeft(): void
    {
        /** @var LinkedListBuffer<string> */
        $buff1 = new LinkedListBuffer();

        /** @var Either<string, int> */
        $right = Either::right(42);
        $right->tapLeft(fn(string $str) => $buff1->append($str));

        $this->assertEquals(LinkedList::empty(), $buff1->toLinkedList());

        /** @var Either<string, int> */
        $left = Either::left('err');
        $left->tapLeft(fn(string $str) => $buff1->append($str));

        $this->assertEquals(LinkedList::collect(['err']), $buff1->toLinkedList());
    }

    public function testFlatTapLeft(): void
    {
        /** @var LinkedListBuffer<int> */
        $buff = new LinkedListBuffer();

        /** @var Either<string, int> */
        $left = Either::left('err');
        $result = $left->flatTap(fn(int $e) => $e > 0
            ? Either::right($buff->append($e))
            : Either::left('invalid'));

        $this->assertEquals(LinkedList::empty(), $buff->toLinkedList());
        $this->assertEquals('err', $result->get());
    }

    public function testFlatTapWhenReturnsLeft(): void
    {
        /** @var LinkedListBuffer<int> */
        $buff = new LinkedListBuffer();

        /** @var Either<string, int> */
        $right = Either::right(0);
        $result = $right->flatTap(fn(int $e) => $e > 0
            ? Either::right($buff->append($e))
            : Either::left('invalid'));

        $this->assertEquals(LinkedList::empty(), $buff->toLinkedList());
        $this->assertEquals('invalid', $result->get());
    }

    public function testFlatTapWhenReturnsRight(): void
    {
        /** @var LinkedListBuffer<int> */
        $buff2 = new LinkedListBuffer();

        /** @var Either<string, int> */
        $right2 = Either::right(1);
        $right2->flatTap(fn(int $e) => $e > 0
            ? Either::right($buff2->append($e))
            : Either::left('invalid'));

        $this->assertEquals(LinkedList::singleton(1), $buff2->toLinkedList());
    }

    public function testFlatTapN(): void
    {
        /** @var LinkedListBuffer<int> */
        $buff = new LinkedListBuffer();

        Either::right([1, 2, 3])->flatTapN(
            fn(int $a, int $b, int $c) => Either::right($buff->append($a)->append($b)->append($c)),
        );

        $this->assertEquals(LinkedList::collect([1, 2, 3]), $buff->toLinkedList());
    }

    public function testTapN(): void
    {
        $right = Either::right([1, true, false])
            ->tapN(function(int $a, bool $b, bool $c) {
                $this->assertEquals(1, $a);
                $this->assertEquals(true, $b);
                $this->assertEquals(false, $c);
            });

        $left = Either::right([1, true, false])
            ->flatMap(function() {
                /** @var Either<string, array{int, bool, bool}> */
                return Either::left('error');
            })
            ->tapN(function(int $a, bool $b, bool $c) {
                $this->assertEquals(1, $a);
                $this->assertEquals(true, $b);
                $this->assertEquals(false, $c);
            });

        $this->assertEquals(Either::right([1, true, false]), $right);
        $this->assertEquals(Either::left('error'), $left);
    }

    public function testMapLeft(): void
    {
        /** @psalm-var Either<string, int> $either1 */
        $either1 = Either::right(1);

        /** @psalm-var Either<string, int> $either2 */
        $either2 = Either::left('error');

        $right = $either1
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->map(fn(int $r) => $r + 1);

        $left = $either2
            ->map(fn(int $r) => $r + 1)
            ->mapLeft(fn(string $l) => match($l) {
                'error' => true,
                default => false,
            })
            ->mapLeft(fn(bool $l) => (int) $l)
            ->mapLeft(fn(int $l) => $l + 9)
            ->map(fn(int $r) => $r + 1);

        $this->assertEquals(3, $right->get());
        $this->assertEquals(10, $left->get());
    }

    public function testIsMethods(): void
    {
        $this->assertFalse(Either::right(1)->isLeft());
        $this->assertTrue(Either::right(1)->isRight());
    }

    public function testTry(): void
    {
        $this->assertInstanceOf(Right::class, Either::try(fn() => 1));
        $this->assertEquals(1, Either::try(fn() => 1)->get());

        $this->assertInstanceOf(Left::class, Either::try(fn() => throw new Exception()));
        $this->assertInstanceOf(Exception::class, Either::try(fn() => throw new Exception())->get());
    }

    public function testFold(): void
    {
        $foldRight = Either::right(1)->fold(
            fn() => 0,
            fn(int $some) => $some + 1,
        );

        $foldLeft = Either::left('err')->fold(
            fn() => 0,
            fn(int $some) => $some + 1,
        );

        $this->assertEquals(2, $foldRight);
        $this->assertEquals(0, $foldLeft);
    }

    public function testGetOrElse(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrElse(0));
        $this->assertEquals(0, Either::left('err')->getOrElse(0));
    }

    public function testGetOrCall(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrCall(fn() => 0));
        $this->assertEquals(0, Either::left('err')->getOrCall(fn() => 0));
    }

    public function testGetOrThrow(): void
    {
        $this->assertEquals(1, Either::right(1)->getOrThrow(fn($err) => new RuntimeException($err)));
        $this->expectExceptionMessage('err');
        Either::left('err')->getOrThrow(fn($err) => new RuntimeException($err));
    }

    public function testOrElse(): void
    {
        $this->assertEquals(
            1,
            Either::right(1)->orElse(fn() => Either::right(2))->get()
        );

        $this->assertEquals(
            2,
            Either::left('err')->orElse(fn() => Either::right(2))->get()
        );
    }

    public function testToOption(): void
    {
        $this->assertEquals(1, Either::right(1)->toOption()->get());
        $this->assertNull(Either::left(1)->toOption()->get());
    }

    public function testToArrayList(): void
    {
        $this->assertEquals(ArrayList::empty(), Either::left(1)->toArrayList());
        $this->assertEquals(ArrayList::singleton(1), Either::right(1)->toArrayList());
    }
}
