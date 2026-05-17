<?php
use App\Http\Controllers\api\provider\mechanic\MechanicJobController;
use Illuminate\Http\Request;


Route::prefix('mechanic')->group(function () {
    // add the route for creating a vhicle data entry
    Route::get('/jobs/{workshop_id}', [MechanicJobController::class, 'index'])->name('mechanic.jobs.index');
    Route::get('/jobs/{workshop_id}/{id}', [MechanicJobController::class, 'show'])->name('mechanic.jobs.show');

});