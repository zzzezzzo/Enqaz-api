<?php

namespace App\Http\Controllers\api\customer;

use App\Events\CustomerCreateRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Models\ServiceRequest;
use App\Models\RequestStatus;
use App\Models\User;
use App\Notifications\CreateRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class ServiceRequestController extends Controller
{
    public function store(StoreServiceRequest $request){
        $validate = $request->validated();
        
        // Get pending status ID
        $pendingStatus = RequestStatus::where('name', 'pending')->first();
        $serviceRequest = ServiceRequest::create([
            'customer_id' => Auth::user()->id,
            'provider_id' => $validate['provider_id'],
            'vehicle_id' => $validate['vehicle_id'],
            'service_id' => $validate['service_id'],
            'latitude' => $validate['latitude'],
            'longitude' => $validate['longitude'],
            'description' => $validate['description'],
            'status_id' => $pendingStatus->id
        ]);


        // Load relationships for response
        $serviceRequest->load([
            'customer:id,name,email,phone',
            'provider:id,name,email,phone',
            'vehicle:id,plate_number,model,brand',
            'service:id,name,description',
            'status:id,name'
        ]);
        event(new CustomerCreateRequest($serviceRequest, Auth::user()->id));

        return response()->json([
            'success' => true,
            'message' => 'Service request created successfully',
            'data' => $serviceRequest
        ], 201);
    }
}
