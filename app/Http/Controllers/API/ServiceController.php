<?php

namespace App\Http\Controllers\Api;

use App\Enums\ResponseCode;
use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Models\ActiveRequest;
use App\Models\ProviderService;
use App\Models\ServicType;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function lockUp(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'service_id' => 'required|exists:' . ServicType::class . ',id',
            'coordinate' => 'required|array',
            'coordinate.latitude' => 'required|numeric',
            'coordinate.longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $availableProviderWithService = User::whereRole('provider')
            ->whereisActive(true)
            ->whereHas('providerServices', function (Builder $query) use ($request) {
                $query->where('servic_type_id', $request->service_id);
            })
            ->whereHas('currentLocation', function ($q) use ($request) {
                //TODO: add the distance filter
            })
            ->with('currentLocation')
            ->doesntHave('providerActiveRequests')
            ->get();

        if ($availableProviderWithService->isEmpty()) {
            return response()->json([
                'message' => 'No provider services available',
            ], ResponseCode::NoContent->value);
        }

        $provider = $availableProviderWithService->first();

        ActiveRequest::create([
            'user_id' => auth()->id(),
            'provider_id' => $provider->id,
            'price' => $provider
                ->providerServices
                ->where('servic_type_id', $request->service_id)->first()->price,
            'status' => ServiceStatus::PendingUserApproved,
        ]);

        $activeRequest = ActiveRequest::latest()->first();

        //TODO notify the provider

        return response()->json([
            'provider' => $provider,
            'active_request' => $activeRequest,
        ], ResponseCode::Success->value);
    }

    public function userApproveRequest(Request $request)
    {
        $request->validate([
            'active_request_id' => 'required|exists:' . ActiveRequest::class . ',id',
        ]);

        $activeRequest = ActiveRequest::find($request->active_request_id);

        $activeRequest->update([
            'status' => ServiceStatus::PendingProviderApproved,
        ]);

        $provider = $activeRequest->provider_id;

        $provider = User::find($provider);

//        $provider->noti



        //TODO notify the provider


        return response()->json([
            'message' => 'Request approved',
        ], ResponseCode::Success->value);
    }
}
