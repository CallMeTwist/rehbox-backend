<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'client_id', 'shop_item_id', 'payment_method',
        'coins_used', 'cash_paid', 'status', 'delivery_address',
    ];

    public function client()  { return $this->belongsTo(Client::class); }
    public function item()    { return $this->belongsTo(ShopItem::class, 'shop_item_id'); }
}
