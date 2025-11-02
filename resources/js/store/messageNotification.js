import axios from 'axios';
import { defineStore } from 'pinia';

export const useMessageNotificationStore = defineStore('messageNotification', {
    state: () => ({
        unreadCount: 0,
        initialized: false,
    }),

    actions: {
        async fetchUnreadCount() {
            try {
                const { data } = await axios.get(
                    '/vendor/unread-messages-count',
                );
                this.unreadCount = data.count;
                this.initialized = true;
            } catch (error) {
                console.error('Failed to fetch unread messages count:', error);
            }
        },

        incrementUnread() {
            this.unreadCount++;
        },

        decrementUnread() {
            if (this.unreadCount > 0) this.unreadCount--;
        },

        listenForMessages(userId) {
            // Listen to your private user channel
            window.Echo.private(`user.${userId}`)
                .listen('.MessageSent', (e) => {
                    this.incrementUnread();
                })
                .listen('.MessageRead', (e) => {
                    this.decrementUnread();
                });
        },

        stopListening(userId) {
            window.Echo.leave(`user.${userId}`);
        },
    },
});
