<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Facades\JWTAuth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        
        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }
        
        // Load the role relationship if not already loaded
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }
        
        if (!$user->role) {
            return response()->json(['message' => 'User has no role assigned'], 403);
        }
        
        if ($user->role->name !== $role) {
            return response()->json([
                'message' => 'Unauthorized. Your role is ' . $user->role->name . ', required: ' . $role
            ], 403);
        }
        
        return $next($request);    
    }
}
