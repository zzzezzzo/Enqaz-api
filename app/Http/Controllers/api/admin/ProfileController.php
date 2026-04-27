<?php

namespace App\Http\Controllers\api\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index(){
        $admin = auth()->user();
        return response()->json([
            'success' => true,
            'data' => $admin
        ]);
    }
    public function store(Request $request){
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->id(),
            'password' => 'required|string|confirmed|min:8',
            'phone' => 'nullable|string|max:20',
        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
            'role_id' => 1,
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Admin profile created successfully'
        ]);
    }
    public function indexAdmins(){
        $admins = User::where('role_id', 1)->get();
        return response()->json([
            'success' => true,
            'data' => $admins
        ]);
    }
}
