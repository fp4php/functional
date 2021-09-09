<?php

declare(strict_types=1);

namespace Fp\Psalm\Util;

use Fp\Collections\ArrayList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptySet;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Extractor\AtomicExtractor;
use Fp\Psalm\Util\Extractor\NodeExtractor;
use Fp\Psalm\Util\Extractor\TypeParamExtractor;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\FunctionLike;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TLiteralFloat;
use Psalm\Type\Atomic\TLiteralInt;
use Psalm\Type\Atomic\TLiteralString;
use Psalm\Type\Union;

use function Fp\Collection\head;

/**
 * Psalm helper methods
 *
 * @internal
 */
class PSL
{
    use TypeParamExtractor;
    use AtomicExtractor;
    use NodeExtractor;
    use Uni;

    /**
     * @psalm-return Option<NonEmptySet<int|float|string>>
     */
    public static function getUnionLiteralValues(Union $union): Option
    {
        $literalValues = ArrayList::collect($union->getLiteralStrings())
            ->appendedAll($union->getLiteralFloats())
            ->appendedAll($union->getLiteralInts())
            ->map(fn(TLiteralString|TLiteralFloat|TLiteralInt $literal) => $literal->value);

        return NonEmptyHashSet::collect($literalValues);
    }

    /**
     * @psalm-return Option<int|float|string>
     */
    public static function getUnionSingleLiteralValue(Union $union): Option
    {
        $someUnion = Option::some($union);

        return $someUnion
            ->filter(fn(Union $union) => $union->isSingleStringLiteral())
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleFloatLiteral()
                );
            })
            ->orElse(function () use ($someUnion) {
                return $someUnion->filter(
                    fn(Union $union) => $union->isSingleIntLiteral()
                );
            })
            ->flatMap(fn(Union $type) => self::getUnionLiteralValues($type))
            ->map(fn(NonEmptySet $literals) => $literals->head());
    }
}
