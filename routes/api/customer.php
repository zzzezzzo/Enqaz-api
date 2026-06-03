<?php

use App\Http\Controllers\api\customer\nearestProviderController;
use App\Http\Controllers\api\customer\ServiceRequestController;
use App\Http\Controllers\api\customer\vehicleController;
use App\Http\Controllers\api\customer\workshopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware('auth:api')->group(function () {
    // add the route for creating a vhicle data entry
    Route::post('/add-vehicle', [vehicleController::class, 'store'])->name('customer.addVehicle');
    Route::get('/vehicles', [vehicleController::class, 'index'])->name('customer.vehicles.index');
    Route::get('/vehicles/{id}/edit', [vehicleController::class, 'edit'])->name('customer.vehicles.edit');
    Route::put('/vehicles/{id}', [vehicleController::class, 'update'])->name('customer.vehicles.update');
    Route::delete('/vehicles/{id}', [vehicleController::class, 'destroy'])->name('customer.vehicles.destroy');
    // add the route for nearest provider
    Route::post('/nearest-providers', [nearestProviderController::class, 'index'])->name('customer.nearestProviders.index');
    // get the service to the workshop 
    Route::get('/services-provider/{id}', [nearestProviderController::class, 'services'])->name('customer.services');
    // select the needed service 
    Route::get('/service-requests',[ServiceRequestController::class,'index'])->name('customer.serviceRequest.index');
    Route::post('/service-requests', [ServiceRequestController::class, 'store'])->name('customer.serviceRequest.store');
    // route to make customer give the service requests a rating 
    Route::post('/service-requests/{id}/rating', [ServiceRequestController::class, 'rate'])->name('customer.serviceRequest.rate');
    // route to get the opening houres of the provider
    Route::get('workshop/{providerId}', [WorkshopController::class, 'show'])->name('customer.workshop.show');
});