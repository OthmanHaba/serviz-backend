<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RequestDetail extends Model
{
    use HasFactory;

    protected $primaryKey = 'detail_id';

    protected $fillable = [
        'request_id',
        'details'
    ];

    protected $casts = [
        'details' => 'array'
    ];

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }
}
