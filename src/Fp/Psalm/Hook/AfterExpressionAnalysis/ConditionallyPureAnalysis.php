<?php

declare(strict_types=1);

namespace Fp\Psalm\Hook\AfterExpressionAnalysis;

use Fp\Collections\ArrayList;
use Fp\Collections\HashMap;
use Fp\Collections\HashSet;
use Fp\Collections\LinkedList;
use Fp\Collections\NonEmptyArrayList;
use Fp\Collections\NonEmptyHashMap;
use Fp\Collections\NonEmptyHashSet;
use Fp\Collections\NonEmptyLinkedList;
use Fp\Collections\Seq;
use Fp\Collections\Set;
use Fp\Streams\Stream;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Psalm\Codebase;
use Psalm\Context;
use Psalm\Internal\Type\Comparator\UnionTypeComparator;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\StatementsSource;
use Psalm\Storage\ClassLikeStorage;
use Psalm\Type\Atomic\TCallable;
use Psalm\Type\Union;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;

/**
 * @psalm-type CallInfo = array{
 *     call_name: string,
 *     call_node: FuncCall|StaticCall,
 * }
 */
final class ConditionallyPureAnalysis implements AfterExpressionAnalysisInterface
{
    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $call_info = yield self::getCallInfo($event->getExpr(), $event->getContext());
            $conditionally_pure = self::getConditionallyPureSet();
            $immutable_pure = self::getInImmutableContextPureSet();
            $call_name = $call_info['call_name'];
            $call_node = $call_info['call_node'];
            $class_likes = $event->getCodebase()->classlikes;

            yield proveTrue($conditionally_pure($call_name) && !self::hasImpureArg(
                    $event->getCodebase(),
                    $event->getStatementsSource(),
                    ArrayList::collect($call_node->args)
                ))
                ->orElse(fn() => proveTrue($immutable_pure($call_name))
                    ->flatMap(fn() => Option::fromNullable($event->getContext()->self))
                    ->flatMap(fn($self) => Option::fromNullable($class_likes->getStorageFor($self)))
                    ->filter(fn(ClassLikeStorage $class_like) => $class_like->mutation_free)
                    ->filter(fn(ClassLikeStorage $class_like) => $class_like->external_mutation_free)
                )
                ->tap(fn() => self::removeImpureCallIssue($event->getStatementsSource(), $call_node));
        });

        return null;
    }

    private static function removeImpureCallIssue(StatementsSource $source, FuncCall|StaticCall $call): void
    {
        Option::do(function() use ($source, $call) {
            $start_func_call_pos = yield proveInt(match (true) {
                $call instanceof FuncCall => $call->getAttribute('startFilePos'),
                $call instanceof StaticCall => $call->name->getAttribute('startFilePos'),
            });

            $pathname = $source->getFilePath();
            $issues = IssueBuffer::getIssuesData();

            yield proveTrue(array_key_exists($pathname, $issues));

            $issue_type = match (true) {
                $call instanceof FuncCall => 'ImpureFunctionCall',
                $call instanceof StaticCall => 'ImpureMethodCall',
            };

            IssueBuffer::remove($pathname, $issue_type, $start_func_call_pos);
        });
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getCallInfo(Expr $expr, Context $context): Option
    {
        return self::getFunctionCallInfo($expr)
            ->orElse(fn() => self::getStaticMethodCallInfo($expr, $context));
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getFunctionCallInfo(Expr $expr): Option
    {
        return Option::do(function() use ($expr) {
            $func_call = yield proveOf($expr, FuncCall::class);
            $func_name = yield proveOf($func_call->name, Name::class)
                ->flatMap(fn($name) => proveString($name->getAttribute('resolvedName')));

            return [
                'call_name' => $func_name,
                'call_node' => $func_call,
            ];
        });
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getStaticMethodCallInfo(Expr $expr, Context $context): Option
    {
        return Option::do(function() use ($expr, $context) {
            $static_call = yield proveOf($expr, StaticCall::class);

            $class_name = yield proveOf($static_call->class, Name::class)
                ->filter(fn(Name $name) => !$name->isSpecialClassName())
                ->flatMap(fn($name) => proveString($name->getAttribute('resolvedName')))
                ->orElse(fn() => Option::fromNullable($context->self));

            $method_name = yield proveOf($static_call->name, Identifier::class)
                ->map(fn($id) => $id->name);

            return [
                'call_name' => "{$class_name}::{$method_name}",
                'call_node' => $static_call,
            ];
        });
    }

    /**
     * All iterable, impure-callable or mixed args are impure.
     *
     * @param Seq<Arg> $call_args
     */
    private static function hasImpureArg(Codebase $codebase, StatementsSource $source, Seq $call_args): bool
    {
        return $call_args
            ->filterMap(fn(Arg $arg) => Psalm::getArgUnion($arg, $source))
            ->exists(fn(Union $arg_type) => self::isArgTypeImpure($codebase, $arg_type));
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

    /**
     * FQN of conditionally pure functions or static methods
     *
     * @return Set<string>
     */
    public static function getConditionallyPureSet(): Set
    {
        static $whiteList = null;

        if (is_null($whiteList)) {
            $whiteList = HashSet::collect([
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
                LinkedList::class.'::collect',
                ArrayList::class.'::collect',
                HashSet::class.'::collect',
                HashMap::class.'::collect',
                HashMap::class.'::collectPairs',
                NonEmptyLinkedList::class.'::collect',
                NonEmptyLinkedList::class.'::collectUnsafe',
                NonEmptyLinkedList::class.'::collectNonEmpty',
                NonEmptyArrayList::class.'::collect',
                NonEmptyArrayList::class.'::collectUnsafe',
                NonEmptyArrayList::class.'::collectNonEmpty',
                NonEmptyHashSet::class.'::collect',
                NonEmptyHashSet::class.'::collectUnsafe',
                NonEmptyHashSet::class.'::collectNonEmpty',
                NonEmptyHashMap::class.'::collect',
                NonEmptyHashMap::class.'::collectUnsafe',
                NonEmptyHashMap::class.'::collectNonEmpty',
                NonEmptyHashMap::class.'::collectPairs',
                NonEmptyHashMap::class.'::collectPairsUnsafe',
                NonEmptyHashMap::class.'::collectPairsNonEmpty',
                Stream::class.'::emits',
            ]);
        }

        /** @var Set<string> */
        return $whiteList;
    }

    /**
     * @return Set<string>
     */
    public static function getInImmutableContextPureSet(): Set
    {
        static $whiteList = null;

        if (is_null($whiteList)) {
            $whiteList = HashSet::collect([
                'Fp\Callable\asGenerator',
            ]);
        }

        /** @var Set<string> */
        return $whiteList;
    }
}
