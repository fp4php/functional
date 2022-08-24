<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Upcast;

use Closure;
use Fp\Functional\Option\Option;
use Fp\PsalmToolkit\Toolkit\PsalmApi;
use Generator;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Type;
use Psalm\Type\Atomic;
use Psalm\Type\Atomic\TIterable;
use Psalm\Type\Atomic\TList;
use Psalm\Type\Atomic\TNonEmptyList;
use Psalm\Type\Atomic\TNonEmptyArray;
use Psalm\Type\Atomic\TArray;
use Psalm\Type\Atomic\TKeyedArray;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Atomic\TMixed;
use Psalm\Type\Atomic\TCallable;

use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class AtomicUpcast
{
    /**
     * @return non-empty-list<Closure(Atomic $input, Atomic $to): Generator<int, Option<mixed>, mixed, Atomic>>
     */
    public static function variants(): array
    {
        return [
            // TNonEmptyList -> TNonEmptyList
            function(Atomic $input, Atomic $to) {
                yield proveOf($input, TNonEmptyList::class);
                yield proveOf($to, TNonEmptyList::class);

                return clone $input;
            },

            // TNonEmptyList -> TList
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyList);
                yield proveTrue($to instanceof TList);

                return new TList(clone $input->type_param);
            },

            // TNonEmptyList -> TNonEmptyArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyList);
                yield proveTrue($to instanceof TNonEmptyArray);

                return new TNonEmptyArray([Type::getInt(), clone $input->type_param]);
            },

            // TNonEmptyList -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyList);
                yield proveTrue($to instanceof TIterable);

                return new TIterable([Type::getInt(), clone $input->type_param]);
            },

            // TNonEmptyArray -> TNonEmptyArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyArray);
                yield proveTrue($to instanceof TNonEmptyArray);

                return clone $input;
            },

            // TNonEmptyArray -> TArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyArray);
                yield proveTrue($to instanceof TArray);

                return new TArray([
                    clone $input->type_params[0],
                    clone $input->type_params[1],
                ]);
            },

            // TNonEmptyArray -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TNonEmptyArray);
                yield proveTrue($to instanceof TIterable);

                return new TIterable([
                    clone $input->type_params[0],
                    clone $input->type_params[1],
                ]);
            },

            // TList -> TList
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TList);
                yield proveTrue($to instanceof TList);

                return clone $input;
            },

            // TList -> TArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TList);
                yield proveTrue($to instanceof TArray);

                return new TArray([Type::getInt(), clone $input->type_param]);
            },

            // TList -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TList);
                yield proveTrue($to instanceof TIterable);

                return new TIterable([Type::getInt(), clone $input->type_param]);
            },

            // TArray -> TArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TArray);
                yield proveTrue($to instanceof TArray);

                return clone $input;
            },

            // TArray -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TArray);
                yield proveTrue($to instanceof TIterable);

                return new TIterable([
                    clone $input->type_params[0],
                    clone $input->type_params[1],
                ]);
            },

            // TKeyedArray -> TKeyedArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TKeyedArray);

                return clone $input;
            },

            // TKeyedArray -> TNonEmptyArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TNonEmptyArray);

                return new TNonEmptyArray([
                    clone $input->getGenericKeyType(),
                    clone $input->getGenericValueType(),
                ]);
            },

            // TKeyedArray -> TArray
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TArray);

                return new TArray([
                    clone $input->getGenericKeyType(),
                    clone $input->getGenericValueType(),
                ]);
            },

            // TKeyedArray -> TNonEmptyList
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TNonEmptyList);

                return new TNonEmptyList(clone $input->getGenericValueType());
            },

            // TKeyedArray -> TList
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TList);

                return new TList(clone $input->getGenericValueType());
            },

            // TKeyedArray -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TKeyedArray);
                yield proveTrue($to instanceof TIterable);

                return new TIterable([
                    clone $input->getGenericKeyType(),
                    clone $input->getGenericValueType(),
                ]);
            },

            // TGenericObject -> TGenericObject
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TGenericObject);
                yield proveTrue($to instanceof TGenericObject);
                $_storage = PsalmApi::$classlikes->getStorage($input);

                return new TMixed();
            },

            // TGenericObject -> TIterable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TGenericObject);
                yield proveTrue($to instanceof TIterable);
                $_storage = yield PsalmApi::$classlikes->getStorage($input)
                    ->filter(fn(ClassLikeStorage $storage) => array_key_exists('traversable', $storage->class_implements));

                // template_extended_offsets

                return new TMixed();
            },

            // TGenericObject -> callable
            function(Atomic $input, Atomic $to) {
                yield proveTrue($input instanceof TGenericObject);
                yield proveTrue($to instanceof TCallable);
                $_storage = PsalmApi::$classlikes->getStorage($input);

                return new TMixed();
            },
        ];
    }
}
