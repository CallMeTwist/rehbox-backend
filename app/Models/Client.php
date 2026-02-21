<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
//    protected $fillable = [
//        'user_id', 'physiotherapist_id', 'phone', 'date_of_birth',
//        'gender', 'primary_condition', 'subscription_status',
//        'subscription_expires_at', 'paystack_customer_code',
//        'language_preference', 'coin_balance',
//    ];

    protected $guarded = [];

    protected $casts = [
        'date_of_birth' => 'date',
        'subscription_expires_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function physiotherapist()
    {
        return $this->belongsTo(Physiotherapist::class);
    }

    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active'
            && ($this->subscription_expires_at === null
                || $this->subscription_expires_at->isFuture());
    }
}
