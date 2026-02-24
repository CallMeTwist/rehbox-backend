<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoinTransaction extends Model
{
    protected $fillable = [
        'client_id', 'amount', 'type', 'description',
        'source_type', 'source_id',
    ];

    public function client() { return $this->belongsTo(Client::class); }

    public function source() { return $this->morphTo(); }
}
