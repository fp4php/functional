<?php

declare(strict_types=1);

namespace Tests;

use Exception;
use Fp\Functional\Option\Option;
use HaydenPierce\ClassFinder\ClassFinder;
use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

use function Fp\Callable\compose;
use function Fp\Cast\asList;
use function Fp\Collection\last;
use function Fp\Collection\map;
use function Fp\Collection\reduce;
use function Fp\Collection\reindex;
use function Fp\Evidence\proveListOfScalar;
use function Fp\Evidence\proveOf;
use function Fp\Evidence\proveString;
use function Fp\Json\jsonSearch;
use function Fp\Json\lastExploded;
use function Symfony\Component\String\u;

/**
 * @psalm-type PhpBlock = string
 * @psalm-type BlockType = string
 * @psalm-type TracedVar = string
 */
abstract class PhpBlockTestCase extends TestCase
{
    /**
     * @psalm-return array<string, class-string>
     * @throws Exception
     */
    protected function getClassMap(): array
    {
        static $classMap = null;

        if (is_null($classMap)) {
            $classes = asList(
                ClassFinder::getClassesInNamespace('Fp\Functional', ClassFinder::RECURSIVE_MODE),
                ClassFinder::getClassesInNamespace('Tests\Mock', ClassFinder::RECURSIVE_MODE),
            );

            $classMap = reindex(
                $classes,
                fn(string $fqcn) => lastExploded($fqcn, '\\')
            );
        }

        /** @var array<string, class-string> */
        return $classMap;
    }

    /**
     * Extracts php block traced types
     *
     * @psalm-param PhpBlock $block
     * @psalm-return list<BlockType>
     */
    protected function analyzeBlock(string $block): array
    {
        $preparedBlock = $this->prepareBlock($block);

        $psalmPath = __DIR__ . '/../vendor/bin/psalm';
        $path = sys_get_temp_dir() . '/psalm_stub.php';
        file_put_contents($path, $preparedBlock);

        exec(
            sprintf('%s --output-format=json %s', ...[
                $psalmPath,
                $path,
            ]),
            $output
        );

        /** @var list<string> $outputLines */
        $outputLines = $output;

        return $this->parseTraceResult($outputLines)->getOrElse([]);
    }

    /**
     * Manipulates AST
     *
     * @psalm-param PhpBlock $block
     * @psalm-return PhpBlock
     */
    public function prepareBlock(string $block): string
    {
        $phpBlock = u($block)
            ->prepend('<?php' . ' ')
            ->append(' ?>')
            ->toString();

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($phpBlock);

        Option::do(function () use ($ast) {
            $stmts      = yield Option::fromNullable($ast);
            $last       = yield last($stmts);
            $expr       = yield proveOf($last, Node\Stmt\Expression::class);
            $assignExpr = yield proveOf($expr->expr, Node\Expr\Assign::class);
            $varExpr    = yield proveOf($assignExpr->var, Node\Expr\Variable::class);
            $varName    = yield proveString($varExpr->name);

            $last->setDocComment(new Doc(
                sprintf('/** @psalm-trace $%s */', $varName),
                $last->getStartLine()
            ));
        });

        return (new Standard())->prettyPrintFile($ast ?? []);
    }

    /**
     * Extracts psalm trace result
     *
     * @psalm-param list<string> $lines
     * @psalm-return Option<list<BlockType>>
     */
    private function parseTraceResult(array $lines): Option
    {
        return Option::do(function () use ($lines) {
            $json           = yield reduce($lines, fn(string $acc, string $line) => $acc . $line);
            $messages       = yield jsonSearch("[?type=='Trace'].message", $json);
            $stringMessages = yield proveListOfScalar($messages, 'string');

            return map(
                $stringMessages,
                fn(string $msg) => u($msg)->after(': ')->toString()
            );
        });
    }

    /**
     * Assert that last php block expression is of provided type
     *
     * @psalm-param PhpBlock $block
     * @psalm-param BlockType $type
     */
    protected function assertBlockTypes(string $block, string ...$types): void
    {
        $trim = fn(string $s): string => u($s)->replace(' ', '')->toString();
        $interpolateClasses = fn(string $s): string => strtr($s, $this->getClassMap());

        $prepareAndInterpolate = compose($trim, $interpolateClasses);

        $actualTypes = map($this->analyzeBlock($block), fn(string $t) => $trim($t));
        $expectedTypes = map($types, fn(string $t) => $prepareAndInterpolate($t));

        $this->assertEquals($expectedTypes, $actualTypes);
    }
}
