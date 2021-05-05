<?php

use Behat\Behat\Context\Context;

use function Fp\Function\fold;
use function Symfony\Component\String\u;

/**
 * Defines application features from the specific context.
 */
class ExpressionContext implements Context
{
    /**
     * Initializes context.
     *
     * Every scenario gets its own context instance.
     * You can also pass arbitrary arguments to the
     * context constructor through behat.yml.
     */
    public function __construct(private string $expr = '')
    {
    }

    /**
     * @Given /expression (.*)/
     */
    public function givenExpression(string $expr): void
    {
        $traced_expr = u($expr)
            ->prepend('<?php /** @psalm-trace $res */ $res = ')
            ->append(' ?>')
            ->toString();

        $this->expr = $traced_expr;
    }

    /**
     * @Then /type is (.*)/
     * @throws Exception
     */
    public function expressionTypeIs(string $type): void
    {
        $expected_type = $type;
        $analyzed_type = $this->parseTraceResult($this->analyze($this->expr));

        if ($expected_type !== $analyzed_type) {
            throw new Exception(sprintf('Expects "%s"' . PHP_EOL . 'Got "%s"', ...[
                $expected_type,
                $analyzed_type,
            ]));
        }
    }

    /**
     * @param string $expr
     * @return list<string>
     */
    protected function analyze(string $expr): array
    {
        $tmp_file_name = tempnam(sys_get_temp_dir(), 'psalm_');
        file_put_contents($tmp_file_name, $expr);

        $psalmPath = __DIR__.'/../../vendor/bin/psalm';
        exec(
            sprintf('%s --output-format=compact %s', ...[
                $psalmPath,
                $tmp_file_name,
            ]),
            $output
        );

        /** @var list<string> $result */
        $result = $output;

        return $result;
    }

    /**
     * @param list<string> $lines
     */
    protected function parseTraceResult(array $lines): string
    {
        return fold(
            init: '',
            collection: $lines,
            callback: function (string $acc, string $line) {
                return $acc . (string) (u($line)->match('/res: (.+?)\s*?\|/u')[1] ?? '');
            }
        );
    }


}
