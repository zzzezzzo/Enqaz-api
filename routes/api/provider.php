<?php

use App\Http\Controllers\api\provider\DashboardController;
use App\Http\Controllers\api\provider\mechanic\MechanicAuthController;
use App\Http\Controllers\api\provider\profileController;
use App\Http\Controllers\api\provider\ProviderActiveRequestController;
use App\Http\Controllers\api\provider\ProviderServiceRequest;
use App\Http\Controllers\api\provider\MechanicMangementController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/services', [profileController::class, 'getAllServices'])->name('provider.services.index');
Route::post('/mechanic/login', [MechanicAuthController::class, 'login'])->name('provider.mechanic.login');

Route::middleware(['auth:api','role:provider'])->group(function(){
    // Route for provider profile management
    Route::get('/profile', [profileController::class, 'index'])->name('provider.profile.index');
    Route::post('/profile', [profileController::class, 'createProfile'])->name('provider.profile.create');
    Route::get('/profile/edit', [profileController::class, 'editProfile'])->name('provider.profile.edit');
    Route::put('/profile', [profileController::class, 'updateProfile'])->name('provider.profile.update');
    // route to get the service to can the porvider serve it 
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
    // add the mechanic to the system 
    Route::post('/add-mechanic', [MechanicMangementController::class, 'addMechanic'])->name('provider.addMechanic');
    Route::get('/mechanic/{id}', [MechanicMangementController::class, 'update'])->name('provider.mechanic.update');
    Route::put('/edit-mechanic/{id}', [MechanicMangementController::class, 'editMechanic'])->name('provider.editMechanic');
    Route::put('/update-mechanic-status/{id}', [MechanicMangementController::class, 'updateMechanicStatus'])->name('provider.updateMechanicStatus');
    Route::delete('/delete-mechanic/{id}', [MechanicMangementController::class, 'deleteMechanic'])->name('provider.deleteMechanic');
    // get all mechanics for a specific workshop
    Route::get('/workshop/{workshop_id}/mechanics', [MechanicMangementController::class, 'index'])->name('provider.mechanics.index');
    // route to assign the mechanic to the request 
    Route::post('/assign-mechanic/{request_id}', [ProviderServiceRequest::class,'assignMechanic'])->name('provider.assignMechanic');
    
});