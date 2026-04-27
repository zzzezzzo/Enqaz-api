<?php

use App\Http\Controllers\api\admin\ProfileController;
use App\Http\Controllers\api\admin\ServiceController;
use App\Http\Controllers\api\admin\WorkShopController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:api','role:admin'])->group(function(){
    // route to get all the workShops is not activated yet
    Route::get('/workshops/pending',[WorkShopController::class, 'pendingWorkshops'])->name('admin.workshops.pending');
    Route::put('/workshops/{id}/availability',[WorkShopController::class, 'activateWorkShop'])->name('admin.workshops.activate');
    // Route to add the service to the system 
    Route::get('/services',[ServiceController::class, 'index'])->name('admin.services.index');
    Route::post('/services',[ServiceController::class, 'store'])->name('admin.services.store');
    Route::get('/services/{id}/edit',[ServiceController::class, 'edit'])->name('admin.services.edit');
    Route::put('/services/{id}',[ServiceController::class, 'update'])->name('admin.services.update');
    Route::put('/services/{id}/active',[ServiceController::class, 'updateServiceActive'])->name('admin.services.update.active');
    Route::delete('/services/{id}',[ServiceController::class, 'destroy'])->name('admin.services.destroy');
    // Route to the admin profile 
    Route::get('/profile', [ProfileController::class, 'index'])->name('admin.profile.index');
    Route::post('/profile', [ProfileController::class, 'store'])->name('admin.profile.store');
    Route::get('/admins', [ProfileController::class, 'indexAdmins'])->name('admin.admins.index');
});