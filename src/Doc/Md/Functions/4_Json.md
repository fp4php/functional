# Json
- #### jsonDecode
  Decode json string into associative array. Returns Left on error

  ```php
  jsonDecode('{"a": [{"b": true}]}')->get(); // ['a' => [['b' => true]]] 
  ```

