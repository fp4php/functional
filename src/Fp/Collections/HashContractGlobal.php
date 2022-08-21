<?php

declare(strict_types=1);

namespace Fp\Collections;

use Closure;
use Fp\Functional\Option\Option;
use function Fp\Collection\at;
use function Fp\Evidence\proveObject;

final class HashContractGlobal implements HashContract
{
    /**
     * @var array<class-string, array{
     *     equals: Closure(object, mixed): bool,
     *     hashCode: Closure(object): string
     * }>
     */
    private static array $hashContracts = [];

    /**
     * @return array<class-string, array{
     *     equals: Closure(object, mixed): bool,
     *     hashCode: Closure(object): string
     * }>
     */
    public static function getAllHashContracts(): array
    {
        return self::$hashContracts;
    }

    /**
     * @param class-string $class
     */
    public static function remove(string $class): void
    {
        unset(self::$hashContracts[$class]);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param Closure(T, mixed): bool $equals
     * @param Closure(T): string $hashCode
     */
    public static function add(string $class, Closure $equals, Closure $hashCode): void
    {
        /** @psalm-suppress PropertyTypeCoercion */
        self::$hashContracts[$class] = [
            'equals' => $equals,
            'hashCode' => $hashCode,
        ];
    }

    /**
     * @return Option<HashContractGlobal>
     */
    public static function get(mixed $value): Option
    {
        return proveObject($value)->flatMap(
            fn(object $object) => at(self::$hashContracts, $object::class)
                ->map(fn(array $functions) => new HashContractGlobal(
                    instance: $object,
                    equals: $functions['equals'],
                    hashCode: $functions['hashCode'],
                ))
        );
    }

    /**
     * @param Closure(object, mixed): bool $equals
     * @param Closure(object): string $hashCode
     */
    private function __construct(
        public readonly object $instance,
        public readonly Closure $equals,
        public readonly Closure $hashCode,
    ) {}

    public function equals(mixed $that): bool
    {
        return ($this->equals)($this->instance, $that);
    }

    public function hashCode(): string
    {
        return ($this->hashCode)($this->instance);
    }
}
