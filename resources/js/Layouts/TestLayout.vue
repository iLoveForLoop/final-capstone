<script setup>
import { onBeforeUnmount, onMounted, ref, computed } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';

import TestSidebar from '@/Components/Admin/TestSidebar.vue';
import MyDropdown from '@/Components/MyDropdown.vue';
import {
    Notivue,
    Notification,
    NotificationProgress,
    pastelTheme
} from 'notivue';
import 'notivue/notification-progress.css';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';

const showingNavigationDropdown = ref(false);
const showingSidebar = ref(false);

const lgQuery = window.matchMedia('(min-width: 1024px)');

function handleScreenChange(e) {
    showingSidebar.value = e.matches;
}

onMounted(() => {
    handleScreenChange(lgQuery);
    lgQuery.addEventListener('change', handleScreenChange);
});

onBeforeUnmount(() => {
    lgQuery.removeEventListener('change', handleScreenChange);
});

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
</script>

<template>
    <div class="h-screen bg-slate-50 dark:bg-gray-900 flex flex-col">
        <ConfirmDialog />
        <!-- Notivue container -->
        <Notivue v-slot="item">
            <Notification :item="item" :theme="pastelTheme">
                <NotificationProgress :item="item" />
            </Notification>
        </Notivue>

        <!-- Page Content -->
        <main class="h-full overflow-hidden bg-white dark:bg-gray-800 flex relative shadow-lg">
            <!-- Sidebar with fixed positioning -->
            <TestSidebar />

            <!-- Main content with animated margin -->
            <div class="flex-1 transition-all duration-300 ease-out overflow-scroll"
                style="scrollbar-width: none; -ms-overflow-style: none;"
                :class="{ '': showingSidebar, 'ml-0': !showingSidebar }">

                <!-- Enhanced Navigation Bar -->
                <nav class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 shadow-sm">
                    <!-- Primary Navigation Menu -->
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex h-16 justify-between items-center">
                            <div class="flex items-center">
                                <!-- Logo/Brand Area -->
                                <div class="flex items-center space-x-3">
                                    <div class="hidden sm:block">
                                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ currentGreeting }}, Admin {{ greetingEmoji }}
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

                            <!-- User Dropdown and Controls -->
                            <div class="flex items-center space-x-4">
                                <MyDropdown />
                            </div>

                            <!-- Mobile menu button -->
                            <div class="flex items-center sm:hidden">
                                <button @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-gray-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex': !showingNavigationDropdown,
                                        }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex': showingNavigationDropdown,
                                        }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Responsive Navigation Menu -->
                    <div :class="{
                        block: showingNavigationDropdown,
                        hidden: !showingNavigationDropdown,
                    }" class="sm:hidden border-t border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800">
                        <div class="px-2 pt-2 pb-3 space-y-1">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')"
                                class="block px-3 py-2 rounded-md text-base font-medium">
                                Dashboard
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="border-t border-gray-200 dark:border-gray-600 pt-4 pb-3">
                            <div class="px-4 space-y-1">
                                <div class="text-base font-semibold text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>

                            <div class="mt-3 px-2 space-y-1">
                                <ResponsiveNavLink :href="route('profile.edit')"
                                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700">
                                    Profile
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('logout')" method="post" as="button"
                                    class="block w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 dark:text-gray-300 hover:text-gray-900 hover:bg-gray-50 dark:hover:text-white dark:hover:bg-gray-700">
                                    Log Out
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Content Slot -->
                <div class="bg-gray-50 dark:bg-gray-900 min-h-full">
                    <slot />
                </div>
            </div>
        </main>
    </div>
</template>
