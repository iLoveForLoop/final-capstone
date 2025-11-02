<script setup>
import { ref, onBeforeUnmount, onMounted, computed } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'
import {
    Bell,
    X,
    CalendarPlus,
    CalendarCheck,
    XCircle,
    Settings,
    CreditCard,
    Star,
    AlertCircle,
    Loader2,
    Search,
    HelpCircle,
    Menu
} from 'lucide-vue-next'
import MyDropdown from '../MyDropdown.vue'
import { useUIStore } from '@/store/ui'
import { useNotificationStore } from '@/store/notification'
import { storeToRefs } from 'pinia'

const showingNavigationDropdown = ref(false)
const showingSidebar = ref(false)
const showNotificationDropdown = ref(false)

const ui = useUIStore()
const page = usePage()

const lgQuery = window.matchMedia('(min-width: 1024px)')

function handleScreenChange(e) {
    showingSidebar.value = e.matches
}

// Dynamic greeting based on time of day
const currentGreeting = computed(() => {
    const hour = new Date().getHours();

    if (hour >= 5 && hour < 12) {
        return 'Good morning';
    } else if (hour >= 12 && hour < 17) {
        return 'Good afternoon';
    } else if (hour >= 17 && hour < 21) {
        return 'Good evening';
    } else {
        return 'Good night';
    }
});

// Optional: Add emoji based on time of day
const greetingEmoji = computed(() => {
    const hour = new Date().getHours();

    if (hour >= 5 && hour < 12) {
        return '☀️';
    } else if (hour >= 12 && hour < 17) {
        return '🌤️';
    } else if (hour >= 17 && hour < 21) {
        return '🌙';
    } else {
        return '✨';
    }
});

// Close dropdown when clicking outside
function handleClickOutside(event) {
    const dropdown = document.getElementById('notification-dropdown')
    const bellButton = document.getElementById('notification-bell')

    if (dropdown && !dropdown.contains(event.target) && !bellButton?.contains(event.target)) {
        showNotificationDropdown.value = false
    }
}

// Get notification store - no initialization needed since it's done at layout level
const notificationStore = useNotificationStore()
const {
    notifications,
    unreadCount,
    bookingUnreadCount,
    recentNotifications,
    isLoading,
    error,
    hasUnreadNotifications
} = storeToRefs(notificationStore)

onMounted(() => {
    handleScreenChange(lgQuery)
    lgQuery.addEventListener('change', handleScreenChange)
    document.addEventListener('click', handleClickOutside)

    // No notification initialization needed here - it's handled at layout level
})

onBeforeUnmount(() => {
    lgQuery.removeEventListener('change', handleScreenChange)
    document.removeEventListener('click', handleClickOutside)

    // Don't cleanup the store here since it's shared across components
    // The layout component handles cleanup
})

// Get notification icon SVG content
function getNotificationIconSVG(iconName) {
    const iconSVGs = {
        'calendar-plus': '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="M10 16h4"/><path d="M12 14v4"/>',
        'check-circle': '<circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/>',
        'x-circle': '<circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/>',
        'calendar-check': '<path d="M8 2v4"/><path d="M16 2v4"/><rect width="18" height="18" x="3" y="4" rx="2"/><path d="M3 10h18"/><path d="m9 16 2 2 4-4"/>',
        'settings': '<path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/><circle cx="12" cy="12" r="3"/>',
        'credit-card': '<rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" x2="22" y1="10" y2="10"/>',
        'star': '<polygon points="12,2 15.09,8.26 22,9.27 17,14.14 18.18,21.02 12,17.77 5.82,21.02 7,14.14 2,9.27 8.91,8.26"/>',
        'alert-circle': '<circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/>',
        'bell': '<path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/><path d="m13.73 21a2 2 0 0 1-3.46 0"/>'
    };
    return iconSVGs[iconName] || iconSVGs['bell'];
}

// Get notification icon class based on type
function getNotificationIconClass(notification) {
    const baseClasses = 'w-8 h-8 rounded-full flex items-center justify-center';

    const colorClasses = {
        'booking_received': 'bg-blue-100 text-blue-500',
        'booking_completed': 'bg-green-100 text-green-500',
        'booking_cancelled': 'bg-red-100 text-red-500',
        'booking_confirmed': 'bg-amber-100 text-amber-500',
        'service_updated': 'bg-purple-100 text-purple-500',
        'payment_received': 'bg-green-100 text-green-500',
        'review_received': 'bg-yellow-100 text-yellow-500',
        'system_alert': 'bg-gray-100 text-gray-500',
    };

    return `${baseClasses} ${colorClasses[notification.type] || 'bg-gray-100 text-gray-500'}`;
}

async function markNotificationAsRead(id) {
    const notification = notifications.value.find(n => n.id === id);
    if (!notification || notification.read) return;

    try {
        await notificationStore.markAsRead(id);

        // Navigate to action URL if available
        if (notification.action_url) {
            window.location.href = notification.action_url;
        }
    } catch (err) {
        console.error('Failed to mark notification as read:', err);
    }
}

async function markAllAsRead() {
    try {
        await notificationStore.markAllAsReadBackend()
    } catch (err) {
        console.error('Failed to mark all as read:', err);
    }
}

async function removeNotification(id, event) {
    event.stopPropagation()

    try {
        await notificationStore.removeNotification(id)
    } catch (err) {
        console.error('Failed to remove notification:', err);
    }
}

function openNotificationDrawer() {
    showNotificationDropdown.value = false
    ui.toggleVendorNotificationOpen()
}

function toggleNotificationDropdown(event) {
    event.stopPropagation()
    showNotificationDropdown.value = !showNotificationDropdown.value
}

async function refreshNotifications() {
    try {
        await notificationStore.fetchAllNotifications()
    } catch (err) {
        console.error('Failed to refresh notifications:', err)
    }
}

// Safe route name access
const routeName = computed(() => {
    try {
        return page.props?.auth?.routeName || page.props?.routeName || null
    } catch (err) {
        return null
    }
})

const titles = {
    'vendor.index': 'Vendor Dashboard',
    'vendor.calendar.index': 'Calendar',
    'vendor.services.index': 'Services',
    'vendor.bookings.index': 'Bookings',
    'vendor.reviews.index': 'Reviews',
    'vendor.messages.index': 'Messages',
    'vendor.profile-setting.index': 'Profile',
}

const title = computed(() => {
    return titles[routeName.value] || 'Dashboard'
})
</script>

<template>
    <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
        <!-- Primary Navigation Menu -->
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                <!-- Left Section: Greeting and Page Context -->
                <div class="flex items-center">
                    <div class="flex items-center space-x-3">
                        <div class="hidden sm:block">
                            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                                {{ currentGreeting }}, {{ page.props?.auth?.user.vendor.business_name }} {{
                                    greetingEmoji
                                }}
                            </h1>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ new Date().toLocaleDateString('en-US', {
                                    weekday: 'long',
                                    year: 'numeric',
                                    month: 'long',
                                    day: 'numeric'
                                }) }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Controls & User -->
                <div class="flex items-center space-x-3">
                    <!-- Search -->
                    <!-- <div class="hidden md:block relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <Search class="h-4 w-4 text-gray-400" />
                        </div>
                        <input type="text" placeholder="Search..."
                            class="pl-10 pr-4 py-2 w-64 text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                    </div> -->

                    <!-- Action Icons -->
                    <div class="flex items-center space-x-1">
                        <!-- Help -->
                        <!-- <button
                            class="p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200">
                            <HelpCircle class="w-5 h-5" />
                        </button> -->

                        <!-- Notification Bell -->
                        <div class="relative" id="notification-dropdown">
                            <button id="notification-bell" @click="toggleNotificationDropdown"
                                class="relative p-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors duration-200 focus:outline-none"
                                :disabled="isLoading">
                                <!-- Loading state -->
                                <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
                                <Bell v-else class="w-5 h-5" />

                                <!-- Badge -->
                                <span v-if="hasUnreadNotifications"
                                    class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-500 rounded-full border-2 border-white dark:border-gray-800">
                                    {{ unreadCount > 9 ? '9+' : unreadCount }}
                                </span>
                            </button>

                            <!-- Notification Dropdown -->
                            <transition enter-active-class="transition ease-out duration-200"
                                enter-from-class="transform opacity-0 scale-95"
                                enter-to-class="transform opacity-100 scale-100"
                                leave-active-class="transition ease-in duration-75"
                                leave-from-class="transform opacity-100 scale-100"
                                leave-to-class="transform opacity-0 scale-95">
                                <div v-if="showNotificationDropdown"
                                    class="absolute right-0 top-full z-50 mt-2 w-96 overflow-hidden rounded-xl border border-gray-200 bg-white dark:bg-gray-800 shadow-lg">

                                    <!-- Header -->
                                    <div
                                        class="border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <h3 class="font-semibold text-gray-900 dark:text-white">Notifications
                                                </h3>
                                                <span v-if="hasUnreadNotifications"
                                                    class="bg-purple-600 text-white text-xs px-2 py-1 rounded-full">
                                                    {{ unreadCount }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <button @click="refreshNotifications"
                                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition-colors duration-200"
                                                    :disabled="isLoading" title="Refresh notifications">
                                                    <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
                                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                                <button v-if="hasUnreadNotifications" @click="markAllAsRead"
                                                    class="text-xs font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200"
                                                    :disabled="isLoading">
                                                    Mark all read
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Error State -->
                                    <div v-if="error"
                                        class="m-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                        <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
                                        <button @click="refreshNotifications"
                                            class="mt-2 text-xs text-red-800 dark:text-red-300 hover:underline">
                                            Try again
                                        </button>
                                    </div>

                                    <!-- Notifications List -->
                                    <div class="max-h-96 overflow-y-auto">
                                        <!-- Loading -->
                                        <div v-if="isLoading && notifications.length === 0" class="p-8 text-center">
                                            <Loader2 class="w-6 h-6 animate-spin mx-auto mb-2 text-purple-600" />
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Loading notifications...
                                            </p>
                                        </div>

                                        <!-- Empty -->
                                        <div v-else-if="!isLoading && recentNotifications.length === 0"
                                            class="p-8 text-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="mx-auto mb-2 text-gray-400"
                                                width="24" height="24" fill="none" stroke="currentColor"
                                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M10 21h4a2 2 0 0 0 2-2v-1H8v1a2 2 0 0 0 2 2z" />
                                                <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9" />
                                            </svg>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                                            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">We'll notify you
                                                when something arrives</p>
                                        </div>

                                        <!-- Notifications -->
                                        <div v-else>
                                            <div v-for="notification in recentNotifications" :key="notification.id"
                                                @click="markNotificationAsRead(notification.id)"
                                                class="group cursor-pointer border-b border-gray-100 dark:border-gray-700 p-4 transition-colors duration-150 hover:bg-gray-50 dark:hover:bg-gray-700"
                                                :class="{ 'bg-purple-50/50 dark:bg-purple-900/20': !notification.read }">

                                                <div class="flex items-start space-x-3">
                                                    <!-- Notification Icon -->
                                                    <div class="flex-shrink-0 mt-0.5"
                                                        :class="getNotificationIconClass(notification)">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                                            viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                            stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            v-html="getNotificationIconSVG(notification.icon)">
                                                        </svg>
                                                    </div>

                                                    <!-- Content -->
                                                    <div class="min-w-0 flex-1">
                                                        <div class="flex items-center justify-between">
                                                            <p :class="{
                                                                'font-medium text-gray-900 dark:text-white': !notification.read,
                                                                'text-gray-500 dark:text-gray-400': notification.read,
                                                            }" class="truncate">
                                                                {{ notification.title }}
                                                            </p>
                                                            <span v-if="!notification.read"
                                                                class="h-2 w-2 flex-shrink-0 rounded-full bg-purple-600"></span>
                                                        </div>

                                                        <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">
                                                            {{ notification.text }}
                                                        </p>

                                                        <div class="mt-2 flex items-center justify-between">
                                                            <p class="text-xs text-gray-400 dark:text-gray-500">
                                                                {{ notification.time }}
                                                            </p>

                                                            <div
                                                                class="flex items-center space-x-1 opacity-0 transition-opacity group-hover:opacity-100">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="12" fill="none" stroke="currentColor"
                                                                    stroke-width="2" stroke-linecap="round"
                                                                    stroke-linejoin="round" class="text-gray-400">
                                                                    <path d="M20 6L9 17l-5-5" />
                                                                </svg>
                                                                <span class="text-xs text-gray-400">Mark read</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Footer -->
                                    <div
                                        class="border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-900 p-4">
                                        <button type="button" @click="openNotificationDrawer"
                                            class="w-full text-center text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400 dark:hover:text-purple-300 transition-colors duration-200">
                                            View all notifications
                                        </button>
                                    </div>
                                </div>

                            </transition>
                        </div>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <MyDropdown />
                    </div>

                    <!-- Mobile menu button -->
                    <div class="flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <Menu class="h-6 w-6" />
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <div :class="{
            block: showingNavigationDropdown,
            hidden: !showingNavigationDropdown,
        }" class="sm:hidden border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
            <!-- Mobile Search -->
            <div class="px-4 pt-3 pb-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <Search class="h-4 w-4 text-gray-400" />
                    </div>
                    <input type="text" placeholder="Search..."
                        class="pl-10 pr-4 py-2 w-full text-sm border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200">
                </div>
            </div>

            <div class="px-2 pt-2 pb-3 space-y-1">
                <Link :href="route('dashboard')"
                    class="block px-3 py-2 rounded-lg text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700 transition-colors duration-200">
                Dashboard
                </Link>
            </div>
        </div>
    </nav>

    <!-- 🔔 Full Notification Drawer (Side Panel) -->
    <teleport to="body">
        <!-- Backdrop -->
        <transition enter-active-class="transition-opacity ease-out duration-300" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition-opacity ease-in duration-200"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="ui.isVendorNotificationOpen" @click="ui.toggleVendorNotificationOpen"
                class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"></div>
        </transition>

        <!-- Drawer -->
        <transition enter-active-class="transition-transform ease-out duration-300"
            enter-from-class="transform translate-x-full" enter-to-class="transform translate-x-0"
            leave-active-class="transition-transform ease-in duration-200" leave-from-class="transform translate-x-0"
            leave-to-class="transform translate-x-full">
            <div v-if="ui.isVendorNotificationOpen"
                class="fixed right-0 top-0 z-50 h-full w-96 bg-white dark:bg-gray-800 shadow-xl flex flex-col border-l border-gray-200 dark:border-gray-700">
                <!-- Header -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border-b border-gray-200 dark:border-gray-700 p-5 m-4 mb-0">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                All Notifications
                            </h2>
                            <span v-if="hasUnreadNotifications"
                                class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ unreadCount }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="refreshNotifications"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200"
                                :disabled="isLoading" title="Refresh notifications">
                                <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button @click="ui.toggleVendorNotificationOpen"
                                class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-200">
                                <X class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div v-if="hasUnreadNotifications" class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <button @click="markAllAsRead"
                            class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium transition-colors duration-200"
                            :disabled="isLoading">
                            Mark all as read
                        </button>
                    </div>
                </div>

                <!-- Error State -->
                <div v-if="error"
                    class="mx-4 mb-4 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <p class="text-sm text-red-600 dark:text-red-400">{{ error }}</p>
                    <button @click="refreshNotifications"
                        class="mt-2 text-xs text-red-800 dark:text-red-300 hover:underline">
                        Try again
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-5">
                        <ul class="space-y-4">
                            <li v-for="notification in notifications" :key="notification.id"
                                class="flex items-start cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 p-3 rounded-lg transition-colors duration-200 group"
                                @click="markNotificationAsRead(notification.id)">
                                <div class="flex-shrink-0 mt-1 mr-3">
                                    <div :class="getNotificationIconClass(notification)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round"
                                            v-html="getNotificationIconSVG(notification.icon)">
                                        </svg>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p :class="{
                                        'font-medium text-gray-900 dark:text-white': !notification.read,
                                        'text-gray-500 dark:text-gray-400': notification.read,
                                    }" class="text-sm">{{ notification.title }}</p>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">{{ notification.text }}</p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">{{ notification.time }}</p>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <div v-if="!notification.read" class="mt-1">
                                        <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                                    </div>
                                </div>
                            </li>

                            <!-- Loading state -->
                            <li v-if="isLoading && notifications.length === 0"
                                class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                                <Loader2 class="w-8 h-8 text-gray-300 dark:text-gray-600 mx-auto mb-4 animate-spin" />
                                <p class="font-medium text-gray-900 dark:text-white mb-2">Loading notifications...</p>
                                <p class="text-gray-500 dark:text-gray-400">Please wait while we fetch your
                                    notifications</p>
                            </li>

                            <!-- Empty state -->
                            <li v-if="!isLoading && notifications.length === 0"
                                class="text-center py-8 text-gray-400 dark:text-gray-500 text-sm">
                                <Bell class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                                <p class="font-medium text-gray-900 dark:text-white mb-2">No notifications</p>
                                <p class="text-gray-500 dark:text-gray-400">When you receive notifications, they'll
                                    appear here</p>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </transition>
    </teleport>
</template>

<style scoped>
/* Custom scrollbar for notification drawer */
.overflow-y-auto::-webkit-scrollbar {
    width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
    background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
    background: #4b5563;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}
</style>
