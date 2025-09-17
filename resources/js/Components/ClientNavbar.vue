<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';
import MyDropdown from './MyDropdown.vue';

// Import Lucide icons
import {
    Bell, MessageSquare, Menu, X, Home, Calendar, Heart,
    LogOut, Settings, User, Search, ChevronDown, CheckCircle, Send
} from 'lucide-vue-next';

const page = usePage()
const isDropdownShowing = ref(false)
const isMobileMenuOpen = ref(false)
const isNotificationsOpen = ref(false)
const isMessagesOpen = ref(false)
const openChats = ref([])

// Backend data
const conversations = ref([])
const notifications = ref([])
const isLoading = ref(false)

// Computed for processed conversations
const processedMessages = computed(() => {
    return conversations.value.map(conv => {
        return {
            id: conv.id,
            sender: conv.title,
            avatar: getInitials(conv.title),
            message: conv.last_message?.content || 'No messages yet',
            time: formatTimestamp(conv.last_message?.created_at),
            read: conv.unread_count === 0,
            online: false, // You can implement online status later
            chatMessages: [] // Will be loaded when chat is opened
        }
    })
})

// Sample notifications - you can replace this with backend data
const sampleNotifications = ref([
    {
        id: 1,
        type: 'booking',
        title: 'Booking Confirmed',
        message: 'Your event booking for Wedding Photography has been confirmed.',
        time: '2 minutes ago',
        read: false,
        icon: 'calendar'
    },
    {
        id: 2,
        type: 'event',
        title: 'Event Reminder',
        message: 'Your event "Anniversary Celebration" is tomorrow at 3:00 PM.',
        time: '1 hour ago',
        read: false,
        icon: 'bell'
    },
    {
        id: 3,
        type: 'favorite',
        title: 'New Service Available',
        message: 'A photographer you favorited has added new packages.',
        time: '3 hours ago',
        read: true,
        icon: 'heart'
    }
])

// Use sample notifications for now
notifications.value = sampleNotifications.value

// Count unread items
const unreadNotifications = computed(() => notifications.value.filter(n => !n.read).length)
const unreadMessages = computed(() => conversations.value.reduce((total, conv) => total + (conv.unread_count || 0), 0))

// Load conversations from backend
const loadConversations = async () => {
    if (!page.props.auth.user) return

    try {
        isLoading.value = true
        const response = await axios.get('/conversations')
        conversations.value = response.data.conversations
    } catch (error) {
        console.error('Failed to load conversations:', error)
    } finally {
        isLoading.value = false
    }
}

// Load messages for a specific conversation
// Load messages for a specific conversation
const loadChatMessages = async (conversationId) => {
    console.log('click a message');

    try {
        const response = await axios.get(`/conversations/${conversationId}/messages`)

        // Sort messages by creation time (oldest first, newest last)
        const sortedMessages = response.data.messages.sort((a, b) =>
            new Date(a.created_at) - new Date(b.created_at)
        );

        return sortedMessages.map(msg => ({
            id: msg.id,
            text: msg.content,
            sent: msg.is_own,
            time: formatTime(msg.created_at)
        }))
    } catch (error) {
        console.error('Failed to load messages:', error)
        return []
    }
}

// Chat window functions
const openChatWindow = async (message) => {
    // Mark conversation as read (update unread count)
    const conversation = conversations.value.find(c => c.id === message.id)
    if (conversation) {
        conversation.unread_count = 0
    }

    // Close messages dropdown
    isMessagesOpen.value = false

    // Check if chat is already open
    const existingChat = openChats.value.find(chat => chat.id === message.id)
    if (existingChat) {
        // If minimized, expand it
        existingChat.minimized = false
        return
    }

    // Load chat messages from backend
    const chatMessages = await loadChatMessages(message.id)

    // Add new chat window (max 3 windows)
    if (openChats.value.length >= 3) {
        openChats.value.shift() // Remove oldest chat
    }

    openChats.value.push({
        ...message,
        minimized: false,
        newMessage: '',
        chatMessages: chatMessages,
        conversationId: message.id
    })

    // Scroll to bottom after adding
    nextTick(() => {
        scrollChatToBottom()
    })

    // Subscribe to real-time updates for this conversation
    subscribeToConversation(message.id)
}

// Subscribe to Echo for real-time updates
const subscribeToConversation = (conversationId) => {
    if (!window.Echo) return


    window.Echo.private(`conversation.${conversationId}`)
        .listen('.MessageSent', (e) => {
            // Find the open chat and add the new message
            const chat = openChats.value.find(c => c.conversationId === conversationId)
            if (chat) {
                chat.chatMessages.push({
                    id: e.message.id,
                    text: e.message.content,
                    sent: e.message.is_own,
                    time: formatTime(e.message.created_at)
                })

                // Scroll to bottom
                nextTick(() => scrollChatToBottom())
            }

            // Update the conversation in the list
            const conversation = conversations.value.find(c => c.id === conversationId)
            if (conversation && !e.message.is_own) {
                conversation.last_message = e.message
                conversation.unread_count = (conversation.unread_count || 0) + 1
            }
        })
}

const closeChatWindow = (chatId) => {
    const index = openChats.value.findIndex(chat => chat.id === chatId)
    if (index !== -1) {
        // Leave the Echo channel
        const chat = openChats.value[index]
        if (window.Echo && chat.conversationId) {
            window.Echo.leave(`conversation.${chat.conversationId}`)
        }
        openChats.value.splice(index, 1)
    }
}

const toggleChatWindow = (chatId) => {
    const chat = openChats.value.find(chat => chat.id === chatId)
    if (chat) {
        chat.minimized = !chat.minimized
        if (!chat.minimized) {
            nextTick(() => {
                scrollChatToBottom()
            })
        }
    }
}

const sendChatMessage = async (chatId) => {
    const chat = openChats.value.find(chat => chat.id === chatId)
    if (!chat || !chat.newMessage?.trim() || !chat.conversationId) return

    const messageContent = chat.newMessage.trim()
    const tempId = Date.now()

    // Add optimistic message
    const optimisticMessage = {
        id: tempId,
        text: messageContent,
        sent: true,
        time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
        sending: true
    }

    chat.chatMessages.push(optimisticMessage)
    chat.newMessage = ''

    // Scroll to bottom
    nextTick(() => scrollChatToBottom())

    try {
        // Send message to backend
        const response = await axios.post(`/conversations/${chat.conversationId}/messages`, {
            content: messageContent,
            type: 'text'
        })

        // Replace optimistic message with real one
        const messageIndex = chat.chatMessages.findIndex(m => m.id === tempId)
        if (messageIndex !== -1) {
            chat.chatMessages[messageIndex] = {
                id: response.data.message.id,
                text: response.data.message.content,
                sent: true,
                time: formatTime(response.data.message.created_at)
            }
        }

        // Update conversation in list
        const conversation = conversations.value.find(c => c.id === chat.conversationId)
        if (conversation) {
            conversation.last_message = response.data.message
        }

    } catch (error) {
        console.error('Failed to send message:', error)
        // Remove optimistic message on error
        const messageIndex = chat.chatMessages.findIndex(m => m.id === tempId)
        if (messageIndex !== -1) {
            chat.chatMessages.splice(messageIndex, 1)
        }
    }
}

const scrollChatToBottom = () => {
    const chatElements = document.querySelectorAll('.chat-body .h-64')
    chatElements.forEach(element => {
        element.scrollTop = element.scrollHeight
    })
}

// Close drawers when clicking outside
const closeDrawers = (event) => {
    if (!event.target.closest('.drawer-container')) {
        isNotificationsOpen.value = false
        isMessagesOpen.value = false
    }
}

// Utility functions
const getInitials = (name) => {
    if (!name) return 'U'
    return name.split(' ').map(n => n[0]).join('').substring(0, 2).toUpperCase()
}

const formatTimestamp = (timestamp) => {
    if (!timestamp) return ''
    const date = new Date(timestamp)
    const now = new Date()
    const diff = now - date

    if (diff < 60000) return 'Just now'
    if (diff < 3600000) return `${Math.floor(diff / 60000)} min ago`
    if (diff < 86400000) return `${Math.floor(diff / 3600000)} hour ago`
    if (diff < 604800000) return `${Math.floor(diff / 86400000)} day ago`
    return date.toLocaleDateString()
}

const formatTime = (timestamp) => {
    if (!timestamp) return ''
    return new Date(timestamp).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit'
    })
}

// Mark notification as read
const markNotificationRead = (id) => {
    const notification = notifications.value.find(n => n.id === id)
    if (notification) notification.read = true
}

// Get icon component by name
const getIconComponent = (iconName) => {
    const icons = {
        calendar: Calendar,
        bell: Bell,
        heart: Heart
    };
    return icons[iconName] || Bell;
};

// Lifecycle hooks
onMounted(() => {
    document.addEventListener('click', closeDrawers)
    loadConversations()



    // Refresh conversations periodically (optional)
    const interval = setInterval(loadConversations, 30000) // Every 30 seconds

    // Cleanup interval on unmount
    onUnmounted(() => {
        clearInterval(interval)
    })
})

onUnmounted(() => {
    document.removeEventListener('click', closeDrawers)

    // Leave all Echo channels
    openChats.value.forEach(chat => {
        if (window.Echo && chat.conversationId) {
            window.Echo.leave(`conversation.${chat.conversationId}`)
        }
    })
})
</script>

<template>
    <div>
        <!-- Navbar -->
        <nav class="sticky top-0 z-30 bg-white/95 backdrop-blur-md border-b border-gray-100 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo -->
                    <Link href="/" class="flex items-center space-x-2 group flex-shrink-0">
                    <div
                        class="w-9 h-9 bg-gradient-to-br from-purple-600 to-indigo-600 rounded-lg flex items-center justify-center shadow-md">
                        <span class="text-white font-bold text-sm">E</span>
                    </div>
                    <span
                        class="text-xl font-bold text-gray-900 group-hover:text-purple-700 transition-colors duration-300">
                        Eventory
                    </span>
                    </Link>

                    <!-- Desktop Navigation -->
                    <div class="hidden lg:flex items-center space-x-1">
                        <!-- Navigation Links -->
                        <Link href="/client"
                            class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                            :class="{ 'text-gray-900 bg-gray-50': page.url === '/client' }">
                        <Home :size="16" class="mr-1.5" />
                        <span>Home</span>
                        </Link>

                        <Link href="/client/services"
                            class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                            :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/services') }">
                        <Search :size="16" class="mr-1.5" />
                        <span>Services</span>
                        </Link>

                        <!-- Conditional Links -->
                        <template v-if="page.props.auth.user">
                            <Link href="/client/bookings"
                                class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/bookings') }">
                            <Calendar :size="16" class="mr-1.5" />
                            <span>Bookings</span>
                            </Link>
                            <Link href="/client/events"
                                class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/events') }">
                            <Calendar :size="16" class="mr-1.5" />
                            <span>Events</span>
                            </Link>
                            <Link href="/client/favorites"
                                class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/favorites') }">
                            <Heart :size="16" class="mr-1.5" />
                            <span>Favorites</span>
                            </Link>
                        </template>
                    </div>

                    <!-- Right Side Actions -->
                    <div class="hidden lg:flex items-center space-x-2">
                        <template v-if="page.props.auth.user">
                            <!-- Notifications -->
                            <div class="drawer-container relative">
                                <button @click="isNotificationsOpen = !isNotificationsOpen"
                                    class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                    <Bell :size="20" />
                                    <span v-if="unreadNotifications > 0"
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                        {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
                                    </span>
                                </button>

                                <!-- Notifications Drawer -->
                                <div v-if="isNotificationsOpen"
                                    class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900">Notifications</h3>
                                            <button v-if="unreadNotifications > 0"
                                                class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                                                Mark all read
                                            </button>
                                        </div>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <div v-for="notification in notifications" :key="notification.id"
                                            @click="markNotificationRead(notification.id)"
                                            class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-150"
                                            :class="{ 'bg-purple-50/50': !notification.read }">
                                            <div class="flex items-start space-x-3">
                                                <component :is="getIconComponent(notification.icon)" :size="18"
                                                    class="mt-0.5 text-purple-600 flex-shrink-0" />
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-gray-900 truncate">{{
                                                            notification.title }}</p>
                                                        <span v-if="!notification.read"
                                                            class="w-2 h-2 bg-purple-600 rounded-full flex-shrink-0"></span>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1">{{ notification.message }}</p>
                                                    <p class="text-xs text-gray-400 mt-2">{{ notification.time }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                                        <Link href="/notifications"
                                            class="block text-center text-purple-600 hover:text-purple-800 font-medium text-sm">
                                        View all notifications
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- Messages -->
                            <div class="drawer-container relative">
                                <button @click="isMessagesOpen = !isMessagesOpen"
                                    class="relative p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200">
                                    <MessageSquare :size="20" />
                                    <span v-if="unreadMessages > 0"
                                        class="absolute -top-1 -right-1 bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                        {{ unreadMessages > 9 ? '9+' : unreadMessages }}
                                    </span>
                                </button>

                                <!-- Messages Drawer -->
                                <div v-if="isMessagesOpen"
                                    class="absolute right-0 top-full mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden z-50">
                                    <div class="p-4 border-b border-gray-100 bg-gray-50">
                                        <div class="flex items-center justify-between">
                                            <h3 class="font-semibold text-gray-900">Messages</h3>
                                            <Link href="/messages"
                                                class="text-xs text-purple-600 hover:text-purple-800 font-medium">
                                            View all
                                            </Link>
                                        </div>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <div v-if="isLoading" class="p-8 text-center">
                                            <div
                                                class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-500 mx-auto">
                                            </div>
                                            <p class="text-sm text-gray-500 mt-2">Loading conversations...</p>
                                        </div>
                                        <div v-else-if="processedMessages.length === 0" class="p-8 text-center">
                                            <MessageSquare :size="24" class="mx-auto text-gray-400 mb-2" />
                                            <p class="text-sm text-gray-500">No conversations yet</p>
                                        </div>
                                        <div v-else v-for="message in processedMessages" :key="message.id"
                                            @click="openChatWindow(message)"
                                            class="p-4 border-b border-gray-100 hover:bg-gray-50 cursor-pointer transition-colors duration-150"
                                            :class="{ 'bg-blue-50/50': !message.read }">
                                            <div class="flex items-start space-x-3">
                                                <div class="relative">
                                                    <div
                                                        class="w-9 h-9 bg-gradient-to-br from-purple-500 to-purple-600 rounded-full flex items-center justify-center text-white font-medium text-xs flex-shrink-0">
                                                        {{ message.avatar }}
                                                    </div>
                                                    <div v-if="message.online"
                                                        class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full">
                                                    </div>
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center justify-between">
                                                        <p class="font-medium text-gray-900 truncate">{{ message.sender
                                                        }}</p>
                                                        <div class="flex items-center space-x-1">
                                                            <span v-if="!message.read"
                                                                class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0"></span>
                                                            <span class="text-xs text-gray-400 whitespace-nowrap">{{
                                                                message.time }}</span>
                                                        </div>
                                                    </div>
                                                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">{{
                                                        message.message }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                                        <Link href="/messages"
                                            class="block text-center text-purple-600 hover:text-purple-800 font-medium text-sm">
                                        Open Messages
                                        </Link>
                                    </div>
                                </div>
                            </div>

                            <!-- User Dropdown -->
                            <MyDropdown />
                        </template>

                        <template v-else>
                            <!-- Login Button -->
                            <Link href="/login"
                                class="px-4 py-2 text-gray-600 hover:text-gray-900 transition-all duration-200 font-medium">
                            Log In
                            </Link>
                            <!-- Get Started Button -->
                            <Link href="/register"
                                class="px-5 py-2.5 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-500 hover:to-indigo-500 transition-all duration-300 font-semibold shadow-md hover:shadow-lg">
                            Get Started
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-all duration-200">
                            <Menu v-if="!isMobileMenuOpen" :size="24" />
                            <X v-else :size="24" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-if="isMobileMenuOpen" class="lg:hidden pb-4 border-t border-gray-200 pt-4">
                    <div class="flex flex-col space-y-1">
                        <Link href="/client" @click="isMobileMenuOpen = false"
                            class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
                            :class="{ 'text-gray-900 bg-gray-50': page.url === '/client' }">
                        <Home :size="18" class="mr-3" />
                        <span>Home</span>
                        </Link>

                        <Link href="/client/services" @click="isMobileMenuOpen = false"
                            class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
                            :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/services') }">
                        <Search :size="18" class="mr-3" />
                        <span>Services</span>
                        </Link>

                        <template v-if="page.props.auth.user">
                            <Link href="/client/bookings" @click="isMobileMenuOpen = false"
                                class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/bookings') }">
                            <Calendar :size="18" class="mr-3" />
                            <span>Bookings</span>
                            </Link>
                            <Link href="/client/events" @click="isMobileMenuOpen = false"
                                class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/events') }">
                            <Calendar :size="18" class="mr-3" />
                            <span>Events</span>
                            </Link>
                            <Link href="/client/favorites" @click="isMobileMenuOpen = false"
                                class="flex items-center px-4 py-3 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium"
                                :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/favorites') }">
                            <Heart :size="18" class="mr-3" />
                            <span>Favorites</span>
                            </Link>

                            <!-- Mobile Notifications & Messages -->
                            <div class="px-4 py-3 space-y-2 border-t border-gray-200 mt-4 pt-4">
                                <Link href="/notifications" @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <Bell :size="18" class="text-gray-600" />
                                    <span class="font-medium text-gray-900">Notifications</span>
                                </div>
                                <span v-if="unreadNotifications > 0"
                                    class="bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ unreadNotifications > 9 ? '9+' : unreadNotifications }}
                                </span>
                                </Link>

                                <Link href="/messages" @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-all duration-200">
                                <div class="flex items-center space-x-3">
                                    <MessageSquare :size="18" class="text-gray-600" />
                                    <span class="font-medium text-gray-900">Messages</span>
                                </div>
                                <span v-if="unreadMessages > 0"
                                    class="bg-green-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ unreadMessages > 9 ? '9+' : unreadMessages }}
                                </span>
                                </Link>
                            </div>
                        </template>

                        <!-- Mobile Auth Section -->
                        <div class="pt-4 border-t border-gray-200 mt-4">
                            <template v-if="page.props.auth.user">
                                <div
                                    class="flex items-center space-x-3 px-4 py-3 text-gray-700 mb-2 bg-gray-50 rounded-lg border border-gray-200">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 flex items-center justify-center text-white font-medium text-sm">
                                        {{page.props.auth.user.name.split(' ').map(word =>
                                            word.charAt(0)).join('').substring(0, 2).toUpperCase()}}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">{{ page.props.auth.user.name }}</p>
                                        <p class="text-xs text-gray-600">{{ page.props.auth.user.email }}</p>
                                    </div>
                                </div>

                                <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false"
                                    class="flex items-center px-4 py-3 text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium rounded-lg">
                                <User :size="18" class="mr-3 text-gray-600" />
                                Profile
                                </Link>

                                <Link href="/settings" @click="isMobileMenuOpen = false"
                                    class="flex items-center px-4 py-3 text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium rounded-lg"
                                    v-if="route().has && route().has('settings')">
                                <Settings :size="18" class="mr-3 text-gray-600" />
                                Settings
                                </Link>

                                <hr class="border-gray-200 my-2">

                                <Link :href="route('logout')" method="post" as="button"
                                    @click="isMobileMenuOpen = false"
                                    class="flex items-center w-full px-4 py-3 text-gray-600 hover:text-red-600 hover:bg-red-50 transition-all duration-200 font-medium rounded-lg">
                                <LogOut :size="18" class="mr-3 text-gray-600" />
                                Sign out
                                </Link>
                            </template>
                            <template v-else>
                                <Link href="/login" @click="isMobileMenuOpen = false"
                                    class="flex items-center px-4 py-3 text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium rounded-lg">
                                <User :size="18" class="mr-3" />
                                Log In
                                </Link>
                                <Link href="/register" @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-center mx-4 mt-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-indigo-600 text-white rounded-lg hover:from-purple-500 hover:to-indigo-500 transition-all duration-300 font-semibold text-center shadow-md">
                                <span>Get Started</span>
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Facebook-Style Chat Windows at Bottom -->
        <div class="fixed bottom-0 right-4 z-50 flex items-end space-x-2">
            <div v-for="chat in openChats" :key="chat.id"
                class="bg-white border border-gray-200 rounded-t-lg shadow-lg overflow-hidden chat-window"
                :class="{ 'minimized': chat.minimized }">
                <!-- Chat Header -->
                <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white cursor-pointer"
                    @click="toggleChatWindow(chat.id)">
                    <div class="flex items-center space-x-2">
                        <div class="relative">
                            <div
                                class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center text-white font-medium text-xs">
                                {{ chat.avatar }}
                            </div>
                            <div v-if="chat.online"
                                class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-white rounded-full">
                            </div>
                        </div>
                        <div>
                            <p class="font-medium text-sm">{{ chat.sender }}</p>
                            <p class="text-xs opacity-80">{{ chat.online ? 'Active now' : 'Last seen recently' }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-1">
                        <button @click.stop="toggleChatWindow(chat.id)"
                            class="p-1 hover:bg-white/20 rounded transition-colors">
                            <ChevronDown :size="16" :class="[!chat.minimized ? 'rotate-180' : '']" />
                        </button>
                        <button @click.stop="closeChatWindow(chat.id)"
                            class="p-1 hover:bg-white/20 rounded transition-colors">
                            <X :size="16" />
                        </button>
                    </div>
                </div>

                <!-- Chat Body -->
                <div v-if="!chat.minimized" class="chat-body">
                    <!-- Messages Area -->
                    <div ref="chatMessages" class="h-64 overflow-y-auto p-3 space-y-3 bg-gray-50 ">
                        <div v-for="msg in chat.chatMessages" :key="msg.id" class="flex"
                            :class="{ 'justify-end': msg.sent, 'justify-start': !msg.sent }">
                            <div class="max-w-xs px-3 py-2 rounded-lg text-sm relative" :class="{
                                'bg-purple-500 text-white': msg.sent,
                                'bg-white border border-gray-200 text-gray-900': !msg.sent
                            }">
                                <p>{{ msg.text }}</p>
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-xs opacity-70">{{ msg.time }}</p>
                                    <div v-if="msg.sending" class="ml-2">
                                        <div
                                            class="w-3 h-3 border border-white border-t-transparent rounded-full animate-spin">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="p-3 border-t border-gray-200 bg-white">
                        <div class="flex items-center space-x-2">
                            <input type="text" v-model="chat.newMessage" @keypress.enter="sendChatMessage(chat.id)"
                                placeholder="Type a message..."
                                class="flex-1 px-3 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                            <button @click="sendChatMessage(chat.id)" :disabled="!chat.newMessage?.trim()"
                                class="p-2 bg-purple-500 text-white rounded-full hover:bg-purple-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                <Send :size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
.chat-window {
    width: 300px;
    transition: all 0.3s ease;
}

.chat-window.minimized {
    height: auto;
}

.chat-window.minimized .chat-body {
    display: none;
}

/* Custom scrollbar for chat */
.chat-body .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
