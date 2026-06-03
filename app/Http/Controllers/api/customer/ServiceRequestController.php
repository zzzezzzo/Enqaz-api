<?php

namespace App\Http\Controllers\api\customer;

use App\Events\CustomerCreateRequest;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Models\Rating;
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
    public function index(){
        $serviceRequests = ServiceRequest::where('customer_id', Auth::user()->id)
            ->with([
                'provider:id,name,email,phone',
                'vehicle:id,plate_number,model,brand',
                'service:id,name,description',
                'status:id,name'
            ])->whereDoesntHave('ratings')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'Service requests retrieved successfully',
            'data' => $serviceRequests
        ], 200);
    }
    public function store(StoreServiceRequest $request){
        $validate = $request->validated();
        
        // Get pending status ID
        $pendingStatus = RequestStatus::where('name', 'pending')->first();
        if($validate['requestTiming'] == 'scheduled'){
            $serviceRequest = ServiceRequest::create([
                'customer_id' => Auth::user()->id,
                'provider_id' => $validate['provider_id'],
                'vehicle_id' => $validate['vehicle_id'],
                'service_id' => $validate['service_id'],
                'latitude' => $validate['latitude'],
                'longitude' => $validate['longitude'],
                'description' => $validate['description'],
                'request_type' => $validate['request_type'],
                'scheduled_date' => $validate['scheduled_date'],
                'scheduled_starts_at' => $validate['scheduled_starts_at'],
                'scheduled_ends_at' => $validate['scheduled_ends_at'],
                'status_id' => $pendingStatus->id
            ]);
        }else{
            $serviceRequest = ServiceRequest::create([
                'customer_id' => Auth::user()->id,
                'provider_id' => $validate['provider_id'],
                'vehicle_id' => $validate['vehicle_id'],
                'service_id' => $validate['service_id'],
                'latitude' => $validate['latitude'],
                'longitude' => $validate['longitude'],
                'description' => $validate['description'],
                'request_type' => $validate['request_type'],
                'status_id' => $pendingStatus->id
            ]);
        }


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
    public function rate(Request $request, $id){
        $serviceRequest = ServiceRequest::find($id);
        if(!$serviceRequest || $serviceRequest->customer_id != Auth::user()->id){
            return response()->json([
                'success' => false,
                'message' => 'Service request not found'
            ], 404);
        }
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string'
        ]);
        Rating::create([
            'service_request_id' => $serviceRequest->id,
            'provider_id' => $serviceRequest->provider_id,
            'customer_id' => Auth::user()->id,
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Service request rated successfully',
            'data' => $serviceRequest
        ], 200);
    }
}
