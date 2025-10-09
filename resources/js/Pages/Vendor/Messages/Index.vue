<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue';
import {
    Search,
    Send,
    Paperclip,
    MoreVertical,
    Phone,
    Video,
    Star,
    Clock,
    CheckCheck,
    Check,
    User,
    Calendar,
    MapPin,
    Image,
    FileText,
    Mic
} from 'lucide-vue-next';
import { usePage } from '@inertiajs/vue3';
import axios from 'axios';


// Conversations state (no longer from Inertia props)
const conversations = ref([]);
const selectedConversation = ref(null);
const messages = ref([]);
const messageText = ref('');
const searchQuery = ref('');
const isLoading = ref(false);
const isTyping = ref(false);
const echoChannel = ref(null);
const messagesContainer = ref(null);

// Current user (still from Inertia auth props)
const page = usePage();
const user = computed(() => page.props.auth?.user);

// Fetch conversations from API
const fetchConversations = async () => {
    try {
        const response = await axios.get('/conversations');
        conversations.value = response.data.conversations;
    } catch (error) {
        console.error('Failed to fetch conversations:', error);
    }
};

// Process conversations data to match frontend format
const processedConversations = computed(() => {
    return conversations.value.map(conv => {
        const otherParticipant = conv.participants?.find(p => p.id !== user.value?.id);
        const clientName = conv.title || otherParticipant?.name || 'Unknown';

        console.log('participants: ', conv);

        return {
            id: conv.id,
            clientName,
            clientAvatar: conv.otherUserAvatar, // You can add avatar logic here
            lastMessage: conv.last_message?.content || 'No messages yet',
            timestamp: formatTimestamp(conv.last_message?.created_at),
            unreadCount: conv.unread_count || 0,
            isOnline: false,
            eventType: conv.event?.title || conv.type || 'General',
            eventDate: conv.event?.date,
            isStarred: false,
            originalData: conv
        };
    });
});




const filteredConversations = computed(() => {
    if (!searchQuery.value) return processedConversations.value;
    return processedConversations.value.filter(conv =>
        conv.clientName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        conv.eventType.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectConversation = async (conversation) => {

    // console.log('Clicked');


    if (selectedConversation.value?.id === conversation.id) return;

    selectedConversation.value = conversation;
    isLoading.value = true;

    try {
        const response = await axios.get(`/conversations/${conversation.id}/messages`);

        // Sort messages by creation time (oldest first, newest last)
        const sortedMessages = response.data.messages.sort((a, b) =>
            new Date(a.created_at) - new Date(b.created_at)
        );

        messages.value = sortedMessages.map(msg => ({
            id: msg.id,
            senderId: msg.user.id,
            senderName: msg.user.name,
            content: msg.content,
            timestamp: formatTime(msg.created_at),
            isRead: msg.is_read,
            isOwn: msg.is_own,
            attachments: msg.attachments,
            type: msg.type
        }));

        subscribeToConversation(conversation.id);

        nextTick(() => {
            setTimeout(() => {
                scrollToBottom();
            }, 50); // Small delay to ensure DOM is fully rendered
        });
    } catch (error) {
        console.error('Failed to load messages:', error);
    } finally {
        isLoading.value = false;
    }
};

const subscribeToConversation = (conversationId) => {
    if (echoChannel.value) {
        window.Echo.leave(`conversation.${echoChannel.value}`);
    }

    echoChannel.value = conversationId;
    window.Echo.private(`conversation.${conversationId}`)
        .listen('.MessageSent', (e) => {
            console.log('listening');

            messages.value.push({
                id: e.message.id,
                senderId: e.message.user.id,
                senderName: e.message.user.name,
                content: e.message.content,
                timestamp: formatTime(e.message.created_at),
                isRead: e.message.is_read,
                isOwn: e.message.is_own,
                attachments: e.message.attachments,
                type: e.message.type
            });

            updateConversationLastMessage(conversationId, e.message);
            scrollToBottom();
        })
        .listenForWhisper('typing', (e) => {
            if (e.user.id !== user.value.id) {
                isTyping.value = true;
                setTimeout(() => isTyping.value = false, 3000);
            }
        });
};

const updateConversationLastMessage = (conversationId, message) => {
    const convIndex = conversations.value.findIndex(c => c.id === conversationId);
    if (convIndex !== -1) {
        conversations.value[convIndex].last_message = message;
        conversations.value[convIndex].last_message_at = message.created_at;
    }
};

const sendMessage = async () => {
    if (!messageText.value.trim() || !selectedConversation.value) return;

    const messageContent = messageText.value.trim();
    const tempId = Date.now();

    const optimisticMessage = {
        id: tempId,
        senderId: user.value.id,
        senderName: user.value.name,
        content: messageContent,
        timestamp: formatTime(new Date()),
        isRead: true,
        isOwn: true,
        sending: true
    };

    messages.value.push(optimisticMessage);
    messageText.value = '';

    scrollToBottom();

    try {
        const response = await axios.post(`/conversations/${selectedConversation.value.id}/messages`, {
            content: messageContent,
            type: 'text'
        });

        const messageIndex = messages.value.findIndex(m => m.id === tempId);
        if (messageIndex !== -1) {
            messages.value[messageIndex] = {
                id: response.data.message.id,
                senderId: response.data.message.user.id,
                senderName: response.data.message.user.name,
                content: response.data.message.content,
                timestamp: formatTime(response.data.message.created_at),
                isRead: true,
                isOwn: true
            };
        }

    } catch (error) {
        console.error('Failed to send message:', error);
        const messageIndex = messages.value.findIndex(m => m.id === tempId);
        if (messageIndex !== -1) {
            messages.value.splice(messageIndex, 1);
        }
    }
};

const handleTyping = () => {
    if (selectedConversation.value && echoChannel.value) {
        window.Echo.private(`conversation.${selectedConversation.value.id}`)
            .whisper('typing', { user: user.value });
    }
};

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

const formatTimestamp = (timestamp) => {
    if (!timestamp) return '';
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;

    if (diff < 60000) return 'Just now';
    if (diff < 3600000) return `${Math.floor(diff / 60000)} min ago`;
    if (diff < 86400000) return `${Math.floor(diff / 3600000)} hour ago`;
    if (diff < 604800000) return `${Math.floor(diff / 86400000)} day ago`;
    return date.toLocaleDateString();
};

const formatTime = (timestamp) => {
    if (!timestamp) return '';
    return new Date(timestamp).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
    });
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};

// Initialize

onMounted(async () => {
    await fetchConversations();

    // ✅ Check if backend passed a conversationId prop
    const conversationId = page.props.conversationId;

    if (conversationId) {
        const conversation = processedConversations.value.find(c => c.id === conversationId);
        if (conversation) {
            await selectConversation(conversation);
        } else {
            // If conversation not yet in processedConversations (rare race condition),
            // wait a tick and try again
            nextTick(() => {
                const retryConv = processedConversations.value.find(c => c.id === conversationId);
                if (retryConv) selectConversation(retryConv);
            });
        }
    }
});



// Cleanup
onUnmounted(() => {
    if (echoChannel.value) {
        window.Echo.leave(`conversation.${echoChannel.value}`);
    }

});
</script>


<template>
    <VendorLayout>
        <div class="flex h-[calc(100vh-4rem)] bg-gray-50">
            <!-- Conversations Sidebar -->
            <div class="w-80 bg-white border-r border-gray-200 flex flex-col shadow-sm">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900 mb-4">Messages</h2>

                    <!-- Search Bar -->
                    <div class="relative">
                        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 h-4 w-4" />
                        <input v-model="searchQuery" type="text" placeholder="Search conversations..."
                            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors" />
                    </div>
                </div>

                <!-- Conversations List -->
                <div class="flex-1 overflow-y-auto">
                    <div v-for="conversation in filteredConversations" :key="conversation.id"
                        @click="selectConversation(conversation)" :class="[
                            'p-4 border-b border-gray-100 cursor-pointer transition-colors group',
                            selectedConversation?.id === conversation.id
                                ? 'bg-blue-50 border-blue-200'
                                : 'hover:bg-gray-50'
                        ]">
                        <div class="flex items-start space-x-3">
                            <!-- Avatar -->
                            <div class="relative flex-shrink-0">
                                <!-- Avatar -->
                                <div
                                    class="w-12 h-12 rounded-full overflow-hidden shadow-md border-2 border-white bg-gray-200 flex items-center justify-center text-white font-semibold text-lg">
                                    <img v-if="conversation.clientAvatar" :src="conversation.clientAvatar"
                                        alt="User avatar" class="w-full h-full object-cover" />
                                    <span v-else
                                        class="bg-gradient-to-br from-blue-400 to-blue-600 w-full h-full flex items-center justify-center">
                                        {{ getInitials(conversation.clientName) }}
                                    </span>
                                </div>

                                <!-- Online Indicator -->
                                <div v-if="conversation.isOnline"
                                    class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full">
                                </div>
                            </div>


                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="text-sm font-semibold text-gray-900 truncate">
                                        {{ conversation.clientName }}
                                    </h3>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">
                                        {{ conversation.timestamp }}
                                    </span>
                                </div>

                                <div class="flex items-center justify-between">
                                    <p class="text-sm text-gray-600 truncate">{{ conversation.lastMessage }}</p>
                                    <div v-if="conversation.unreadCount > 0"
                                        class="bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center ml-2 flex-shrink-0">
                                        {{ conversation.unreadCount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="flex-1 flex flex-col" v-if="selectedConversation">

                <!-- Chat Header -->
                <div class="bg-white border-b border-gray-200 px-6 py-4 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">

                            <!-- <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-medium shadow-sm">
                                {{ getInitials(selectedConversation.clientName) }}
                            </div> -->
                            <div
                                class="w-12 h-12 rounded-full overflow-hidden shadow-md border-2 border-white bg-gray-200 flex items-center justify-center text-white font-semibold text-lg">
                                {{ console.log('Avatar: ', selectedConversation.clientAvatar) }}
                                <img v-if="selectedConversation.clientAvatar" :src="selectedConversation.clientAvatar"
                                    alt="User avatar" class="w-full h-full object-cover" />
                                <span v-else
                                    class="bg-gradient-to-br from-blue-400 to-blue-600 w-full h-full flex items-center justify-center">
                                    {{ getInitials(selectedConversation.clientName) }}
                                </span>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ selectedConversation.clientName }}</h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                    <div v-if="selectedConversation.isOnline"
                                        class="flex items-center space-x-1 text-green-500">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-xs">Online</span>
                                    </div>
                                    <div v-if="isTyping" class="text-blue-500 text-xs">
                                        Typing...
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-1">
                            <!-- <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Phone class="h-5 w-5" />
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Video class="h-5 w-5" />
                            </button> -->
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <MoreVertical class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div ref="messagesContainer" class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50 messages-container">
                    <!-- Loading state -->
                    <div v-if="isLoading" class="flex justify-center items-center h-full">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
                    </div>

                    <!-- Messages -->
                    <div v-else v-for="message in messages" :key="message.id" :class="[
                        'flex',
                        message.isOwn ? 'justify-end' : 'justify-start'
                    ]">
                        <div :class="[
                            'max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm relative',
                            message.isOwn
                                ? 'bg-blue-500 text-white rounded-br-md'
                                : 'bg-white border border-gray-200 text-gray-900 rounded-bl-md'
                        ]">
                            <p class="text-sm">{{ message.content }}</p>
                            <div class="flex items-center justify-between mt-2">
                                <span :class="[
                                    'text-xs',
                                    message.isOwn ? 'text-blue-100' : 'text-gray-500'
                                ]">
                                    {{ message.timestamp }}
                                </span>
                                <div v-if="message.isOwn" class="ml-2 flex items-center space-x-1">
                                    <div v-if="message.sending"
                                        class="w-3 h-3 border border-blue-200 border-t-transparent rounded-full animate-spin">
                                    </div>
                                    <CheckCheck v-else-if="message.isRead" class="h-3 w-3 text-blue-200" />
                                    <Check v-else class="h-3 w-3 text-blue-200" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Message Input -->
                <div class="bg-white border-t border-gray-200 p-4 shadow-md">
                    <div class="flex items-end space-x-2">
                        <div class="flex">
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Image class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="flex-1 relative">
                            <textarea v-model="messageText" @keydown.enter.exact.prevent="sendMessage"
                                @input="handleTyping" placeholder="Type your message..." rows="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent max-h-32 transition-colors"></textarea>
                        </div>

                        <div class="flex">
                            <button @click="sendMessage" :disabled="!messageText.trim()" :class="[
                                'p-3 rounded-xl transition-colors shadow-sm',
                                messageText.trim()
                                    ? 'bg-blue-500 text-white hover:bg-blue-600'
                                    : 'bg-gray-100 text-gray-400 cursor-not-allowed'
                            ]">
                                <Send class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center max-w-md mx-4">
                    <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
                        <User class="h-8 w-8 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">No conversation selected</h3>
                    <p class="text-gray-500">
                        Choose a conversation from the sidebar to start messaging with your clients
                    </p>
                </div>
            </div>
        </div>
    </VendorLayout>
</template>

<style>
/* Custom scrollbar styling */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Auto-resize textarea */
textarea {
    field-sizing: content;
    min-height: 44px;
}
</style>
