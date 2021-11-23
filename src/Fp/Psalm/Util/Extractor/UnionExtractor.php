<?php

declare(strict_types=1);

namespace Fp\Psalm\Util\Extractor;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\Return_;
use PhpParser\Node\VariadicPlaceholder;
use Psalm\Plugin\EventHandler\Event\FunctionReturnTypeProviderEvent;
use Psalm\Plugin\EventHandler\Event\MethodReturnTypeProviderEvent;
use Psalm\StatementsSource;
use Psalm\Type\Union;

use function Fp\Collection\head;

/**
 * @internal
 */
trait UnionExtractor
{
    /**
     * @psalm-return Option<Union>
     */
    public static function getArgUnion(Arg|VariadicPlaceholder $arg, StatementsSource $source): Option
    {
        $provider = $source->getNodeTypeProvider();

        return Option::some($arg)
            ->filter(fn($arg) => $arg instanceof Arg)
            ->flatMap(fn($arg) => Option::fromNullable($provider->getType($arg->value)));
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getNodeUnion(Expr|Name|Return_ $node, StatementsSource $source): Option
    {
        return Option::fromNullable($source->getNodeTypeProvider()->getType($node));
    }

    /**
     * @psalm-return Option<Union>
     */
    public static function getFirstArgUnion(MethodReturnTypeProviderEvent|FunctionReturnTypeProviderEvent $event): Option
    {
        return Option::do(function () use ($event) {
            $arg = yield head($event->getCallArgs());
            return yield self::getArgUnion($arg, match(true) {
                $event instanceof MethodReturnTypeProviderEvent => $event->getSource(),
                $event instanceof FunctionReturnTypeProviderEvent => $event->getStatementsSource(),
            });
        });
    }
}
