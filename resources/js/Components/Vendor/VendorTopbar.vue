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
    Loader2
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
    <nav class="border-b border-gray-800 bg-gray-900">
        <!-- Primary Navigation Menu -->
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 justify-between items-center">
                <!-- Left side -->
                <div class="flex items-center space-x-8">
                    <p class="text-lg font-semibold text-white tracking-wide">
                        {{ title }}
                    </p>
                </div>

                <!-- Right side -->
                <div class="flex items-center space-x-4">
                    <!-- Notification Bell -->
                    <div class="relative" id="notification-dropdown">
                        <button id="notification-bell" @click="toggleNotificationDropdown"
                            class="relative p-2 text-gray-300 hover:bg-gray-800 hover:text-white rounded-full focus:outline-none transition-all duration-200"
                            :disabled="isLoading">
                            <!-- Loading state -->
                            <Loader2 v-if="isLoading" class="w-6 h-6 animate-spin" />
                            <Bell v-else class="w-6 h-6" />

                            <!-- Badge -->
                            <span v-if="hasUnreadNotifications"
                                class="absolute -top-0.5 -right-0.5 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-red-600 rounded-full">
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
                                class="absolute right-0 z-50 mt-2 w-96 bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                                <!-- Header -->
                                <div class="flex justify-between items-center mb-5">
                                    <div class="flex items-center space-x-3">
                                        <h2 class="text-lg font-semibold text-gray-800">
                                            Notifications
                                        </h2>
                                        <span v-if="hasUnreadNotifications"
                                            class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                            {{ unreadCount }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="refreshNotifications"
                                            class="text-sm text-gray-500 hover:text-gray-700" :disabled="isLoading"
                                            title="Refresh notifications">
                                            <Loader2 v-if="isLoading" class="w-4 h-4 animate-spin" />
                                            <svg v-else class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                            </svg>
                                        </button>
                                        <button @click="openNotificationDrawer"
                                            class="text-sm text-indigo-600 hover:underline">
                                            View All
                                        </button>
                                    </div>
                                </div>

                                <!-- Error State -->
                                <div v-if="error" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p class="text-sm text-red-600">{{ error }}</p>
                                    <button @click="refreshNotifications"
                                        class="mt-2 text-xs text-red-800 hover:underline">
                                        Try again
                                    </button>
                                </div>

                                <!-- Notifications List -->
                                <ul class="space-y-4">
                                    <li v-for="notification in recentNotifications" :key="notification.id"
                                        class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors"
                                        @click="markNotificationAsRead(notification.id)">
                                        <div class="flex-shrink-0 mt-1 mr-3">
                                            <div :class="getNotificationIconClass(notification)">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                    v-html="getNotificationIconSVG(notification.icon)">
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p :class="{
                                                'font-medium text-gray-900': !notification.read,
                                                'text-gray-500': notification.read,
                                            }" class="text-sm">{{ notification.title }}</p>
                                            <p class="text-sm text-gray-600 mt-1">{{ notification.text }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                                        </div>
                                        <div v-if="!notification.read" class="ml-2 mt-1">
                                            <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                                        </div>
                                    </li>
                                    <li v-if="!isLoading && recentNotifications.length === 0"
                                        class="text-center py-4 text-gray-400 text-sm">
                                        No notifications
                                    </li>
                                    <li v-if="isLoading && notifications.length === 0"
                                        class="text-center py-4 text-gray-400 text-sm">
                                        <Loader2 class="w-5 h-5 animate-spin mx-auto mb-2" />
                                        Loading notifications...
                                    </li>
                                </ul>

                                <!-- Footer Actions -->
                                <div v-if="hasUnreadNotifications" class="mt-4 pt-4 border-t border-gray-100">
                                    <button @click="markAllAsRead"
                                        class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200"
                                        :disabled="isLoading">
                                        Mark all as read
                                    </button>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative">
                        <MyDropdown />
                    </div>

                    <!-- Hamburger -->
                    <div class="-me-2 flex items-center sm:hidden">
                        <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                            class="inline-flex items-center justify-center rounded-md p-2 text-gray-300 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 focus:ring-offset-gray-900 transition-colors duration-200">
                            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                <path
                                    :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16" />
                                <path
                                    :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                    stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Responsive Navigation Menu -->
        <transition enter-active-class="transition ease-out duration-200"
            enter-from-class="transform opacity-0 -translate-y-2" enter-to-class="transform opacity-100 translate-y-0"
            leave-active-class="transition ease-in duration-150" leave-from-class="transform opacity-100 translate-y-0"
            leave-to-class="transform opacity-0 -translate-y-2">
            <div v-if="showingNavigationDropdown" class="sm:hidden">
                <div class="space-y-1 pb-3 pt-2">
                    <Link :href="route('dashboard')"
                        class="block px-4 py-2 text-gray-200 hover:bg-gray-800 hover:text-white rounded-md transition-colors duration-200">
                    Dashboard
                    </Link>
                </div>
            </div>
        </transition>
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
                class="fixed right-0 top-0 z-50 h-full w-96 bg-white shadow-xl flex flex-col">
                <!-- Header -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 m-4 mb-0">
                    <div class="flex justify-between items-center mb-5">
                        <div class="flex items-center space-x-3">
                            <h2 class="text-lg font-semibold text-gray-800">
                                All Notifications
                            </h2>
                            <span v-if="hasUnreadNotifications"
                                class="bg-red-500 text-white text-xs px-2 py-1 rounded-full">
                                {{ unreadCount }}
                            </span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button @click="refreshNotifications"
                                class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
                                :disabled="isLoading" title="Refresh notifications">
                                <Loader2 v-if="isLoading" class="w-5 h-5 animate-spin" />
                                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </button>
                            <button @click="ui.toggleVendorNotificationOpen"
                                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                                <X class="w-5 h-5" />
                            </button>
                        </div>
                    </div>

                    <!-- Action Bar -->
                    <div v-if="hasUnreadNotifications" class="border-t border-gray-100 pt-4">
                        <button @click="markAllAsRead"
                            class="text-sm text-indigo-600 hover:text-indigo-800 font-medium transition-colors duration-200"
                            :disabled="isLoading">
                            Mark all as read
                        </button>
                    </div>
                </div>

                <!-- Error State -->
                <div v-if="error" class="mx-4 mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ error }}</p>
                    <button @click="refreshNotifications" class="mt-2 text-xs text-red-800 hover:underline">
                        Try again
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                        <ul class="space-y-4">
                            <li v-for="notification in notifications" :key="notification.id"
                                class="flex items-start cursor-pointer hover:bg-gray-50 p-2 rounded-lg transition-colors group"
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
                                        'font-medium text-gray-900': !notification.read,
                                        'text-gray-500': notification.read,
                                    }" class="text-sm">{{ notification.title }}</p>
                                    <p class="text-sm text-gray-600 mt-1">{{ notification.text }}</p>
                                    <p class="text-xs text-gray-400 mt-1">{{ notification.time }}</p>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <div v-if="!notification.read" class="mt-1">
                                        <span class="h-2 w-2 rounded-full bg-blue-500 block"></span>
                                    </div>
                                    <!-- Optional: Remove button (uncomment if you want this feature) -->
                                    <!-- <button
                                        @click="removeNotification(notification.id, $event)"
                                        class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-red-500 transition-all duration-200"
                                        title="Remove notification"
                                    >
                                        <X class="w-4 h-4" />
                                    </button> -->
                                </div>
                            </li>

                            <!-- Loading state -->
                            <li v-if="isLoading && notifications.length === 0"
                                class="text-center py-8 text-gray-400 text-sm">
                                <Loader2 class="w-8 h-8 text-gray-300 mx-auto mb-4 animate-spin" />
                                <p class="font-medium text-gray-900 mb-2">Loading notifications...</p>
                                <p>Please wait while we fetch your notifications</p>
                            </li>

                            <!-- Empty state -->
                            <li v-if="!isLoading && notifications.length === 0"
                                class="text-center py-8 text-gray-400 text-sm">
                                <Bell class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                                <p class="font-medium text-gray-900 mb-2">No notifications</p>
                                <p>When you receive notifications, they'll appear here</p>
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

/* Loading animation */
@keyframes pulse-fade {

    0%,
    100% {
        opacity: 1;
    }

    50% {
        opacity: 0.5;
    }
}

.animate-pulse-fade {
    animation: pulse-fade 2s ease-in-out infinite;
}
</style>
