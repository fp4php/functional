<?php

declare(strict_types=1);

namespace Fp\Collections;

use Fp\Functional\Option\Option;
use Fp\Operations\FoldOperation;
use Fp\Psalm\Hook\MethodReturnTypeProvider\FoldMethodReturnTypeProvider;

/**
 * @template TK
 * @template-covariant TV
 *
 * @psalm-suppress InvalidTemplateParam
 */
interface MapTerminalOps
{
    /**
     * Get an element by its key
     * Alias for {@see MapOps::get}
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function __invoke(mixed $key): Option;

    /**
     * Get an element by its key
     *
     * ```php
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('b')->getOrElse(0);
     * => 2
     *
     * >>> HashMap::collect(['a' => 1, 'b' => 2])->get('c')->getOrElse(0);
     * => 0
     * ```
     *
     * @param TK $key
     * @return Option<TV>
     */
    public function get(mixed $key): Option;

    /**
     * Returns true if every collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($value) => $value > 0);
     * => true
     *
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->every(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function every(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::every()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function everyKV(callable $predicate): bool;

    /**
     * Returns true if some collection element satisfy the condition
     * false otherwise
     *
     * ```php
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->exists(fn($value) => $value > 0);
     * => true
     *
     * >>> HashMap::collectPairs([['a', 1], ['b', 2]])->exists(fn($value) => $value > 1);
     * => false
     * ```
     *
     * @param callable(TV): bool $predicate
     */
    public function exists(callable $predicate): bool;

    /**
     * Same as {@see MapTerminalOps::exists()}, but passing also the key to the $predicate function.
     *
     * @param callable(TK, TV): bool $predicate
     */
    public function existsKV(callable $predicate): bool;

    /**
     * Suppose you have an HashMap<TV> and you want to format each element with a function that returns an Option<TVO>.
     * Using traverseOption you can apply $callback to all elements and directly obtain as a result an Option<HashMap<TVO>>
     * i.e. an Some<HashMap<TVO>> if all the results are Some<TVO>, or a None if at least one result is None.
     *
     * ```php
     * >>> HashMap::collectPairs(['a' => 1, 'b' => 2])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => Some(HashMap('a' -> 1, 'b' -> 2))
     *
     * >>> HashMap::collectPairs(['a' => 0, 'b' => 1])->traverseOption(fn($x) => $x >= 1 ? Option::some($x) : Option::none());
     * => None
     * ```
     *
     * @template TVO
     *
     * @param callable(TV): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOption(callable $callback): Option;

    /**
     * Same as {@see MapTerminalOps::traverseOption()}, but passing also the key to the $predicate function.
     *
     * @template TVO
     *
     * @param callable(TK, TV): Option<TVO> $callback
     * @return Option<Map<TK, TVO>>
     */
    public function traverseOptionKV(callable $callback): Option;

    /**
     * Same as {@see MapTerminalOps::traverseOption()} but use {@see id()} implicitly for $callback.
     *
     * ```php
     * >>> HashMap::collect([Option::some(1), Option::some(2), Option::some(3)])->sequenceOption();
     * => Some(HashMap(0 -> 1, 1 -> 2, 2 -> 3))
     *
     * >>> HashMap::collect([Option::none(), Option::some(1), Option::some(2)])->sequenceOption();
     * => None
     * ```
     *
     * @template TVO
     * @psalm-if-this-is Map<TK, Option<TVO>>
     *
     * @return Option<Map<TK, TVO>>
     */
    public function sequenceOption(): Option;

    /**
     * Fold many elements into one
     *
     * ```php
     * >>> HashMap::collect(['fst' => 1, 'snd' => 2, 'thr' => 3])->fold('0')(fn($acc, $cur) => $acc . $cur);
     * => '0123'
     * ```
     *
     * @template TVO
     *
     * @param TVO $init
     * @return FoldOperation<TV, TVO>
     *
     * @see FoldMethodReturnTypeProvider
     */
    public function fold(mixed $init): FoldOperation;

    public function isEmpty(): bool;

    /**
     * Returns sequence of collection keys
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->keys(fn($elem) => $elem + 1)->toList();
     * => ['1', '2']
     * ```
     *
     * @return Seq<TK>
     */
    public function keys(): Seq;

    /**
     * Returns sequence of collection values
     *
     * ```php
     * >>> $collection = HashMap::collectPairs([['1', 1], ['2', 2]]);
     * => HashMap('1' -> 1, '2' -> 2)
     *
     * >>> $collection->values(fn($elem) => $elem + 1)->toList();
     * => [1, 2]
     * ```
     *
     * @return Seq<TV>
     */
    public function values(): Seq;
}
