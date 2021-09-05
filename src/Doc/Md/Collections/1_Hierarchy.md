# Hierarchy

- #### empty collections
    ```
    Collection<TV> -> Seq<TV> -> LinearSeq<TV> -> LinkedList<TV>
    
    Collection<TV> -> Seq<TV> -> IndexedSeq<TV> -> ArrayList<TV>
    
    Collection<TV> -> Set<TV> -> HashSet<TV>
    
    Collection<TV> -> Map<TK, TV> -> HashMap<TK, TV>
    ```

- #### non-empty collections
    ```
    NonEmptyCollection<TV> -> NonEmptySeq<TV> -> NonEmptyLinearSeq<TV> -> NonEmptyLinkedList<TV>
    
    NonEmptyCollection<TV> -> NonEmptySeq<TV> -> NonEmptyIndexedSeq<TV> -> NonEmptyArrayList<TV>
    
    NonEmptyCollection<TV> -> NonEmptySet<TV> -> NonEmptyHashSet<TV>
    
    NonEmptyCollection<TV> -> NonEmptyMap<TK, TV> -> NonEmptyHashMap<TK, TV>
    ```
