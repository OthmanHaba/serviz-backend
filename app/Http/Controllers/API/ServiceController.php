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
            'service_id' => 'required|exists:'.ServicType::class.',id',
            'coordinate' => 'required|array',
            'coordinate.latitude' => 'required|numeric',
            'coordinate.longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }

       $availableProviderWithService = User::whereRole('provider')
           ->whereisActive( true)
           ->whereHas('providerServices', function (Builder $query) use ($request) {
               $query->where('servic_type_id', $request->service_id);
           })
           ->whereHas('currentLocation',function($q) use ($request) {
           })
           ->with('currentLocation')
           ->doesntHave('providerActiveRequests')
           ->get();

        if($availableProviderWithService->isEmpty()){
            return response()->json([
                'message' => 'No provider services available',
            ],ResponseCode::NoContent->value);
        }

        $provider = $availableProviderWithService->first();

        $activeRequest = ActiveRequest::create([
            'user_id' => auth()->id(),
            'provider_id' => $provider->id,
            'price' => $provider
                ->providerServices
                ->where('servic_type_id', $request->service_id)->first()->price,
            'status' => ServiceStatus::PendingUserApproved,
        ]);

        //TODO notify the provider

        return response()->json([
            'providers' => $availableProviderWithService,
        ],ResponseCode::Success->value);
    }
}
