# Do-notation

In case of long computation chain you can use do notation to shorten amount of code.
Do-notation is just syntax-sugar.

```php

/** 
 * @return Option<User> 
 */
function getUserById(int $id): Option {
  /** 
   * @var User|null $user 
   */
  $user = $db->getUser($id);
  
  return Option::of($user);
}

/** 
 * @return Option<Order> 
 */
function getUserFirstOrder(User $user): Option {
  /** 
   * @var Order|null $order 
   */
  $order = $user->getOrders()[0] ?? null;
  
  return Option::of($order);
}


/** 
 * @return Option<TrackNumber> 
 */
function getOrderTrackNumber(Order $order): Option {
  /** 
   * @var TrackNumber|null $order 
   */
  $trackNumber = $order->getTracknumber();
  
  return Option::of($trackNumber);
}

/** 
 * @return Option<string> 
 */
function getTrackingStatus(TrackingNumber $trackingNumber): Option {
  /** 
   * @var string|null $order 
   */
  $status = $trackingNumber->getLastTrackingStatus();
  
  return Option::of($status);
}

/** @var string $status */
$status = Option::do(function () {
    $user = yield getUserById(654);
    $order = yield getUserFirstOrder($user);
    $trackNumber = yield getOrderTrackNumber($order);
    return yield getTrackingStatus($trackNumber);
})->getOrElse('no status info');
```

