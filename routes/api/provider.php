<?php

use App\Http\Controllers\api\provider\DashboardController;
use App\Http\Controllers\api\provider\profileController;
use App\Http\Controllers\api\provider\ProviderActiveRequestController;
use App\Http\Controllers\api\provider\ProviderServiceRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','role:provider'])->group(function(){
    // Route for provider profile management
    Route::get('/profile', [profileController::class, 'index'])->name('provider.profile.index');
    Route::post('/profile', [profileController::class, 'createProfile'])->name('provider.profile.create');
    Route::get('/profile/edit', [profileController::class, 'editProfile'])->name('provider.profile.edit');
    Route::put('/profile', [profileController::class, 'updateProfile'])->name('provider.profile.update');
    // route to get the service to can the porvider serve it 
    Route::get('/services', [profileController::class, 'getAllServices'])->name('provider.services.index');
    // get the dashboard of the provider 
    
    Route::get('/dashboard',[DashboardController::class, 'index'])->name('provider.dashboard.index');
    // get all request to serve
    Route::get('/service-requests', [ProviderServiceRequest::class,'index'])->name('provider.serviceRequest.index');
    Route::post('/service-requests/{id}', [ProviderServiceRequest::class,'updateStatus'])->name('provider.serviceRequest.update');
    // get the active request of the provider
    Route::get('/active-requests', [ProviderActiveRequestController::class,'index'])->name('provider.activeRequest.index');
    Route::post('/active-requests/{id}', [ProviderActiveRequestController::class,'updateStatus'])->name('provider.activeRequest.update');
    // get the completed request of the provider 
    Route::get('/completed-requests', [ProviderActiveRequestController::class,'completedRequests'])->name('provider.completedRequest.index');

    
});