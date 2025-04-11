<?php

namespace App\Action;

use App\Models\User;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class SendNotificationToUser
{
    public function __construct()
    {
        //
    }

    /**
     * @throws ConnectionException
     */
    public function __invoke(User $user, string $title, string $body): void
    {

        if ($user->expoToken === null) {
            return;
        }

        $expoToken = $user->expoToken?->token;

        Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $expoToken,
            'title' => $title,
            'body' => $body,
        ]);
    }
}
