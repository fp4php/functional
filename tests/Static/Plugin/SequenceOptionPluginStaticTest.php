<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Fp\Functional\Option\Option;
use function Fp\Collection\sequenceOption;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveString;

final class SequenceOptionPluginStaticTest
{
    /**
     * @param array<string, Option<int>> $values
     * @return Option<array<string, int>>
     */
    public function sequenceArray(array $values): Option
    {
        return sequenceOption($values);
    }

    /**
     * @param non-empty-array<string, Option<int>> $values
     * @return Option<non-empty-array<string, int>>
     */
    public function sequenceNonEmptyArray(array $values): Option
    {
        return sequenceOption($values);
    }

    /**
     * @param list<Option<int>> $values
     * @return Option<list<int>>
     */
    public function sequenceList(array $values): Option
    {
        return sequenceOption($values);
    }

    /**
     * @param non-empty-list<Option<int>> $values
     * @return Option<non-empty-list<int>>
     */
    public function sequenceNonEmptyList(array $values): Option
    {
        return sequenceOption($values);
    }

    /**
     * @return Option<array{name: string, age: int}>
     */
    public function sequenceShape(mixed $name, mixed $age): Option
    {
        return sequenceOption([
            'name' => proveString($name),
            'age' => proveInt($age),
        ]);
    }

    /**
     * @return Option<array{string, int}>
     */
    public function sequenceTuple(mixed $name, mixed $age): Option
    {
        return sequenceOption([
            proveString($name),
            proveInt($age),
        ]);
    }
}
