<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use MatanYadaev\EloquentSpatial\Objects\Point;
use MatanYadaev\EloquentSpatial\Traits\HasSpatial;

class Location extends Model
{
    use HasFactory, HasSpatial;

    protected $primaryKey = 'location_id';

    protected $fillable = [
        'provider_id',
        'coordinates',
    ];

    protected $casts = [
        'coordinates' => Point::class,
    ];

    public function provider()
    {
        return $this->belongsTo(ServiceProvider::class, 'provider_id');
    }
}
