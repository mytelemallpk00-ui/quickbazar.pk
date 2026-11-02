<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendChatMessageRequest;
use App\Models\Chat;
use App\Models\Message;
use App\Services\Chat\ChatbotResponder;
use App\Support\LiveChatSettings;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function send(SendChatMessageRequest $request, ChatbotResponder $bot): JsonResponse
    {
        if (! LiveChatSettings::isEnabled()) {
            return response()->json(['success' => false, 'message' => 'Live chat is unavailable.'], 503);
        }

        $data = $request->validated();
        $chat = Chat::findOrCreateForSession(
            $data['session_id'] ?? null,
            $data['customer_name'] ?? null,
            $data['customer_phone'] ?? null,
        );

        if ($chat->status === Chat::STATUS_CLOSED) {
            $chat->update(['status' => Chat::STATUS_ACTIVE]);
        }

        $customerText = trim((string) $data['message']);
        $clientMessageId = isset($data['client_message_id']) ? trim((string) $data['client_message_id']) : '';

        if ($clientMessageId !== '') {
            $cached = $this->findCachedExchange($chat, $clientMessageId, $customerText);
            if ($cached !== null) {
                return response()->json($cached);
            }
        } else {
            $duplicate = $this->findRecentDuplicateExchange($chat, $customerText);
            if ($duplicate !== null) {
                return response()->json($duplicate);
            }
        }

        $customerMessage = $chat->messages()->create([
            'message' => $customerText,
            'sender_type' => Message::SENDER_CUSTOMER,
        ]);

        $botResult = $bot->replyFor($customerText);

        if ($botResult['escalate']) {
            $chat->update(['status' => Chat::STATUS_PENDING_AGENT]);
        }

        $botMessage = $chat->messages()->create([
            'message' => $botResult['reply'],
            'sender_type' => Message::SENDER_BOT,
        ]);

        $payload = [
            'success' => true,
            'session_id' => $chat->session_id,
            'chat_status' => $chat->status,
            'client_message_id' => $clientMessageId !== '' ? $clientMessageId : null,
            'messages' => [
                $this->formatMessage($customerMessage),
                $this->formatMessage($botMessage),
            ],
            'checkout_url' => $botResult['checkout_url'],
            'track_url' => $botResult['track_url'],
            'products' => $botResult['products'] ?? [],
            'whatsapp_url' => $botResult['whatsapp_url'] ?? null,
        ];

        if ($clientMessageId !== '') {
            cache()->put($this->clientMessageCacheKey($chat->session_id, $clientMessageId), $payload, now()->addMinutes(10));
        }

        return response()->json($payload);
    }

    public function history(Request $request): JsonResponse
    {
        if (! LiveChatSettings::isEnabled()) {
            return response()->json(['success' => false, 'message' => 'Live chat is unavailable.'], 503);
        }

        $sessionId = trim((string) $request->query('session_id', ''));
        if ($sessionId === '') {
            return response()->json([
                'success' => true,
                'session_id' => null,
                'messages' => [],
            ]);
        }

        $chat = Chat::query()->where('session_id', $sessionId)->first();
        if (! $chat) {
            return response()->json([
                'success' => true,
                'session_id' => $sessionId,
                'messages' => [],
            ]);
        }

        $messages = $chat->messages()
            ->orderBy('id')
            ->limit(80)
            ->get()
            ->map(fn (Message $message) => $this->formatMessage($message))
            ->values()
            ->all();

        return response()->json([
            'success' => true,
            'session_id' => $chat->session_id,
            'chat_status' => $chat->status,
            'messages' => $messages,
        ]);
    }

    /** @return array<string, mixed>|null */
    protected function findRecentDuplicateExchange(Chat $chat, string $customerText): ?array
    {
        $recentCustomer = $chat->messages()
            ->where('sender_type', Message::SENDER_CUSTOMER)
            ->where('message', $customerText)
            ->where('created_at', '>=', now()->subSeconds(20))
            ->latest('id')
            ->first();

        if (! $recentCustomer) {
            return null;
        }

        $botMessage = $chat->messages()
            ->where('sender_type', Message::SENDER_BOT)
            ->where('id', '>', $recentCustomer->id)
            ->orderBy('id')
            ->first();

        if (! $botMessage) {
            return null;
        }

        return [
            'success' => true,
            'session_id' => $chat->session_id,
            'chat_status' => $chat->status,
            'messages' => [
                $this->formatMessage($recentCustomer),
                $this->formatMessage($botMessage),
            ],
            'checkout_url' => null,
            'track_url' => null,
            'products' => [],
            'duplicate' => true,
        ];
    }

    /** @return array<string, mixed>|null */
    protected function findCachedExchange(Chat $chat, string $clientMessageId, string $customerText): ?array
    {
        $key = $this->clientMessageCacheKey($chat->session_id, $clientMessageId);
        $cached = cache()->get($key);
        if (is_array($cached)) {
            $cached['duplicate'] = true;

            return $cached;
        }

        return $this->findRecentDuplicateExchange($chat, $customerText);
    }

    protected function clientMessageCacheKey(string $sessionId, string $clientMessageId): string
    {
        return 'chat:client_msg:'.hash('sha256', $sessionId.'|'.$clientMessageId);
    }

    /** @return array<string, mixed> */
    protected function formatMessage(Message $message): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'sender_type' => $message->sender_type,
            'created_at' => $message->created_at?->toIso8601String(),
        ];
    }
}
