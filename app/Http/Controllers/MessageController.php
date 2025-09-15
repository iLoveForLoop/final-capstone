<?php
namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $conversations = Conversation::whereJsonContains('participants', $user->id)
            ->with(['lastMessage.user', 'event'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayName($user->id),
                    'type' => $conversation->type,
                    'event' => $conversation->event,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'participants' => $conversation->users()->get(['id', 'name', 'email'])
                ];
            });

        return inertia('Vendor/Messages/Index', [
            'conversations' => $conversations
        ]);
    }

    public function show(Conversation $conversation)
    {
        $user = auth()->user();

        // Check if user is participant
        if (!$conversation->hasParticipant($user->id)) {
            abort(403, 'Unauthorized');
        }

        $messages = $conversation->messages()
            ->with(['user', 'reads'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'attachments' => $message->attachments,
                    'user' => $message->user,
                    'created_at' => $message->created_at,
                    'is_read' => $message->isReadBy($user->id),
                    'is_own' => $message->user_id === $user->id
                ];
            });

        // Mark messages as read
        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->get()
            ->each(function ($message) use ($user) {
                $message->markAsReadBy($user->id);
            });

        return Inertia::render('Messages/Show', [
            'conversation' => [
                'id' => $conversation->id,
                'title' => $conversation->title,
                'type' => $conversation->type,
                'event' => $conversation->event,
                'participants' => $conversation->users()->get(['id', 'name', 'email'])
            ],
            'messages' => $messages
        ]);
    }

    // API route to get messages for a conversation
    public function getMessages(Conversation $conversation)
    {
        $user = auth()->user();

        // Check if user is participant
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $messages = $conversation->messages()
            ->with(['user', 'reads'])
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($message) use ($user) {
                return [
                    'id' => $message->id,
                    'content' => $message->content,
                    'type' => $message->type,
                    'attachments' => $message->attachments,
                    'user' => [
                        'id' => $message->user->id,
                        'name' => $message->user->name,
                        'email' => $message->user->email
                    ],
                    'created_at' => $message->created_at,
                    'is_read' => $message->isReadBy($user->id),
                    'is_own' => $message->user_id === $user->id
                ];
            });

        // Mark messages as read
        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get()
            ->each(function ($message) use ($user) {
                $message->markAsReadBy($user->id);
            });

        return response()->json([
            'messages' => $messages
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $user = auth()->user();

        // Check if user is participant
        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
            'type' => 'in:text,file,system'
        ]);

        $message = $conversation->messages()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'attachments' => $request->attachments
        ]);

        // Update conversation last message time
        $conversation->update(['last_message_at' => now()]);

        // Load relationships for response
        $message->load('user');

        // Broadcast the message
        broadcast(new MessageSent($message))->toOthers();

        return response()->json([
            'message' => [
                'id' => $message->id,
                'content' => $message->content,
                'type' => $message->type,
                'attachments' => $message->attachments,
                'user' => [
                    'id' => $message->user->id,
                    'name' => $message->user->name,
                    'email' => $message->user->email
                ],
                'created_at' => $message->created_at,
                'is_own' => true,
                'is_read' => true
            ]
        ]);
    }

    public function createConversation(Request $request)
    {
        $request->validate([
            'participants' => 'required|array|min:1',
            'participants.*' => 'exists:users,id',
            'title' => 'nullable|string|max:255',
            'event_id' => 'nullable|exists:events,id',
            'type' => 'in:direct,support'
        ]);

        $user = auth()->user();
        $participants = array_unique(array_merge($request->participants, [$user->id]));

        // Check if conversation already exists for these participants
        $existingConversation = Conversation::where('type', $request->type ?? 'direct')
            ->where('event_id', $request->event_id)
            ->get()
            ->filter(function ($conversation) use ($participants) {
                return count($conversation->participants) === count($participants) &&
                       empty(array_diff($conversation->participants, $participants));
            })
            ->first();

        if ($existingConversation) {
            return redirect()->route('messages.show', $existingConversation);
        }

        $conversation = Conversation::create([
            'title' => $request->title,
            'type' => $request->type ?? 'direct',
            'event_id' => $request->event_id,
            'participants' => $participants,
            'last_message_at' => now()
        ]);

        return redirect()->route('messages.show', $conversation);
    }

    // API route to get conversations for AJAX calls
    public function getConversations()
    {
        $user = auth()->user();

        $conversations = Conversation::whereJsonContains('participants', $user->id)
            ->with(['lastMessage.user', 'event'])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conversation) use ($user) {
                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayName($user->id),
                    'type' => $conversation->type,
                    'event' => $conversation->event,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'participants' => $conversation->users()->get(['id', 'name', 'email'])
                ];
            });

        return response()->json([
            'conversations' => $conversations
        ]);
    }
}
