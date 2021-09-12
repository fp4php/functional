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
use Fp\Collections\Set;
use Fp\Collections\Stream;
use Fp\Functional\Option\Option;
use Fp\Psalm\Util\Psalm;
use PhpParser\Node\Expr;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use Psalm\Context;
use Psalm\IssueBuffer;
use Psalm\Plugin\EventHandler\AfterExpressionAnalysisInterface;
use Psalm\Plugin\EventHandler\Event\AfterExpressionAnalysisEvent;
use Psalm\StatementsSource;
use Psalm\Type\Atomic\TGenericObject;

use function Fp\Evidence\proveInt;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Evidence\proveTrue;

/**
 * @psalm-type CallInfo = array{
 *     call_name: string,
 *     call_node: FuncCall|MethodCall,
 * }
 */
final class UnusedCallAnalysis implements AfterExpressionAnalysisInterface
{
    /**
     * @return Set<string>
     */
    public static function getWhiteList(): Set
    {
        static $set = null;

        if (is_null($set)) {
            $set = HashSet::collect([
                'Fp\Collection\forAll',
                LinkedList::class.'::forAll',
                ArrayList::class.'::forAll',
                HashSet::class.'::forAll',
                HashMap::class.'::forAll',
                NonEmptyLinkedList::class.'::forAll',
                NonEmptyArrayList::class.'::forAll',
                NonEmptyHashSet::class.'::forAll',
                NonEmptyHashMap::class.'::forAll',
                Stream::class.'::forAll',
                Stream::class.'::toFile',
                Stream::class.'::drain',
            ]);
        }

        /** @var Set<string> */
        return $set;
    }

    public static function afterExpressionAnalysis(AfterExpressionAnalysisEvent $event): ?bool
    {
        Option::do(function() use ($event) {
            $call_info = yield self::getCallInfo($event->getExpr(), $event->getStatementsSource());
            yield proveTrue(self::getWhiteList()($call_info['call_name']));
            self::removeUnusedMethodCallIssue($event->getStatementsSource(), $call_info['call_node']);
        });

        return null;
    }

    private static function removeUnusedMethodCallIssue(StatementsSource $source, FuncCall|MethodCall $call): void
    {
        Option::do(function() use ($source, $call) {
            $start_func_call_pos = yield proveInt(match (true) {
                $call instanceof FuncCall => $call->getAttribute('startFilePos'),
                $call instanceof MethodCall => $call->name->getAttribute('startFilePos'),
            });

            $pathname = $source->getFilePath();
            $issues = IssueBuffer::getIssuesData();

            if (!array_key_exists($pathname, $issues)) {
                return;
            }

            $issue_type = match (true) {
                $call instanceof FuncCall => 'UnusedFunctionCall',
                $call instanceof MethodCall => 'UnusedMethodCall',
            };

            IssueBuffer::remove($pathname, $issue_type, $start_func_call_pos);
        });
    }

    /**
     * @return Option<CallInfo>
     */
    public static function getCallInfo(Expr $expr, StatementsSource $source): Option
    {
        return self::getFunctionCallInfo($expr)
            ->orElse(fn() => self::getMethodCallInfo($expr, $source));
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
    public static function getMethodCallInfo(Expr $expr, StatementsSource $source): Option
    {
        return Option::do(function() use ($expr, $source) {
            $method_call = yield proveOf($expr, MethodCall::class);

            $method_name = yield proveOf($method_call->name, Identifier::class)
                ->map(fn(Identifier $id) => $id->name);

            $var_node = yield Psalm::getNodeUnion($method_call->var, $source);
            $var_type = yield Psalm::getUnionSingleAtomicOf($var_node, TGenericObject::class);

            return [
                'call_name' => "{$var_type->value}::{$method_name}",
                'call_node' => $method_call,
            ];
        });
    }
}
