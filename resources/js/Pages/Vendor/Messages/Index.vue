<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { ref, computed, onMounted } from 'vue';
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

// Get data from Inertia page props
const page = usePage();
const conversations = ref(page.props.conversations || []);
const selectedConversation = ref(null);
const messageText = ref('');
const searchQuery = ref('');

// Default data for UI purposes (will be replaced by backend data)
const defaultConversations = [
    {
        id: 1,
        clientName: 'Sarah Johnson',
        clientAvatar: null,
        lastMessage: 'Thank you for the beautiful wedding setup! Everything was perfect.',
        timestamp: '2 min ago',
        unreadCount: 0,
        isOnline: true,
        eventType: 'Wedding',
        eventDate: '2024-03-15',
        isStarred: true
    },
    {
        id: 2,
        clientName: 'Michael Chen',
        clientAvatar: null,
        lastMessage: 'Can we discuss the catering options for the corporate event?',
        timestamp: '1 hour ago',
        unreadCount: 3,
        isOnline: false,
        eventType: 'Corporate Event',
        eventDate: '2024-04-20',
        isStarred: false
    },
    {
        id: 3,
        clientName: 'Emma Rodriguez',
        clientAvatar: null,
        lastMessage: 'I love the birthday party theme you suggested!',
        timestamp: '3 hours ago',
        unreadCount: 1,
        isOnline: true,
        eventType: 'Birthday Party',
        eventDate: '2024-03-28',
        isStarred: false
    },
    {
        id: 4,
        clientName: 'David Thompson',
        clientAvatar: null,
        lastMessage: 'What time should we arrive for the setup?',
        timestamp: 'Yesterday',
        unreadCount: 0,
        isOnline: false,
        eventType: 'Anniversary',
        eventDate: '2024-04-05',
        isStarred: true
    }
];

const defaultMessages = [
    {
        id: 1,
        senderId: 2,
        senderName: 'Michael Chen',
        content: 'Hi! I wanted to discuss the catering options for our upcoming corporate event.',
        timestamp: '10:30 AM',
        isRead: true,
        isOwn: false
    },
    {
        id: 2,
        senderId: 1,
        senderName: 'You',
        content: 'Hello Michael! I\'d be happy to help you with the catering options. What type of cuisine are you looking for?',
        timestamp: '10:35 AM',
        isRead: true,
        isOwn: true
    },
    {
        id: 3,
        senderId: 2,
        senderName: 'Michael Chen',
        content: 'We\'re thinking something international - maybe a mix of Asian and Mediterranean dishes. The event is for about 150 people.',
        timestamp: '10:40 AM',
        isRead: true,
        isOwn: false
    },
    {
        id: 4,
        senderId: 1,
        senderName: 'You',
        content: 'Perfect! I can arrange a beautiful spread with both Asian and Mediterranean options. Would you like me to send you our premium catering menu with pricing?',
        timestamp: '10:45 AM',
        isRead: true,
        isOwn: true
    },
    {
        id: 5,
        senderId: 2,
        senderName: 'Michael Chen',
        content: 'That would be great! Also, can we discuss the setup timeline?',
        timestamp: '11:20 AM',
        isRead: false,
        isOwn: false
    }
];

// Use provided data or fall back to defaults
if (conversations.value.length === 0) {
    conversations.value = defaultConversations;
}

const messages = ref(page.props.messages || defaultMessages);

const filteredConversations = computed(() => {
    if (!searchQuery.value) return conversations.value;
    return conversations.value.filter(conv =>
        conv.clientName.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
        conv.eventType.toLowerCase().includes(searchQuery.value.toLowerCase())
    );
});

const selectConversation = (conversation) => {
    selectedConversation.value = conversation;
    // In a real app, you would fetch messages for this conversation
};

const sendMessage = () => {
    if (!messageText.value.trim()) return;

    // In a real app, you would send this to the backend via Inertia
    messages.value.push({
        id: messages.value.length + 1,
        senderId: 1,
        senderName: 'You',
        content: messageText.value,
        timestamp: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        isRead: true,
        isOwn: true
    });

    messageText.value = '';
};

const getInitials = (name) => {
    return name.split(' ').map(n => n[0]).join('').toUpperCase();
};

// Set default selected conversation if none is selected
onMounted(() => {
    if (!selectedConversation.value && conversations.value.length > 0) {
        selectedConversation.value = conversations.value[1];
    }
});
</script>

<template>
    <VendorLayout title="Messages">
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
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-medium shadow-sm">
                                    {{ getInitials(conversation.clientName) }}
                                </div>
                                <div v-if="conversation.isOnline"
                                    class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 border-2 border-white rounded-full">
                                </div>
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-1">
                                    <div class="flex items-center space-x-2">
                                        <h3 class="text-sm font-semibold text-gray-900 truncate">
                                            {{ conversation.clientName }}
                                        </h3>
                                        <button class="opacity-0 group-hover:opacity-100 transition-opacity">
                                            <Star :class="[
                                                'h-4 w-4 transition-colors',
                                                conversation.isStarred ? 'text-yellow-400 fill-current' : 'text-gray-300'
                                            ]" />
                                        </button>
                                    </div>
                                    <span class="text-xs text-gray-500 whitespace-nowrap">{{ conversation.timestamp
                                        }}</span>
                                </div>

                                <div class="flex items-center space-x-2 mb-2">
                                    <div class="flex items-center text-xs text-gray-500">
                                        <Calendar class="h-3 w-3 mr-1" />
                                        <span>{{ conversation.eventType }}</span>
                                    </div>
                                    <span class="text-xs text-gray-400">•</span>
                                    <span class="text-xs text-gray-500">{{ conversation.eventDate }}</span>
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
                            <div
                                class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center text-white font-medium shadow-sm">
                                {{ getInitials(selectedConversation.clientName) }}
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-900">{{ selectedConversation.clientName }}</h3>
                                <div class="flex items-center space-x-4 text-sm text-gray-500 mt-1">
                                    <div class="flex items-center space-x-1">
                                        <Calendar class="h-3 w-3" />
                                        <span>{{ selectedConversation.eventType }}</span>
                                    </div>
                                    <div class="flex items-center space-x-1">
                                        <MapPin class="h-3 w-3" />
                                        <span>{{ selectedConversation.eventDate }}</span>
                                    </div>
                                    <div v-if="selectedConversation.isOnline"
                                        class="flex items-center space-x-1 text-green-500">
                                        <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        <span class="text-xs">Online</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-1">
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Phone class="h-5 w-5" />
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Video class="h-5 w-5" />
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <MoreVertical class="h-5 w-5" />
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Messages Area -->
                <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-gray-50">
                    <div v-for="message in messages" :key="message.id" :class="[
                        'flex',
                        message.isOwn ? 'justify-end' : 'justify-start'
                    ]">
                        <div :class="[
                            'max-w-xs lg:max-w-md px-4 py-3 rounded-2xl shadow-sm',
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
                                <div v-if="message.isOwn" class="ml-2">
                                    <CheckCheck v-if="message.isRead" class="h-3 w-3 text-blue-200" />
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
                                <Paperclip class="h-5 w-5" />
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <Image class="h-5 w-5" />
                            </button>
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                <FileText class="h-5 w-5" />
                            </button>
                        </div>

                        <div class="flex-1 relative">
                            <textarea v-model="messageText" @keydown.enter.exact.prevent="sendMessage"
                                placeholder="Type your message..." rows="1"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-transparent max-h-32 transition-colors"></textarea>
                        </div>

                        <div class="flex">
                            <button
                                class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors mr-1">
                                <Mic class="h-5 w-5" />
                            </button>
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
                    <p class="text-gray-500">Choose a conversation from the sidebar to start messaging with your clients
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
