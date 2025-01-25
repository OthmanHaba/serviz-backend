<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingModel extends Model
{
    use HasFactory;

    protected $primaryKey = 'model_id';

    protected $fillable = [
        'service_type',
        'base_fee',
        'fee_per_km',
        'parameters'
    ];

    protected $casts = [
        'base_fee' => 'decimal:2',
        'fee_per_km' => 'decimal:2',
        'parameters' => 'array'
    ];
}
