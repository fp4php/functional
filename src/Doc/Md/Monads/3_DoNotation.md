# Do-notation

In case of long computation chain you can use do notation to shorten amount of code.
Do-notation is just syntax-sugar.

```php
<?php

declare(strict_types=1);

use Fp\Functional\Option\Option;

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

