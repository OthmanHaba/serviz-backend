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

        if($user->role == 'provider'){
            $activeRequest = $user->providerActiveRequests()
                ->whereStatus(ServiceStatus::InProgress)
//                ->orWhere('status',ServiceStatus::PendingProviderApproved)
                ->first();

            if($activeRequest) {
                return response()->json([
                    'id' => $activeRequest->id
                ]);
            }
        }
        else if($user->role == 'user'){
            $activeRequest = $user->userActiveRequests()
                ->whereStatus(ServiceStatus::InProgress)
                ->first();


            if($activeRequest) {
                return response()->json([
                    'id' => $activeRequest->id
                ]);
            }
        }

        return response()->json([], 204);
    }
}
