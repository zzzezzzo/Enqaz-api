<?php

use App\Http\Controllers\api\auth\registerController;
use App\Http\Controllers\api\auth\loginController;
use App\Http\Controllers\api\auth\logoutController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/****************** AUTH ******************/

Route::middleware('guest:api')->group(function () {
    Route::post('/register', [registerController::class, 'register'])->name('register');
    Route::post('/login', [loginController::class, 'login'])->name('login');
    Route::post('/refresh', [loginController::class, 'refresh'])->name('refresh');
    Route::post('/logout', [logoutController::class, 'logout'])->name('logout');
});
