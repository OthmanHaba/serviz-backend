<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class ServiceRequest extends Model
{
    use HasFactory, HasSpatial;

    protected $primaryKey = 'request_id';

    protected $fillable = [
        'user_id',
        'provider_id',
        'service_type',
        'status',
        'pickup_location',
        'total_price',
        'requested_at'
    ];

    protected $casts = [
        'pickup_location' => Point::class,
        'total_price' => 'decimal:2',
        'requested_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($serviceRequest) {
            $serviceRequest->requested_at = now();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }

    public function details()
    {
        return $this->hasOne(RequestDetail::class, 'request_id');
    }

    public function payment()
    {
        return $this->hasOne(Payment::class, 'request_id');
    }
}
