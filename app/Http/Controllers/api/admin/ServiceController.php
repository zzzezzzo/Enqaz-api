<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(){
        $services = Service::all();
        return response()->json($services);
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $service = Service::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Service created successfully',
            'service' => $service
        ], 201);
    }
    public function edit($id){
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message' => 'Service not found'], 404);
        }
        return response()->json($service);
    }
    public function update(Request $request, $id){
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message' => 'Service not found'], 404);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $service->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return response()->json([
            'message' => 'Service updated successfully',
            'service' => $service
        ]);
    }
    public function destroy($id){
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message' => 'Service not found'], 404);
        }
        $service->delete();
        return response()->json([
            'message' => 'Service deleted successfully'
        ]);
    }
    public function updateServiceActive(Request $request, $id){
        $service = Service::find($id);
        if(!$service){
            return response()->json(['message' => 'Service not found'], 404);
        }
        $request->validate([
            'is_active' => 'required|boolean',
        ]);
        // dd($request->is_active);
        
        $service->update([
            'is_active' => $request->is_active,
        ]);

        return response()->json([
            'message' => 'Service active status updated successfully',
            'service' => $service
        ]);
    }
}
