<script setup>
import { Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref, onMounted, onUnmounted, computed, nextTick } from 'vue';
import { useNavbarStore } from '@/store/navbarStore';
import MyDropdown from './MyDropdown.vue';
import {
    Bell,
    MessageSquare,
    Menu,
    X,
    Home,
    Calendar,
    Heart,
    LogOut,
    Settings,
    User,
    Search,
    ChevronDown,
    CheckCircle,
    Send,
    CheckCheck,
    Check,
} from 'lucide-vue-next';
import emitter from '@/utils/eventBus';
import axios from 'axios';
import DesktopNavs from './ClientNavbar/DesktopNavs.vue';
import ChatWindow from './ClientNavbar/ChatWindow.vue';
import { useUIStore } from '@/store/ui';

const page = usePage();
const navbarStore = useNavbarStore();
const ui = useUIStore();

// Local UI state
const isDropdownShowing = ref(false);
const isMobileMenuOpen = ref(false);
const isNotificationsOpen = ref(false);
const isMessagesOpen = ref(false);
const openChats = ref([]);

// ===== Messages drawer (keep wrapper mounted for leave) =====
const isMessagesDrawerOpen = ref(false);
const isMessagesDrawerActive = ref(false);
const panelEl = ref(null);

// ===== Notifications drawer (same behavior) =====
const isNotificationsDrawerOpen = ref(false);
const isNotificationsDrawerActive = ref(false);
const notifPanelEl = ref(null);

// Store-mapped state
const conversations = computed(() => navbarStore.conversations);
const notifications = computed(() => navbarStore.notifications);
const isLoading = computed(() => navbarStore.isLoading);
const isLoadingNotifications = computed(
    () => navbarStore.isLoadingNotifications,
);
const unreadCount = computed(() => navbarStore.unreadCount);
const unreadNotifications = computed(() => navbarStore.unreadNotifications);
const unreadMessages = computed(() => navbarStore.unreadMessages);
const processedMessages = computed(() => navbarStore.processedMessages);

const loadInitialData = async () => {
    if (!page.props.auth.user) return;
    await navbarStore.initialize();
};

const loadChatMessages = async (conversationId) => {
    try {
        const response = await axios.get(
            `/conversations/${conversationId}/messages`,
        );
        const sorted = response.data.messages.sort(
            (a, b) => new Date(a.created_at) - new Date(b.created_at),
        );
        return sorted.map((msg) => ({
            id: msg.id,
            text: msg.content,
            sent: msg.is_own,
            time: formatTime(msg.created_at),
        }));
    } catch (error) {
        console.error('Failed to load messages:', error);
        return [];
    }
};

// Open chat window; auto-close drawers/popovers (leave animations will play)
const openChatWindow = async (message) => {
    ui.isInMessage = true;
    navbarStore.updateConversation(message.id, { unread_count: 0 });
    // Close any popovers and drawers
    isMessagesOpen.value = false;
    isNotificationsOpen.value = false;
    if (isMessagesDrawerOpen.value) isMessagesDrawerOpen.value = false;
    if (isNotificationsDrawerOpen.value)
        isNotificationsDrawerOpen.value = false;

    const existingChat = openChats.value.find((chat) => chat.id === message.id);
    if (existingChat) {
        existingChat.minimized = false;
        return;
    }

    const chatMessages = await loadChatMessages(message.id);

    if (openChats.value.length >= 3) {
        openChats.value.shift();
    }

    openChats.value.push({
        ...message,
        minimized: false,
        newMessage: '',
        chatMessages,
        conversationId: message.id,
    });

    nextTick(() => {
        scrollChatToBottom();
    });

    subscribeToConversation(message.id);
};

const subscribeToConversation = (conversationId) => {
    if (!window.Echo) return;

    window.Echo.private(`conversation.${conversationId}`).listen(
        '.MessageSent',
        async (e) => {
            const chat = openChats.value.find(
                (c) => c.conversationId === conversationId,
            );

            if (chat) {
                chat.chatMessages.push({
                    id: e.message.id,
                    text: e.message.content,
                    sent: e.message.is_own,
                    time: formatTime(e.message.created_at),
                });

                nextTick(() => scrollChatToBottom());

                if (!e.message.is_own) {
                    await axios.post(
                        `/conversations/${conversationId}/mark-as-read`,
                    );
                    navbarStore.updateConversation(conversationId, {
                        unread_count: 0,
                    });
                }
            } else {
                const conversation = navbarStore.conversations.find(
                    (c) => c.id === conversationId,
                );
                if (conversation && !e.message.is_own) {
                    navbarStore.updateConversation(conversationId, {
                        last_message: e.message,
                        unread_count: (conversation.unread_count || 0) + 1,
                    });
                }
            }
        },
    );
};

const subscribeToNotifications = () => {
    if (!window.Echo || !page.props.auth.user) return;

    const clientId = page.props.auth.clientId;

    window.Echo.private(`client.${clientId}`)
        .listen('.notification.created', async (e) => {
            await navbarStore.addNotification({
                id: e.id,
                type: e.type,
                title: e.title,
                message: e.message,
                priority: e.priority,
                action_url: e.action_url,
                created_at: e.created_at,
                time_ago: e.time_ago,
                read_at: null,
                is_read: false,
            });
            playNotificationSound();
        })
        .error((error) => {
            console.error('Echo subscription error:', error);
        });
};

const playNotificationSound = () => {
    /* optional */
};

const closeChatWindow = (chatId) => {
    ui.isInMessage = false;
    const index = openChats.value.findIndex((chat) => chat.id === chatId);
    if (index !== -1) {
        const chat = openChats.value[index];
        if (window.Echo && chat.conversationId) {
            window.Echo.leave(`conversation.${chat.conversationId}`);
        }
        openChats.value.splice(index, 1);
    }
};

const toggleChatWindow = (chatId) => {
    const chat = openChats.value.find((chat) => chat.id === chatId);
    if (chat) {
        chat.minimized = !chat.minimized;
        if (!chat.minimized) {
            nextTick(() => {
                scrollChatToBottom();
            });
        }
    }
};

const sendChatMessage = async (chatId) => {
    const chat = openChats.value.find((chat) => chat.id === chatId);
    if (!chat || !chat.newMessage?.trim() || !chat.conversationId) return;

    const messageContent = chat.newMessage.trim();
    const tempId = Date.now();

    const optimisticMessage = {
        id: tempId,
        text: messageContent,
        sent: true,
        time: new Date().toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
        }),
        sending: true,
    };

    chat.chatMessages.push(optimisticMessage);
    chat.newMessage = '';

    nextTick(() => scrollChatToBottom());

    try {
        const response = await axios.post(
            `/conversations/${chat.conversationId}/messages`,
            {
                content: messageContent,
                type: 'text',
            },
        );

        const messageIndex = chat.chatMessages.findIndex(
            (m) => m.id === tempId,
        );
        if (messageIndex !== -1) {
            chat.chatMessages[messageIndex] = {
                id: response.data.message.id,
                text: response.data.message.content,
                sent: true,
                time: formatTime(response.data.message.created_at),
            };
        }

        const conversation = navbarStore.conversations.find(
            (c) => c.id === chat.conversationId,
        );
        if (conversation) {
            navbarStore.updateConversation(chat.conversationId, {
                last_message: response.data.message,
            });
        }
    } catch (error) {
        console.error('Failed to send message:', error);
        const messageIndex = chat.chatMessages.findIndex(
            (m) => m.id === tempId,
        );
        if (messageIndex !== -1) {
            chat.chatMessages.splice(messageIndex, 1);
        }
    }
};

const scrollChatToBottom = () => {
    const chatElements = document.querySelectorAll('.chat-body .h-64');
    chatElements.forEach((element) => {
        element.scrollTop = element.scrollHeight;
    });
};

const closeDrawers = (event) => {
    if (!event.target.closest('.drawer-container')) {
        isNotificationsOpen.value = false;
        isMessagesOpen.value = false;
    }
};

const getInitials = (name) => {
    return navbarStore.getInitials(name);
};

const formatTimestamp = (timestamp) => {
    return navbarStore.formatTimestamp(timestamp);
};

const formatTime = (timestamp) => {
    if (!timestamp) return '';
    return new Date(timestamp).toLocaleTimeString([], {
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getNotificationIcon = (type) => {
    const iconMap = {
        booking_confirmed_client: CheckCircle,
        booking_submitted: Calendar,
        booking_updated: Calendar,
        booking_in_progress: Calendar,
        booking_completed_client: CheckCheck,
        booking_cancelled_client: X,
        payment_confirmed: CheckCircle,
        vendor_message: MessageSquare,
        booking_reminder: Bell,
    };
    return iconMap[type] || Bell;
};

const getNotificationIconColor = (type) => {
    const colorMap = {
        booking_confirmed_client: 'text-green-600',
        booking_submitted: 'text-blue-600',
        booking_updated: 'text-blue-600',
        booking_in_progress: 'text-orange-600',
        booking_completed_client: 'text-green-600',
        booking_cancelled_client: 'text-red-600',
        payment_confirmed: 'text-green-600',
        vendor_message: 'text-purple-600',
        booking_reminder: 'text-yellow-600',
    };
    return colorMap[type] || 'text-gray-600';
};

// ===== Body scroll lock shared by both drawers =====
const originalOverflow = ref('');
const lockScroll = (lock) => {
    const body = document.body;
    if (lock) {
        originalOverflow.value = body.style.overflow;
        body.style.overflow = 'hidden';
    } else {
        body.style.overflow = originalOverflow.value || '';
    }
};

// ===== Messages drawer lifecycle =====
const openMessagesDrawer = async () => {
    // Close mini popover and the notifications popover/drawer
    isMessagesOpen.value = false;
    isNotificationsOpen.value = false;
    if (isNotificationsDrawerOpen.value)
        isNotificationsDrawerOpen.value = false;

    isMessagesDrawerActive.value = true;
    lockScroll(true);
    await nextTick();
    isMessagesDrawerOpen.value = true;
    await nextTick();
    panelEl.value?.focus?.();
};
const closeMessagesDrawer = () => {
    isMessagesDrawerOpen.value = false;
};
const onDrawerAfterLeave = () => {
    isMessagesDrawerActive.value = false;
    lockScroll(false);
};

// ===== Notifications drawer lifecycle =====
const openNotificationsDrawer = async () => {
    // Close mini popover and any messages popover/drawer
    isNotificationsOpen.value = false;
    isMessagesOpen.value = false;
    if (isMessagesDrawerOpen.value) isMessagesDrawerOpen.value = false;

    isNotificationsDrawerActive.value = true;
    lockScroll(true);
    await nextTick();
    isNotificationsDrawerOpen.value = true;
    await nextTick();
    notifPanelEl.value?.focus?.();
};
const closeNotificationsDrawer = () => {
    isNotificationsDrawerOpen.value = false;
};
const onNotifDrawerAfterLeave = () => {
    isNotificationsDrawerActive.value = false;
    lockScroll(false);
};

// ESC to close whichever is open
const onKeydown = (e) => {
    if (e.key === 'Escape') {
        if (isMessagesDrawerOpen.value) closeMessagesDrawer();
        if (isNotificationsDrawerOpen.value) closeNotificationsDrawer();
    }
};

// Lifecycle
onMounted(() => {
    ui.isInMessage = false;
    document.addEventListener('click', closeDrawers);
    document.addEventListener('keydown', onKeydown);

    if (page.props.auth.user) {
        loadInitialData();
        subscribeToNotifications();

        emitter.on('chat-vendor', async (payload) => {
            const newConversationData = {
                participants: [
                    Number(page.props.auth.user.id),
                    Number(payload),
                ],
            };

            try {
                const res = await axios.post(
                    route('conversation.create', newConversationData),
                );
                const conv = res.data;

                const message = {
                    // id: conv.id,
                    // sender: conv.title,
                    // initials: getInitials(conv.title),
                    // message: conv.last_message?.content || 'No messages yet',
                    // time: formatTimestamp(conv.last_message?.created_at),
                    // read: conv.unread_count === 0,
                    // online: false,
                    // chatMessages: [],

                    id: conv.id,
                    sender: conv.title,
                    avatar: conv.otherUserAvatar,
                    initials: getInitials(conv.title),
                    message: conv.last_message?.content || 'No messages yet',
                    time: formatTimestamp(conv.last_message?.created_at),
                    read: conv.unread_count === 0,
                    online: false,
                    chatMessages: [],
                };

                openChatWindow(message);
            } catch (error) {
                console.log('Error creating conversation: ', error.message);
            }
        });

        const interval = setInterval(() => {
            navbarStore.refreshData();
        }, 60000);

        onUnmounted(() => {
            clearInterval(interval);
        });
    }
});

onUnmounted(() => {
    document.removeEventListener('click', closeDrawers);
    document.removeEventListener('keydown', onKeydown);
    lockScroll(false);

    openChats.value.forEach((chat) => {
        if (window.Echo && chat.conversationId) {
            window.Echo.leave(`conversation.${chat.conversationId}`);
        }
    });

    if (window.Echo && page.props.auth.user) {
        window.Echo.leave(`client.${page.props.auth.user.id}`);
    }

    emitter.off('chat-vendor');
});
</script>

<template>
    <div>
        <!-- Navbar -->
        <nav class="sticky top-0 z-30 border-b border-gray-100 bg-white/95 shadow-sm backdrop-blur-md">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex h-16 items-center justify-between">
                    <!-- Logo -->
                    <div class="flex flex-1 justify-start">
                        <Link href="/" class="group flex flex-shrink-0 items-center space-x-2">
                        <span
                            class="text-2xl font-bold text-gray-900 transition-colors duration-300 group-hover:text-yellow-400">
                            Eventory
                        </span>
                        </Link>
                    </div>

                    <!-- Desktop Navigation - Always Centered -->
                    <DesktopNavs />

                    <!-- Right Side Actions -->
                    <div class="flex flex-1 justify-end">
                        <div class="hidden items-center space-x-2 lg:flex">
                            <template v-if="page.props.auth.user">
                                <!-- Notifications -->
                                <div class="drawer-container relative">
                                    <button @click="
                                        isNotificationsOpen =
                                        !isNotificationsOpen;
                                    // optimize icons behavior:
                                    if (isNotificationsOpen) {
                                        isMessagesOpen = false;
                                        if (isMessagesDrawerOpen)
                                            isMessagesDrawerOpen = false;
                                        if (isNotificationsDrawerOpen)
                                            isNotificationsDrawerOpen = false; // ensure drawer closed when toggling popover
                                    }
                                    "
                                        class="relative rounded-lg p-2 text-gray-500 transition-all duration-200 hover:bg-gray-100 hover:text-gray-700">
                                        <Bell :size="20" />
                                        <span v-if="unreadNotifications > 0"
                                            class="absolute -right-1 -top-1 flex h-5 w-5 animate-pulse items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                            {{
                                                unreadNotifications > 9
                                                    ? '9+'
                                                    : unreadNotifications
                                            }}
                                        </span>
                                    </button>

                                    <!-- Notifications Popover -->
                                    <div v-if="isNotificationsOpen"
                                        class="absolute right-0 top-full z-50 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 bg-gray-50 p-4">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-semibold text-gray-900">
                                                    Notifications
                                                </h3>
                                                <button @click="
                                                    navbarStore.markAllNotificationsAsRead
                                                " class="text-xs font-medium text-purple-600 hover:text-purple-800">
                                                    Mark all read
                                                </button>
                                            </div>
                                        </div>
                                        <div class="max-h-96 overflow-y-auto">
                                            <div v-if="isLoadingNotifications" class="p-8 text-center">
                                                <div
                                                    class="mx-auto h-6 w-6 animate-spin rounded-full border-b-2 border-purple-500">
                                                </div>
                                                <p class="mt-2 text-sm text-gray-500">
                                                    Loading notifications...
                                                </p>
                                            </div>
                                            <div v-else-if="
                                                notifications.length === 0
                                            " class="p-8 text-center">
                                                <Bell :size="24" class="mx-auto mb-2 text-gray-400" />
                                                <p class="text-sm text-gray-500">
                                                    No notifications yet
                                                </p>
                                                <p class="mt-1 text-xs text-gray-400">
                                                    We'll notify you when
                                                    something arrives
                                                </p>
                                            </div>
                                            <div v-else v-for="notification in notifications" :key="notification.id"
                                                @click="
                                                    navbarStore.markNotificationRead(
                                                        notification.id,
                                                    )
                                                    "
                                                class="group cursor-pointer border-b border-gray-100 p-4 transition-colors duration-150 hover:bg-gray-50"
                                                :class="{
                                                    'bg-purple-50/50':
                                                        !notification.is_read,
                                                }">
                                                <div class="flex items-start space-x-3">
                                                    <component :is="getNotificationIcon(
                                                        notification.type,
                                                    )
                                                        " :size="18" class="mt-0.5 flex-shrink-0" :class="getNotificationIconColor(
                                                            notification.type,
                                                        )
                                                            " />
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <p class="truncate font-medium text-gray-900">
                                                                {{
                                                                    notification.title
                                                                }}
                                                            </p>
                                                            <span v-if="
                                                                !notification.is_read
                                                            "
                                                                class="h-2 w-2 flex-shrink-0 rounded-full bg-purple-600"></span>
                                                        </div>
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            {{
                                                                notification.message
                                                            }}
                                                        </p>
                                                        <div class="mt-2 flex items-center justify-between">
                                                            <p class="text-xs text-gray-400">
                                                                {{
                                                                    notification.time_ago
                                                                }}
                                                            </p>
                                                            <div
                                                                class="flex items-center space-x-1 opacity-0 transition-opacity group-hover:opacity-100">
                                                                <Check :size="12" class="text-gray-400" />
                                                                <span class="text-xs text-gray-400">Mark
                                                                    read</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Footer: View all -> Button opens slide-in drawer -->
                                        <div class="border-t border-gray-100 bg-gray-50 p-4">
                                            <button type="button" @click="openNotificationsDrawer"
                                                class="w-full text-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                                View all notifications
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Messages -->
                                <div class="drawer-container relative">
                                    <button @click="
                                        isMessagesOpen = !isMessagesOpen;
                                    // optimize icons behavior:
                                    if (isMessagesOpen) {
                                        isNotificationsOpen = false;
                                        if (isNotificationsDrawerOpen)
                                            isNotificationsDrawerOpen = false;
                                        if (isMessagesDrawerOpen)
                                            isMessagesDrawerOpen = false; // ensure drawer closed when toggling popover
                                    }
                                    "
                                        class="relative rounded-lg p-2 text-gray-500 transition-all duration-200 hover:bg-gray-100 hover:text-gray-700">
                                        <MessageSquare :size="20" />
                                        <span v-if="unreadMessages > 0"
                                            class="absolute -right-1 -top-1 flex h-5 w-5 animate-pulse items-center justify-center rounded-full bg-green-500 text-xs text-white">
                                            {{
                                                unreadMessages > 9
                                                    ? '9+'
                                                    : unreadMessages
                                            }}
                                        </span>
                                    </button>

                                    <!-- Messages Popover -->
                                    <div v-if="isMessagesOpen"
                                        class="absolute right-0 top-full z-50 mt-2 w-80 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                                        <div class="border-b border-gray-100 bg-gray-50 p-4">
                                            <div class="flex items-center justify-between">
                                                <h3 class="font-semibold text-gray-900">
                                                    Messages
                                                </h3>
                                            </div>
                                        </div>
                                        <div class="max-h-96 overflow-y-auto">
                                            <div v-if="isLoading" class="p-8 text-center">
                                                <div
                                                    class="mx-auto h-6 w-6 animate-spin rounded-full border-b-2 border-purple-500">
                                                </div>
                                                <p class="mt-2 text-sm text-gray-500">
                                                    Loading conversations...
                                                </p>
                                            </div>
                                            <div v-else-if="
                                                processedMessages.length ===
                                                0
                                            " class="p-8 text-center">
                                                <MessageSquare :size="24" class="mx-auto mb-2 text-gray-400" />
                                                <p class="text-sm text-gray-500">
                                                    No conversations yet
                                                </p>
                                            </div>
                                            <div v-else v-for="message in processedMessages" :key="message.id"
                                                @click="openChatWindow(message)"
                                                class="cursor-pointer border-b border-gray-100 p-4 transition-colors duration-150 hover:bg-gray-50"
                                                :class="{
                                                    'bg-blue-50/50':
                                                        !message.read,
                                                }">
                                                <div class="flex items-start space-x-3">
                                                    <div class="relative flex-shrink-0">
                                                        <div
                                                            class="flex h-9 w-9 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-gray-100 text-xs font-semibold text-white shadow-sm">
                                                            <img v-if="
                                                                message.avatar
                                                            " :src="message.avatar
                                                                " alt="User avatar"
                                                                class="h-full w-full object-cover" />
                                                            <span v-else
                                                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700">
                                                                {{
                                                                    message.initials
                                                                }}
                                                            </span>
                                                        </div>
                                                        <div v-if="
                                                            message.online
                                                        "
                                                            class="absolute bottom-0 right-0 h-3 w-3 rounded-full border-2 border-white bg-green-500 shadow-sm" />
                                                    </div>

                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center justify-between">
                                                            {{
                                                                console.log(
                                                                    'Message: ',
                                                                    message,
                                                                )
                                                            }}
                                                            <p class="truncate font-medium text-gray-900">
                                                                {{
                                                                    message.sender
                                                                }}
                                                            </p>
                                                            <div class="flex items-center space-x-1">
                                                                <span v-if="
                                                                    !message.read
                                                                "
                                                                    class="h-2 w-2 flex-shrink-0 rounded-full bg-blue-600"></span>
                                                                <span class="whitespace-nowrap text-xs text-gray-400">
                                                                    {{
                                                                        message.time
                                                                    }}
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <p class="mt-1 line-clamp-2 text-sm text-gray-600">
                                                            {{
                                                                message.message
                                                            }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Footer: View More -> Button opens slide-in drawer -->
                                        <div class="border-t border-gray-100 bg-gray-50 p-4">
                                            <button type="button" @click="openMessagesDrawer"
                                                class="w-full text-center text-sm font-medium text-purple-600 hover:text-purple-800">
                                                View more
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Dropdown -->
                                <MyDropdown />
                            </template>

                            <template v-else>
                                <!-- Login Button -->
                                <Link href="/login"
                                    class="px-4 py-2 font-medium text-gray-600 transition-all duration-200 hover:text-gray-900">
                                Log In
                                </Link>
                                <!-- Get Started Button -->
                                <Link href="/join"
                                    class="rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 px-5 py-2.5 font-semibold text-white shadow-md transition-all duration-300 hover:from-purple-500 hover:to-indigo-500 hover:shadow-lg">
                                Get Started
                                </Link>
                            </template>
                        </div>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="lg:hidden">
                        <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                            class="rounded-lg p-2 text-gray-600 transition-all duration-200 hover:bg-gray-100 hover:text-gray-900">
                            <Menu v-if="!isMobileMenuOpen" :size="24" />
                            <X v-else :size="24" />
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div v-if="isMobileMenuOpen" class="border-t border-gray-200 pb-4 pt-4 lg:hidden">
                    <div class="flex flex-col space-y-1">
                        <Link :href="page.props.auth.user ? '/client' : '/'" @click="isMobileMenuOpen = false"
                            class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                            :class="{
                                'bg-gray-50 text-gray-900':
                                    page.url === '/client' || page.url === '/',
                            }">
                        <Home :size="18" class="mr-3" />
                        <span>Home</span>
                        </Link>

                        <Link :href="page.props.auth.user
                            ? '/client/services'
                            : '/services'
                            " @click="isMobileMenuOpen = false"
                            class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                            :class="{
                                'bg-gray-50 text-gray-900':
                                    page.url.startsWith('/client/services') ||
                                    page.url.startsWith('/services'),
                            }">
                        <Search :size="18" class="mr-3" />
                        <span>Services</span>
                        </Link>

                        <template v-if="page.props.auth.user">
                            <Link href="/client/bookings" @click="isMobileMenuOpen = false"
                                class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                                :class="{
                                    'bg-gray-50 text-gray-900':
                                        page.url.startsWith('/client/bookings'),
                                }">
                            <Calendar :size="18" class="mr-3" />
                            <span>Bookings</span>
                            </Link>
                            <Link href="/client/events" @click="isMobileMenuOpen = false"
                                class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                                :class="{
                                    'bg-gray-50 text-gray-900':
                                        page.url.startsWith('/client/events'),
                                }">
                            <Calendar :size="18" class="mr-3" />
                            <span>Events</span>
                            </Link>
                            <Link href="/client/favorites" @click="isMobileMenuOpen = false"
                                class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                                :class="{
                                    'bg-gray-50 text-gray-900':
                                        page.url.startsWith(
                                            '/client/favorites',
                                        ),
                                }">
                            <Heart :size="18" class="mr-3" />
                            <span>Favorites</span>
                            </Link>

                            <!-- Mobile Notifications & Messages -->
                            <div class="mt-4 space-y-2 border-t border-gray-200 px-4 py-3 pt-4">
                                <Link href="/notifications" @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-between rounded-lg bg-gray-50 p-3 transition-all duration-200 hover:bg-gray-100">
                                <div class="flex items-center space-x-3">
                                    <Bell :size="18" class="text-gray-600" />
                                    <span class="font-medium text-gray-900">Notifications</span>
                                </div>
                                <span v-if="unreadNotifications > 0"
                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-red-500 text-xs text-white">
                                    {{
                                        unreadNotifications > 9
                                            ? '9+'
                                            : unreadNotifications
                                    }}
                                </span>
                                </Link>

                                <Link href="/messages" @click="isMobileMenuOpen = false"
                                    class="flex items-center justify-between rounded-lg bg-gray-50 p-3 transition-all duration-200 hover:bg-gray-100">
                                <div class="flex items-center space-x-3">
                                    <MessageSquare :size="18" class="text-gray-600" />
                                    <span class="font-medium text-gray-900">Messages</span>
                                </div>
                                <span v-if="unreadMessages > 0"
                                    class="flex h-5 w-5 items-center justify-center rounded-full bg-green-500 text-xs text-white">
                                    {{
                                        unreadMessages > 9
                                            ? '9+'
                                            : unreadMessages
                                    }}
                                </span>
                                </Link>
                            </div>
                        </template>

                        <!-- Mobile Auth Section -->
                        <div class="mt-4 border-t border-gray-200 pt-4">
                            <template v-if="page.props.auth.user">
                                <div
                                    class="mb-2 flex items-center space-x-3 rounded-lg border border-gray-200 bg-gray-50 px-4 py-3 text-gray-700">
                                    <div
                                        class="flex h-10 w-10 items-center justify-center rounded-full bg-gradient-to-br from-purple-500 to-indigo-500 text-sm font-medium text-white">
                                        {{
                                            page.props.auth.user.name
                                                .split(' ')
                                                .map((word) => word.charAt(0))
                                                .join('')
                                                .substring(0, 2)
                                                .toUpperCase()
                                        }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900">
                                            {{ page.props.auth.user.name }}
                                        </p>
                                        <p class="text-xs text-gray-600">
                                            {{ page.props.auth.user.email }}
                                        </p>
                                    </div>
                                </div>

                                <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false"
                                    class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900">
                                <User :size="18" class="mr-3 text-gray-600" />
                                Profile
                                </Link>

                                <Link href="/settings" @click="isMobileMenuOpen = false"
                                    class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900"
                                    v-if="
                                        route().has && route().has('settings')
                                    ">
                                <Settings :size="18" class="mr-3 text-gray-600" />
                                Settings
                                </Link>

                                <hr class="my-2 border-gray-200" />

                                <Link :href="route('logout')" method="post" as="button"
                                    @click="isMobileMenuOpen = false"
                                    class="flex w-full items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-red-50 hover:text-red-600">
                                <LogOut :size="18" class="mr-3 text-gray-600" />
                                Sign out
                                </Link>
                            </template>
                            <template v-else>
                                <Link href="/login" @click="isMobileMenuOpen = false"
                                    class="flex items-center rounded-lg px-4 py-3 font-medium text-gray-600 transition-all duration-200 hover:bg-gray-50 hover:text-gray-900">
                                <User :size="18" class="mr-3" />
                                Log In
                                </Link>
                                <Link href="/join" @click="isMobileMenuOpen = false"
                                    class="mx-4 mt-2 flex items-center justify-center rounded-lg bg-gradient-to-r from-purple-600 to-indigo-600 px-6 py-3 text-center font-semibold text-white shadow-md transition-all duration-300 hover:from-purple-500 hover:to-indigo-500">
                                <span>Get Started</span>
                                </Link>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Facebook-Style Chat Windows at Bottom -->
        <ChatWindow :openChats="openChats" @toggle-chat-window="toggleChatWindow" @close-chat-window="closeChatWindow"
            @send-chat-message="sendChatMessage" />

        <!-- ================= Slide-in Drawer: All Messages ================= -->
        <Teleport to="body">
            <div v-if="isMessagesDrawerActive" class="fixed inset-0 z-[999] flex justify-end">
                <!-- Backdrop -->
                <transition appear enter-active-class="transition-opacity duration-300 ease-out"
                    enter-from-class="opacity-0" enter-to-class="opacity-100"
                    leave-active-class="transition-opacity duration-200 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-if="isMessagesDrawerOpen" class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"
                        @click="closeMessagesDrawer" aria-hidden="true" />
                </transition>

                <!-- Panel -->
                <transition appear enter-active-class="transform transition duration-300 ease-out"
                    enter-from-class="translate-x-full" enter-to-class="translate-x-0"
                    leave-active-class="transform transition duration-200 ease-in" leave-from-class="translate-x-0"
                    leave-to-class="translate-x-full" @after-leave="onDrawerAfterLeave">
                    <aside v-if="isMessagesDrawerOpen" ref="panelEl"
                        class="relative flex h-svh w-full max-w-md flex-col bg-white shadow-2xl focus:outline-none"
                        role="dialog" aria-modal="true" aria-label="All Messages" tabindex="-1">
                        <header class="flex items-center justify-between border-b border-slate-200 p-4">
                            <h2 class="text-lg font-semibold text-slate-800">
                                All Messages
                            </h2>
                            <button @click="closeMessagesDrawer"
                                class="rounded-full p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Close panel">
                                <X :size="20" />
                            </button>
                        </header>

                        <div class="grow overflow-y-auto">
                            <div v-if="isLoading" class="p-6 text-center text-gray-500">
                                Loading conversations...
                            </div>
                            <div v-else-if="processedMessages.length === 0" class="p-6 text-center text-gray-500">
                                No conversations yet
                            </div>
                            <div v-else v-for="message in processedMessages" :key="message.id"
                                @click="openChatWindow(message)"
                                class="cursor-pointer border-b border-gray-100 p-4 hover:bg-gray-50"
                                :class="{ 'bg-blue-50/50': !message.read }">
                                <div class="flex items-start space-x-3">
                                    <div class="relative flex-shrink-0">
                                        <div
                                            class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-full border border-gray-200 bg-gray-100 text-xs font-semibold text-white shadow-sm">
                                            <img v-if="message.avatar" :src="message.avatar" alt="User avatar"
                                                class="h-full w-full object-cover" />
                                            <span v-else
                                                class="flex h-full w-full items-center justify-center bg-gradient-to-br from-purple-500 to-purple-700">
                                                {{ message.initials }}
                                            </span>
                                        </div>
                                        <div v-if="message.online"
                                            class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full border-2 border-white bg-green-500" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="truncate font-medium text-gray-900">
                                                {{ message.sender }}
                                            </p>
                                            <span class="whitespace-nowrap text-xs text-gray-400">{{ message.time
                                            }}</span>
                                        </div>
                                        <p class="mt-1 line-clamp-1 text-sm text-gray-600">
                                            {{ message.message }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </transition>
            </div>
        </Teleport>
        <!-- ================= /Slide-in Drawer ================= -->

        <!-- ================= Slide-in Drawer: All Notifications ================= -->
        <Teleport to="body">
            <div v-if="isNotificationsDrawerActive" class="fixed inset-0 z-[999] flex justify-end">
                <!-- Backdrop -->
                <transition appear enter-active-class="transition-opacity duration-300 ease-out"
                    enter-from-class="opacity-0" enter-to-class="opacity-100"
                    leave-active-class="transition-opacity duration-200 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-if="isNotificationsDrawerOpen" class="absolute inset-0 bg-black/50 backdrop-blur-[2px]"
                        @click="closeNotificationsDrawer" aria-hidden="true" />
                </transition>

                <!-- Panel -->
                <transition appear enter-active-class="transform transition duration-300 ease-out"
                    enter-from-class="translate-x-full" enter-to-class="translate-x-0"
                    leave-active-class="transform transition duration-200 ease-in" leave-from-class="translate-x-0"
                    leave-to-class="translate-x-full" @after-leave="onNotifDrawerAfterLeave">
                    <aside v-if="isNotificationsDrawerOpen" ref="notifPanelEl"
                        class="relative flex h-svh w-full max-w-md flex-col bg-white shadow-2xl focus:outline-none"
                        role="dialog" aria-modal="true" aria-label="All Notifications" tabindex="-1">
                        <header class="flex items-center justify-between border-b border-slate-200 p-4">
                            <h2 class="text-lg font-semibold text-slate-800">
                                All Notifications
                            </h2>
                            <button @click="closeNotificationsDrawer"
                                class="rounded-full p-1 text-slate-500 transition hover:bg-slate-100 hover:text-slate-800 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                aria-label="Close panel">
                                <X :size="20" />
                            </button>
                        </header>

                        <div class="grow overflow-y-auto">
                            <div v-if="isLoadingNotifications" class="p-6 text-center text-gray-500">
                                Loading notifications...
                            </div>
                            <div v-else-if="notifications.length === 0" class="p-6 text-center text-gray-500">
                                No notifications yet
                            </div>
                            <div v-else v-for="notification in notifications" :key="notification.id" @click="
                                navbarStore.markNotificationRead(
                                    notification.id,
                                )
                                " class="cursor-pointer border-b border-gray-100 p-4 hover:bg-gray-50" :class="{
                                    'bg-purple-50/50': !notification.is_read,
                                }">
                                <div class="flex items-start space-x-3">
                                    <component :is="getNotificationIcon(
                                        notification.type,
                                    )
                                        " :size="18" class="mt-0.5 flex-shrink-0" :class="getNotificationIconColor(
                                            notification.type,
                                        )
                                            " />
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center justify-between">
                                            <p class="truncate font-medium text-gray-900">
                                                {{ notification.title }}
                                            </p>
                                            <span v-if="!notification.is_read"
                                                class="h-2 w-2 flex-shrink-0 rounded-full bg-purple-600"></span>
                                        </div>
                                        <p class="mt-1 text-sm text-gray-600">
                                            {{ notification.message }}
                                        </p>
                                        <div class="mt-2 flex items-center justify-between">
                                            <p class="text-xs text-gray-400">
                                                {{ notification.time_ago }}
                                            </p>
                                            <div class="flex items-center space-x-1">
                                                <Check :size="12" class="text-gray-400" />
                                                <span class="text-xs text-gray-400">Mark read</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </aside>
                </transition>
            </div>
        </Teleport>
        <!-- ================= /Slide-in Drawer ================= -->
    </div>
</template>

<style>
.chat-window {
    width: 300px;
    transition: all 0.3s ease;
}

.chat-window.minimized {
    height: auto;
}

.chat-window.minimized .chat-body {
    display: none;
}

/* Custom scrollbar for chat */
.chat-body .overflow-y-auto::-webkit-scrollbar {
    width: 4px;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.chat-body .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
