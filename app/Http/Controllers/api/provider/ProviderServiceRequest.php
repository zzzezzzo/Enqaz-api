<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;

use App\Models\ProviderProfile;
use App\Models\RequestStatus;
use App\Models\ServiceRequest;
use App\Models\TrackingSession;
use App\Models\WorkshopMechanic;

use App\Models\ServiceRequestLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProviderServiceRequest extends Controller
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

        $requests = ServiceRequest::selectRaw("
                service_requests.*,
                (6371 * acos(
                    cos(radians(?)) 
                    * cos(radians(service_requests.latitude)) 
                    * cos(radians(service_requests.longitude) - radians(?)) 
                    + sin(radians(?)) 
                    * sin(radians(service_requests.latitude))
                )) AS distance
            ", [$lat, $lng, $lat])
            ->whereHas('status', function ($q) {
                $q->where('name', 'pending');
            })->where('provider_id' , $provider->id)
            ->with(['customer', 'service'])
            ->orderBy('distance')
            ->paginate(10)
            ->map(function ($req) {
            return [
                'id' => $req->id,
                'latitude' => $req->latitude,
                'longitude' => $req->longitude,
                'customer_name' => $req->customer?->name,
                'description' => $req->description,
                'service_name' => $req->service?->name,
                'scheduled_date' => $req->scheduled_date,
                'scheduled_starts_at' => $req->scheduled_starts_at,
                'scheduled_ends_at' => $req->scheduled_ends_at,
                'distance' => round($req->distance, 2) . ' km',
                'minutes_ago' => $req->created_at
                    ? now()->diffInMinutes($req->created_at) . ' min'
                    : null,
            ];
        });
        return [
            'requests' => $requests
        ];
    }
    public function updateStatus(Request $request, $id){
        $request->validate([
            'status' => 'required|in:accepted,rejected,completed'
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
            if($request->status === 'accepted'){
                TrackingSession::create([
                    'service_request_id' => $serviceRequest->id,
                    'provider_id' => Auth::id(),
                    'started_at' => now()
                ]);
            } elseif($request->status === 'completed'){
                $trackingSession = TrackingSession::where('service_request_id', $serviceRequest->id)->first();
                if($trackingSession){
                    $trackingSession->ended_at = now();
                    $trackingSession->save();
                }
            }
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
    private function getStatusId($statusName){
        $status = RequestStatus::where('name', $statusName)->firstOrFail();
        return $status->id;
    }
    public function assignMechanic(Request $request, $id){
        $request->validate([
            'mechanic_id' => 'required|exists:workshop_mechanics,id'
        ]);
        // update the mechainc in jop and change the status to assigned
        $mechanic = WorkshopMechanic::findOrFail($request->mechanic_id); // ensure mechanic exists
        $mechanic->status = 'in_job';
        $mechanic->save();
        $serviceRequest = ServiceRequest::findOrFail($id);
        $serviceRequest->assigned_mechanic_id = $request->mechanic_id;
        $serviceRequest->dispatch_status = 'assigned';
        $serviceRequest->save();
        return response()->json([
            'message' => 'Mechanic assigned successfully'
        ], 200);
    }
}
