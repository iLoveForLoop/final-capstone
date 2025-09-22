// resources/js/store/notification.js
import { usePage } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import { computed, ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const bookingUnreadCount = ref(0);
    const channel = ref(null);
    const isInitialized = ref(false);
    const isLoading = ref(false);
    const error = ref(null);

    const { props } = usePage();

    // Computed properties for better reactivity
    const hasUnreadNotifications = computed(() => unreadCount.value > 0);
    const hasUnreadBookings = computed(() => bookingUnreadCount.value > 0);
    const recentNotifications = computed(() => notifications.value.slice(0, 5));

    // Icon mapping
    const getNotificationIcon = (type) => {
        const iconMap = {
            booking_received: 'calendar-plus',
            booking_completed: 'check-circle',
            booking_cancelled: 'x-circle',
            booking_confirmed: 'calendar-check',
            service_updated: 'settings',
            payment_received: 'credit-card',
            review_received: 'star',
            system_alert: 'alert-circle',
        };
        return iconMap[type] || 'bell';
    };

    // Color mapping
    const getNotificationColor = (type, priority) => {
        if (priority === 'high') return 'red';

        const colorMap = {
            booking_received: 'blue',
            booking_completed: 'green',
            booking_cancelled: 'red',
            booking_confirmed: 'amber',
            service_updated: 'purple',
            payment_received: 'green',
            review_received: 'yellow',
            system_alert: 'gray',
        };
        return colorMap[type] || 'gray';
    };

    // Calculate counts from notifications array
    const recalculateCounts = () => {
        const unread = notifications.value.filter((n) => !n.read);
        unreadCount.value = unread.length;
        bookingUnreadCount.value = unread.filter(
            (n) => n.type === 'booking_received',
        ).length;
    };

    // Initialize notifications by fetching from backend
    const initializeNotifications = async () => {
        if (isInitialized.value) return;

        try {
            isLoading.value = true;
            error.value = null;

            await fetchAllNotifications();

            // Only initialize real-time listening after successful fetch
            listenForNotifications();
            // requestNotificationPermission();

            isInitialized.value = true;
        } catch (err) {
            error.value = 'Failed to initialize notifications';
            console.error('Notification initialization failed:', err);
        } finally {
            isLoading.value = false;
        }
    };

    // Fetch notifications from backend
    const fetchAllNotifications = async () => {
        try {
            isLoading.value = true;
            error.value = null;

            const response = await window.axios.get('/vendor/notifications');

            if (response.data && Array.isArray(response.data.data)) {
                notifications.value = response.data.data.map(
                    (notification) => ({
                        id: notification.id,
                        title: notification.title,
                        text: notification.message || notification.text,
                        time: formatNotificationTime(notification.created_at),
                        read: notification.read_at !== null,
                        type: notification.type,
                        priority: notification.priority || 'normal',
                        action_url: notification.action_url,
                        icon: getNotificationIcon(notification.type),
                        color: getNotificationColor(
                            notification.type,
                            notification.priority,
                        ),
                        created_at: notification.created_at,
                    }),
                );

                recalculateCounts();
            }
        } catch (err) {
            error.value = 'Failed to fetch notifications';
            console.error('Failed to fetch notifications:', err);
            // Don't throw, just log the error
        } finally {
            isLoading.value = false;
        }
    };

    // Format notification time
    const formatNotificationTime = (timestamp) => {
        if (!timestamp) return 'Just now';

        try {
            const date = new Date(timestamp);
            const now = new Date();
            const diffInMinutes = Math.floor((now - date) / (1000 * 60));

            if (diffInMinutes < 1) return 'Just now';
            if (diffInMinutes < 60) return `${diffInMinutes}m ago`;

            const diffInHours = Math.floor(diffInMinutes / 60);
            if (diffInHours < 24) return `${diffInHours}h ago`;

            const diffInDays = Math.floor(diffInHours / 24);
            if (diffInDays < 7) return `${diffInDays}d ago`;

            return date.toLocaleDateString();
        } catch (err) {
            console.warn('Error formatting notification time:', err);
            return 'Recently';
        }
    };

    // Listen for real-time notifications
    const listenForNotifications = () => {
        const user = props.auth?.user;
        const vendor = user?.vendor;

        if (!vendor) {
            console.warn('Vendor not found, skipping real-time notifications');
            return;
        }

        if (!window.Echo) {
            console.warn(
                'Echo not initialized, skipping real-time notifications',
            );
            return;
        }

        try {
            const channelName = `vendor.${vendor.id}`;

            channel.value = window.Echo.private(channelName)
                .listen('.notification.created', (data) => {
                    console.log('listening');
                    console.log('New notification received:', data);
                    handleNewNotification(data);
                })
                .error((error) => {
                    console.error('Channel subscription error:', error);
                });

            console.log(`Listening on channel: ${channelName}`);
        } catch (err) {
            console.error('Failed to setup real-time notifications:', err);
        }
    };

    // Handle new notification from real-time channel
    const handleNewNotification = (data) => {
        try {
            const newNotification = {
                id: data.id,
                title: data.title,
                text: data.message,
                time: 'Just now',
                read: false,
                type: data.type,
                priority: data.priority || 'normal',
                action_url: data.action_url,
                icon: getNotificationIcon(data.type),
                color: getNotificationColor(data.type, data.priority),
                created_at: new Date().toISOString(),
            };

            // Add to beginning of notifications array
            notifications.value.unshift(newNotification);

            // Update counts
            unreadCount.value++;
            if (newNotification.type === 'booking_received') {
                bookingUnreadCount.value++;
            }

            // Show browser notification and play sound
            showBrowserNotification(newNotification);
            playNotificationSound();

            // Limit notifications array size
            if (notifications.value.length > 100) {
                notifications.value = notifications.value.slice(0, 100);
            }
        } catch (err) {
            console.error('Error handling new notification:', err);
        }
    };

    // Show browser notification
    const showBrowserNotification = (notification) => {
        if (Notification.permission === 'granted') {
            try {
                new Notification(notification.title, {
                    body: notification.text,
                    icon: '/favicon.ico',
                    tag: `notification-${notification.id}`,
                    badge: '/favicon.ico',
                    requireInteraction: false,
                });
            } catch (err) {
                console.warn('Failed to show browser notification:', err);
            }
        }
    };

    // Play notification sound
    const playNotificationSound = () => {
        try {
            const audio = new Audio('/sounds/notif.mp3');
            audio.volume = 0.3;
            audio.play().catch((err) => {
                console.log('Could not play notification sound:', err);
            });
        } catch (error) {
            console.log('Notification sound not available:', error);
        }
    };

    // Request browser notification permission
    const requestNotificationPermission = () => {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission()
                .then((permission) => {
                    console.log('Notification permission:', permission);
                })
                .catch((err) => {
                    console.warn(
                        'Failed to request notification permission:',
                        err,
                    );
                });
        }
    };

    // Mark notification as read
    const markAsRead = async (id) => {
        const notification = notifications.value.find((n) => n.id === id);
        if (!notification || notification.read) return;

        try {
            // Optimistically update UI
            notification.read = true;
            unreadCount.value = Math.max(0, unreadCount.value - 1);

            if (notification.type === 'booking_received') {
                bookingUnreadCount.value = Math.max(
                    0,
                    bookingUnreadCount.value - 1,
                );
            }

            // Send to backend
            await window.axios.post(`/vendor/notifications/${id}/read`);
        } catch (err) {
            // Revert on error
            notification.read = false;
            recalculateCounts();
            console.error('Failed to mark notification as read:', err);
        }
    };

    // Mark all notifications as read
    const markAllAsRead = () => {
        notifications.value.forEach((n) => {
            if (!n.read) {
                n.read = true;
            }
        });
        unreadCount.value = 0;
        bookingUnreadCount.value = 0;
    };

    // Mark all as read on backend
    const markAllAsReadBackend = async () => {
        try {
            await window.axios.post('/vendor/notifications/mark-read');
            markAllAsRead();
        } catch (error) {
            console.error('Failed to mark all notifications as read:', error);
            throw error;
        }
    };

    // Remove notification
    const removeNotification = async (id) => {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index === -1) return;

        const notification = notifications.value[index];

        try {
            // Optimistically remove from UI
            notifications.value.splice(index, 1);

            if (!notification.read) {
                unreadCount.value = Math.max(0, unreadCount.value - 1);
                if (notification.type === 'booking_received') {
                    bookingUnreadCount.value = Math.max(
                        0,
                        bookingUnreadCount.value - 1,
                    );
                }
            }

            // Send to backend (optional - depends on your backend implementation)
            await window.axios.delete(`/vendor/notifications/${id}`);
        } catch (err) {
            // Revert on error
            notifications.value.splice(index, 0, notification);
            recalculateCounts();
            console.error('Failed to remove notification:', err);
        }
    };

    // Cleanup resources
    const cleanup = () => {
        if (channel.value) {
            const vendor = props.auth?.user?.vendor;
            if (vendor) {
                try {
                    window.Echo.leave(`vendor.${vendor.id}`);
                } catch (err) {
                    console.warn('Failed to leave Echo channel:', err);
                }
            }
            channel.value = null;
        }
    };

    // Reset store state
    const reset = () => {
        cleanup();
        notifications.value = [];
        unreadCount.value = 0;
        bookingUnreadCount.value = 0;
        isInitialized.value = false;
        isLoading.value = false;
        error.value = null;
    };

    return {
        // State
        notifications,
        unreadCount,
        bookingUnreadCount,
        isInitialized,
        isLoading,
        error,

        // Computed
        hasUnreadNotifications,
        hasUnreadBookings,
        recentNotifications,

        // Actions
        initializeNotifications,
        fetchAllNotifications,
        markAsRead,
        markAllAsRead,
        markAllAsReadBackend,
        removeNotification,
        requestNotificationPermission,
        cleanup,
        reset,

        // Utilities (exposed for component use)
        getNotificationIcon,
        getNotificationColor,
    };
});
