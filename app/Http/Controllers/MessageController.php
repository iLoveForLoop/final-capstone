<?php
namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Events\MessageSent;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MessageController extends Controller
{

    public function index(Request $request)
    {
        $user = auth()->user();

        $conversationId = null;

        // ✅ Check if the request contains data to create/find a conversation
        if ($request->has('participants')) {
            $request->validate([
                'participants' => 'required|array|min:1',
                'participants.*' => 'exists:users,id',
                'title' => 'nullable|string|max:255',
                'event_id' => 'nullable|exists:events,id',
                'type' => 'in:direct,support'
            ]);

            $participants = array_map(
                'intval',
                array_unique(array_merge($request->participants, [$user->id]))
            );

            // Try to find existing conversation with exact same participants, type, and event
            $existingConversation = Conversation::where('type', $request->type ?? 'direct')
                ->where('event_id', $request->event_id)
                ->get()
                ->filter(function ($conversation) use ($participants) {
                    return count($conversation->participants) === count($participants)
                        && empty(array_diff($conversation->participants, $participants));
                })
                ->first();

            if ($existingConversation) {
                $conversationId = $existingConversation->id;
            } else {
                // Create new conversation
                $conversation = Conversation::create([
                    'title' => $request->title,
                    'type' => $request->type ?? 'direct',
                    'event_id' => $request->event_id,
                    'participants' => $participants,
                    'last_message_at' => now()
                ]);

                $conversationId = $conversation->id;
            }
        }

        // ✅ Load all conversations for sidebar
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

        // ✅ Pass the selected conversation id if one was created or found
        return inertia('Vendor/Messages/Index', [
            'conversations' => $conversations,
            'conversationId' => $conversationId
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
        $participants = array_map(
            'intval',
            array_unique(array_merge($request->participants, [$user->id]))
        );

        // Check if conversation already exists for these participants
        $existingConversation = Conversation::where('type', $request->type ?? 'direct')
            ->where('event_id', $request->event_id)
            ->get()
            ->filter(function ($conversation) use ($participants) {
                return count($conversation->participants) === count($participants) &&
                       empty(array_diff($conversation->participants, $participants));
            })
            ->first();

        $existingOtherUser = $existingConversation->users()
                ->where('id', '!=', $user->id)
                ->first();

        if ($existingConversation) {
            return response()->json([
                'id' => $existingConversation->id,
                'title' => $existingConversation->getDisplayName($user->id),
                'type' => $existingConversation->type,
                'event' => $existingConversation->event,
                'last_message' => $existingConversation->lastMessage,
                'unread_count' => $existingConversation->getUnreadCountForUser($user->id),
                'participants' => $existingConversation->users()->get(['id', 'name', 'email']),
                'otherUserAvatar' => $existingOtherUser?->getFirstMediaUrl('avatar'),
                'already_exists' => true
            ]);
        }

        $conversation = Conversation::create([
            'title' => $request->title,
            'type' => $request->type ?? 'direct',
            'event_id' => $request->event_id,
            'participants' => $participants,
            'last_message_at' => now()
        ]);

        $newOtherUser = $conversation->users()
                ->where('id', '!=', $user->id)
                ->first();

        return response()->json([
            'id' => $conversation->id,
            'title' => $conversation->getDisplayName($user->id),
            'type' => $conversation->type,
            'event' => $conversation->event,
            'last_message' => $conversation->lastMessage,
            'unread_count' => $conversation->getUnreadCountForUser($user->id),
            'participants' => $conversation->users()->get(['id', 'name', 'email']),
            'otherUserAvatar' => $newOtherUser?->getFirstMediaUrl('avatar'),
            'already_exists' => false
        ]);
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

                $otherUser = $conversation->users()
                ->where('id', '!=', $user->id)
                ->first();

                return [
                    'id' => $conversation->id,
                    'title' => $conversation->getDisplayName($user->id),
                    'type' => $conversation->type,
                    'event' => $conversation->event,
                    'last_message' => $conversation->lastMessage,
                    'unread_count' => $conversation->getUnreadCountForUser($user->id),
                    'participants' => $conversation->users()->get(['id', 'name', 'email']),
                    'otherUserAvatar' => $otherUser?->getFirstMediaUrl('avatar')
                ];
            });

        return response()->json([
            'conversations' => $conversations
        ]);
    }

    // ConversationController.php
    public function markAsRead(Conversation $conversation)
    {
        $user = auth()->user();

        if (!$conversation->hasParticipant($user->id)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $conversation->messages()
            ->where('user_id', '!=', $user->id)
            ->whereDoesntHave('reads', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->get()
            ->each(function ($message) use ($user) {
                $message->markAsReadBy($user->id);
            });

        return response()->json(['success' => true]);
    }

    public function getUnreadMessagesCount()
    {
        $user = auth()->user();

        // Get conversations where user is a participant
        $userConversations = Conversation::whereJsonContains('participants', $user->id)
            ->pluck('id');

        $unreadCount = Message::whereIn('conversation_id', $userConversations)
            ->where('user_id', '!=', $user->id) // Messages from other users
            ->whereNull('read_at')
            ->count();

        return response()->json([
            'unread_count' => $unreadCount
        ]);
    }

}