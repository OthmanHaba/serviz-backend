<?php

namespace Database\Factories;

use App\Models\SupportMessage;
use App\Models\SupportSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupportMessageFactory extends Factory
{
    protected $model = SupportMessage::class;

    public function definition(): array
    {
        return [
            'message' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'sender_id' => User::factory(),
            'support_session_id' => SupportSession::factory(),
        ];
    }
}
