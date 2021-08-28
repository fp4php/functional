<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Psalm\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\TypeRefinement\GetPredicateFunction;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Param;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Fp\Psalm\TypeRefinement\RefineByPredicate;
use Fp\Psalm\TypeRefinement\RefinementContext;
use Fp\Psalm\TypeRefinement\RefinementResult;
use Fp\Functional\Option\Option;

use function Fp\Collection\first;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class CollectionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public const FILTER = 'filter';
    public const FILTER_NOT_NULL = 'filternotnull';

    public const ALLOWED_METHODS = [
        self::FILTER,
        self::FILTER_NOT_NULL,
    ];

    public static function getClassLikeNames(): array
    {
        return [
            HashMap::class,
            LinkedList::class,
            NonEmptyLinkedList::class,
            ArrayList::class,
            NonEmptyArrayList::class,
            HashSet::class,
            NonEmptyHashSet::class,
            Seq::class,
            NonEmptySeq::class,
            Set::class,
            NonEmptySet::class,
            Map::class,
        ];
    }

    /**
     * @psalm-suppress InternalMethod
     */
    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Type\Union
    {
        $reconciled = Option::do(function() use ($event) {
            yield proveTrue(in_array($event->getMethodNameLowercase(), self::ALLOWED_METHODS, true));
            $source = yield proveOf($event->getSource(), StatementsAnalyzer::class);
            $predicate = yield self::extractPredicateArg($event)->flatMap([GetPredicateFunction::class, 'from']);
            $template_params = yield Option::fromNullable($event->getTemplateTypeParameters());

            $collection_type_params = 2 === count($template_params)
                ? new CollectionTypeParams($template_params[0], $template_params[1])
                : new CollectionTypeParams(Type::getArrayKey(), $template_params[0]);

            $refinement_context = new RefinementContext(
                refine_for: $event->getFqClasslikeName(),
                predicate: $predicate,
                execution_context: $event->getContext(),
                codebase: $source->getCodebase(),
                source: $source,
            );

            $result = RefineByPredicate::for(
                $refinement_context,
                $collection_type_params,
            );

            return yield self::getReturnType($event, $result);
        });

        return $reconciled->get();
    }

    /**
     * @psalm-return Option<Arg>
     */
    public static function extractPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return first($event->getCallArgs())->orElse(fn() => self::mockNotNullPredicateArg($event));
    }

    /**
     * @psalm-return Option<Arg>
     */
    public static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            yield proveTrue(self::FILTER_NOT_NULL === $event->getMethodNameLowercase());

            $var = new Variable('$elem');
            $expr = new Isset_([$var]);
            $param = new Param($var);

            $expr = new VirtualArrowFunction([
                'expr' => $expr,
                'params' => [$param],
            ]);

            return new Arg($expr);
        });
    }

    /**
     * @psalm-return Option<Type\Union>
     */
    private static function getReturnType(MethodReturnTypeProviderEvent $event, RefinementResult $result): Option
    {
        $class_name = str_replace('NonEmpty', '', $event->getFqClasslikeName());

        $template_params = is_a($class_name, Map::class, true)
            ? [$result->collection_key_type, $result->collection_value_type]
            : [$result->collection_value_type];

        return Option::some(new Type\Union([
            new Type\Atomic\TGenericObject($class_name, $template_params),
        ]));
    }
}
