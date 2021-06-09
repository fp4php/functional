<?php

declare(strict_types=1);

namespace Tests\Static\Classes\Semigroup;

use Fp\Functional\Semigroup\Semigroup;
use Tests\PhpBlockTestCase;

final class SemigroupTest extends PhpBlockTestCase
{
    public function testLhsAndRhsInstances(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Semigroup\Semigroup;
                use Tests\Mock\Foo;
                
                /** @psalm-trace $i1 */
                $i1 = Semigroup::lhsInstance("float");
                
                /** @psalm-trace $i2 */
                $i2 = Semigroup::lhsInstance("int");
                
                /** @psalm-trace $i3 */
                $i3 = Semigroup::lhsInstance("string");
                
                /** @psalm-trace $i4 */
                $i4 = Semigroup::lhsInstance("bool");
                
                /** @psalm-trace $i5 */
                $i5 = Semigroup::lhsInstance("scalar");
                
                /** @psalm-trace $i6 */
                $i6 = Semigroup::lhsInstance(Foo::class);
            ',
            'Semigroup<float>',
            'Semigroup<int>',
            'Semigroup<string>',
            'Semigroup<bool>',
            'Semigroup<scalar>',
            'Semigroup<Foo>',
        );

        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Semigroup\Semigroup;
                use Tests\Mock\Foo;
                
                /** @psalm-trace $i1 */
                $i1 = Semigroup::rhsInstance("float");
                
                /** @psalm-trace $i2 */
                $i2 = Semigroup::rhsInstance("int");
                
                /** @psalm-trace $i3 */
                $i3 = Semigroup::rhsInstance("string");
                
                /** @psalm-trace $i4 */
                $i4 = Semigroup::rhsInstance("bool");
                
                /** @psalm-trace $i5 */
                $i5 = Semigroup::rhsInstance("scalar");
                
                /** @psalm-trace $i6 */
                $i6 = Semigroup::rhsInstance(Foo::class);
            ',
            'Semigroup<float>',
            'Semigroup<int>',
            'Semigroup<string>',
            'Semigroup<bool>',
            'Semigroup<scalar>',
            'Semigroup<Foo>',
        );
    }

    public function testListInstance(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                use Fp\Functional\Semigroup\Semigroup;
                use Tests\Mock\Foo;
                
                /** @psalm-trace $i1 */
                $i1 = Semigroup::listInstance("float");
                
                /** @psalm-trace $i2 */
                $i2 = Semigroup::listInstance("int");
                
                /** @psalm-trace $i3 */
                $i3 = Semigroup::listInstance("string");
                
                /** @psalm-trace $i4 */
                $i4 = Semigroup::listInstance("bool");
                
                /** @psalm-trace $i5 */
                $i5 = Semigroup::listInstance("scalar");
                
                /** @psalm-trace $i6 */
                $i6 = Semigroup::listInstance(Foo::class);
            ',
            'Semigroup<list<float>>',
            'Semigroup<list<int>>',
            'Semigroup<list<string>>',
            'Semigroup<list<bool>>',
            'Semigroup<list<scalar>>',
            'Semigroup<list<Foo>>',
        );
    }

    public function testNonEmptyListInstance(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Semigroup\Semigroup;
                use Tests\Mock\Foo;
                
                /** @psalm-trace $i1 */
                $i1 = Semigroup::nonEmptyListInstance("float");
                
                /** @psalm-trace $i2 */
                $i2 = Semigroup::nonEmptyListInstance("int");
                
                /** @psalm-trace $i3 */
                $i3 = Semigroup::nonEmptyListInstance("string");
                
                /** @psalm-trace $i4 */
                $i4 = Semigroup::nonEmptyListInstance("bool");
                
                /** @psalm-trace $i5 */
                $i5 = Semigroup::nonEmptyListInstance("scalar");
                
                /** @psalm-trace $i6 */
                $i6 = Semigroup::nonEmptyListInstance(Foo::class);
            ',
            'Semigroup<non-empty-list<float>>',
            'Semigroup<non-empty-list<int>>',
            'Semigroup<non-empty-list<string>>',
            'Semigroup<non-empty-list<bool>>',
            'Semigroup<non-empty-list<scalar>>',
            'Semigroup<non-empty-list<Foo>>',
        );
    }

    public function testValidatedInstance(): void
    {
        $this->assertBlockTypes(
        /** @lang InjectablePHP */ '
                use Fp\Functional\Semigroup\Semigroup;
 
                $validInstance = Semigroup::listInstance("int");
                $invalidInstance = Semigroup::listInstance("string");
                               
                /** @psalm-trace $validatedInstance */
                $validatedInstance = Semigroup::validatedInstance($validInstance, $invalidInstance);
            ',
            'Semigroup<Validated<list<string>, list<int>>>',
        );
    }

}
