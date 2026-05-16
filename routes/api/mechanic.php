<?php
use App\Http\Controllers\api\provider\mechanic\MechanicJobController;
use Illuminate\Http\Request;


Route::prefix('mechanic')->middleware('auth:api')->group(function () {
    // add the route for creating a vhicle data entry
    Route::get('/jobs', [MechanicJobController::class, 'index'])->name('mechanic.jobs.index');
    Route::get('/jobs/{id}', [MechanicJobController::class, 'show'])->name('mechanic.jobs.show');

});