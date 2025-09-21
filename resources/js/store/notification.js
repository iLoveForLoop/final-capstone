// resources/js/store/notification.js
import { usePage } from '@inertiajs/vue3';
import { defineStore } from 'pinia';
import { ref } from 'vue';

export const useNotificationStore = defineStore('notification', () => {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const bookingUnreadCount = ref(0);
    const channel = ref(null);

    const { props } = usePage();

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

    // Initialize from Inertia props (only if available)
    const initializeNotifications = () => {
        if (props.notifications) {
            notifications.value = props.notifications;
        }
        if (props.unreadNotificationCount !== undefined) {
            unreadCount.value = props.unreadNotificationCount;
        }
        bookingUnreadCount.value = notifications.value.filter(
            (n) => n.type === 'booking_received' && !n.read,
        ).length;
    };

    // Listen for real-time notifications
    const listenForNotifications = () => {
        const user = props.auth?.user;
        const vendor = user?.vendor;

        if (!vendor || !window.Echo) {
            console.warn('Vendor not found or Echo not initialized');
            return;
        }

        console.log('Listening on channel: vendor.' + vendor.id);

        channel.value = window.Echo.private(`vendor.${vendor.id}`)
            .listen('.notification.created', (data) => {
                console.log('New notification received:', data);

                const newNotification = {
                    id: data.id,
                    title: data.title,
                    text: data.message,
                    time: 'Just now',
                    read: false,
                    type: data.type,
                    priority: data.priority,
                    action_url: data.action_url,
                    icon: getNotificationIcon(data.type),
                    color: getNotificationColor(data.type, data.priority),
                };

                notifications.value.unshift(newNotification);
                unreadCount.value++;

                if (newNotification.type === 'booking_received') {
                    bookingUnreadCount.value++;
                }

                showBrowserNotification(newNotification);
                playNotificationSound();

                if (notifications.value.length > 50) {
                    notifications.value = notifications.value.slice(0, 50);
                }
            })
            .error((error) => {
                console.error('Channel subscription error:', error);
            });
    };

    // Show browser notification
    const showBrowserNotification = (notification) => {
        if (Notification.permission === 'granted') {
            new Notification(notification.title, {
                body: notification.text,
                icon: '/favicon.ico',
                tag: notification.id,
            });
        }
    };

    // Play sound
    const playNotificationSound = () => {
        try {
            const audio = new Audio('/sounds/notif.mp3');
            audio.volume = 0.3;
            audio
                .play()
                .catch((e) =>
                    console.log('Could not play notification sound:', e),
                );
        } catch (error) {
            console.log('Notification sound not available');
        }
    };

    // Request browser notification permission
    const requestNotificationPermission = () => {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    };

    // Mark as read
    const markAsRead = (id) => {
        const n = notifications.value.find((n) => n.id === id);
        if (n && !n.read) {
            n.read = true;
            unreadCount.value = Math.max(0, unreadCount.value - 1);

            if (n.type === 'booking_received') {
                bookingUnreadCount.value = Math.max(
                    0,
                    bookingUnreadCount.value - 1,
                );
            }
        }
    };

    // Mark all as read
    const markAllAsRead = () => {
        notifications.value.forEach((n) => {
            if (!n.read) {
                if (n.type === 'booking_received') {
                    bookingUnreadCount.value = Math.max(
                        0,
                        bookingUnreadCount.value - 1,
                    );
                }
                n.read = true;
            }
        });
        unreadCount.value = 0;
    };

    // Remove notification
    const removeNotification = (id) => {
        const index = notifications.value.findIndex((n) => n.id === id);
        if (index > -1) {
            const n = notifications.value[index];
            if (!n.read) {
                unreadCount.value = Math.max(0, unreadCount.value - 1);
                if (n.type === 'booking_received') {
                    bookingUnreadCount.value = Math.max(
                        0,
                        bookingUnreadCount.value - 1,
                    );
                }
            }
            notifications.value.splice(index, 1);
        }
    };

    // Cleanup Echo
    const cleanup = () => {
        if (channel.value) {
            window.Echo.leave(`vendor.${props.auth?.user?.vendor?.id}`);
        }
    };

    async function fetchAllNotifications() {
        try {
            const response = await axios.get('/vendor/notifications');
            notifications.value = response.data.data || [];
            // unreadCount.value = notifications.value.filter(
            //     (n) => !n.read,
            // ).length;
            // bookingUnreadCount.value = notifications.value.filter(
            //     (n) => n.type === 'booking_received' && !n.read,
            // ).length;
        } catch (error) {
            console.error('Failed to fetch notifications:', error);
        }
    }

    async function markAllAsReadBackend() {
        try {
            await axios.post('/vendor/notifications/mark-read');
            notifications.value.forEach((n) => (n.read = true));
            unreadCount.value = 0;
            bookingUnreadCount.value = 0;
        } catch (error) {
            console.error('Failed to mark all as read:', error);
        }
    }

    return {
        // state
        notifications,
        unreadCount,
        bookingUnreadCount,

        // actions
        fetchAllNotifications, // 👈 new
        markAllAsReadBackend,
        initializeNotifications,
        listenForNotifications,
        requestNotificationPermission,
        markAsRead,
        markAllAsRead,
        removeNotification,
        cleanup,
    };
});
