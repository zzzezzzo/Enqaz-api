<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
Broadcast::channel('provider.{providerId}', function($user, $providerId){
    return (int) $user->id === (int) $providerId;
});