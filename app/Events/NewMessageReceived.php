<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class NewMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public Message $message) {}

    public function broadcastOn(): array
    {
        // Broadcast to both sender and receiver channels
        return [
            new PrivateChannel('chat.'.$this->message->client_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->message->id,
            'body' => $this->message->body,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'client_id' => $this->message->client_id,
            'sender_name' => $this->message->sender->name,
            'sender_role' => $this->message->sender->role,
            'created_at' => $this->message->created_at,
        ];
    }
}
