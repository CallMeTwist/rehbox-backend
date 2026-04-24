<?php

namespace App\Http\Controllers\Api\Client;

use App\Events\MessageRead;
use App\Events\NewMessageReceived;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\MarkChatReadRequest;
use App\Http\Requests\Chat\SendMessageRequest;
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

        if (! $clientId && $user->role === 'client') {
            $clientId = $user->client?->id;
        }

        if (! $clientId) {
            return response()->json(['messages' => []]);
        }

        $messages = Message::query()
            ->where('client_id', $clientId)
            ->with(['sender:id,name,role'])
            ->orderBy('created_at')
            ->get();

        return response()->json(['messages' => $messages]);
    }

    public function store(SendMessageRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = $request->user();

        $clientId = $data['client_id'] ?? null;
        if (! $clientId && $user->role === 'client') {
            $clientId = $user->client?->id;
        }
        if (! $clientId) {
            return response()->json(['message' => 'client_id is required.'], 422);
        }

        $client = Client::with(['user', 'physiotherapist'])->findOrFail($clientId);

        $this->authorizeConversation($user, $client);

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

        AppNotification::create([
            'user_id' => $message->receiver_id,
            'type' => 'message_received',
            'title' => 'New message',
            'body' => $user->name.': '.(str($message->body)->limit(60) ?: '[file]'),
            'data' => ['client_id' => $message->client_id],
        ]);

        return response()->json(['message' => $message], 201);
    }

    public function markRead(MarkChatReadRequest $request): JsonResponse
    {
        $user = $request->user();
        $clientId = $request->input('client_id');

        if (! $clientId && $user->role === 'client') {
            $clientId = $user->client?->id;
        }

        if (! $clientId) {
            return response()->json(['updated' => 0]);
        }

        $client = Client::with('physiotherapist')->findOrFail($clientId);
        $this->authorizeConversation($user, $client);

        $now = now();

        $lastUnread = Message::query()
            ->where('client_id', $clientId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->orderByDesc('id')
            ->first();

        $updated = Message::query()
            ->where('client_id', $clientId)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => $now]);

        if ($updated > 0) {
            event(new MessageRead(
                clientId: (int) $clientId,
                readerId: (int) $user->id,
                lastReadMessageId: $lastUnread?->id,
                readAt: $now->toIso8601String(),
            ));
        }

        return response()->json([
            'updated' => $updated,
            'last_read_message_id' => $lastUnread?->id,
            'read_at' => $now->toIso8601String(),
        ]);
    }

    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user->role === 'pt') {
            $pt = $user->physiotherapist;
            if (! $pt) {
                return response()->json(['counts' => [], 'total' => 0]);
            }

            $counts = Message::query()
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->whereIn('client_id', $pt->clients()->pluck('clients.id'))
                ->selectRaw('client_id, COUNT(*) as total')
                ->groupBy('client_id')
                ->pluck('total', 'client_id');

            return response()->json([
                'counts' => $counts,
                'total' => (int) $counts->sum(),
            ]);
        }

        $total = Message::query()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'counts' => (object) [],
            'total' => $total,
        ]);
    }

    private function authorizeConversation($user, Client $client): void
    {
        if ($user->role === 'pt') {
            $pt = $user->physiotherapist;
            abort_unless($pt && $client->physiotherapist_id === $pt->id, 403, 'Not your client.');

            return;
        }

        abort_unless($client->user_id === $user->id, 403, 'Not your conversation.');
    }
}
