<?php

declare(strict_types=1);

namespace Fp\Callable;

use Fp\Collections\NonEmptyLinkedList;

use function Fp\Collection\filterNotNull;

/**
 * Compose functions
 * Output of one function will be passed as input to another function
 *
 * Given callable(int): bool and callable(bool): string
 * Returns callable(int): string
 *
 * REPL:
 * >>> $aToB = fn(int $a): bool => true;
 * >>> $bToC = fn(bool $b): string => (string) $b;
 * >>> $cTod = fn(string $c): float => (float) $c;
 * >>> compose($aToB, $bToC, $cTod);
 * => callable(int): float
 *
 * @psalm-template A
 * @psalm-template B
 * @psalm-template C
 * @psalm-template D
 * @psalm-template F
 * @psalm-template G
 * @psalm-template H
 * @psalm-template I
 * @psalm-template J
 * @psalm-template K
 * @psalm-template L
 *
 * @psalm-param callable(A): B $aToB
 * @psalm-param callable(B): C $bToC
 * @psalm-param callable(C): D $cToD
 * @psalm-param callable(D): F $dToF
 * @psalm-param callable(F): G $fToG
 * @psalm-param callable(G): H $gToH
 * @psalm-param callable(H): I $hToI
 * @psalm-param callable(I): J $iToJ
 * @psalm-param callable(J): K $jToK
 * @psalm-param callable(K): L $kToL
 *
 * @psalm-return (
 *     func_num_args() is 2 ? callable(A): C : (
 *     func_num_args() is 3 ? callable(A): D : (
 *     func_num_args() is 4 ? callable(A): F : (
 *     func_num_args() is 5 ? callable(A): G : (
 *     func_num_args() is 6 ? callable(A): H : (
 *     func_num_args() is 7 ? callable(A): I : (
 *     func_num_args() is 8 ? callable(A): J : (
 *     func_num_args() is 9 ? callable(A): K : (
 *     callable(A): L
 * )))))))))
 *
 * @psalm-suppress all
 */
function compose(
    callable $aToB,
    callable $bToC,
    callable $cToD = null,
    callable $dToF = null,
    callable $fToG = null,
    callable $gToH = null,
    callable $hToI = null,
    callable $iToJ = null,
    callable $jToK = null,
    callable $kToL = null,
): callable
{
    $callableChain = filterNotNull([$aToB, $bToC, $cToD, $dToF, $fToG, $gToH, $hToI, $iToJ, $jToK, $kToL]);

    return NonEmptyLinkedList::collectUnsafe($callableChain)
        ->reduce(function(callable $acc, callable $cur) {
            return fn(mixed $v): mixed => $cur($acc($v));
        });
}

