<?php

namespace Database\Factories;

use App\Models\SupportSession;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class SupportSessionFactory extends Factory
{
    protected $model = SupportSession::class;

    public function definition(): array
    {
        return [
            'status' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

            'user_id' => User::factory(),
            'admin_id' => User::factory(),
        ];
    }
}
