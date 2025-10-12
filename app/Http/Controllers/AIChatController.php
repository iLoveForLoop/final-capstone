<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIChatController extends Controller
{
    private const MAX_CONVERSATION_LENGTH = 10;

    public function sendMessage(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
            'conversation_id' => 'nullable|string'
        ]);



        try {
            // Get or create conversation history
            $conversationId = $validated['conversation_id'] ?? uniqid('chat_', true);
            $messages = $this->getConversationHistory($conversationId);

            // Add user message
            $messages[] = [
                'role' => 'user',
                'content' => $validated['message']
            ];

            // Call OpenRouter API
            $response = Http::timeout(60)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('openrouter.openrouter_api_key'),
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => 'Eventory'
                ])
                ->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => 'openai/gpt-oss-20b:free',
                    'messages' => array_merge(
                        [$this->getSystemPrompt()],
                        $messages
                    ),
                    'temperature' => 0.7,
                    'max_tokens' => 500,
                ]);

            if ($response->failed()) {
                throw new \Exception('API request failed: ' . $response->status());
            }

            $result = $response->json();
            $assistantMessage = $result['choices'][0]['message']['content'] ?? '';

            if (empty($assistantMessage)) {
                throw new \Exception('Empty response from API');
            }

            // Store conversation history
            $messages[] = [
                'role' => 'assistant',
                'content' => $assistantMessage
            ];
            $this->saveConversationHistory($conversationId, $messages);

            return response()->json([
                'success' => true,
                'message' => $assistantMessage,
                'conversation_id' => $conversationId
            ]);

        } catch (\Exception $e) {
            Log::error('Chatbot error: ' . $e->getMessage(), [
                'conversation_id' => $conversationId ?? null,
                'message' => $validated['message'] ?? null
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, I encountered an error. Please try again.'
            ], 500);
        }
    }

    private function getSystemPrompt(): array
    {
        return [
            'role' => 'system',
            'content' => <<<EOT
You are Eve, a helpful customer support assistant for Eventory, an event planning and vendor booking platform.

**About Eventory:**
- Event planning and vendor booking system
- Connects event organizers with vendors (caterers, photographers, venues, decorators, etc.)
- Features: vendor search, booking management, reviews, calendar integration, bundled deals

**Your Role:**
- Help users understand how to use Eventory
- Guide them through finding and booking vendors
- Explain features and functionality
- Answer questions about the platform
- Be friendly, concise, and helpful

**Guidelines:**
- Keep responses short and clear (2-4 sentences)
- If you don't know something, admit it and suggest contacting support
- Stay focused on Eventory-related topics
- Use a warm, professional tone
- Never make up features or capabilities

**Some Trivia About Eventory:**
- The developer of Eventory is Michael Feltimos
- The founder of Eventory is Marc Bustamante

**Common Topics:**
- How to search for vendors
- How to make bookings
- Account management
- Payment and pricing
- Vendor registration
EOT
        ];
    }

    private function getConversationHistory(string $conversationId): array
    {
        return Cache::get("chat_{$conversationId}", []);
    }

    private function saveConversationHistory(string $conversationId, array $messages): void
    {
        // Keep only last N messages to avoid token limits
        $messages = array_slice($messages, -self::MAX_CONVERSATION_LENGTH);

        // Store for 2 hours
        Cache::put("chat_{$conversationId}", $messages, 7200);
    }
}
