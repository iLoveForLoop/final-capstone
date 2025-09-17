<template>
    <div class="fixed bottom-6 right-6 z-50">
        <!-- Floating Messages Button -->
        <button @click="toggleMessages"
            class="relative w-14 h-14 bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-full shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 flex items-center justify-center"
            :class="{ 'ring-4 ring-purple-300 ring-opacity-50': hasUnreadMessages }">
            <MessageCircle :size="24" />
            <div v-if="unreadCount > 0"
                class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1 animate-pulse">
                {{ unreadCount > 99 ? '99+' : unreadCount }}
            </div>
        </button>

        <!-- Messages Window -->
        <transition name="slide-up">
            <div v-if="isExpanded"
                class="absolute bottom-16 right-0 w-80 h-96 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden flex flex-col"
                @click.stop>
                <!-- Conversations List View -->
                <div v-if="!selectedConversation" class="flex flex-col h-full">
                    <!-- Header -->
                    <div
                        class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                        <h3 class="font-semibold text-gray-800 text-lg">Messages</h3>
                        <button @click="toggleMessages"
                            class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                            <X :size="18" />
                        </button>
                    </div>

                    <!-- Search Bar -->
                    <div class="p-3 border-b border-gray-100">
                        <div class="relative">
                            <Search :size="16"
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                            <input type="text" placeholder="Search conversations..." v-model="searchQuery"
                                class="w-full pl-10 pr-4 py-2.5 bg-gray-50 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:bg-white transition-all" />
                        </div>
                    </div>

                    <!-- Conversations List -->
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        <div class="p-2">
                            <div v-for="conversation in filteredConversations" :key="conversation.id"
                                @click="selectConversation(conversation)"
                                class="flex items-center p-3 rounded-xl cursor-pointer hover:bg-gray-50 transition-all duration-200 group"
                                :class="{ 'bg-purple-50': conversation.unreadCount > 0 }">
                                <div class="relative flex-shrink-0">
                                    <img :src="conversation.avatar" :alt="conversation.name"
                                        class="w-12 h-12 rounded-full object-cover border-2 border-white shadow-sm" />
                                    <div v-if="conversation.online"
                                        class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full" />
                                </div>
                                <div class="ml-3 flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h4
                                            class="font-medium text-gray-900 text-sm truncate group-hover:text-purple-600 transition-colors">
                                            {{ conversation.name }}
                                        </h4>
                                        <span class="text-xs text-gray-500">{{ conversation.lastMessageTime }}</span>
                                    </div>
                                    <p class="text-sm text-gray-600 truncate"
                                        :class="{ 'font-medium text-gray-900': conversation.unreadCount > 0 }">
                                        {{ conversation.lastMessage }}
                                    </p>
                                </div>
                                <div v-if="conversation.unreadCount > 0" class="ml-2 flex-shrink-0">
                                    <span
                                        class="bg-purple-500 text-white text-xs font-bold rounded-full min-w-[18px] h-4.5 px-1.5 flex items-center justify-center">
                                        {{ conversation.unreadCount }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chat View -->
                <div v-if="selectedConversation" class="flex flex-col h-full">
                    <!-- Chat Header -->
                    <div
                        class="flex items-center justify-between p-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
                        <div class="flex items-center">
                            <button @click="selectedConversation = null"
                                class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors mr-2">
                                <ArrowLeft :size="18" />
                            </button>
                            <img :src="selectedConversation.avatar" :alt="selectedConversation.name"
                                class="w-8 h-8 rounded-full object-cover mr-3" />
                            <!-- <div>
                                <h4 class="font-semibold text-gray-900 text-sm">{{ selectedConversation.name }}</h4>
                                <p class="text-xs text-gray-500">{{ selectedConversation.online ?? 'Active now' : 'Last
                                    seen recently' }}</p>
                            </div> -->
                        </div>
                        <div class="flex items-center space-x-1">
                            <button
                                class="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-full transition-colors">
                                <Phone :size="16" />
                            </button>
                            <button
                                class="p-1.5 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-full transition-colors">
                                <Video :size="16" />
                            </button>
                            <button
                                class="p-1.5 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                                <Info :size="16" />
                            </button>
                        </div>
                    </div>

                    <!-- Messages Container -->
                    <div ref="messagesContainer" class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3">
                        <div v-for="message in selectedConversation.messages" :key="message.id" class="flex"
                            :class="{ 'justify-end': message.sent, 'justify-start': !message.sent }">
                            <div class="max-w-xs px-4 py-2 rounded-2xl shadow-sm" :class="{
                                'bg-gradient-to-br from-purple-500 to-pink-500 text-white': message.sent,
                                'bg-gray-100 text-gray-900': !message.sent
                            }">
                                <p class="text-sm">{{ message.text }}</p>
                                <p class="text-xs mt-1 opacity-70">{{ message.time }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-100 bg-gray-50">
                        <div class="flex items-center space-x-2">
                            <button
                                class="p-2 text-gray-500 hover:text-purple-600 hover:bg-purple-50 rounded-full transition-colors">
                                <Smile :size="18" />
                            </button>
                            <div class="flex-1 relative">
                                <input type="text" v-model="messageInput" @keypress.enter="sendMessage"
                                    placeholder="Type a message..."
                                    class="w-full px-4 py-2.5 bg-white border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all" />
                            </div>
                            <button @click="sendMessage" :disabled="!messageInput.trim()"
                                class="p-2.5 bg-gradient-to-br from-purple-500 to-pink-500 text-white rounded-full hover:shadow-lg transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed disabled:transform-none">
                                <Send :size="16" />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { MessageCircle, X, Search, Send, Phone, Video, Info, ArrowLeft, Smile } from 'lucide-vue-next'

export default {
    name: 'InstagramFloatingMessages',
    components: {
        MessageCircle,
        X,
        Search,
        Send,
        Phone,
        Video,
        Info,
        ArrowLeft,
        Smile
    },
    data() {
        return {
            isExpanded: false,
            selectedConversation: null,
            searchQuery: '',
            messageInput: '',
            conversations: [
                {
                    id: 1,
                    name: 'Sarah Johnson',
                    avatar: 'https://images.unsplash.com/photo-1494790108755-2616b612b515?w=50&h=50&fit=crop&crop=face&auto=format',
                    lastMessage: 'Looking forward to our session tomorrow! 🎉',
                    lastMessageTime: '2m',
                    online: true,
                    unreadCount: 2,
                    messages: [
                        { id: 1, text: "Hey! How are you?", sent: false, time: "10:30 AM", read: true },
                        { id: 2, text: "I'm doing great! Just finished the event planning", sent: true, time: "10:32 AM", read: true },
                        { id: 3, text: "That sounds amazing! Can't wait to see how it turns out", sent: false, time: "10:35 AM", read: true },
                        { id: 4, text: "Looking forward to our session tomorrow! 🎉", sent: false, time: "2m ago", read: false },
                        { id: 5, text: "It's going to be incredible!", sent: false, time: "1m ago", read: false }
                    ]
                },
                {
                    id: 2,
                    name: 'Mike Davis',
                    avatar: 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=50&h=50&fit=crop&crop=face&auto=format',
                    lastMessage: 'Here are the menu options for your event...',
                    lastMessageTime: '1h',
                    online: false,
                    unreadCount: 0,
                    messages: [
                        { id: 1, text: "Here are the menu options for your event", sent: false, time: "Yesterday", read: true },
                        { id: 2, text: "Thanks! These look perfect", sent: true, time: "Yesterday", read: true }
                    ]
                },
                {
                    id: 3,
                    name: 'Jessica Miller',
                    avatar: 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=50&h=50&fit=crop&crop=face&auto=format',
                    lastMessage: 'Are you available for a corporate event?',
                    lastMessageTime: '3h',
                    online: true,
                    unreadCount: 1,
                    messages: [
                        { id: 1, text: "Hi! I have a question about your services", sent: false, time: "3h ago", read: true },
                        { id: 2, text: "Are you available for a corporate event?", sent: false, time: "3h ago", read: false }
                    ]
                },
                {
                    id: 4,
                    name: 'Event Support',
                    avatar: 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?w=50&h=50&fit=crop&crop=face&auto=format',
                    lastMessage: 'Your event registration has been processed.',
                    lastMessageTime: '1d',
                    online: false,
                    unreadCount: 0,
                    messages: [
                        { id: 1, text: "Your event registration has been processed.", sent: false, time: "1 day ago", read: true },
                        { id: 2, text: "Thank you for the confirmation!", sent: true, time: "1 day ago", read: true }
                    ]
                }
            ]
        }
    },
    computed: {
        unreadCount() {
            return this.conversations.reduce((total, conv) => total + conv.unreadCount, 0)
        },
        hasUnreadMessages() {
            return this.unreadCount > 0
        },
        filteredConversations() {
            if (!this.searchQuery) return this.conversations
            const query = this.searchQuery.toLowerCase()
            return this.conversations.filter(conv =>
                conv.name.toLowerCase().includes(query) ||
                conv.lastMessage.toLowerCase().includes(query)
            )
        }
    },
    methods: {
        toggleMessages() {
            this.isExpanded = !this.isExpanded
            if (!this.isExpanded) {
                this.selectedConversation = null
                this.searchQuery = ''
            }
        },
        selectConversation(conversation) {
            conversation.unreadCount = 0
            this.selectedConversation = conversation
            this.$nextTick(() => {
                this.scrollToBottom()
            })
        },
        sendMessage() {
            if (!this.messageInput.trim() || !this.selectedConversation) return

            const newMessage = {
                id: Date.now(),
                text: this.messageInput.trim(),
                sent: true,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                read: true
            }

            this.selectedConversation.messages.push(newMessage)
            this.selectedConversation.lastMessage = newMessage.text
            this.selectedConversation.lastMessageTime = 'now'

            this.messageInput = ''
            this.$nextTick(() => {
                this.scrollToBottom()
            })

            // Simulate reply after 2 seconds
            setTimeout(() => {
                this.simulateReply()
            }, 2000)
        },
        simulateReply() {
            if (!this.selectedConversation) return

            const replies = [
                "Thanks for the message!",
                "That sounds great!",
                "I'll get back to you soon",
                "Perfect! Let's discuss this further",
                "Absolutely! Count me in"
            ]

            const replyMessage = {
                id: Date.now(),
                text: replies[Math.floor(Math.random() * replies.length)],
                sent: false,
                time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
                read: false
            }

            this.selectedConversation.messages.push(replyMessage)
            this.selectedConversation.lastMessage = replyMessage.text
            this.selectedConversation.lastMessageTime = 'now'

            this.$nextTick(() => {
                this.scrollToBottom()
            })
        },
        scrollToBottom() {
            if (this.$refs.messagesContainer) {
                this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight
            }
        },
        handleClickOutside(event) {
            const messagesContainer = event.target.closest('.fixed.bottom-6.right-6')
            if (!messagesContainer && this.isExpanded) {
                this.toggleMessages()
            }
        }
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside)
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside)
    }
}
</script>

<style scoped>
/* Custom scrollbar */
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f8fafc;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* Transition animations */
.slide-up-enter-active,
.slide-up-leave-active {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.slide-up-enter-from,
.slide-up-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.slide-up-enter-to,
.slide-up-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}

/* Smooth scrolling */
.custom-scrollbar {
    scroll-behavior: smooth;
}

/* Hover effects */
.group:hover .group-hover\:text-purple-600 {
    color: #9333ea;
}
</style>
