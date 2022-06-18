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
 * ```php
 * >>> $aToB = fn(int $a): bool => true;
 * >>> $bToC = fn(bool $b): string => (string) $b;
 * >>> $cTod = fn(string $c): float => (float) $c;
 *
 * >>> compose($aToB, $bToC, $cTod);
 * => fn(int $a): float => $cTod($bToC($aToB($a)))
 * ```
 *
 * @template A
 * @template B
 * @template C
 * @template D
 * @template F
 * @template G
 * @template H
 * @template I
 * @template J
 * @template K
 * @template L
 *
 * @param callable(A): B $aToB
 * @param callable(B): C $bToC
 * @param callable(C): D $cToD
 * @param callable(D): F $dToF
 * @param callable(F): G $fToG
 * @param callable(G): H $gToH
 * @param callable(H): I $hToI
 * @param callable(I): J $iToJ
 * @param callable(J): K $jToK
 * @param callable(K): L $kToL
 *
 * @return (
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
 * @psalm-suppress all todo
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

