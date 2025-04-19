<?php

namespace Database\Seeders;

use App\Models\ServicType;
use App\Models\Setting;
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
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
        ]);

        $provider = User::factory()->create([
            'name' => 'provider',
            'email' => 'user@provider.com',
            'role' => 'provider',
        ]);

        User::factory()->create([
            'name' => 'user',
            'email' => 'user@user.com',
            'role' => 'user',
        ]);

        $this->createServiceType();

        $provider->providerServices()->createMany([
            [
                'servic_type_id' => 1,
                'price' => 100,
            ],
            [
                'servic_type_id' => 2,
                'price' => 200,
            ],
            [
                'servic_type_id' => 3,
                'price' => 300,
            ],
        ]);

        Setting::create([
            'key' => 'Service_radio',
            'value' => 5,
        ]);

    }

    private function createServiceType(): void
    {
        ServicType::create([
            'name' => 'سحب السيارات',
            'description' => 'خدمات سحب السيارات',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

        ServicType::create([
            'name' => 'إصلاح متنقل',
            'description' => 'خدمات إصلاح متنقل',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

        ServicType::create([
            'name' => 'إصلاح الإطارات',
            'description' => 'خدمات إصلاح الإطارات',
            'image' => 'https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png',
        ]);

    }
}
