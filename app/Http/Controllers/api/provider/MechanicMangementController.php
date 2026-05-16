<?php

namespace App\Http\Controllers\api\provider;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkshopMechanic;
use Illuminate\Support\Facades\Hash;

class MechanicMangementController extends Controller
{
    public function index($workshop_id)
    {
        $mechanics = WorkshopMechanic::where('workshop_id', $workshop_id)->get();
        return response()->json(['mechanics' => $mechanics], 200);
    }
    public function addMechanic(Request $request)
    {
        // Validate the request
        $request->validate([
            'workshop_id' => 'required|exists:provider_profiles,id',
            'name' => 'required|string',
            'user_name' => 'required|string|unique:workshop_mechanics,user_name',
            'password' => 'required|string|min:6',
            'phone_number' => 'required|string',
        ]);

        // Create a new mechanic
        $mechanic = WorkshopMechanic::create([
            'workshop_id' => $request->workshop_id,
            'name' => $request->name,
            'user_name' => $request->user_name,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
        ]);

        return response()->json(['message' => 'Mechanic added successfully', 'mechanic' => $mechanic], 201);
    }
    public function update($id)
    {
        $mechanic = WorkshopMechanic::findOrFail($id);
        return response()->json(['mechanic' => $mechanic], 200);
    }
    public function editMechanic(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'name' => 'sometimes|required|string',
            'user_name' => 'sometimes|required|string|unique:workshop_mechanics,user_name,' . $id,
            'password' => 'sometimes|required|string|min:6',
            'phone_number' => 'sometimes|required|string',
        ]);

        // Find the mechanic
        $mechanic = WorkshopMechanic::findOrFail($id);

        // Update the mechanic's information
        if ($request->has('name')) {
            $mechanic->name = $request->name;
        }
        if ($request->has('user_name')) {
            $mechanic->user_name = $request->user_name;
        }
        if ($request->has('password')) {
            $mechanic->password = Hash::make($request->password);
        }
        if ($request->has('phone_number')) {
            $mechanic->phone_number = $request->phone_number;
        }
        // Save the updated mechanic
        $mechanic->save();
        return response()->json(['message' => 'Mechanic updated successfully', 'mechanic' => $mechanic], 200);
    }
    public function updateMechanicStatus(Request $request, $id)
    {
        // Validate the request
        $request->validate([
            'status' => 'required|in:offline,in_job,available',
        ]);

        // Find the mechanic
        $mechanic = WorkshopMechanic::findOrFail($id);
        // Update the mechanic's status
        $mechanic->status = $request->status;
        $mechanic->save();

        return response()->json(['message' => 'Mechanic status updated successfully', 'mechanic' => $mechanic], 200);
    }
    public function deleteMechanic($id)
    {
        $mechanic = WorkshopMechanic::findOrFail($id);
        $mechanic->delete();
        return response()->json(['message' => 'Mechanic deleted successfully'], 200);
    }
}
