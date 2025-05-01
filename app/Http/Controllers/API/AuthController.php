<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ExpoTokens;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'vehicle_info' => 'nullable|array',
            'role' => 'required|in:user,provider',
        ];

        if ($request->role === 'provider') {
            $rules['service_type'] = 'required|array';
            $rules['service_type.*.servic_type_id'] = 'required|exists:servic_types,id';
            $rules['service_type.*.price'] = 'required|numeric|min:0';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $this->authService->register($request->all());

        return response()->json([
            'message' => 'User registered successfully',
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {

            $result = $this->authService->login($request->only('email', 'password'));

            if ($result['type'] == 'error') {
                return response()->json($result, 204);
            }

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
    }

    public function logout(Request $request)
    {
        $this->authService->logout();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function storeExpoToken(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $authUser = auth()->user();

        ExpoTokens::create([
            'user_id' => $authUser->id,
            'token' => $request->token,
        ]);

        return response()->json(['message' => 'Token stored successfully']);
    }
}
