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
        return response()->json([
            'sessions' => Auth::user()->supportSessions()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject' => 'required|string',
        ]);

        $session = SupportSession::create([
            'user_id' => Auth::id(),
            'admin_id' => 1,
            'status' => FlagEnum::OPEN,
            'subject' => $request->subject,
        ]);

        return response()->noContent();
    }

    public function show(SupportSession $session)
    {
        return response()->json([
            'session' => $session,
            'messages' => $session->supportMessages()->with('sender')->get(),
        ]);
    }

    public function sendMessage(SupportSession $session, Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $session->supportMessages()->create([
            'sender_id' => Auth::id(),
            'message' => $request->message,
        ]);
    }
}
