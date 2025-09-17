import { nextTick, ref } from 'vue';

export function useConversationEcho(conversations, messages, openChats, user) {
    const echoChannels = ref({});

    const subscribeToConversation = (conversationId) => {
        if (echoChannels.value[conversationId]) return; // Already subscribed

        echoChannels.value[conversationId] = window.Echo.private(
            `conversation.${conversationId}`,
        ).listen('MessageSent', (e) => {
            // Update global messages (for full view)
            if (messages.value && Array.isArray(messages.value)) {
                messages.value.push({
                    id: e.message.id,
                    senderId: e.message.user.id,
                    senderName: e.message.user.name,
                    content: e.message.content,
                    timestamp: new Date(
                        e.message.created_at,
                    ).toLocaleTimeString([], {
                        hour: '2-digit',
                        minute: '2-digit',
                    }),
                    isOwn: e.message.user.id === user.value.id,
                    attachments: e.message.attachments,
                    type: e.message.type,
                });
            }

            // Update open chat window (for mini chat)
            const chat = openChats.value?.find(
                (c) => c.conversationId === conversationId,
            );
            if (chat) {
                chat.chatMessages.push({
                    id: e.message.id,
                    text: e.message.content,
                    sent: e.message.user.id === user.value.id,
                    time: new Date(e.message.created_at).toLocaleTimeString(
                        [],
                        { hour: '2-digit', minute: '2-digit' },
                    ),
                });
                nextTick(() => {
                    const elements =
                        document.querySelectorAll('.chat-body .h-64');
                    elements.forEach((el) => (el.scrollTop = el.scrollHeight));
                });
            }

            // Update conversation list (last message + unread count)
            const conv = conversations.value.find(
                (c) => c.id === conversationId,
            );
            if (conv) {
                conv.last_message = e.message;
                if (e.message.user.id !== user.value.id) {
                    conv.unread_count = (conv.unread_count || 0) + 1;
                }
            }
        });
    };

    const leaveConversation = (conversationId) => {
        if (echoChannels.value[conversationId]) {
            window.Echo.leave(`conversation.${conversationId}`);
            delete echoChannels.value[conversationId];
        }
    };

    return { subscribeToConversation, leaveConversation };
}
