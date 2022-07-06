<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\MethodReturnTypeProvider;

use Fp\Functional\Option\Option;
use Fp\Functional\Option\Some;
use Fp\Psalm\Util\TypeRefinement\CollectionTypeParams;
use Fp\Psalm\Util\TypeRefinement\RefineByPredicate;
use Fp\Psalm\Util\TypeRefinement\RefinementContext;
use PhpParser\Node\Arg;
use PhpParser\Node\FunctionLike;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\MethodReturnTypeProviderInterface;
use Psalm\Type;
use Psalm\Type\Atomic\TGenericObject;
use Psalm\Type\Union;

use function Fp\Collection\first;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveTrue;

final class OptionFilterMethodReturnTypeProvider implements MethodReturnTypeProviderInterface
{
    public static function getClassLikeNames(): array
    {
        return [Option::class, Some::class];
    }

    public static function getMethodReturnType(MethodReturnTypeProviderEvent $event): ?Union
    {
        $return_type = Option::do(function() use ($event) {
            yield proveTrue('filter' === $event->getMethodNameLowercase());

            $predicate = yield first($event->getCallArgs())
                ->flatMap(fn(Arg $arg) => proveOf($arg->value, FunctionLike::class));

            $source = yield proveOf($event->getSource(), StatementsAnalyzer::class);
            $option_type_param = yield first($event->getTemplateTypeParameters() ?? []);

            $collection_type_params = new CollectionTypeParams(
                key_type: Type::getArrayKey(),
                val_type: $option_type_param,
            );

            $refinement_context = new RefinementContext(
                refine_for: RefinementContext::FILTER_VALUE,
                predicate: $predicate,
                execution_context: $event->getContext(),
                source: $source,
            );

            $result = RefineByPredicate::for($refinement_context, $collection_type_params);

            return new Union([
                new TGenericObject(Option::class, [$result->val_type]),
            ]);
        });

        return $return_type->get();
    }
}
