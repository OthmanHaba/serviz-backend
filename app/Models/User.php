<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory,Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'email',
        'phone',
        'password',
        'vehicle_info',
        'role',
    ];

    protected $relations = [
        'expoToken',
        'wallet',
        'currentLocation',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vehicle_info' => 'array',
            'password' => 'hashed',
        ];
    }

    public function providerServices(): HasMany
    {
        return $this->hasMany(ProviderService::class, 'user_id');
    }

    public function currentLocation(): HasOne
    {
        return $this->hasOne(Location::class)->latestOfMany();
    }

    public function userActiveRequests(): User|HasMany
    {
        return $this->hasMany(ActiveRequest::class, 'user_id');
    }

    public function providerActiveRequests(): User|HasMany
    {
        return $this->hasMany(ActiveRequest::class, 'provider_id');
    }

    public function expoToken(): HasOne
    {
        return $this->hasOne(ExpoTokens::class, 'user_id')->latestOfMany();
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class, 'user_id')->latestOfMany();
    }

    public function deposit($amount): void
    {
        $this->wallet()->firstOrCreate()->deposit($amount);
    }

    public function isProvider(): bool
    {
        return $this->role === 'provider';
    }

    public function sendPushNotification(string $title , string $body): void
    {
        if ($this->expoToken === null) {
            return;
        }

        $expoToken = $this->expoToken->token;

        Http::post('https://exp.host/--/api/v2/push/send', [
            'to' => $expoToken,
            'title' => $title,
            'body' => $body,
        ]);
    }
}
