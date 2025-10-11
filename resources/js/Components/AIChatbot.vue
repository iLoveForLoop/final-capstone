<template>
    <div class="chatbot-container">
        <!-- Chat Window -->
        <Transition name="chat-window">
            <div v-if="isOpen" class="chat-window">
                <!-- Header -->
                <div class="chat-header">
                    <div class="flex items-center gap-3">
                        <div class="bot-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-5 h-5">
                                <path d="M12 8V4H8" />
                                <rect width="16" height="12" x="4" y="8" rx="2" />
                                <path d="M2 14h2" />
                                <path d="M20 14h2" />
                                <path d="M15 13v2" />
                                <path d="M9 13v2" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="chat-title">Eventory Assistant</h3>
                            <p class="chat-status">
                                <span class="status-dot"></span>
                                Online
                            </p>
                        </div>
                    </div>
                    <button @click="closeChat" class="close-button">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-5 h-5">
                            <path d="M18 6 6 18" />
                            <path d="m6 6 12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Messages Container -->
                <div ref="messagesContainer" class="messages-container">
                    <!-- Welcome Message -->
                    <div v-if="messages.length === 0" class="welcome-message">
                        <div class="welcome-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-8 h-8">
                                <path d="M12 8V4H8" />
                                <rect width="16" height="12" x="4" y="8" rx="2" />
                                <path d="M2 14h2" />
                                <path d="M20 14h2" />
                                <path d="M15 13v2" />
                                <path d="M9 13v2" />
                            </svg>
                        </div>
                        <h4 class="welcome-title">Welcome to Eventory!</h4>
                        <p class="welcome-text">
                            Hi there! 👋 I'm your AI assistant. I can help you with:
                        </p>
                        <ul class="welcome-list">
                            <li>Finding the perfect vendors for your event</li>
                            <li>Understanding how bookings work</li>
                            <li>Navigating platform features</li>
                            <li>Answering any questions about Eventory</li>
                        </ul>
                        <p class="welcome-prompt">How can I assist you today?</p>
                    </div>

                    <!-- Messages -->
                    <div v-for="(message, index) in messages" :key="index" class="message-wrapper"
                        :class="message.type">
                        <div v-if="message.type === 'bot'" class="message-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-4 h-4">
                                <path d="M12 8V4H8" />
                                <rect width="16" height="12" x="4" y="8" rx="2" />
                                <path d="M2 14h2" />
                                <path d="M20 14h2" />
                                <path d="M15 13v2" />
                                <path d="M9 13v2" />
                            </svg>
                        </div>
                        <div class="message-content" :class="message.type">
                            <p class="message-text">{{ message.text }}</p>
                            <span class="message-time">{{ message.time }}</span>
                        </div>
                    </div>

                    <!-- Typing Indicator -->
                    <div v-if="isTyping" class="message-wrapper bot">
                        <div class="message-avatar">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-4 h-4">
                                <path d="M12 8V4H8" />
                                <rect width="16" height="12" x="4" y="8" rx="2" />
                                <path d="M2 14h2" />
                                <path d="M20 14h2" />
                                <path d="M15 13v2" />
                                <path d="M9 13v2" />
                            </svg>
                        </div>
                        <div class="typing-indicator">
                            <span></span>
                            <span></span>
                            <span></span>
                        </div>
                    </div>
                </div>

                <!-- Input Area -->
                <div class="input-container">
                    <form @submit.prevent="sendMessage" class="input-form">
                        <input v-model="userInput" type="text" placeholder="Type your message..." class="chat-input"
                            :disabled="isTyping" />
                        <button type="submit" class="send-button" :disabled="!userInput.trim() || isTyping">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="w-5 h-5">
                                <path d="m22 2-7 20-4-9-9-4Z" />
                                <path d="M22 2 11 13" />
                            </svg>
                        </button>
                    </form>
                    <p class="powered-by">Powered by Eventory AI</p>
                </div>
            </div>
        </Transition>

        <!-- Floating Button -->
        <Transition name="bubble">
            <button v-if="!isOpen" @click="openChat" class="floating-button">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-6 h-6">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
                <span v-if="unreadCount > 0" class="notification-badge">{{ unreadCount }}</span>
            </button>
        </Transition>
    </div>
</template>

<script setup>
import { ref, nextTick } from 'vue';

const isOpen = ref(false);
const userInput = ref('');
const messages = ref([]);
const isTyping = ref(false);
const unreadCount = ref(0);
const messagesContainer = ref(null);

const openChat = () => {
    isOpen.value = true;
    unreadCount.value = 0;
    nextTick(() => {
        scrollToBottom();
    });
};

const closeChat = () => {
    isOpen.value = false;
};

const sendMessage = () => {
    if (!userInput.value.trim()) return;

    const now = new Date();
    const time = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' });

    messages.value.push({
        type: 'user',
        text: userInput.value,
        time: time
    });

    userInput.value = '';
    scrollToBottom();

    // Simulate bot response
    isTyping.value = true;
    setTimeout(() => {
        isTyping.value = false;
        messages.value.push({
            type: 'bot',
            text: 'Thank you for your message! This is a demo response. In production, this will be connected to your AI backend.',
            time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })
        });
        scrollToBottom();
    }, 1500);
};

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesContainer.value) {
            messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight;
        }
    });
};
</script>

<style scoped>
.chatbot-container {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
}

/* Floating Button */
.floating-button {
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
    box-shadow: 0 8px 24px rgba(102, 126, 234, 0.4);
    transition: all 0.3s ease;
    position: relative;
}

.floating-button:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 32px rgba(102, 126, 234, 0.5);
}

.floating-button:active {
    transform: scale(0.95);
}

.notification-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #ef4444;
    color: white;
    border-radius: 12px;
    padding: 2px 6px;
    font-size: 11px;
    font-weight: 600;
    min-width: 20px;
    text-align: center;
    border: 2px solid white;
}

/* Chat Window */
.chat-window {
    width: 400px;
    height: 600px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 16px 48px rgba(0, 0, 0, 0.15);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Header */
.chat-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.bot-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(10px);
}

.chat-title {
    font-size: 16px;
    font-weight: 600;
    margin: 0;
}

.chat-status {
    font-size: 13px;
    margin: 4px 0 0 0;
    display: flex;
    align-items: center;
    gap: 6px;
    opacity: 0.95;
}

.status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #10b981;
    animation: pulse 2s infinite;
}

@keyframes pulse {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.close-button {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.close-button:hover {
    background: rgba(255, 255, 255, 0.3);
}

/* Messages Container */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: #f9fafb;
    scroll-behavior: smooth;
}

.messages-container::-webkit-scrollbar {
    width: 6px;
}

.messages-container::-webkit-scrollbar-track {
    background: transparent;
}

.messages-container::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

.messages-container::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}

/* Welcome Message */
.welcome-message {
    text-align: center;
    padding: 40px 20px;
}

.welcome-icon {
    width: 64px;
    height: 64px;
    margin: 0 auto 16px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.welcome-title {
    font-size: 20px;
    font-weight: 600;
    color: #111827;
    margin: 0 0 8px 0;
}

.welcome-text {
    color: #6b7280;
    margin: 0 0 16px 0;
    font-size: 14px;
}

.welcome-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
    text-align: left;
}

.welcome-list li {
    padding: 10px 16px;
    background: white;
    border-radius: 8px;
    margin-bottom: 8px;
    color: #374151;
    font-size: 14px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.welcome-list li:before {
    content: "✓";
    color: #667eea;
    font-weight: bold;
    margin-right: 10px;
}

.welcome-prompt {
    color: #667eea;
    font-weight: 500;
    font-size: 14px;
    margin: 0;
}

/* Messages */
.message-wrapper {
    display: flex;
    gap: 10px;
    margin-bottom: 16px;
    animation: slideIn 0.3s ease;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.message-wrapper.user {
    flex-direction: row-reverse;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.message-content {
    max-width: 75%;
    padding: 12px 16px;
    border-radius: 12px;
    position: relative;
}

.message-content.bot {
    background: white;
    color: #374151;
    border-bottom-left-radius: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.message-content.user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom-right-radius: 4px;
}

.message-text {
    margin: 0 0 4px 0;
    font-size: 14px;
    line-height: 1.5;
    word-wrap: break-word;
}

.message-time {
    font-size: 11px;
    opacity: 0.7;
}

/* Typing Indicator */
.typing-indicator {
    background: white;
    padding: 12px 16px;
    border-radius: 12px;
    border-bottom-left-radius: 4px;
    display: flex;
    gap: 4px;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.typing-indicator span {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #9ca3af;
    animation: typing 1.4s infinite;
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
        transform: translateY(-10px);
        opacity: 1;
    }
}

/* Input Container */
.input-container {
    background: white;
    border-top: 1px solid #e5e7eb;
    padding: 16px;
}

.input-form {
    display: flex;
    gap: 8px;
    margin-bottom: 8px;
}

.chat-input {
    flex: 1;
    padding: 12px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    font-size: 14px;
    outline: none;
    transition: all 0.2s;
}

.chat-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.chat-input:disabled {
    background: #f9fafb;
    cursor: not-allowed;
}

.send-button {
    width: 44px;
    height: 44px;
    border-radius: 50%;
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

.send-button:hover:not(:disabled) {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
}

.send-button:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.powered-by {
    text-align: center;
    font-size: 11px;
    color: #9ca3af;
    margin: 0;
}

/* Transitions */
.chat-window-enter-active,
.chat-window-leave-active {
    transition: all 0.3s ease;
}

.chat-window-enter-from {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.chat-window-leave-to {
    opacity: 0;
    transform: translateY(20px) scale(0.95);
}

.bubble-enter-active,
.bubble-leave-active {
    transition: all 0.3s ease;
}

.bubble-enter-from,
.bubble-leave-to {
    opacity: 0;
    transform: scale(0);
}

/* Mobile Responsive */
@media (max-width: 640px) {
    .chatbot-container {
        bottom: 16px;
        right: 16px;
    }

    .floating-button {
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
        max-width: 100vw;
        max-height: 100vh;
    }

    .message-content {
        max-width: 80%;
    }

    .welcome-message {
        padding: 20px;
    }
}

@media (max-width: 480px) {
    .chat-header {
        padding: 14px 16px;
    }

    .bot-avatar {
        width: 36px;
        height: 36px;
    }

    .chat-title {
        font-size: 15px;
    }

    .chat-status {
        font-size: 12px;
    }

    .messages-container {
        padding: 16px;
    }

    .message-text {
        font-size: 13px;
    }

    .chat-input {
        font-size: 16px;
        /* Prevents zoom on iOS */
    }
}
</style>
