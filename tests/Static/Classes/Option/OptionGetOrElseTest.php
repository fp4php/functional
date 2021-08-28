<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Option;

use Tests\PhpBlockTestCase;

final class OptionGetOrElseTest extends PhpBlockTestCase
{
    public function testGetOrElse(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                /** @var 1|null $input1 */
                $input1 = null;
                /** @psalm-trace $result1 */
                $result1 = Fp\Functional\Option\Option::fromNullable($input1)->getOrElse(2);
                
                /** @var bool|null $input2 */
                $input2 = null;
                /** @psalm-trace $result2 */
                $result2 = Fp\Functional\Option\Option::fromNullable($input2)->getOrElse(true);
                
                /** @var int|null $input3 */
                $input3 = null;
                /** @psalm-trace $result3 */
                $result3 = Fp\Functional\Option\Option::fromNullable($input3)->getOrElse(1);
                
                /** @var true|null $input4 */
                $input4 = null;
                /** @psalm-trace $result4 */
                $result4 = Fp\Functional\Option\Option::fromNullable($input4)->getOrElse(false);
                
                /** @var float|null $input5 */
                $input5 = null;
                /** @psalm-trace $result5 */
                $result5 = Fp\Functional\Option\Option::fromNullable($input5)->getOrElse(1.1);
                
                /** @var 1.1|null $input6 */
                $input6 = null;
                /** @psalm-trace $result6 */
                $result6 = Fp\Functional\Option\Option::fromNullable($input6)->getOrElse(2.2);
                
                /** @var string|null $input7 */
                $input7 = null;
                /** @psalm-trace $result7 */
                $result7 = Fp\Functional\Option\Option::fromNullable($input7)->getOrElse("1");
                
                /** @var string|null $input8 */
                $input8 = null;
                /** @psalm-trace $result8 */
                $result8 = Fp\Functional\Option\Option::fromNullable($input8)->getOrElse(\Tests\Mock\Foo::class);
                
                /** @var class-string<\Tests\Mock\Foo>|null $input9 */
                $input9 = null;
                /** @psalm-trace $result9 */
                $result9 = Fp\Functional\Option\Option::fromNullable($input9)->getOrElse("1.1");
                
                /** @var list<int>|null $input10 */
                $input10 = null;
                /** @psalm-trace $result10 */
                $result10 = Fp\Functional\Option\Option::fromNullable($input10)->getOrElse([]);
                
                /** @var list<int>|null $input11 */
                $input11 = null;
                /** @psalm-trace $result11 */
                $result11 = Fp\Functional\Option\Option::fromNullable($input11)->getOrElse([1]);
                
                /** @var non-empty-list<int>|null $input12 */
                $input12 = null;
                /** @psalm-trace $result12 */
                $result12 = Fp\Functional\Option\Option::fromNullable($input12)->getOrElse([]);
                
                /** @var non-empty-list<int>|null $input13 */
                $input13 = null;
                /** @psalm-trace $result13 */
                $result13 = Fp\Functional\Option\Option::fromNullable($input13)->getOrElse([1]);
                
                /** @var array<string, int>|null $input14 */
                $input14 = null;
                /** @psalm-trace $result14 */
                $result14 = Fp\Functional\Option\Option::fromNullable($input14)->getOrElse([]);

                /** @var array<string, int>|null $input15 */
                $input15 = null;
                /** @psalm-trace $result15 */
                $result15 = Fp\Functional\Option\Option::fromNullable($input15)->getOrElse([true]);
                
                /** @var non-empty-array<string, int>|null $input16 */
                $input16 = null;
                /** @psalm-trace $result16 */
                $result16 = Fp\Functional\Option\Option::fromNullable($input16)->getOrElse([]);
                
                /** @var non-empty-array<string, int>|null $input17 */
                $input17 = null;
                /** @psalm-trace $result17 */
                $result17 = Fp\Functional\Option\Option::fromNullable($input17)->getOrElse([1]);
                
                /** @var list<bool>|array<string, int>|null $input18 */
                $input18 = null;
                /** @psalm-trace $result18 */
                $result18 = Fp\Functional\Option\Option::fromNullable($input18)->getOrElse([]);
                
                /** @var non-empty-list<bool>|array<string, int>|null $input19 */
                $input19 = null;
                /** @psalm-trace $result19 */
                $result19 = Fp\Functional\Option\Option::fromNullable($input19)->getOrElse([]);
                
                /** @var non-empty-list<bool>|non-empty-array<string, int>|null $input20 */
                $input20 = null;
                /** @psalm-trace $result20 */
                $result20 = Fp\Functional\Option\Option::fromNullable($input20)->getOrElse(["x"]);
                
                /** @var non-empty-list<bool>|non-empty-array<string, int>|null $input21 */
                $input21 = null;
                /** @psalm-trace $result21 */
                $result21 = Fp\Functional\Option\Option::fromNullable($input21)->getOrCall(fn() => ["x"]);
            ',
            '1|2',
            'bool',
            'int',
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
            'non-empty-array<int|string, "x"|bool|int>',
            'non-empty-array<int|string, "x"|bool|int>'
        );
    }
}
