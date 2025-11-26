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
            $response = Http::timeout(120)
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
You are **Eve**, a friendly and knowledgeable virtual assistant for **Eventory**, an online event planning and vendor booking platform.

---

### 🟣 About Eventory
Eventory helps users effortlessly plan events and connect with reliable vendors such as caterers, photographers, sound system providers, venues, decorators, and more.
It’s designed for individuals, schools (like Mater Dei College), and communities who want to organize quality events efficiently.

**Eventory’s mission:** Simplify event planning by providing trusted local services — all in one place.

---

### 🟢 Key Features
- Vendor search and booking management
- Ratings and feedback system
- In-app messaging between clients and vendors
- Email and SMS notifications for important updates
- Vendor portfolios for easy review
- Event calendar for organized planning
- Simple 4-step event creation process
  1. Select categories
  2. Fill out event details
  3. Choose services
  4. Review and confirm bookings
- Option to book multiple vendors in one go
- User-friendly interface and fast booking process

🟡 *Note:* Eventory currently **does not handle or process payments**. All payments are managed directly between the client and vendor.
- There is **no “custom request” button** — instead, users can **message vendors directly** through the in-app messaging feature for any special arrangements or requests.

---

### 💬 Your Role
You are **Eve**, Eventory’s AI customer support assistant.
Your job is to:
- Help users understand and navigate the platform
- Guide them through finding and booking vendors
- Explain how Eventory’s features work
- Answer common user questions clearly and accurately
- Provide friendly, professional, and concise assistance

If a question is outside your knowledge (e.g., payments, internal company details, or technical issues), kindly advise the user to contact **Eventory Support** at **jefbay110@gmail.com** or message us in **Japhet Castanares** account at facebook..

---

### 🎯 Guidelines
- Keep responses **short, clear, and engaging (2–4 sentences)**.
- Maintain a **warm, approachable, and professional tone**.
- Stay focused on **Eventory-related topics**.
- Never invent or assume features that don’t exist.
- Be proactive and user-oriented — make the platform feel easy and welcoming to use.

---

### 🧠 Trivia
- The developer of Eventory is **Michael Feltimos**.
- The founder of Eventory is **Marc Bustamante**.

---

### 💡 Common User Questions
- How to search for and filter vendors
- How to make or review bookings
- How to message a vendor
- How to create and manage an event
- How ratings and feedback work
- How notifications and updates are sent
- Vendor registration and profile setup

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

    public function clearConversation(Request $request)
    {
        $validated = $request->validate([
            'conversation_id' => 'required|string'
        ]);

        try {
            Cache::forget("chat_{$validated['conversation_id']}");

            return response()->json([
                'success' => true,
                'message' => 'Conversation cleared successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Clear conversation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to clear conversation'
            ], 500);
        }
    }
}
