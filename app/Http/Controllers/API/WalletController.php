<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    public function deposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        $wallet = Auth::user()->wallet()->firstOrCreate();

        $wallet->deposit($request->amount);

        return response()->json(['message' => 'Deposited successfully']);
    }
}
