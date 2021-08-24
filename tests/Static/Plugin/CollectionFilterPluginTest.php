<?php

declare(strict_types=1);

namespace Tests\Static\Plugin;

use Tests\PhpBlockTestCase;

final class CollectionFilterPluginTest extends PhpBlockTestCase
{
    public function testFilter(): void
    {
        $this->assertBlockTypes(
            /** @lang InjectablePHP */ '
                
                /** @psalm-trace $r1 */
                $r1 = \Fp\Collections\ArrayList::collect([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r2 */
                $r2 = \Fp\Collections\LinkedList::collect([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r3 */
                $r3 = \Fp\Collections\HashSet::collect([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r4 */
                $r4 = \Fp\Collections\HashMap::collect([["a", 1], ["b", null], ["c", 2]])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r5 */
                $r5 = \Fp\Collections\NonEmptyArrayList::collectNonEmpty([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r6 */
                $r6 = \Fp\Collections\NonEmptyLinkedList::collectNonEmpty([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                    
                /** @psalm-trace $r7 */
                $r7 = \Fp\Collections\NonEmptyHashSet::collectNonEmpty([1, null, 2])
                    ->filter(fn($e) => null !== $e);
                   
                /** @var \Fp\Collections\Seq<1|null|2> $seq */ 
                $seq = \Fp\Collections\LinkedList::collect([1, null, 2]);
                
                /** @var \Fp\Collections\Set<1|null|2> $set */ 
                $set = \Fp\Collections\LinkedList::collect([1, null, 2]);
                
                /** @var \Fp\Collections\Map<"a"|"b"|"c", 1|null|2> $map */ 
                $map = \Fp\Collections\LinkedList::collect([["a", 1], ["b", null], ["c", 2]]);
                
                /** @var \Fp\Collections\NonEmptySeq<1|null|2> $nonEmptySeq */ 
                $nonEmptySeq = \Fp\Collections\NonEmptyLinkedList::collectNonEmpty([1, null, 2]);
                
                /** @var \Fp\Collections\NonEmptySet<1|null|2> $nonEmptySet */ 
                $nonEmptySet = \Fp\Collections\NonEmptyLinkedList::collectNonEmpty([1, null, 2]);
                
                /** @psalm-trace $r8 */
                $r8 = $seq->filter(fn($e) => null !== $e);
                
                /** @psalm-trace $r9 */
                $r9 = $set->filter(fn($e) => null !== $e);
                
                /** @psalm-trace $r10 */
                $r10 = $map->filter(fn($e) => null !== $e);
                
                /** @psalm-trace $r11 */
                $r11 = $nonEmptySeq->filter(fn($e) => null !== $e);
                
                /** @psalm-trace $r12 */
                $r12 = $nonEmptySet->filter(fn($e) => null !== $e);
            ',
            'ArrayList<1|2>',
            'LinkedList<1|2>',
            'HashSet<1|2>',
            'HashMap<"a"|"b"|"c", 1|2>',
            'ArrayList<1|2>',
            'LinkedList<1|2>',
            'HashSet<1|2>',
            'Fp\Collections\Seq<1|2>',
            'Fp\Collections\Set<1|2>',
            'Fp\Collections\Map<"a"|"b"|"c", 1|2>',
            'Fp\Collections\Seq<1|2>',
            'Fp\Collections\Set<1|2>',
        );
    }
}
