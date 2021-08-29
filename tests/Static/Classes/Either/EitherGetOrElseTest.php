<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Either;

use Tests\PhpBlockTestCase;

final class EitherGetOrElseTest extends PhpBlockTestCase
{
    public function testGetOrElseWithInt(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                /** @var int $input1 */
                $input1 = null;
                /** @psalm-trace $result1 */
                $result1 = Fp\Functional\Either\Either::right($input1)->getOrElse(1);
                
                /** @var 1 $input2 */
                $input2 = null;
                /** @psalm-trace $result2 */
                $result2 = Fp\Functional\Either\Either::right($input2)->getOrElse(2);
                
                /** @var bool $input3 */
                $input3 = null;
                /** @psalm-trace $result3 */
                $result3 = Fp\Functional\Either\Either::right($input3)->getOrElse(true);
                
                /** @var true $input4 */
                $input4 = null;
                /** @psalm-trace $result4 */
                $result4 = Fp\Functional\Either\Either::right($input4)->getOrElse(false);
                
                /** @var float $input5 */
                $input5 = null;
                /** @psalm-trace $result5 */
                $result5 = Fp\Functional\Either\Either::right($input5)->getOrElse(1.1);
                
                /** @var 1.1 $input6 */
                $input6 = null;
                /** @psalm-trace $result6 */
                $result6 = Fp\Functional\Either\Either::right($input6)->getOrElse(2.2);
                
                /** @var string $input7 */
                $input7 = null;
                /** @psalm-trace $result7 */
                $result7 = Fp\Functional\Either\Either::right($input7)->getOrElse("1");
                
                /** @var string $input8 */
                $input8 = null;
                /** @psalm-trace $result8 */
                $result8 = Fp\Functional\Either\Either::right($input8)->getOrElse(\Tests\Mock\Foo::class);
                
                /** @var class-string<\Tests\Mock\Foo> $input9 */
                $input9 = null;
                /** @psalm-trace $result9 */
                $result9 = Fp\Functional\Either\Either::right($input9)->getOrElse("1.1");
                
                /** @var list<int> $input10 */
                $input10 = null;
                /** @psalm-trace $result10 */
                $result10 = Fp\Functional\Either\Either::right($input10)->getOrElse([]);
                
                /** @var list<int> $input11 */
                $input11 = null;
                /** @psalm-trace $result11 */
                $result11 = Fp\Functional\Either\Either::right($input11)->getOrElse([1]);
                
                /** @var non-empty-list<int> $input12 */
                $input12 = null;
                /** @psalm-trace $result12 */
                $result12 = Fp\Functional\Either\Either::right($input12)->getOrElse([]);
                
                /** @var non-empty-list<int> $input13 */
                $input13 = null;
                /** @psalm-trace $result13 */
                $result13 = Fp\Functional\Either\Either::right($input13)->getOrElse([1]);
                
                /** @var array<string, int> $input14 */
                $input14 = null;
                /** @psalm-trace $result14 */
                $result14 = Fp\Functional\Either\Either::right($input14)->getOrElse([]);
                
                /** @var array<string, int> $input15 */
                $input15 = null;
                /** @psalm-trace $result15 */
                $result15 = Fp\Functional\Either\Either::right($input15)->getOrElse([true]);
                
                /** @var non-empty-array<string, int> $input16 */
                $input16 = null;
                /** @psalm-trace $result16 */
                $result16 = Fp\Functional\Either\Either::right($input16)->getOrElse([]);
                
                /** @var non-empty-array<string, int> $input17 */
                $input17 = null;
                /** @psalm-trace $result17 */
                $result17 = Fp\Functional\Either\Either::right($input17)->getOrElse([1]);
                
                /** @var list<bool>|array<string, int> $input18 */
                $input18 = null;
                /** @psalm-trace $result18 */
                $result18 = Fp\Functional\Either\Either::right($input18)->getOrElse([]);
                
                /** @var non-empty-list<bool>|array<string, int> $input19 */
                $input19 = null;
                /** @psalm-trace $result19 */
                $result19 = Fp\Functional\Either\Either::right($input19)->getOrElse([]);
                
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input20 */
                $input20 = null;
                /** @psalm-trace $result20 */
                $result20 = Fp\Functional\Either\Either::right($input20)->getOrElse([]);
                
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input21 */
                $input21 = null;
                /** @psalm-trace $result21 */
                $result21 = Fp\Functional\Either\Either::right($input21)->getOrElse(["x"]);
                
                /** @var non-empty-list<bool>|non-empty-array<string, int> $input22 */
                $input22 = null;
                /** @psalm-trace $result22 */
                $result22 = Fp\Functional\Either\Either::right($input22)->getOrCall(fn() => ["x"]);
            ',
            'int',
            '1|2',
            'bool',
            'bool',
            'float',
            'float(1.1)|float(2.2)',
            'string',
            'string',
            '"1.1"|class-string<Foo>',
            'list<int>',
            'list<int>',
            'list<int>',
            'non-empty-list<int>',
            'array<string, int>',
            'array<0|string, int|true>',
            'array<string, int>',
            'non-empty-array<0|string, int>',
            'array<int|string, bool|int>',
            'array<int|string, bool|int>',
            'array<int|string, bool|int>',
            'non-empty-array<int|string, "x"|bool|int>',
            'non-empty-array<int|string, "x"|bool|int>',
        );
    }
}
