<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShopItem extends Model
{
    protected $fillable = [
        'name', 'description', 'image_url', 'category',
        'coin_cost', 'cash_price', 'stock', 'is_active',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'coin_cost'  => 'integer',
        'cash_price' => 'decimal:2',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function isAffordableWithCoins(int $clientCoins): bool
    {
        return $this->coin_cost !== null && $clientCoins >= $this->coin_cost;
    }

    public function isInStock(): bool
    {
        return $this->stock === -1 || $this->stock > 0;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
