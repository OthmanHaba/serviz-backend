<?php

namespace Database\Seeders;

use App\Models\ServicType;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@admin.com',
        ]);

        $this->createServiceType();

    }

    private function createServiceType()
    {
        ServicType::create([
            'name' => 'Towing',
            'description' => 'Towing services',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

        ServicType::create([
            'name' => 'Mobile Repair',
            'description' => 'Mobile Repair services',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

        ServicType::create([
            'name' => 'Repair Tires',
            'description' => 'Repair Tires services',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

    }
}
