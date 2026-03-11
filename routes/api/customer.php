<?php

use App\Http\Controllers\api\customer\nearestProviderController;
use App\Http\Controllers\api\customer\ServiceRequestController;
use App\Http\Controllers\api\customer\vehicleController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    // add the route for creating a vhicle data entry
    Route::post('/add-vehicle', [vehicleController::class, 'store'])->name('customer.addVehicle');
    Route::get('/vehicles/{id}/edit', [vehicleController::class, 'edit'])->name('customer.vehicles.edit');
    Route::put('/vehicles/{id}', [vehicleController::class, 'update'])->name('customer.vehicles.update');
    Route::delete('/vehicles/{id}', [vehicleController::class, 'destroy'])->name('customer.vehicles.destroy');
    // add the route for nearest provider
    Route::post('/nearest-providers', [nearestProviderController::class, 'index'])->name('customer.nearestProviders.index');
    // select the needed service 
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('customer.serviceRequest.store');
});