<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function register(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($data['password']),
                    'vehicle_info' => $data['vehicle_info'] ?? null,
                    'role' => $data['role'],
                ]);

                if ($data['role'] === 'provider') {
                    $user->providerServices()->createMany($data['service_type']);
                }

                $user->wallet()->create([
                    'balance' => 0,
                ]);

                return $user;
            });
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function login(array $credentials)
    {
        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        /** @var User $user */
        $user = Auth::user()->load('wallet');

        $token = $user->createToken('auth-token')->plainTextToken;

        if ($user->email_verified_at === null) {
            return [
                'type' => 'error',
                'message' => 'لم يتم تآكيد حسابك بعد',
            ];
        }

        return [
            'user' => $user,
            'token' => $token,
            'type' => 'success',
        ];
    }

    public function logout()
    {
        Auth::user()->tokens()->delete();
    }
}
