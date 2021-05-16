# Json
- #### jsonDecode
  Decode json string into associative array. Returns Left on error

  ```php
  jsonDecode('{"a": [{"b": true}]}')->get(); // ['a' => [['b' => true]]] 
  ```


- #### jsonSearch 
  Search by JsonPath expression. Returns None if there is no data by given expression. @see jmespath

  ```php
  jsonSearch('a[0].b', ['a' => [['b' => true]]]); // true
  jsonSearch('a[0].b', '{"a": [{"b": true}]}'); // true
  ```
