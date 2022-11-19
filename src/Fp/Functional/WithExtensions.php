<?php

namespace Fp\Functional;

use Closure;
use Error;

trait WithExtensions
{
    /** @var array<non-empty-string, (Closure(object, mixed...): mixed)> */
    private static array $instanceExtensions = [];

    /** @var array<non-empty-string, (Closure(mixed...): mixed)> */
    private static array $staticExtensions = [];

    /**
     * @param non-empty-string $name
     */
    public static function removeInstanceExtension(string $name): void
    {
        unset(self::$instanceExtensions[$name]);
    }

    /**
     * @param non-empty-string $name
     */
    public static function removeStaticExtension(string $name): void
    {
        unset(self::$staticExtensions[$name]);
    }

    /**
     * @template T of object
     *
     * @param non-empty-string $name
     * @param (Closure(T, mixed...): mixed) $function
     */
    public static function addInstanceExtension(string $name, Closure $function): void
    {
        if (array_key_exists($name, self::$instanceExtensions)) {
            throw new Error("Instance extension method '{$name}' is already defined!");
        }

        /** @var Closure(object, mixed...): mixed $function */;
        self::$instanceExtensions[$name] = $function;
    }

    /**
     * @param non-empty-string $name
     * @param (Closure(mixed...): mixed) $function
     */
    public static function addStaticExtension(string $name, Closure $function): void
    {
        if (array_key_exists($name, self::$staticExtensions)) {
            throw new Error("Static extension method '{$name}' is already defined!");
        }

        self::$staticExtensions[$name] = $function;
    }

    /**
     * @param non-empty-string $name
     * @return Closure(object, mixed...): mixed
     */
    public static function getInstanceExtension(string $name): Closure
    {
        return self::$instanceExtensions[$name] ?? throw new Error("Instance extension method '{$name}' is not defined!");
    }

    /**
     * @param non-empty-string $name
     * @return Closure(mixed...): mixed
     */
    public static function getStaticExtension(string $name): Closure
    {
        return self::$staticExtensions[$name] ?? throw new Error("Static extension method '{$name}' is not defined!");
    }

    /**
     * @return array<non-empty-string, (Closure(object, mixed...): mixed)>
     */
    public static function getAllInstanceExtensions(): array
    {
        return self::$instanceExtensions;
    }

    /**
     * @return array<non-empty-string, (Closure(mixed...): mixed)>
     */
    public static function getAllStaticExtensions(): array
    {
        return self::$staticExtensions;
    }

    /**
     * @param non-empty-string $name
     */
    public static function call(object $instance, string $name, array $arguments): mixed
    {
        return self::getInstanceExtension($name)($instance, ...$arguments);
    }

    /**
     * @param non-empty-string $name
     */
    public static function callStatic(string $name, array $arguments): mixed
    {
        return self::getStaticExtension($name)(...$arguments);
    }
}
