<?php

namespace App\Http\Controllers\Api\Client;

use App\Events\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    // Get conversation between PT and client
    public function index(Request $request)
    {
        $user   = $request->user();
        $client = $request->query('client_id');

        $messages = Message::where('client_id', $client)
            ->with(['sender:id,name,role'])
            ->orderBy('created_at')
            ->get();

        // Mark as read
        Message::where('client_id', $client)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json($messages);
    }

    // Send a message
    public function store(Request $request)
    {
        $data = $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'client_id'   => 'required|exists:clients,id',
            'body'        => 'required|string|max:2000',
        ]);

        $message = Message::create([
            'sender_id'   => $request->user()->id,
            'receiver_id' => $data['receiver_id'],
            'client_id'   => $data['client_id'],
            'body'        => $data['body'],
        ]);

        $message->load('sender:id,name,role');

        // Broadcast to receiver via Reverb
        event(new NewMessageReceived($message));

        return response()->json($message, 201);
    }
}
