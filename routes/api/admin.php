<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','role:admin'])->group(function(){
    Route::get('/info', function(){
        return response()->json([
            'message' => 'This is admin info',
            'user' => auth()->user()
        ]);
    });
    Route::get('/dashboard', function(){
        return response()->json([
            'message' => 'This is admin dashboard',
            // 'user' => auth()->user()
        ]);
    });
});