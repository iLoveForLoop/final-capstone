// stores/navbarStore.js
import axios from 'axios';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useNavbarStore = defineStore('navbar', () => {
    // State
    const conversations = ref([]);
    const notifications = ref([]);
    const unreadCount = ref(0);
    const isLoading = ref(false);
    const isLoadingNotifications = ref(false);
    const lastLoaded = ref(null);
    const notificationIds = ref(new Set());

    // FIXED: Computed properties - Use BOTH sources properly
    const unreadNotifications = computed(() => {
        // Use the server count as primary source
        // But if server count is 0 but we have unread notifications locally, use local count
        const localUnread = notifications.value.filter(
            (n) => !n.is_read,
        ).length;

        if (unreadCount.value > 0) {
            return unreadCount.value;
        }

        // If server says 0 but we have unread locally, trust the local count
        return localUnread > 0 ? localUnread : 0;
    });

    const unreadMessages = computed(() => {
        return conversations.value.reduce(
            (total, conv) => total + (conv.unread_count || 0),
            0,
        );
    });

    const processedMessages = computed(() => {
        return conversations.value.map((conv) => ({
            id: conv.id,
            sender: conv.title,
            avatar: conv.otherUserAvatar,
            initials: getInitials(conv.title),
            message: conv.last_message?.content || 'No messages yet',
            time: formatTimestamp(conv.last_message?.created_at),
            read: conv.unread_count === 0,
            online: false,
            chatMessages: [],
        }));
    });

    // Utility functions
    const getInitials = (name) => {
        if (!name) return 'U';
        return name
            .split(' ')
            .map((n) => n[0])
            .join('')
            .substring(0, 2)
            .toUpperCase();
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

    // Actions
    const loadUnreadCount = async () => {
        try {
            const response = await axios.get(
                '/client/notifications/unread-count',
            );
            unreadCount.value = response.data.unread_count;
        } catch (error) {
            console.error('Failed to load unread count:', error);
        }
    };

    const loadConversations = async () => {
        try {
            isLoading.value = true;
            const response = await axios.get('/conversations');
            conversations.value = response.data.conversations;
        } catch (error) {
            console.error('Failed to load conversations:', error);
        } finally {
            isLoading.value = false;
        }
    };

    const loadNotifications = async () => {
        try {
            isLoadingNotifications.value = true;
            const response = await axios.get('/client/notifications', {
                params: { limit: 10 },
            });

            // Clear existing data
            notifications.value = [];
            notificationIds.value.clear();

            // Add new notifications
            response.data.data.forEach((notification) => {
                if (!notificationIds.value.has(notification.id)) {
                    notifications.value.push(notification);
                    notificationIds.value.add(notification.id);
                }
            });

            // Debug: Show what we have
            const localUnread = notifications.value.filter(
                (n) => !n.is_read,
            ).length;
        } catch (error) {
            console.error('Failed to load notifications:', error);
        } finally {
            isLoadingNotifications.value = false;
        }
    };

    const markNotificationRead = async (notificationId) => {
        try {
            await axios.patch(`/client/notifications/${notificationId}/read`);

            const notification = notifications.value.find(
                (n) => n.id === notificationId,
            );
            if (notification) {
                notification.read_at = new Date().toISOString();
                notification.is_read = true;
            }

            // Update unread count from server
            await loadUnreadCount();
        } catch (error) {
            console.error('Failed to mark notification as read:', error);
        }
    };

    const markAllNotificationsAsRead = async () => {
        try {
            await axios.post('/client/notifications/mark-all-read');

            notifications.value.forEach((notification) => {
                notification.read_at = new Date().toISOString();
                notification.is_read = true;
            });

            unreadCount.value = 0;
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
        }
    };

    // FIXED: When real-time notification comes in, sync with server count
    const addNotification = async (notification) => {
        // Check if notification already exists
        if (notificationIds.value.has(notification.id)) {
            return;
        }

        // Add to notifications array and track ID
        notifications.value.unshift(notification);
        notificationIds.value.add(notification.id);

        // FIXED: Always sync with server to get accurate count
        await loadUnreadCount();

        // Keep only latest 10 notifications
        if (notifications.value.length > 10) {
            const removed = notifications.value.pop();
            notificationIds.value.delete(removed.id);
        }
    };

    const updateConversation = (conversationId, updates) => {
        const index = conversations.value.findIndex(
            (c) => c.id === conversationId,
        );
        if (index !== -1) {
            conversations.value[index] = {
                ...conversations.value[index],
                ...updates,
            };
        }
    };

    const refreshData = async () => {
        await loadConversations();
    };

    const initialData = async () => {
        // Load conversations and notifications in parallel
        await Promise.all([
            loadConversations(),
            loadNotifications(),
            loadUnreadCount(),
        ]);
    };

    // Initialize
    const initialize = async () => {
        await initialData();
    };

    return {
        // State
        conversations,
        notifications,
        unreadCount,
        isLoading,
        isLoadingNotifications,

        // Computed
        unreadNotifications,
        unreadMessages,
        processedMessages,

        // Actions
        loadUnreadCount,
        loadConversations,
        loadNotifications,
        markNotificationRead,
        markAllNotificationsAsRead,
        addNotification,
        updateConversation,
        refreshData,
        initialize,

        // Utility functions
        getInitials,
        formatTimestamp,
    };
});
