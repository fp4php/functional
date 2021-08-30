# Option monad

Represents optional computation.

Consists of Some and None subclasses.

Prevents null pointer exceptions and allow short-circuiting the computation if there was step which returned None.

```php

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

