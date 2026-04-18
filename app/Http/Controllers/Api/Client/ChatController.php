<?php

namespace App\Http\Controllers\Api\Client;

use App\Events\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Models\AppNotification;
use App\Models\Client;
use App\Models\Message;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function index(Request $request): JsonResponse
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

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'body' => 'nullable|string|max:5000',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf|max:10240',
            'client_id' => 'sometimes|integer|exists:clients,id',
            'receiver_id' => 'sometimes|integer|exists:users,id',
        ]);

        if (empty($data['body']) && ! $request->hasFile('file')) {
            return response()->json(['message' => 'Message body or file is required.'], 422);
        }

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

        $fileUrl = null;
        $fileType = null;
        $fileName = null;
        $fileSize = null;

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $mimeType = $file->getMimeType();
            $fileType = str_starts_with($mimeType, 'image/') ? 'image' : 'pdf';
            $fileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $path = $file->store('chat-files', 'public');
            $fileUrl = url('/api/chat/files/'.basename($path));
        }

        $message = Message::create([
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'client_id' => $clientId,
            'body' => $data['body'] ?? '',
            'file_url' => $fileUrl,
            'file_type' => $fileType,
            'file_name' => $fileName,
            'file_size' => $fileSize,
        ]);

        $message->load('sender:id,name,role');

        event(new NewMessageReceived($message));

        // Notify the receiver
        AppNotification::create([
            'user_id' => $message->receiver_id,
            'type' => 'message_received',
            'title' => 'New message',
            'body' => $request->user()->name.': '.(str($message->body)->limit(60) ?: '[file]'),
            'data' => ['client_id' => $message->client_id],
        ]);

        return response()->json(['message' => $message], 201);
    }
}
