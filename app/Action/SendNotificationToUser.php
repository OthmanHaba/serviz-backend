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

    public function __invoke(User $user,string $title, string $body)
    {
        $notification = [
            'title' => $title,
            'body' => $body,
        ];

        $expoToken = $user->expoToken->token;

        $this->sendNotification($expoToken, $notification);
    }

    /**
     * @throws ConnectionException
     */
    private function sendNotification($expoToken, array $notification)
    {
        Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $expoToken,
            'title' => $notification['title'],
            'body' => $notification['body'],
        ]);


    }
}
