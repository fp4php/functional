# Monads
**Contents**
- [Option monad](#Option-monad)
- [Either monad](#Either-monad)
- [Do-notation](#Do-notation)
- [Examples](#Examples)
  - [Type assertions with Option](#Type-assertions-with-Option)
  - [Filter chaining](#Filter-chaining)

# Option monad

Represents optional computation.

Consists of Some and None subclasses.

Prevents null pointer exceptions and allow short-circuiting the
computation if there was step which returned None.

``` php
/** 
 * @return Option<User> 
 */
function getUserById(int $id): Option {
  /** 
   * @var User|null $user 
   */
  $user = $db->getUser($id);
  
  return Option::fromNullable($user);
}

/** 
 * @return Option<Order> 
 */
function getUserFirstOrder(User $user): Option {
  /** 
   * @var Order|null $order 
   */
  $order = $user->getOrders()[0] ?? null;
  
  return Option::fromNullable($order);
}


/** 
 * @return Option<TrackNumber> 
 */
function getOrderTrackNumber(Order $order): Option {
  /** 
   * @var TrackNumber|null $order 
   */
  $trackNumber = $order->getTracknumber();
  
  return Option::fromNullable($trackNumber);
}

/** 
 * @return Option<string> 
 */
function getTrackingStatus(TrackingNumber $trackingNumber): Option {
  /** 
   * @var string|null $order 
   */
  $status = $trackingNumber->getLastTrackingStatus();
  
  return Option::fromNullable($status);
}

/** @var string $status */
$status = getUserById(654)
    ->flatMap(fn(User $user) => getUserFirstOrder($user))
    ->flatMap(fn(Order $order) => getOrderTrackNumber($order))
    ->flatMap(fn(TrackingNumber $number) => getTrackingStatus($number))
    ->getOrElse('no status info');
```

# Either monad

Represents computation with possible errors.

Consists of Left and Right subclasses. Left represents error outcome and
Right represents successful outcome.

Allow short-circuiting the computation if there was step which returned
Left (error outcome).

``` php
/** 
 * @return Either<string, User> 
 */
function getUserById(int $id): Either {
  /** 
   * @var User|null $user 
   */
  $user = $db->getUser($id);
  
  return isset($user)
    ? Either::right($user)
    : Either::left('User not found!');
}

/** 
 * @return Either<string, Order> 
 */
function getUserFirstOrder(User $user): Either {
  /** 
   * @var Order|null $order 
   */
  $order = $user->getOrders()[0] ?? null;
  
  return isset($order)
    ? Either::right($order)
    : Either::left('Order not found!');
}


/** 
 * @return Either<string, TrackNumber> 
 */
function getOrderTrackNumber(Order $order): Either {
  /** 
   * @var TrackNumber|null $order 
   */
  $trackNumber = $order->getTracknumber();
  
  return isset($trackNumber)
    ? Either::right($trackNumber)
    : Either::left('No track number yet. But will be after 30 seconds');
}

/** 
 * @return Either<string, string> 
 */
function getTrackingStatus(TrackingNumber $trackingNumber): Either {
  /** 
   * @var string|null $order 
   */
  $status = $trackingNumber->getLastTrackingStatus();
  
  return isset($status)
    ? Either::right($status)
    : Either::left('Unable to parse track current status');
}

/** @var string $statusOrErrorMessage */
$statusOrErrorMessage = getUserById(654)
    ->flatMap(fn(User $user) => getUserFirstOrder($user))
    ->flatMap(fn(Order $order) => getOrderTrackNumber($order))
    ->flatMap(fn(TrackingNumber $number) => getTrackingStatus($number))
    ->get();
```

# Do-notation

In case of long computation chain you can use do notation to shorten
amount of code. Do-notation is just syntax-sugar.

``` php
/** 
 * @return Option<User> 
 */
function getUserById(int $id): Option {}

/** 
 * @return Option<Order> 
 */
function getUserFirstOrder(User $user): Option {}


/** 
 * @return Option<TrackNumber> 
 */
function getOrderTrackNumber(Order $order): Option {}

/** 
 * @return Option<string> 
 */
function getTrackingStatus(TrackingNumber $trackingNumber): Option {}

/** 
 * @var string $status 
 */
$status = Option::do(function () {
    $user = yield getUserById(654);
    $order = yield getUserFirstOrder($user);
    $trackNumber = yield getOrderTrackNumber($order);
    return yield getTrackingStatus($trackNumber);
})->getOrElse('no status info');
```

# Examples

-   #### Type assertions with Option

    ``` php
    /**
     * Inferred type is Option<Foo> 
     */ 
    $maybeFooMaybeNot = Option::do(function() use ($untrusted) {
        $notNull = yield Option::fromNullable($untrusted);
        yield proveTrue(is_array($notNull)); // Inferred type is array<array-key, mixed> 
        $list = yield proveList($notNull); // Inferred type is list<mixed>
        $nonEmptyList = yield proveNonEmptyList($list); // Inferred type is non-empty-list<mixed>
        $nonEmptyListOfFoo = yield proveNonEmptyListOf($nonEmptyList, Foo::class); // Inferred type is non-empty-list<Foo>
        $firstFoo = $nonEmptyListOfFoo[0]; // Inferred type is Foo

        return $firstFoo; // I'm sure it's Foo object
    });

    /**
     * Inferred type is Foo
     */
    $foo = $maybeFooMaybeNot->getOrCall(fn() => new Foo(0))
    ```

-   #### Filter chaining

    ``` php
    /**
     * @return Option<Union>
     */
    function getUnionTypeParam(Union $union): Option
    {
        return Option::do(function () use ($union) {
            $atomics = $union->getAtomicTypes();
            yield proveTrue(1 === count($atomics));
            $atomic = yield head($atomics);

            return yield self::filterTIterableTypeParam($atomic)
                ->orElse(fn() => self::filterTArrayTypeParam($atomic))
                ->orElse(fn() => self::filterTListTypeParam($atomic))
                ->orElse(fn() => self::filterTGenericObjectTypeParam($atomic))
                ->orElse(fn() => self::filterTKeyedArrayTypeParam($atomic));
        });
    }

    /**
     * @return Option<Union>
     */
    function filterTIterableTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TIterable)
            ->map(fn(TIterable $a) => $a->type_params[1]);
    }

    /**
     * @return Option<Union>
     */
    function filterTArrayTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TArray)
            ->map(fn(TArray $a) => $a->type_params[1]);
    }

    /**
     * @return Option<Union>
     */
    function filterTListTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TList)
            ->map(fn(TList $a) => $a->type_param);
    }

    /**
     * @return Option<Union>
     */
    function filterTKeyedArrayTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TKeyedArray)
            ->map(fn(TKeyedArray $a) => $a->getGenericValueType());
    }

    /**
     * @return Option<Union>
     */
    function filterTGenericObjectTypeParam(Atomic $atomic): Option
    {
        return Option::some($atomic)
            ->filter(fn(Atomic $a) => $a instanceof TGenericObject)
            ->flatMap(fn(TGenericObject $a) => Option::fromNullable(match (true) {
                is_a($a->value, Seq::class, true) => $a->type_params[0],
                is_a($a->value, Set::class, true) => $a->type_params[0],
                is_a($a->value, Map::class, true) => $a->type_params[1],
                is_a($a->value, NonEmptySeq::class, true) => $a->type_params[0],
                is_a($a->value, NonEmptySet::class, true) => $a->type_params[0],
                default => null
            }));
    }
    ```
