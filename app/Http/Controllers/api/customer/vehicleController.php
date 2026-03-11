<?php

namespace App\Http\Controllers\api\customer;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Auth;
use Illuminate\Http\Request;

class vehicleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number',
            'model' => 'required|string|max:255',
            'brand' => 'required|integer|min:1900|max:' . date('Y'),
        ]);

        $vehicle = Vehicle::create([
            'user_id' => auth()->user()->id,
            'plate_number' => $request->plate_number,
            'model' => $request->model,
            'brand' => $request->brand,
        ]);

        return response()->json(['message' => 'Vehicle added successfully', 'vehicle' => $vehicle], 201);
    }
    public function edit(Request $request , $id)
    {
        $vehicle = Vehicle::where('user_id', $request->user()->id)->where('id', $id)->first();

        if (!$vehicle) {
            return response()->json(['message' => 'Vehicle not found'], 404);
        }

        return response()->json(['vehicle' => $vehicle], 200);
    }
    public function update(Request $request, $id){
        $vehicle = Vehicle::where('user_id', $request->user()->id)->where('id', $id)->first();
        if(!$vehicle){
            return response()->json(['message'=> 'Vehicle not found'], 404);
        }
        $request->validate([
            'plate_number' => 'required|string|max:255|unique:vehicles,plate_number,' . $vehicle->id,
            'model' => 'required|string|max:255',
            'brand' => 'required|integer|min:1900|max:' . date('Y'),
        ]);
        $vehicle->update($request->all());
        return response()->json(['message' => 'Vehicle updated successfully', 'vehicle' => $vehicle], 200);
    }
    public function destroy(Request $request, $id){
        $vehicle = Vehicle::where('user_id', $request->user()->id)->where('id', $id)->first();
        if(!$vehicle){
            return response()->json(['message'=> 'Vehicle not found'], 404);
        }
        $vehicle->delete();
        return response()->json(['message' => 'Vehicle deleted successfully'], 200);
    }

}
