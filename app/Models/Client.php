<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Client extends Model
{
    use HasFactory;
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

    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    public function exercisePlans()
    {
        return $this->hasMany(ExercisePlan::class);
    }

    public function exerciseSessions()
    {
        return $this->hasMany(ExerciseSession::class);
    }

    public function isSubscribed(): bool
    {
        return $this->subscription_status === 'active'
            && ($this->subscription_expires_at === null
                || $this->subscription_expires_at->isFuture());
    }

    public function isFree(): bool
    {
        return $this->subscription_plan === 'free';
    }

    public function isPaid(): bool
    {
        return in_array($this->subscription_plan, ['standard', 'enterprise'], true);
    }

    public function hasStandardAccess(): bool
    {
        return $this->isPaid() && $this->isSubscribed();
    }

    public function coinTransactions()
    {
        return $this->hasMany(CoinTransaction::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(ClientAssessment::class);
    }

    // Award coins and log the transaction
    public function awardCoins(int $amount, string $description, $source = null): void
    {
        $this->increment('coin_balance', $amount);

        CoinTransaction::create([
            'client_id' => $this->id,
            'amount' => $amount,
            'type' => 'earned',
            'description' => $description,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);
    }

    // Spend coins and log the transaction
    public function spendCoins(int $amount, string $description, $source = null): bool
    {
        if ($this->coin_balance < $amount) {
            return false;
        }

        $this->decrement('coin_balance', $amount);

        CoinTransaction::create([
            'client_id' => $this->id,
            'amount' => -$amount,
            'type' => 'redeemed',
            'description' => $description,
            'source_type' => $source ? get_class($source) : null,
            'source_id' => $source?->id,
        ]);

        return true;
    }
}
