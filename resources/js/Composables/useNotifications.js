// resources/js/composables/useNotifications.js
import { usePage } from '@inertiajs/vue3';
import { onMounted, onUnmounted, ref } from 'vue';

export function useNotifications() {
    const notifications = ref([]);
    const unreadCount = ref(0);
    const bookingUnreadCount = ref(0); // 🔹 track booking_received only
    const channel = ref(null);

    const { props } = usePage();

    // Get notification icon based on type
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

    // Get notification color based on type and priority
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

    // Initialize notifications from page props
    const initializeNotifications = () => {
        // console.log(props.notifications);

        if (props.notifications) {
            notifications.value = props.notifications;
        }
        if (props.unreadNotificationCount !== undefined) {
            unreadCount.value = props.unreadNotificationCount;
        }

        // 🔹 initialize booking-specific count
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

        // console.log('Listening on channel: vendor.' + vendor.id);

        channel.value = window.Echo.private(`vendor.${vendor.id}`)
            .listen('.notification.created', (data) => {
                // console.log('New notification received:', data);

                // Add the new notification to the beginning of the array
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

                // 🔹 update booking-specific counter
                console.log('type outside: ', newNotification.type);

                if (newNotification.type === 'booking_received') {
                    console.log('type inside: ', newNotification.type);
                    bookingUnreadCount.value++;
                }

                // Show browser notification if permission granted
                showBrowserNotification(newNotification);

                // Play notification sound (optional)
                playNotificationSound();

                // Limit notifications array to prevent memory issues
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

    // Play notification sound
    const playNotificationSound = () => {
        try {
            const audio = new Audio('/sounds/notif.mp3'); // Add a notification sound file in public/sounds/
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

    // Request notification permission
    const requestNotificationPermission = () => {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }
    };

    // Mark notification as read
    const markAsRead = (notificationId) => {
        const notification = notifications.value.find(
            (n) => n.id === notificationId,
        );
        if (notification && !notification.read) {
            notification.read = true;
            unreadCount.value = Math.max(0, unreadCount.value - 1);

            // 🔹 adjust booking-specific counter if applicable
            if (notification.type === 'booking_received') {
                bookingUnreadCount.value = Math.max(
                    0,
                    bookingUnreadCount.value - 1,
                );
            }
        }
    };

    // Mark all notifications as read
    const markAllAsRead = () => {
        notifications.value.forEach((notification) => {
            if (!notification.read) {
                if (notification.type === 'booking_received') {
                    bookingUnreadCount.value = Math.max(
                        0,
                        bookingUnreadCount.value - 1,
                    );
                }
                notification.read = true;
            }
        });
        unreadCount.value = 0;
    };

    // Remove notification
    const removeNotification = (notificationId) => {
        const index = notifications.value.findIndex(
            (n) => n.id === notificationId,
        );
        if (index > -1) {
            const notification = notifications.value[index];
            if (!notification.read) {
                unreadCount.value = Math.max(0, unreadCount.value - 1);

                // 🔹 adjust booking-specific counter if applicable
                if (notification.type === 'booking_received') {
                    bookingUnreadCount.value = Math.max(
                        0,
                        bookingUnreadCount.value - 1,
                    );
                }
            }
            notifications.value.splice(index, 1);
        }
    };

    // Cleanup
    const cleanup = () => {
        if (channel.value) {
            window.Echo.leave(`vendor.${props.auth?.user?.vendor?.id}`);
        }
    };

    onMounted(() => {
        initializeNotifications();
        listenForNotifications();
        requestNotificationPermission();
    });

    onUnmounted(() => {
        cleanup();
    });

    return {
        notifications,
        unreadCount,
        bookingUnreadCount, // 🔹 expose booking-specific count
        markAsRead,
        markAllAsRead,
        removeNotification,
        getNotificationIcon,
        getNotificationColor,
    };
}
