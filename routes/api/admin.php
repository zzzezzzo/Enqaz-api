<?php

use App\Http\Controllers\api\admin\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','role:admin'])->group(function(){
    // Route to add the service to the system 
    Route::post('/services',[ServiceController::class, 'store'])->name('admin.services.store');
});