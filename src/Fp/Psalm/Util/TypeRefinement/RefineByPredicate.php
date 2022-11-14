<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\TypeRefinement;

use Fp\PsalmToolkit\Toolkit\PsalmApi;
use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Stmt\Return_;
use Fp\Functional\Option\Option;
use Psalm\CodeLocation;
use Psalm\Internal\Algebra;
use Psalm\Internal\Algebra\FormulaGenerator;
use Psalm\Type\Reconciler;
use Psalm\Type\Union;

use function Fp\Collection\at;
use function Fp\Collection\first;
use function Fp\Collection\second;
use function Fp\Collection\sequenceOptionT;
use function Fp\Evidence\proveNonEmptyArray;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;

/**
 * @psalm-type PsalmAssertions = array<string, array<array<int, string>>>
 */
final class RefineByPredicate
{
    /**
     * Refine collection type-parameters
     * By predicate expression
     */
    public static function for(RefinementContext $context): CollectionTypeParams
    {
        return new CollectionTypeParams(
            self::forKey($context)->getOrElse($context->type_params->key_type),
            self::forValue($context)->getOrElse($context->type_params->val_type),
        );
    }

    /**
     * Try to refine collection key type-parameter
     *
     * @psalm-return Option<Union>
     */
    private static function forKey(RefinementContext $context): Option
    {
        return sequenceOptionT(
            fn() => self::getPredicateKeyArgumentName($context),
            fn() => self::getPredicateSingleReturn($context),
            fn() => Option::some($context),
            fn() => Option::some($context->type_params->key_type),
        )->flatMapN(self::refine(...));
    }

    /**
     * Try to refine collection value type-parameter
     *
     * @psalm-return Option<Union>
     */
    private static function forValue(RefinementContext $context): Option
    {
        return sequenceOptionT(
            fn() => self::getPredicateValueArgumentName($context),
            fn() => self::getPredicateSingleReturn($context),
            fn() => Option::some($context),
            fn() => Option::some($context->type_params->val_type),
        )->flatMapN(self::refine(...));
    }

    /**
     * Returns key argument name of $predicate that going to be refined.
     *
     * @psalm-return Option<non-empty-string>
     */
    private static function getPredicateKeyArgumentName(RefinementContext $context): Option
    {
        return Option::some($context->refine_for)
            ->filter(fn($refine_for) => RefineForEnum::KeyValue === $context->refine_for)
            ->flatMap(fn() => first($context->predicate->getParams()))
            ->flatMap(fn($key_param) => proveOf($key_param->var, Variable::class))
            ->flatMap(fn($variable) => proveString($variable->name))
            ->map(fn($name) => '$' . $name);
    }

    /**
     * Returns value argument name of $predicate that going to be refined.
     *
     * @psalm-return Option<non-empty-string>
     */
    private static function getPredicateValueArgumentName(RefinementContext $context): Option
    {
        return Option::some($context->refine_for)
            ->filter(fn($refine_for) => RefineForEnum::Value === $context->refine_for)
            ->flatMap(fn() => first($context->predicate->getParams()))
            ->orElse(fn() => second($context->predicate->getParams()))
            ->flatMap(fn($value_param) => proveOf($value_param->var, Node\Expr\Variable::class))
            ->flatMap(fn($variable) => proveString($variable->name))
            ->map(fn($name) => '$' . $name);
    }

    /**
     * Returns single return expression of $predicate if present.
     * Collection type parameter can be refined only for function with single return.
     *
     * @psalm-return Option<Expr>
     */
    private static function getPredicateSingleReturn(RefinementContext $context): Option
    {
        return Option::fromNullable($context->predicate->getStmts())
            ->filter(fn($stmts) => 1 === count($stmts))
            ->flatMap(fn($stmts) => proveOf($stmts[0], Return_::class))
            ->flatMap(fn($return) => Option::fromNullable($return->expr));
    }

    /**
     * Collects assertion for $predicate_arg_name from $return_expr.
     *
     * @return Option<PsalmAssertions>
     */
    private static function collectAssertions(RefinementContext $context, Expr $return_expr): Option
    {
        $cond_object_id = spl_object_id($return_expr);

        $truths = Algebra::getTruthsFromFormula(
            clauses: FormulaGenerator::getFormula(
                conditional_object_id: $cond_object_id,
                creating_object_id: $cond_object_id,
                conditional: $return_expr,
                this_class_name: $context->execution_context->self,
                source: $context->source,
                codebase: PsalmApi::$codebase,
            ),
            creating_conditional_id: $cond_object_id,
        );

        return proveNonEmptyArray($truths);
    }

    /**
     * Reconciles $collection_type_param with $assertions using internal Psalm api.
     *
     * @psalm-param PsalmAssertions $assertions
     * @psalm-return Option<Union>
     */
    private static function refine(
        string $arg_name,
        Expr $return_expr,
        RefinementContext $context,
        Union $type,
    ): Option
    {
        // reconcileKeyedTypes takes it by ref
        $changed_var_ids = [];

        return self::collectAssertions($context, $return_expr)
            ->map(fn($assertions) => Reconciler::reconcileKeyedTypes(
                new_types: $assertions,
                active_new_types: $assertions,
                existing_types: [$arg_name => $type],
                changed_var_ids: $changed_var_ids,
                referenced_var_ids: [$arg_name => true],
                statements_analyzer: $context->source,
                template_type_map: $context->source->getTemplateTypeMap() ?: [],
                code_location: new CodeLocation($context->source, $return_expr)
            ))
            ->flatMap(fn($reconciled_types) => at($reconciled_types, $arg_name));
    }
}
