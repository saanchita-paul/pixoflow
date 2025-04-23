<?php

use Illuminate\Support\Facades\Broadcast;

//Broadcast::channel('notification.{id}', function ($id) {
//    return true;
//});


//Broadcast::channel('order.{orderId}', function ($user, $orderId) {
//    return true;
//});
//Broadcast::channel('notification.{fileId}', function ($user, $fileId) {
//    return $user->role === 'admin';
//});
Broadcast::channel('notification.admin', function ($user) {
    \Illuminate\Support\Facades\Log::info('logogogo');
    return $user->role === 'admin';
});
