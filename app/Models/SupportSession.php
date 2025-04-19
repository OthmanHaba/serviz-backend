<?php

namespace App\Models;

use App\Enums\FlagEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportSession extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => FlagEnum::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function supportMessages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'support_session_id');
    }
}
