<script setup>
import { ChevronDown, Send, X } from 'lucide-vue-next';

defineProps({
    openChats: Array,
})

const emit = defineEmits(['toggle-chat-window', 'close-chat-window', 'send-chat-message'])

</script>

<template>
    <div class="fixed bottom-0 right-4 z-50 flex items-end space-x-2">
        <div v-for="chat in openChats" :key="chat.id"
            class="bg-white border border-gray-200 rounded-t-lg shadow-lg overflow-hidden chat-window"
            :class="{ 'minimized': chat.minimized }">
            <!-- Chat Header -->
            <div class="flex items-center justify-between p-3 bg-gradient-to-r from-purple-500 to-indigo-500 text-white cursor-pointer"
                @click="emit('toggle-chat-window', chat.id)">
                <div class="flex items-center space-x-2">
                    <div class="relative flex-shrink-0">
                        <!-- Avatar -->
                        <div
                            class="w-9 h-9 rounded-full overflow-hidden shadow-sm border border-gray-200 bg-gray-100 flex items-center justify-center text-white font-semibold text-xs">
                            <!-- If user has an avatar image -->
                            <img v-if="chat.avatar" :src="chat.avatar" alt="User avatar"
                                class="w-full h-full object-cover" />

                            <!-- Fallback: initials with gradient -->
                            <span v-else
                                class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700">
                                {{ chat.initials }}
                            </span>
                        </div>

                        <!-- Online Indicator -->
                        <div v-if="chat.online"
                            class="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full shadow-sm">
                        </div>
                    </div>

                    <div>
                        <p class="font-medium text-sm">{{ chat.sender }}</p>
                        <p class="text-xs opacity-80">{{ chat.online ? 'Active now' : 'Last seen recently' }}</p>
                    </div>
                </div>
                <div class="flex items-center space-x-1">
                    <button @click.stop="emit('toggle-chat-window', chat.id)"
                        class="p-1 hover:bg-white/20 rounded transition-colors">
                        <ChevronDown :size="16" :class="[!chat.minimized ? 'rotate-180' : '']" />
                    </button>
                    <button @click.stop="emit('close-chat-window', chat.id)"
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
                        <input type="text" v-model="chat.newMessage"
                            @keypress.enter="emit('send-chat-message', chat.id)" placeholder="Type a message..."
                            class="flex-1 px-3 py-2 border border-gray-200 rounded-full text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent" />
                        <button @click="emit('send-chat-message', chat.id)" :disabled="!chat.newMessage?.trim()"
                            class="p-2 bg-purple-500 text-white rounded-full hover:bg-purple-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <Send :size="16" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style></style>
