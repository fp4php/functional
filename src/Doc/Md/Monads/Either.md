# Either monad

Represents computation with possible errors.

Consists of Left and Right subclasses. Left represents error outcome and Right represents successful outcome.

Allow short-circuiting the computation if there was step which returned Left (error outcome).

```php

/** 
 * @return Either<string, User> 
 */
function getUserById(int $id): Either {
  /** 
   * @var User|null $user 
   */
  $user = $db->getUser($id);
  
  return isset($user)
    ? Right::of($user)
    : Left::of('User not found!');
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
    ? Right::of($order)
    : Left::of('Order not found!');
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
    ? Right::of($trackNumber)
    : Left::of('No track number yet. But will be after 30 seconds');
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
    ? Right::of($status)
    : Left::of('Unable to parse track current status');
}

/** @var string $statusOrErrorMessage */
$statusOrErrorMessage = getUserById(654)
    ->flatMap(fn(User $user) => getUserFirstOrder($user))
    ->flatMap(fn(Order $order) => getOrderTrackNumber($order))
    ->flatMap(fn(TrackingNumber $number) => getTrackingStatus($number))
    ->get();
```

