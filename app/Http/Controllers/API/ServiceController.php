<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    public function lockUp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'service' => 'required|exsits:services,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(),403);
        }

        $avalibleProviderWithService = User::whereRole('provider')
            ->whereIsActive(true);
            // ->whereHas('activeRequest')

    }
}
