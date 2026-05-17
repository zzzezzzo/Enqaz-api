<?php

namespace App\Http\Controllers\api\provider\mechanic;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\WorkshopMechanic;
use Illuminate\Http\Request;

class MechanicJobController extends Controller
{
    //  GET | `/mechanic/jobs` | Active jobs for this mechanic 
    public function index($workshop_id){
        // get the service requests assigned to the authenticated mechanic
        // $provider = auth()->user();
        // $workshop  = $provider->providerProfile;
        $mechanicIds = WorkshopMechanic::where('workshop_id', $workshop_id)
        ->pluck('id');

    $requests = ServiceRequest::whereIn('assigned_mechanic_id', $mechanicIds)
        ->with([
            'customer',
            'vehicle',
            'service',
            'status',
            'assignedMechanic'
        ])
        ->get();

        return response()->json(
        $requests->map(function ($request) {
            return [
                'request_id' => $request->id,
                'status' => $request->status?->name,
                'customer' => [
                    'name' => $request->customer?->name,
                    'phone' => $request->customer?->phone,
                ],
                'location' => [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                ],
                'service' => [
                    'name' => $request->service?->name,
                    'description' => $request->service?->description,
                ],
                'problem_description' => $request->description,
                'vehicle' => [
                    'brand' => $request->vehicle?->brand,
                    'model' => $request->vehicle?->model,
                    'plate_number' => $request->vehicle?->plate_number,
                ],

                'created_at' => $request->created_at,
            ];
        })
    );

    }
    public function show($id){
        $provider = auth()->user();
        $workshop  = $provider->providerProfile;
        $mechanicIds = WorkshopMechanic::where('workshop_id', $workshop->id)
        ->pluck('id');
        $request = ServiceRequest::whereIn('assigned_mechanic_id', $mechanicIds)
        ->where('id', $id)
        ->with([
            'customer',
            'vehicle',
            'service',
            'status',
            'assignedMechanic'
        ])
        ->first();
        if (!$request) {
            return response()->json(['message' => 'Request not found'], 404);
        }
        return response()->json($request);
    }
}
