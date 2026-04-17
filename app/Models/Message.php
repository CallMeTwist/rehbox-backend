<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'sender_id',
        'receiver_id',
        'client_id',
        'body',
        'read_at',
        'file_url',
        'file_type',
        'file_name',
        'file_size',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
