<?php

namespace App\Http\Controllers\API;

use App\Enums\ResponseCode;
use App\Enums\ServiceStatus;
use App\Events\NewActiveRequestHasBeenCreated;
use App\Http\Controllers\Controller;
use App\Models\ActiveRequest;
use App\Models\ServicType;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function lockUp(Request $request): JsonResponse
    {

        $validator = Validator::make($request->all(), [
            'service_id' => ['required', Rule::exists(ServicType::class, 'id')],
            'coordinate' => 'required|array',
            'coordinate.latitude' => 'required|numeric',
            'coordinate.longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

        $latitude = $request->coordinate['latitude'];

        $radius = Setting::where('key', 'Service_radio')
            ->firstOrCreate([
            'key' => 'Service_radio',
            'value' => 5,
        ]);

        $radius = $radius->value('value');

        if ($radius === null) {
            return response()->json([
                'message' => 'Service radio not found',
            ], ResponseCode::NoContent->value);
        }

        $availableProviderWithService = User::whereRole('provider')
            ->whereisActive(true)
            ->whereHas('providerServices', function (Builder $query) use ($request) {
                $query->where('servic_type_id', $request->service_id);
            })
            ->whereHas('currentLocation', function ($query) use ($latitude, $longitude, $radius) {
                // filter by distance and only get the provider within 5km
                $query->whereRaw(
                    '(6371 * acos(cos((? * pi() / 180)) * cos((CAST(latitude AS DOUBLE PRECISION) * pi() / 180))
                            * cos((CAST(longitude AS DOUBLE PRECISION) * pi() / 180) - (? * pi() / 180)) + sin((? * pi() / 180))
                            * sin((CAST(latitude AS DOUBLE PRECISION) * pi() / 180)))) <= ?',
                    [$latitude, $longitude, $latitude, $radius]
                );
            })
            ->with('currentLocation')
            ->doesntHave('providerActiveRequests', 'and', function ($query) {
                $query->where('status', ServiceStatus::InProgress);
            })
            ->get();

        if ($availableProviderWithService->isEmpty()) {
            return response()->json([
                'message' => 'لا يوجد مزود خدمة متاح في منطقتك',
            ], ResponseCode::NoContent->value);
        }

        $provider = $availableProviderWithService->first();

        $price = $provider
            ->providerServices
            ->where('servic_type_id', $request->service_id)->first()->price;

        if (auth()->user()->wallet->balance < $price) {
            return response()->json([
                'message' => 'low wallet balance service price is '.$price,
            ], ResponseCode::NoContent->value);
        }

        ActiveRequest::create([
            'user_id' => auth()->id(),
            'provider_id' => $provider->id,
            'price' => $price,
            'status' => ServiceStatus::PendingUserApproved,
            'service_id' => $request->service_id,
        ]);

        $activeRequest = ActiveRequest::latest()->first();

        return response()->json([
            'provider' => $provider,
            'active_request' => $activeRequest,
        ], ResponseCode::Success->value);
    }

    /**
     * @throws ConnectionException
     */
    public function userApproveRequest(Request $request)
    {
        $request->validate([
            'active_request_id' => 'required|exists:'.ActiveRequest::class.',id',
            'action' => 'required|in:approve,decline',
        ]);

        $activeRequest = ActiveRequest::with([
            'user',
            'service',
        ])->find($request->active_request_id);

        if ($request->action === 'decline') {
            $activeRequest->delete();

            return response()->json([
                'message' => 'Request declined',
            ], ResponseCode::Success->value);
        }

        $activeRequest->update([
            'status' => ServiceStatus::PendingProviderApproved,
        ]);

        $provider = $activeRequest->provider_id;

        $provider = User::find($provider);

        event(new NewActiveRequestHasBeenCreated($activeRequest, $provider));

        try {
            $provider->sendPushNotification(
                'New Request',
                'New request from '.$activeRequest->user->name.' for '.$activeRequest->service->name
            );
        } catch (ConnectionException $e) {
            Log::error($e->getMessage());
        }

        return response()->json([
            'message' => 'Request approved',
        ], ResponseCode::Success->value);
    }

    public function providerApproveOrDeclineRequest(Request $request)
    {
        $request->validate([
            'active_request_id' => 'required|exists:'.ActiveRequest::class.',id',
            'status' => 'required|in:approved,declined',
        ]);

        $activeRequest = ActiveRequest::find($request->active_request_id);

        if ($request->status === 'approved') {
            $activeRequest->update([
                'status' => ServiceStatus::InProgress,
            ]);
            event(new NewActiveRequestHasBeenCreated($activeRequest, $activeRequest->user));
        } else {
            $activeRequest->delete();
        }

        return response()->json([
            'message' => 'Request '.$request->status,
            'id' => $activeRequest->id,
        ], ResponseCode::Success->value);
    }

    public function getStatus(Request $request)
    {
        $request->validate([
            'active_request_id' => 'required|exists:'.ActiveRequest::class.',id',
        ]);
        $request = ActiveRequest::find($request->active_request_id)
            ->load(['provider.currentLocation', 'user.currentLocation']);

        return response()->json($request, ResponseCode::Success->value);
    }
}
