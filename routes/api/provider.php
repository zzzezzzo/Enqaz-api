<?php

use App\Http\Controllers\api\provider\profileController;
use App\Http\Controllers\api\provider\ProviderServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','role:provider'])->group(function(){
    // Route for provider profile management
    Route::get('/profile', [profileController::class, 'index'])->name('provider.profile.index');
    Route::post('/profile', [profileController::class, 'createProfile'])->name('provider.profile.create');
    Route::get('/profile/edit', [profileController::class, 'editProfile'])->name('provider.profile.edit');
    Route::put('/profile', [profileController::class, 'updateProfile'])->name('provider.profile.update');
    // management of the request sent to the provider
    // get all request to serve
    Route::get('/service-requests', [ProviderServiceRequest::class,'index'])->name('provider.serviceRequest.index');
});