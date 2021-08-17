# Monads
**Contents**
- [Do-notation](#Do-notation)
- [Either monad](#Either-monad)
- [Option monad](#Option-monad)

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
