<?php

declare(strict_types=1);

namespace Fp\Psalm\Hooks;

use Fp\Functional\Option\Option;
use Fp\Psalm\Psalm;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Psalm\Codebase;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Union;
use function Fp\Collection\exists;
use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;

final class ConditionallyPureFunctionsAnalysis implements AfterExpressionAnalysisInterface
{
    private const CONDITIONALLY_PURE_FUNCTIONS = [
        'Fp\Collection\at',
        'Fp\Collection\butLast',
        'Fp\Collection\every',
        'Fp\Collection\exists',
        'Fp\Collection\filter',
        'Fp\Collection\first',
        'Fp\Collection\flatMap',
        'Fp\Collection\fold',
        'Fp\Collection\groupBy',
        'Fp\Collection\head',
        'Fp\Collection\keys',
        'Fp\Collection\last',
        'Fp\Collection\map',
        'Fp\Collection\partition',
        'Fp\Collection\pluck',
        'Fp\Collection\pop',
        'Fp\Collection\reduce',
        'Fp\Collection\reindex',
        'Fp\Collection\reverse',
        'Fp\Collection\second',
        'Fp\Collection\shift',
        'Fp\Collection\tail',
        'Fp\Collection\unique',
        'Fp\Collection\zip',
    ];

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $func_call = yield proveOf($event->getExpr(), FuncCall::class)
                ->filter(fn($call) => self::isConditionallyPure($call));

            if (self::hasImpureArg($event->getCodebase(), $event->getStatementsSource(), $func_call)) {
                return;
            }

            self::removeImpureFunctionCallIssue($event->getStatementsSource(), $func_call);
        });

        return null;
    }

    private static function removeImpureFunctionCallIssue(StatementsSource $source, FuncCall $func_call): void
    {
        Option::do(function() use ($source, $func_call) {
            $start_func_call_pos = yield proveInt($func_call->getAttribute('startFilePos'));

            $pathname = $source->getFilePath();
            $issues = IssueBuffer::getIssuesData();

            if (!array_key_exists($pathname, $issues)) {
                return;
            }

            IssueBuffer::remove($pathname, 'ImpureFunctionCall', $start_func_call_pos);
        });
    }

    private static function isConditionallyPure(FuncCall $func_call): bool
    {
        return proveOf($func_call->name, Name::class)
            ->flatMap(fn($name) => proveString($name->getAttribute('resolvedName')))
            ->map(fn($name) => in_array($name, self::CONDITIONALLY_PURE_FUNCTIONS, true))
            ->getOrElse(false);
    }

    /**
     * All iterable, impure-callable or mixed args are impure.
     */
    private static function hasImpureArg(Codebase $codebase, StatementsSource $source, FuncCall $func_call): bool
    {
        $arg_type_has_iterable = fn(Arg $arg): bool => Psalm::getArgUnion($arg, $source)
            ->map(fn($arg_type) => self::isArgTypeImpure($codebase, $arg_type))
            ->getOrElse(true);

        return exists($func_call->args, $arg_type_has_iterable);
    }

    private static function isArgTypeImpure(Codebase $codebase, Union $arg_type): bool
    {
        if ($arg_type->hasIterable()) {
            return true;
        }

        if ($arg_type->hasCallableType() && !self::isPureCallable($codebase, $arg_type)) {
            return true;
        }

        return false;
    }

    private static function isPureCallable(Codebase $codebase, Union $callable_type): bool
    {
        $pure_callable_type = new Union([
            new TCallable(is_pure: true),
        ]);

        return UnionTypeComparator::isContainedBy($codebase, $callable_type, $pure_callable_type);
    }
}
