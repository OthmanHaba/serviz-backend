<?php

namespace App\Http\Controllers\Api;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActiveRequestResource;
use App\Models\ActiveRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProviderController extends Controller
{
    public function activeProviders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'radius' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $latitude = $request->latitude;
        $longitude = $request->longitude;
        $radius = $request->radius;

        $providers = User::whereAll([
            'is_active' => true,
            'role' => 'provider',
        ])
            ->with('currentLocation')
            ->whereRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) <= ?',
                [$latitude, $longitude, $latitude, $radius])
            ->get();

        return response()->json($providers);
    }

    public function toggleActive(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'is_active' => 'required|boolean',
        ]);

        $user->is_active = $request->is_active;
        $user->save();

        return response()->json(['is_active' => $user->is_active]);
    }

    public function updateLocation(Request $request)
    {
        /**
         * @var User
         */
        $user = Auth::user();
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $user->currentLocation()->firstOrCreate([
            'latitude' => $request->lat,
            'longitude' => $request->long,
        ]);

        return response()->json(['message' => 'Location updated successfully']);
    }

    public function getActiveRequests(Request $request)
    {
        $user = Auth::user();

        $requests = $user
            ->providerActiveRequests()
            ->whereStatus(ServiceStatus::PendingProviderApproved)
            ->with([
                'user.currentLocation',
                'service',
            ])
            ->get();

        return response()->json(ActiveRequestResource::collection($requests));
    }

    public function completeActiveRequest(Request $req)
    {
        $req->validate([
            'active_request_id' => 'required|exists:'.ActiveRequest::class.',id',
        ]);

        $ActiveRequest = ActiveRequest::find($req->active_request_id);

        $ActiveRequest->update([
            'status' => ServiceStatus::Completed,
        ]);

        return response()->json(['message' => 'Request completed successfully']);
    }
}
