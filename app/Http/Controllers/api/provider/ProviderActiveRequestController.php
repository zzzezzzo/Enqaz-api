<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;
use App\Models\ProviderProfile;
use App\Models\RequestStatus;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestLog;
use Auth;
use DB;
use Illuminate\Http\Request;

class ProviderActiveRequestController extends Controller
{
    public function index(){
        return response()->json([
                'message' => 'Provider profile updated successfully',
                'data' => $this->incomeRequest()
            ], 200);
    }
    private function incomeRequest()
    {
        $provider = ProviderProfile::where('user_id', auth()->id())->firstOrFail();

        $lat = $provider->latitude;
        $lng = $provider->longitude;

        $requests = ServiceRequest::where('provider_id' , $provider->id)
            ->whereHas('status', function ($q) {
                $q->where('name', 'accepted')
                ->orWhere('name', 'in_progress');
            })
            ->with(['customer', 'service', 'vehicle'])
            ->get()
            ->map(function ($req) use ($lat, $lng) {
            return [
                'id' => $req->id,
                'vehicle_details' => $req->vehicle?->model . ' ' . $req->vehicle?->brand . ' (' . $req->vehicle?->plate_number . ')',
                'latitude' => $req->latitude,
                'longitude' => $req->longitude,
                'customer_name' => $req->customer?->name,
                'description' => $req->description,
                'service_name' => $req->service?->name,
                'status' => $req->status?->name,
            ];

        });

        return [
            'workShopLocation' => [
                    'latitude' => $lat,
                    'longitude' => $lng,
                ],
            'active_requests' => $requests,
        ];
    }
    public function updateStatus(Request $request, $id){
        $request->validate([
            'status' => 'required|in:completed,in_progress,cancelled,rejected'
        ]);
        DB::beginTransaction();
        try {
            $serviceRequest = ServiceRequest::findOrFail($id);
            $oldStatusId = $serviceRequest->status_id;
            $newStatusId = $this->getStatusId($request->status);
            $serviceRequest->status_id = $newStatusId;
            $serviceRequest->save();
            
            ServiceRequestLog::create([
                'service_request_id' => $serviceRequest->id,
                'old_status_id' => $oldStatusId,
                'new_status_id' => $newStatusId,
                'changed_by' => Auth::id(),
                'comment' => "Status changed to {$request->status} by provider"
            ]);
            DB::commit();
            return response()->json([
                'message' => 'Service request status updated successfully'
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to update service request status',
                'error' => $e->getMessage()
            ], 500);

    }
    }
    private function getStatusId($statusName)
    {
        $status = RequestStatus::where('name', $statusName)->first();
        return $status ? $status->id : null;
    }
    public function completedRequests(){
        $provider = ProviderProfile::where('user_id', auth()->id())->firstOrFail();

        $lat = $provider->latitude;
        $lng = $provider->longitude;

        $requests = ServiceRequest::where('provider_id' , $provider->id)
            ->whereHas('status', function ($q) {
                $q->where('name', 'completed');
            })
            ->with(['customer', 'service', 'vehicle'])
            
            ->get()
            ->map(function ($req) use ($lat, $lng) {
            return [
                'id' => $req->id,
                'vehicle_details' => $req->vehicle?->model . ' ' . $req->vehicle?->brand . ' (' . $req->vehicle?->plate_number . ')',
                'latitude' => $req->latitude,
                'longitude' => $req->longitude,
                'customer_name' => $req->customer?->name,
                'description' => $req->description,
                'service_name' => $req->service?->name,
                'status' => $req->status?->name,
                'completed_at' => $req->updated_at->format('h:i A'),
                'customer_contact' => $req->customer?->phone,
            ];

        });

        return response()->json([
            'message' => 'Completed requests retrieved successfully',
            'data' => [
                'completed_requests' => $requests,
            ]
        ], 200);
    }
}
