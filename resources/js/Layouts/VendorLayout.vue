<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import VendorSidebar from '@/Components/Vendor/VendorSidebar.vue'
import VendorTopbar from '@/Components/Vendor/VendorTopbar.vue'
import { useNotificationStore } from '@/store/notification'
import {
    Notivue,
    Notification,
    // FIX: This must be NotificationProgress
    NotificationProgress,
    pastelTheme
} from 'notivue';

// Import the required theme CSS
// import 'notivue/themes/pastel.css';

// IMPORTANT: Also import the required CSS for the progress bar itself
import 'notivue/notification-progress.css';



const showingNavigationDropdown = ref(false)
const showingSidebar = ref(false)

const lgQuery = window.matchMedia('(min-width: 1024px)')

function handleScreenChange(e) {
    showingSidebar.value = e.matches
}

// Initialize notifications once at the layout level
const notificationStore = useNotificationStore()

// Handle page visibility changes to reconnect if needed
const handleVisibilityChange = async () => {
    if (document.visibilityState === 'visible') {
        // Reconnect notifications when page becomes visible
        if (!notificationStore.isInitialized) {
            try {
                await notificationStore.initializeNotifications()
            } catch (error) {
                console.error('Failed to reconnect notifications:', error)
            }
        }
    }
}

onMounted(async () => {
    // Handle screen size changes
    handleScreenChange(lgQuery)
    lgQuery.addEventListener('change', handleScreenChange)

    // Add visibility change listener
    document.addEventListener('visibilitychange', handleVisibilityChange)

    // Initialize the notification system when the layout mounts
    try {
        await notificationStore.initializeNotifications()
    } catch (error) {
        console.error('Failed to initialize notifications:', error)
        // You could show a toast notification here about the error
        // or set up a retry mechanism
    }
})

onBeforeUnmount(() => {
    // Remove event listeners
    lgQuery.removeEventListener('change', handleScreenChange)
    document.removeEventListener('visibilitychange', handleVisibilityChange)

    // Clean up when the layout is destroyed (user logs out, etc.)
    notificationStore.cleanup()
})
</script>

<template>
    <div class="h-screen flex flex-col">
        <!-- Notivue container -->
        <Notivue v-slot="item">
            <Notification :item="item" :theme="pastelTheme">
                <NotificationProgress :item="item" />

            </Notification>
        </Notivue>

        <!-- Page Heading (optional) -->
        <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="h-full overflow-hidden flex relative">
            <!-- Sidebar with fixed positioning -->
            <VendorSidebar />

            <!-- Main content with animated margin -->
            <div class="flex-1 transition-all duration-300 ease-out overflow-scroll"
                style="scrollbar-width: none; -ms-overflow-style: none;"
                :class="{ '': showingSidebar, 'ml-0': !showingSidebar }">
                <!-- Topbar -->
                <VendorTopbar />

                <!-- Page content slot -->
                <slot />

            </div>
        </main>
    </div>
</template>
