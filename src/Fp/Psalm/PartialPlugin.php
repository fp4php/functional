<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Issue\InvalidArgument;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\StatementsSource;
use Psalm\Type;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;
use SimpleXMLElement;

use function Fp\Function\head;
use function Fp\Function\tail;
use function Symfony\Component\String\u;

class PartialPlugin implements PluginEntryPointInterface, FunctionReturnTypeProviderInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        $registration->registerHooksFromClass(self::class);
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionIds(): array
    {
        return [
            'fp\function\partial',
            'fp\function\partialleft',
            'fp\function\partialright',
        ];
    }

    /**
     * @inheritDoc
     */
    public static function getFunctionReturnType(
        StatementsSource $statements_source,
        string $function_id,
        array $call_args,
        Context $context,
        CodeLocation $code_location
    ): ?Union
    {
        $is_partial_right = u($function_id)->endsWith('right');
        $location = $code_location;
        $source = $statements_source;
        $args = $call_args;
        $codebase = $source->getCodebase();

        return head($args)
            ->flatMap(fn(Arg $head_arg) => self::getArgType($head_arg, $source))
            ->flatMap(fn(Union $head_arg_type) => head($head_arg_type->getClosureTypes()))
            ->map(function (TClosure $closure_type) use ($function_id, $source, $args, $location, $codebase, $is_partial_right) {
                $closure_type_copy = clone $closure_type;
                $closure_params = $closure_type_copy->params ?? [];
                $tail_args = tail($args);

                self::assertValidClosureArgs(
                    function_id: $function_id,
                    codebase: $codebase,
                    statements_source: $source,
                    location: $location,
                    closure: $closure_type_copy,
                    args: $tail_args,
                    is_partial_right: $is_partial_right
                );

                $args_tail_size = count($tail_args);

                if (0 === $args_tail_size) {
                    return new Union([$closure_type_copy]);
                }

                $free_params = $is_partial_right
                    ? array_slice($closure_params, 0, -$args_tail_size)
                    : array_slice($closure_params, $args_tail_size);

                $closure_type_copy->params = $free_params;

                return new Union([$closure_type_copy]);
            })
            ->get();
    }

    /**
     * @psalm-param array<array-key, Arg> $args
     */
    private static function assertValidClosureArgs(
        string $function_id,
        Codebase $codebase,
        StatementsSource $statements_source,
        CodeLocation $location,
        TClosure $closure,
        array $args,
        bool $is_partial_right
    ): void
    {
        $args_list = array_values($args);
        $params_list = $is_partial_right
            ? array_reverse($closure->params ?? [])
            : $closure->params ?? [];

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args_list[$i] ?? null;
            $param = $params_list[$i] ?? null;

            if (!isset($arg, $param)) {
                continue;
            }

            $param_type = $param->type ?? Type::getMixed();
            $arg_type = self::getArgType($arg, $statements_source);

            if ($arg_type->isEmpty()) {
                continue;
            }

            $is_subtype_of = $codebase->isTypeContainedByType($arg_type->get(), $param_type);

            if ($is_subtype_of) {
                continue;
            }

            self::issueInvalidArgument(
                function_id: $function_id,
                code_location: $location,
                expected_type: (string) $param_type
            );
        }
    }


    private static function issueInvalidArgument(string $function_id, CodeLocation $code_location, string $expected_type): void
    {
        $issue = new InvalidArgument(
            sprintf('argument should be of type %s', $expected_type),
            $code_location,
            $function_id
        );

        IssueBuffer::accepts($issue);
    }

    /**
     * @psalm-return Option<Union>
     */
    private static function getArgType(Arg $arg, StatementsSource $source): Option
    {
        return Option::of($source->getNodeTypeProvider()->getType($arg->value));
    }
}
