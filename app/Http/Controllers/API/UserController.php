<?php

namespace App\Http\Controllers\API;

use App\Enums\ServiceStatus;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function refreshActiveRequest()
    {
        $user = Auth::user();

        if ($user->role == 'provider') {
            $activeRequest = $user->providerActiveRequests()
                ->whereStatus(ServiceStatus::InProgress)
//                ->orWhere('status',ServiceStatus::PendingProviderApproved)
                ->first();

            if ($activeRequest) {
                return response()->json([
                    'id' => $activeRequest->id,
                ]);
            }
        } elseif ($user->role == 'user') {
            $activeRequest = $user->userActiveRequests()
                ->whereStatus(ServiceStatus::InProgress)
                ->first();

            if ($activeRequest) {
                return response()->json([
                    'id' => $activeRequest->id,
                ]);
            }
        }

        return response()->json([], 204);
    }

    public function history()
    {
        $user = Auth::user();

        $requests = $user->role == 'provider' ? $user
            ->load('providerActiveRequests')
            ->providerActiveRequests
            ->load([
                'user', 'provider', 'service',
            ]) :
            $user
                ->load('userActiveRequests')
                ->userActiveRequests
                ->load([
                    'user', 'provider', 'service',
                ]);

        return response()->json($requests);
    }

    public function profile()
    {
        $user = Auth::user()->load('wallet');

        if ($user->isProvider()) {
            $user->load('providerServices.serviceType');
        }

        return response()->json($user);
    }
}
