# Hierarchy

- #### empty collections
    ```
    Collection<TV> -> Seq<TV> -> LinearSeq<TV> -> LinkedList<TV>
    
    Collection<TV> -> Seq<TV> -> IndexedSeq<TV> -> TODO
    
    Collection<TV> -> Set<TV> -> HashSet<TV>
    
    Collection<array{TK, TV}> -> Map<TK, TV> -> HashMap<TK, TV>
    ```

- #### non-empty collections
    ```
    NonEmptyCollection<TV> -> NonEmptySeq<TV> -> NonEmptyLinearSeq<TV> -> NonEmptyLinkedList<TV>
    
    NonEmptyCollection<TV> -> NonEmptySeq<TV> -> NonEmptyIndexedSeq<TV> -> TODO
    
    NonEmptyCollection<TV> -> NonEmptySet<TV> -> NonEmptyHashSet<TV>
    ```
