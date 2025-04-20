<?php

namespace App\Http\Controllers;

use App\Enums\FlagEnum;
use App\Models\SupportSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportSessionController extends Controller
{
    public function index()
    {
        return Auth::user()->supportSessions();
    }

    public function store(Request $request)
    {
        $session = SupportSession::create([
            'user_id' => Auth::id(),
            'admin_id' => 1,
            'status' => FlagEnum::OPEN,
            'subject' => $request->subject
        ]);

        return response()->json([
            'session_id' => $session->id
        ]);
    }

    public function show(SupportSession $session)
    {
        return response()->json([
            'messages' =>  $session->supportMessages()
        ]);
    }
}
