<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => fake()->phoneNumber(),
            'vehicle_info' => json_encode([
                'make' => fake()->randomElement(['Toyota', 'Honda', 'Ford', 'Chevrolet', 'Volkswagen', 'BMW', 'Mercedes-Benz', 'Audi', 'Porsche', 'Lamborghini', 'Ferrari', 'Maserati', 'Jaguar', 'Land Rover', 'Volvo', 'Saab', 'Mitsubishi', 'Suzuki', 'Yamaha', 'Kawasaki', 'Honda', 'KTM', 'Ducati', 'Aprilia', 'MV Agusta', 'Benelli', 'Triumph', 'Harley-Davidson', 'Kawasaki', 'Yamaha', 'Suzuki', 'KTM', 'BMW', 'Mercedes-Benz', 'Audi', 'Porsche', 'Lamborghini', 'Ferrari', 'Maserati', 'Jaguar', 'Land Rover', 'Volvo', 'Saab', 'Mitsubishi', 'Suzuki', 'Yamaha', 'Kawasaki', 'Honda', 'KTM', 'Ducati', 'Aprilia', 'MV Agusta', 'Benelli', 'Triumph', 'Harley-Davidson']),
                'model' => fake()->randomElement(['Camry', 'Accord', 'F-150', 'Silverado', 'Golf', 'X5', 'C-Class', 'A4', '911', 'Huracan', '488', 'GTC', 'F8', 'DBX', 'S-Class', 'E-Class', 'A-Class', 'GLC', 'GLE', 'GLS', 'S-Class', 'Maybach', 'AMG', 'GT', 'AMG GT', 'GT R', 'GT S', 'GT C', 'GT Roadster', 'GT 4', 'GT 5', 'GT 6', 'GT 7', 'GT 8', 'GT 9', 'GT 10', 'GT 11', 'GT 12', 'GT 13', 'GT 14', 'GT 15', 'GT 16', 'GT 17', 'GT 18', 'GT 19', 'GT 20']),
                'year' => fake()->year(),
                'color' => fake()->colorName(),
                'vin' => fake()->randomNumber(8, true),
            ]),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
