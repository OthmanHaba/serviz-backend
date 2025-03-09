<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): \Illuminate\Database\Eloquent\Relations\HasMany|Wallet
    {
        return $this->hasMany(Transaction::class, 'sender_id');
    }

    public function receivedTransactions(): \Illuminate\Database\Eloquent\Relations\HasMany|Wallet
    {
        return $this->hasMany(Transaction::class, 'receiver_id');
    }

    public function deposit($amount): void
    {
        $this->balance += $amount;
        $this->save();
    }

    public function withdraw($amount): void
    {
        $this->balance -= $amount;
        $this->save();
    }

    public function transfer($amount, Wallet $receiver): void
    {
        $this->withdraw($amount);
        $receiver->deposit($amount);
        Transaction::create([
            'sender_id' => $this->id,
            'receiver_id' => $receiver->id,
            'amount' => $amount,
        ]);
    }
}
