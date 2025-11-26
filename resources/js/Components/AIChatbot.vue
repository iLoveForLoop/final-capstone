<script setup>
import { ref, nextTick, onMounted, onBeforeUnmount } from 'vue';
import { MessageCircle, X, Send, Sparkles, BotMessageSquare, Bot } from 'lucide-vue-next';
import axios from 'axios';
import { useUIStore } from '@/store/ui';

// State
const isOpen = ref(false);
const userInput = ref('');
const messages = ref([]);
const isTyping = ref(false);
const unreadCount = ref(0);
const messagesContainer = ref(null);
const conversationId = ref(null);
const error = ref(null);
const ui = useUIStore()

// Suggestions
const suggestions = [
    'What is eventory?',
    'How do I find and book vendors?',
    'Can I book multiple services for one event?',
    // 'How does the 4-step booking process work?',
    'Does Eventory handle payments?',
    'How do I register as a vendor?',
];

// Lifecycle hooks
onMounted(() => {
    loadConversation();
    window.addEventListener('user-logged-out', handleLogout);
});

onBeforeUnmount(() => {
    window.removeEventListener('user-logged-out', handleLogout);
});

// Load conversation from sessionStorage
const loadConversation = () => {
    const savedConversationId = sessionStorage.getItem('eventory_chat_conversation_id');
    const savedMessages = sessionStorage.getItem('eventory_chat_messages');

    if (savedConversationId) {
        conversationId.value = savedConversationId;
    }

    if (savedMessages) {
        try {
            messages.value = JSON.parse(savedMessages);
        } catch (e) {
            console.error('Failed to load messages:', e);
        }
    }
};

// Chat controls
const openChat = () => {
    isOpen.value = true;
    unreadCount.value = 0;
    nextTick(() => scrollToBottom());
};

const closeChat = () => {
    isOpen.value = false;
};

const startNewChat = () => {
    if (messages.value.length === 0) return;

    if (confirm('Start a new conversation? This will clear your current chat history.')) {
        clearChat();
    }
};

const clearChat = () => {
    messages.value = [];
    conversationId.value = null;
    error.value = null;
    sessionStorage.removeItem('eventory_chat_messages');
    sessionStorage.removeItem('eventory_chat_conversation_id');
};

const handleLogout = () => {
    clearChat();
};

// Message handling
const handleSuggestion = (text) => {
    userInput.value = text;
    sendMessage();
};

const sendMessage = async () => {
    if (!userInput.value.trim() || isTyping.value) return;

    const userMessage = userInput.value.trim();
    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    // Add user message
    messages.value.push({
        type: 'user',
        text: userMessage,
        time: time
    });

    userInput.value = '';
    error.value = null;
    scrollToBottom();
    saveMessages();

    // Show typing indicator
    isTyping.value = true;

    try {
        // Call backend API
        const response = await axios.post('/api/ai/message', {
            message: userMessage,
            conversation_id: conversationId.value
        });

        isTyping.value = false;

        if (response.data.success) {
            // Save conversation ID
            if (!conversationId.value && response.data.conversation_id) {
                conversationId.value = response.data.conversation_id;
                sessionStorage.setItem('eventory_chat_conversation_id', conversationId.value);
            }

            // Add bot response
            messages.value.push({
                type: 'bot',
                text: response.data.message,
                time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
            });

            saveMessages();
            scrollToBottom();
        } else {
            throw new Error('Failed to get response');
        }
    } catch (err) {
        isTyping.value = false;
        console.error('Chat error:', err);
        console.error('Response data:', err.response?.data);

        const errorMessage = err.response?.data?.message || 'Sorry, I encountered an error. Please try again in a moment.';

        messages.value.push({
            type: 'bot',
            text: errorMessage,
            time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
        });

        saveMessages();
        scrollToBottom();
    }
};

// Utility functions
const saveMessages = () => {
    try {
        sessionStorage.setItem('eventory_chat_messages', JSON.stringify(messages.value));
        if (conversationId.value) {
            sessionStorage.setItem('eventory_chat_conversation_id', conversationId.value);
        }
    } catch (e) {
        console.error('Failed to save messages:', e);
    }
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};
</script>

<template>
    <div class="chatbot-container" :class="ui.isInMessage ? 'left-5' : 'right-5'">
        <!-- Chat Window -->
        <Transition name="chat">
            <div v-if="isOpen" class="chat-window">
                <!-- Header -->
                <div class="chat-header">
                    <div class="header-left">
                        <div class="bot-avatar">
                            <Bot :size="20" />
                        </div>
                        <div>
                            <h3>Eve</h3>
                        </div>
                    </div>
                    <div class="header-actions">
                        <button @click="startNewChat" class="new-chat-btn" title="Start new conversation">
                            <Sparkles :size="16" />
                        </button>
                        <button @click="closeChat" class="close-btn">
                            <X :size="20" />
                        </button>
                    </div>
                </div>

                <!-- Messages -->
                <div ref="messagesContainer" class="messages">
                    <!-- Welcome -->
                    <div v-if="messages.length === 0" class="welcome">
                        <div class="bot-avatar-large">
                            <Bot :size="28" />
                        </div>
                        <h4>Hi! I'm your Eventory AI assistant 👋</h4>
                        <p>I can help you with finding vendors, bookings, and answering questions about the platform.
                        </p>

                        <div class="suggestions">
                            <button v-for="suggestion in suggestions" :key="suggestion"
                                @click="handleSuggestion(suggestion)" class="suggestion-btn">
                                {{ suggestion }}
                            </button>
                        </div>
                    </div>

                    <!-- Message List -->
                    <div v-for="(msg, idx) in messages" :key="idx" :class="['message', msg.type]">
                        <div v-if="msg.type === 'bot'" class="bot-avatar-small">
                            <Bot :size="14" />
                        </div>
                        <div class="bubble-wrapper">
                            <div class="bubble">
                                {{ msg.text }}
                            </div>
                            <span class="timestamp">{{ msg.time }}</span>
                        </div>
                    </div>

                    <!-- Typing -->
                    <div v-if="isTyping" class="message bot">
                        <div class="bot-avatar-small">
                            <Bot :size="14" />
                        </div>
                        <div class="bubble-wrapper">
                            <div class="bubble typing">
                                <div class="typing-indicator">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Error Message -->
                    <div v-if="error" class="message bot error">
                        <div class="bot-avatar-small">
                            <Bot :size="14" />
                        </div>
                        <div class="bubble-wrapper">
                            <div class="bubble error-bubble">
                                {{ error }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Input -->
                <div class="input-area">
                    <div class="input-wrapper">
                        <input v-model="userInput" @keypress.enter="sendMessage" type="text"
                            placeholder="Ask me anything..." :disabled="isTyping" />
                        <button @click="sendMessage" :disabled="!userInput.trim() || isTyping" class="send-btn">
                            <Send :size="18" />
                        </button>
                    </div>
                    <div class="powered-by">
                        <Sparkles :size="10" />
                        <span>AI assistant powered by Eventory. Responses may vary.</span>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Float Button -->
        <Transition name="button">
            <button v-if="!isOpen" @click="openChat" class="float-btn">
                <BotMessageSquare :size="24" />
                <span v-if="unreadCount > 0" class="badge">{{ unreadCount }}</span>
            </button>
        </Transition>
    </div>
</template>

<style scoped>
.chatbot-container {
    position: fixed;
    bottom: 20px;
    z-index: 9999;
}

/* Float Button */
.float-btn {
    position: relative;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
}

.float-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.5);
}

.badge {
    position: absolute;
    top: -2px;
    right: -2px;
    background: #ef4444;
    color: white;
    border-radius: 10px;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: 600;
    min-width: 18px;
    text-align: center;
    border: 2px solid white;
}

/* Chat Window */
.chat-window {
    width: 400px;
    height: 650px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    color: white;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-actions {
    display: flex;
    align-items: center;
    gap: 8px;
}

.bot-avatar {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: rgba(255, 255, 255, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.chat-header h3 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.new-chat-btn,
.close-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.new-chat-btn:hover,
.close-btn:hover {
    background: rgba(255, 255, 255, 0.25);
    transform: scale(1.05);
}

/* Messages */
.messages {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f7f8fa;
    scroll-behavior: smooth;
}

.messages::-webkit-scrollbar {
    width: 5px;
}

.messages::-webkit-scrollbar-track {
    background: transparent;
}

.messages::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 10px;
}

/* Welcome */
.welcome {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    padding: 30px 10px;
}

.bot-avatar-large {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-bottom: 16px;
    box-shadow: 0 4px 14px rgba(102, 126, 234, 0.3);
}

.welcome h4 {
    margin: 0 0 8px 0;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
}

.welcome p {
    margin: 0 0 20px 0;
    font-size: 14px;
    color: #6b7280;
    line-height: 1.5;
}

.suggestions {
    display: flex;
    flex-direction: column;
    gap: 8px;
    width: 100%;
}

.suggestion-btn {
    padding: 12px 16px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    color: #374151;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
    text-align: left;
}

.suggestion-btn:hover {
    background: #f9fafb;
    border-color: #667eea;
    color: #667eea;
}

/* Message */
.message {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message.user {
    flex-direction: row-reverse;
}

.bot-avatar-small {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.bubble-wrapper {
    display: flex;
    flex-direction: column;
    gap: 4px;
    max-width: 75%;
}

.message.user .bubble-wrapper {
    align-items: flex-end;
}

.bubble {
    padding: 10px 14px;
    border-radius: 12px;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.message.bot .bubble {
    background: white;
    color: #1f2937;
    border: 1px solid #e5e7eb;
    border-bottom-left-radius: 4px;
}

.message.user .bubble {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.error-bubble {
    background: #fee;
    border: 1px solid #fcc;
    color: #c33;
}

.timestamp {
    font-size: 11px;
    color: #9ca3af;
    padding: 0 4px;
}

/* Typing */
.bubble.typing {
    padding: 12px 16px;
}

.typing-indicator {
    display: flex;
    gap: 4px;
}

.typing-indicator span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #9ca3af;
    animation: typing 1.4s infinite;
}

.typing-indicator span:nth-child(1) {
    animation-delay: 0s;
}

.typing-indicator span:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-indicator span:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {

    0%,
    60%,
    100% {
        transform: translateY(0);
        opacity: 0.7;
    }

    30% {
        transform: translateY(-8px);
        opacity: 1;
    }
}

/* Input */
.input-area {
    padding: 16px 18px;
    background: white;
    border-top: 1px solid #e5e7eb;
}

.input-wrapper {
    display: flex;
    gap: 8px;
    align-items: center;
    background: #f9fafb;
    border: 1.5px solid #e5e7eb;
    border-radius: 12px;
    padding: 4px;
    transition: all 0.2s;
}

.input-wrapper:focus-within {
    background: white;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-area input {
    flex: 1;
    padding: 10px 12px;
    border: none;
    background: transparent;
    font-size: 14px;
    outline: none;
    color: #111827;
}

.input-area input::placeholder {
    color: #9ca3af;
}

.input-area input:disabled {
    cursor: not-allowed;
    opacity: 0.6;
}

.send-btn {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
    opacity: 0.9;
    transform: scale(1.05);
}

.send-btn:disabled {
    background: #e5e7eb;
    color: #9ca3af;
    cursor: not-allowed;
}

.powered-by {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
    margin-top: 8px;
    font-size: 11px;
    color: #9ca3af;
}

.powered-by svg {
    color: #667eea;
}

/* Transitions */
.chat-enter-active,
.chat-leave-active {
    transition: all 0.3s ease;
}

.chat-enter-from {
    opacity: 0;
    transform: translateY(10px) scale(0.95);
}

.chat-leave-to {
    opacity: 0;
    transform: translateY(10px) scale(0.95);
}

.button-enter-active,
.button-leave-active {
    transition: all 0.3s ease;
}

.button-enter-from,
.button-leave-to {
    opacity: 0;
    transform: scale(0);
}

/* Mobile */
@media (max-width: 480px) {
    .chatbot-container {
        bottom: 16px;
        right: 16px;
    }

    .float-btn {
        width: 56px;
        height: 56px;
    }

    .chat-window {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 100%;
        border-radius: 0;
    }

    .bubble-wrapper {
        max-width: 80%;
    }

    .suggestions {
        width: 100%;
    }
}
</style>
