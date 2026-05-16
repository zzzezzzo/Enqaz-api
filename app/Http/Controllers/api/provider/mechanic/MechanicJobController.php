<?php

namespace App\Http\Controllers\api\provider\mechanic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MechanicJobController extends Controller
{
    //  GET | `/mechanic/jobs` | Active jobs for this mechanic 
    public function index(){
        // get the authenticated mechanic
        $mechanic = auth()->user();
        $jobs = $mechanic->jobs()->where('status', 'active')->get()
            ->map(function ($job) {
                return [
                    'id' => $job->id,
                    'customer_name' => $job->customer->name,
                    'vehicle' => $job->vehicle->make . ' ' . $job->vehicle->model,
                    'service' => $job->service->name,
                    'status' => $job->status,
                    'created_at' => $job->created_at->toDateTimeString(),
                ];
            });
        return response()->json(['jobs' => $jobs], 200);
    }
}
