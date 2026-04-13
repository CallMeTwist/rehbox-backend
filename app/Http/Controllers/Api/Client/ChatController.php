<?php

namespace App\Http\Controllers\Api\Client;

use App\Events\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Message;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $clientId = $request->query('client_id');

        // Auto-resolve for client users
        if (! $clientId && $user->role === 'client') {
            $clientId = $user->client?->id;
        }

        if (! $clientId) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::where('client_id', $clientId)
            ->with(['sender:id,name,role'])
            ->orderBy('created_at')
            ->get();

        Message::where('client_id', $clientId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['messages' => $messages]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'body' => 'required|string|max:2000',
            'client_id' => 'sometimes|integer|exists:clients,id',
            'receiver_id' => 'sometimes|integer|exists:users,id',
        ]);

        $user = $request->user();

        // Resolve client_id
        $clientId = $data['client_id'] ?? null;
        if (! $clientId && $user->role === 'client') {
            $clientId = $user->client?->id;
        }
        if (! $clientId) {
            return response()->json(['message' => 'client_id is required.'], 422);
        }

        $client = Client::with(['user', 'physiotherapist'])->findOrFail($clientId);

        // Resolve receiver_id
        $receiverId = $data['receiver_id'] ?? null;
        if (! $receiverId) {
            if ($user->role === 'pt') {
                $receiverId = $client->user_id;
            } else {
                $pt = $client->physiotherapist;
                if (! $pt) {
                    return response()->json([
                        'message' => 'No physiotherapist linked. Add activation code first.',
                    ], 422);
                }
                $receiverId = $pt->user_id;
            }
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'client_id' => $clientId,
            'body' => $data['body'],
        ]);

        $message->load('sender:id,name,role');

        event(new NewMessageReceived($message));

        return response()->json(['message' => $message], 201);
    }
}
