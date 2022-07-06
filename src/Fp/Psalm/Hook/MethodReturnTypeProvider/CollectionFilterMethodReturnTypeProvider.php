<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\Map;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\NonEmptyMap;
use Fp\Collections\NonEmptySeq;
use Fp\Collections\NonEmptySet;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Streams\Stream;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\Isset_;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Param;
use Psalm\Node\Expr\VirtualArrowFunction;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Fp\Functional\Option\Option;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Collection\first;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;
use function Fp\classOf;

final class CollectionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getMethodNames(): array
    {
        return [
            'filter',
            strtolower('filterKV'),
            strtolower('filterNotNull'),
        ];
    }

    public static function getClassLikeNames(): array
    {
        return [
            HashMap::class,
            NonEmptyHashMap::class,
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
            NonEmptyMap::class,
            Stream::class,
        ];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        $reconciled = Option::do(function() use ($event) {
            yield proveTrue(in_array($event->getMethodNameLowercase(), self::getMethodNames()));

            $source          = yield proveOf($event->getSource(), StatementsAnalyzer::class);
            $predicate_arg   = yield self::extractPredicateArg($event);
            $predicate       = yield proveOf($predicate_arg->value, FunctionLike::class);
            $template_params = yield Option::fromNullable($event->getTemplateTypeParameters());

            $collection_type_params = 2 === count($template_params)
                ? new CollectionTypeParams($template_params[0], $template_params[1])
                : new CollectionTypeParams(Type::getArrayKey(), $template_params[0]);

            $refinement_context = new RefinementContext(
                refine_for: $event->getMethodNameLowercase() === 'filterkv'
                    ? RefinementContext::FILTER_KEY_VALUE
                    : RefinementContext::FILTER_VALUE,
                predicate: $predicate,
                execution_context: $event->getContext(),
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
        return first($event->getCallArgs())
            ->orElse(fn() => self::mockNotNullPredicateArg($event));
    }

    /**
     * @psalm-return Option<Arg>
     */
    public static function mockNotNullPredicateArg(MethodReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            yield proveTrue(strtolower('filterNotNull') === $event->getMethodNameLowercase());

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
     * @psalm-return Option<Union>
     */
    private static function getReturnType(MethodReturnTypeProviderEvent $event, CollectionTypeParams $result): Option
    {
        $class_name = str_replace('NonEmpty', '', $event->getFqClasslikeName());

        $template_params = classOf($class_name, Map::class) || classOf($class_name, NonEmptyMap::class)
            ? [$result->key_type, $result->val_type]
            : [$result->val_type];

        return Option::some(new Union([
            new TGenericObject($class_name, $template_params),
        ]));
    }
}
