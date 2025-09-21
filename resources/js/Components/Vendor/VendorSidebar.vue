<script setup>
import {
    ChevronDown,
    LayoutDashboard,
    CalendarDays,
    BriefcaseBusiness,
    BookUser,
    Star,
    MessageSquare,
    UserCircle,
    Settings,
    ArrowLeftFromLine,
    ClipboardList,
    WalletCards,
    BadgeCheck
} from 'lucide-vue-next';
import { useUIStore } from '@/store/ui';
import { useNotifications } from '@/Composables/useNotifications'; // Add this import
import NewNavLink from '../NewNavLink.vue';
import { onMounted, onUnmounted, ref } from 'vue';
import axios from 'axios';
import { storeToRefs } from 'pinia';
import { useNotificationStore } from '@/store/notification';

const ui = ref(useUIStore())

// Use the notification composable to get booking-specific unread count
// const { bookingUnreadCount } = useNotifications()

// Keep your existing method as fallback for initial load
// const unreadNotificationCount = ref(null)

// const fetchUnreadNotifications = async () => {
//     try {
//         const response = await axios.get(`/api/vendor-bookings-notifications`)
//         unreadNotificationCount.value = response.data.success ? response.data.data : 0
//     } catch (error) {
//         console.log(error.response?.data?.message || 'Network error occurred')
//     }
// }

// onMounted(() => {
//     fetchUnreadNotifications()
// })

const notificationStore = useNotificationStore()
const { bookingUnreadCount } = storeToRefs(notificationStore)

onMounted(() => {
    notificationStore.initializeNotifications()
    notificationStore.listenForNotifications()
    notificationStore.requestNotificationPermission()
})

onUnmounted(() => {
    notificationStore.cleanup()
})

</script>

<template>
    <div class="hidden lg:flex md:flex-shrink-0 max-h-screen bg-gray-900 border-r border-gray-800">
        <div :class="[ui.sidebarCollapsed ? 'w-20' : 'w-64', 'flex flex-col transition-all duration-300']">
            <!-- Logo/Brand -->
            <div
                :class="['flex items-center h-16 px-5 border-b border-gray-800', ui.sidebarCollapsed ? 'justify-center' : '']">
                <div class="w-full flex items-center"
                    :class="ui.sidebarCollapsed ? 'justify-center' : 'justify-between'">
                    <div v-if="!ui.sidebarCollapsed" class="flex gap-2 items-center">
                        <span class="text-xl font-bold text-white">Eventory</span>
                    </div>

                    <div class="flex items-center">
                        <button class="text-gray-400 hover:text-white rounded-full transition-colors"
                            @click="ui.toggleSidebar">
                            <ArrowLeftFromLine
                                :class="[ui.sidebarCollapsed ? 'rotate-180' : '', 'h-5 w-5 transition-all duration-300']" />
                        </button>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col overflow-y-auto no-scrollbar">
                <nav class="flex-1 px-2 py-4 space-y-1">
                    <NewNavLink :href="route('vendor.index')" :active="route().current('vendor.index')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <LayoutDashboard class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300" v-if="!ui.sidebarCollapsed">
                            Dashboard
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.calendar.index')"
                        :active="route().current('vendor.calendar.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <CalendarDays class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300" v-if="!ui.sidebarCollapsed">
                            Calendar
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.services.index')"
                        :active="route().current('vendor.services.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <BriefcaseBusiness class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300 truncate" v-if="!ui.sidebarCollapsed">
                            My Services
                        </span>
                    </NewNavLink>

                    <!-- Updated to use booking-specific real-time count -->
                    <NewNavLink :href="route('vendor.bookings.index')" :notificationCount="bookingUnreadCount"
                        :active="route().current('vendor.bookings.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <BookUser class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300 truncate" v-if="!ui.sidebarCollapsed">
                            Bookings
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.reviews.index')" :active="route().current('vendor.reviews.index')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Star class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300" v-if="!ui.sidebarCollapsed">
                            Reviews
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.messages.index')"
                        :active="route().current('vendor.messages.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <MessageSquare class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300" v-if="!ui.sidebarCollapsed">
                            Messages
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('vendor.profile-setting.index')"
                        :active="route().current('vendor.profile-setting.index')" :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <UserCircle class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300 truncate" v-if="!ui.sidebarCollapsed">
                            Profile Settings
                        </span>
                    </NewNavLink>

                    <NewNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                        :isCollapsed="ui.sidebarCollapsed">
                        <template #icon>
                            <Settings class="h-5 w-5" />
                        </template>
                        <span class="text-gray-300" v-if="!ui.sidebarCollapsed">
                            Account
                        </span>
                    </NewNavLink>
                </nav>

                <!-- Bottom spacer -->
                <div class="flex-grow"></div>

                <!-- Optional: Bottom section for user/profile -->
                <!-- <div class="px-4 py-3 border-t border-gray-800">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <img class="h-8 w-8 rounded-full" src="https://via.placeholder.com/32" alt="User avatar">
                        </div>
                        <div v-if="!ui.sidebarCollapsed" class="ml-3">
                            <p class="text-sm font-medium text-white">User Name</p>
                            <p class="text-xs font-medium text-gray-400">View profile</p>
                        </div>
                    </div>
                </div> -->
            </div>
        </div>
    </div>
</template>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}

.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
