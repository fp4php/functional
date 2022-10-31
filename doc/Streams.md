# Streams
**Contents**
- [Overview](#Overview)
- [Bulk insert into multiple tables](#Bulk-insert-into-multiple-tables)
- [JSON Lines example](#JSON-Lines-example)

# Overview

Streams are based on generators. They are immutable generator object
wrappers.

Their operations are lazy and will be applied only once when stream
terminal operation like `toArray` will be called.

Every non-terminal stream operation will produce new stream fork. No
more than one fork can be made from stream object.

Stream can be created from any iterable. Additionally, there are fabric
static methods.

``` php
Stream::emit(1)
    ->repeat() // [1, 1, ...] infinite stream
    ->map(fn(int $i) => $i + 1) // [2, 2, ...] infinite stream
    ->take(5) // [2, 2, 2, 2, 2]
    ->toList(); // [2, 2, 2, 2, 2]
```

``` php
Stream::infinite() 
    ->map(fn() => rand(0, 9)) // infinite stream of random digits
    ->intersperse(',') // [x1, ',', x2, ',', ...]
    ->tap(function () {
        // constant memory usage
        echo memory_get_usage(true) . PHP_EOL; 
    })
    ->take(50000) // make infinite stream finite
    ->fold('', fn(string $acc, $elem) => $acc . $elem); // call terminal operation to run stream
```

``` php
/**
 * @return Option<float>
 */
function safeDiv(int $dividend, int $divisor): Option {
    return Option::condLazy(0 !== $divisor, fn() => $dividend / $divisor);
}

Stream::emits([0, 2])
    ->repeatN(3) // [0, 2, 0, 2, 0, 2]
    ->filterMap(fn(int $i) => safeDiv($i, $i))  // [1, 1, 1]
    ->take(9999) // [1, 1, 1]
    ->toFile('/dev/null');
```

``` php
/**
 * Several streams may be interleaved together
 * It's zip + flatMap combination 
 */

Stream::emits([1, 2, 3])
    ->interleave(Stream::emits([4, 5, 6, 7])) // [1, 4, 2, 5, 3, 6]
    ->intersperse('+') // [1, '+', 4, '+', 2, '+', 5, '+', 3, '+', 6]
    ->fold('', fn(string $acc, $cur) => $acc . $cur) // '1+4+2+5+3+6'
```

``` php
Stream::awakeEvery(5) // emit elapsed time every 5 seconds
    ->map(fn(int $elapsed) => "$elapsed seconds elapsed from stream start")
    ->lines() // print element every 5 seconds to stdout
```

# Bulk insert into multiple tables

``` php
Stream::emits($iterableDatabaseCursor)
    ->chunks(5000)
    // Insert chunks of 5000 rows to 'events' table
    ->tap(fn(Seq $chunk) => $client->insert('events', $chunk))
    ->flatMap(function(Seq $chunk) {
        return $chunk->filter(fn(Event $event) => $event->type === 'SOME_TYPE')
    })
    ->chunks(5000)
    // Insert chunks of 5000 rows to 'events_of_some_type' table
    ->tap(fn(Seq $chunk) => $client->insert('events_of_some_type', $chunk))
    ->drain();
```

# JSON Lines example

``` php
class Foo
{
    public function __construct(public int $a, public bool $b = true, public bool $c = true) { }
}

function generateJsonLinesFile(string $path): void
{
    Stream::infinite()
        ->map(fn() => new Foo(rand(), 1 === rand(0, 1), 1 === rand(0, 1)))
        ->map(fn(Foo $foo) => json_encode([$foo->a, $foo->b, $foo->c]))
        ->prepended(json_encode(["a", "b", "c"]))
        ->take(10000)
        ->intersperse(PHP_EOL)
        ->toFile($path);
}

/**
 * @return list<Foo>
 */
function parseJsonLinesFile(string $path): array
{
    $chars = function () use ($path): Generator {
        $file = new SplFileObject($path);
        
        while(false !== ($char = $file->fgetc())) {
            yield $char;
        }
        
        $file = null;
    };

    return Stream::emits($chars())
        ->groupAdjacentBy(fn($char) => PHP_EOL === $char)
        ->map(fn(array $pair) => $pair[1])
        ->map(fn(Seq $line) => $line->mkString(sep: ''))
        ->filterMap(parseFoo(...))
        ->toList();
}

/**
 * @return Option<Foo>
 */
function parseFoo(string $json): Option
{
    return Option::try(fn() => json_decode($json, associative: true, flags: JSON_THROW_ON_ERROR))
        ->filter(fn($candidate) => is_array($candidate))
        ->filter(fn($candidate) => array_key_exists(0, $candidate) && is_int($candidate[0]))
        ->filter(fn($candidate) => array_key_exists(1, $candidate) && is_bool($candidate[1]))
        ->filter(fn($candidate) => array_key_exists(2, $candidate) && is_bool($candidate[2]))
        ->map(fn($tuple) => new Foo($tuple[0], $tuple[1], $tuple[2]));
}

generateJsonLinesFile('out.jsonl');
parseJsonLinesFile('out.jsonl');
```
