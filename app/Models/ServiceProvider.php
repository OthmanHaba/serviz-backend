<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceProvider extends Model
{
    use HasFactory;

    protected $primaryKey = 'provider_id';

    protected $fillable = [
        'name',
        'provider_type',
        'rating',
        'service_radius_km',
        'is_available'
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'service_radius_km' => 'decimal:2',
        'is_available' => 'boolean'
    ];

    public function serviceRequests()
    {
        return $this->hasMany(ServiceRequest::class, 'provider_id');
    }

    public function locations()
    {
        return $this->hasMany(Location::class, 'provider_id');
    }

    public function currentLocation()
    {
        return $this->hasOne(Location::class, 'provider_id')->latest('updated_at');
    }
}
