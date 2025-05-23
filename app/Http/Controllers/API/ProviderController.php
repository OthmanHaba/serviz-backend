<?php

namespace App\Http\Controllers\API;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\ActiveRequestResource;
use App\Models\ActiveRequest;
use App\Models\ProviderService;
use App\Models\ServicType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
         * @var User $user
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

    /**
     * @throws \Throwable
     * @throws \Exception
     */
    public function completeActiveRequest(Request $req)
    {
        $req->validate([
            'active_request_id' => 'required|exists:'.ActiveRequest::class.',id',
        ]);

        DB::transaction(function () use ($req) {

            $activeRequest = ActiveRequest::find($req->active_request_id);

            $activeRequest->update([
                'status' => ServiceStatus::Completed,
            ]);

            $user = $activeRequest->user;
            if ($activeRequest->type == 'Wallet') {
                $user->wallet->transfer(
                    (float) $activeRequest->price * 0.7,
                    $activeRequest->provider->wallet
                );

                $user->wallet->transfer(
                    (float) $activeRequest->price * 0.3,
                    User::find(1)->wallet
                );

            } else {
                $activeRequest->provider->wallet->deposit(
                    $activeRequest->price * 0.7
                );

                User::find(1)->wallet->deposit(
                    $activeRequest->price * 0.3
                );
            }
        });

        return response()->json(['message' => 'تم إكمال الطلب بنجاح']);
    }

    public function todayStatics()
    {
        $user = Auth::user();

        $totalRevenue = $user->providerActiveRequests()
            ->whereDate('created_at', '=', now()->format('Y-m-d'))
            ->where('status', ServiceStatus::Completed)
            ->sum('price');

        $totalRequests = $user->providerActiveRequests()
            ->whereDate('created_at', '=', now()->format('Y-m-d'))
            ->where('status', ServiceStatus::Completed)
            ->count();

        $lastUpdateStatus = Carbon::parse($user->updated_at);

        $workedHours = 0;
        // check if the updated at is from this day or not
        if ($lastUpdateStatus->isToday()) {
            $workedHours = $lastUpdateStatus->diffInHours(now());
        }

        $statics = [
            'total_revenue' => $totalRevenue,
            'total_requests' => $totalRequests,
            'total_worked_hours' => $workedHours,
            'total_rates' => 0,
        ];

        return response()->json($statics);
    }

    public function addOrSaveService(Request $request)
    {
        $request->validate([
            'service_type_id' => 'required|exists:'.ServicType::class.',id',
            'price' => 'required|numeric',
        ]);

        $prov = ProviderService::where('servic_type_id', $request->service_type_id)
            ->where('user_id', Auth::id())
            ->first();

        if ($prov) {
            $prov->update([
                'price' => $request->price,
            ]);
        } else {

            Auth::user()->providerServices()->create([
                'servic_type_id' => $request->service_type_id,
                'price' => $request->price,
            ]);
        }

        return response()->json([
            'message' => 'Service added successfully',
        ]);
    }
}
