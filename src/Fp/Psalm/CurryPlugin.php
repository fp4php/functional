<?php

declare(strict_types=1);

namespace Fp\Psalm;

use Fp\Functional\Option\Option;
use PhpParser\Node\Arg;
use Psalm\Codebase;
use Psalm\CodeLocation;
use Psalm\Context;
use Psalm\Internal\Analyzer\StatementsAnalyzer;
use Psalm\Issue\InvalidArgument;
use Psalm\IssueBuffer;
use Psalm\Plugin\Hook\FunctionReturnTypeProviderInterface;
use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use Psalm\StatementsSource;
use Psalm\Storage\FunctionLikeParameter;
use Psalm\Type;
use Psalm\Type\Atomic\TClosure;
use Psalm\Type\Union;
use SimpleXMLElement;

use function Fp\Function\first;
use function Fp\Function\head;
use function Fp\Function\tail;

class CurryPlugin implements PluginEntryPointInterface, FunctionReturnTypeProviderInterface
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
            'fp\function\curry',
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
        $codebase = $statements_source->getCodebase();

        return head($call_args)
            ->map(fn(Arg $head_arg) => self::getArgType($head_arg, $statements_source))
            ->map(fn(Union $head_arg_type) => head($head_arg_type->getClosureTypes())->get())
            ->map(function (TClosure $closure_type) use ($statements_source, $call_args, $code_location, $codebase) {
                $closure_params = $closure_type->params ?? [];
                $tail_args = tail($call_args);

                self::assertValidClosureArgs($codebase, $statements_source,$code_location, $closure_type, $tail_args);

                $args_tail_size = count($tail_args);
                $curried_params = array_slice($closure_params, $args_tail_size);
                $closure_type->params = $curried_params;

                return new Union([$closure_type]);
            })
            ->get();
    }

    /**
     * @psalm-param array<array-key, Arg> $args
     */
    private static function assertValidClosureArgs(
        Codebase $codebase,
        StatementsSource $statements_source,
        CodeLocation $location,
        TClosure $closure,
        array $args
    ): void
    {
        $args_list = array_values($args);
        $params_list = $closure->params ?? [];

        for ($i = 0; $i < count($args); $i++) {
            $arg = $args_list[$i] ?? null;
            $param = $params_list[$i] ?? null;

            if (!isset($arg, $param)) {
                continue;
            }

            $param_type = $param->type ?? Type::getMixed();

            Option::of(self::getArgType($arg, $statements_source))
                ->map(function (Union $arg_type) use ($param_type, $codebase) {
                    $isSubtypeOf = $codebase->isTypeContainedByType($arg_type, $param_type);
                    return !$isSubtypeOf ? $arg_type : null;
                })
                ->map(fn(Union $arg_type) => self::issueInvalidArgument($location, (string) $param_type));
        }
    }


    private static function issueInvalidArgument(CodeLocation $code_location, string $expected_type): void
    {
        $issue = new InvalidArgument(
            sprintf('argument should be of type %s', $expected_type),
            $code_location,
            self::getFunctionIds()[0]
        );

        IssueBuffer::accepts($issue);
    }

    private static function getArgType(Arg $arg, StatementsSource $source): ?Union
    {
        return $source->getNodeTypeProvider()->getType($arg->value);
    }
}
