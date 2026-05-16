<?php

namespace App\Http\Controllers\api\provider\mechanic;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\WorkshopMechanic;
use Illuminate\Support\Facades\Hash;

class MechanicAuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate the request
        $request->validate([
            'user_name' => 'required|string',
            'password' => 'required|string',
        ]);

        // Attempt to find the mechanic by user_name
        $mechanic = WorkshopMechanic::where('user_name', $request->user_name)->first();

        // Check if mechanic exists and password is correct
        if (!$mechanic || !Hash::check($request->password, $mechanic->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Generate an API token for the mechanic
        $token = $mechanic->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'mechanic' => $mechanic,
        ]);
    }
}
