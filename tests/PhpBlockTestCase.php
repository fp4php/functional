<?php

declare(strict_types=1);

namespace Tests;

use Fp\Functional\Option\Option;
use PhpParser\Comment\Doc;
use PhpParser\ParserFactory;
use PhpParser\PrettyPrinter\Standard;
use PHPUnit\Framework\TestCase;

use function Fp\Function\Cast\asList;
use function Fp\Function\Cast\proveString;
use function Fp\Function\Collection\fold;
use function Fp\Function\Collection\last;
use function Fp\Function\Collection\reverse;
use function Fp\Function\Collection\tail;
use function Fp\Function\Json\jsonSearch;
use function Symfony\Component\String\u;

/**
 * @psalm-type PhpBlock = string
 * @psalm-type BlockType = string
 */
abstract class PhpBlockTestCase extends TestCase
{
    /**
     * Extracts php block last expression type
     *
     * @psalm-param PhpBlock $block
     * @psalm-return Option<BlockType>
     */
    protected function analyzeBlock(string $block): Option
    {
        $preparedBlock = $this->prepareBlock($block);

        $psalmPath = __DIR__ . '/../vendor/bin/psalm';
        $tmpFileName = tempnam(sys_get_temp_dir(), 'psalm_');
        file_put_contents($tmpFileName, $preparedBlock);

        exec(
            sprintf('%s --output-format=json %s', ...[
                $psalmPath,
                $tmpFileName,
            ]),
            $output
        );

        /** @var list<string> $outputLines */
        $outputLines = $output;

        return $this->parseTraceResult($outputLines);
    }

    /**
     * Manipulates AST
     *
     * @psalm-param PhpBlock $block
     * @psalm-return PhpBlock
     */
    private function prepareBlock(string $block): string
    {
        $phpBlock = u($block)
            ->prepend('<?php' . ' ')
            ->append(' ?>')
            ->toString();

        $parser = (new ParserFactory())->create(ParserFactory::PREFER_PHP7);
        $ast = $parser->parse($phpBlock);

        $mappedAst = Option::do(function () use ($ast) {
            $stmts = yield Option::of($ast);
            $listOfStmts = asList($stmts);
            $reversedHead = yield last($listOfStmts);
            $reversedTail = reverse(tail(reverse($listOfStmts)));

            $docComment = new Doc('/** @psalm-trace $result */', $reversedHead->getStartLine());
            $reversedHead->setDocComment($docComment);

            return [...$reversedTail, $reversedHead];
        });

        $prettyPrinter = new Standard();
        return $prettyPrinter->prettyPrintFile($mappedAst->get() ?? []);
    }

    /**
     * Extracts psalm trace result
     * @psalm-param list<string> $lines
     * @psalm-return Option<string>
     */
    private function parseTraceResult(array $lines): Option
    {
        $json = fold(
            init: '',
            collection: $lines,
            callback: fn(string $acc, string $line) => $acc . $line
        );

        return jsonSearch("[?type=='Trace'].message|[0]", $json)
            ->flatMap(fn($message) => proveString($message))
            ->map(fn(string $message) => u($message)->after('$result: ')->toString());
    }

    /**
     * Assert that last php block expression is of provided type
     *
     * @psalm-param PhpBlock $block
     * @psalm-param BlockType $type
     */
    protected function assertBlockType(string $block, string $type): void
    {
        $trim = fn(string $s): string => u($s)->replace(' ', '')->toString();

        $expectedType = $trim($type);
        $actualType = $trim($this->analyzeBlock($block)->getOrElse(''));

        $this->assertEquals($expectedType, $actualType);
    }
}
