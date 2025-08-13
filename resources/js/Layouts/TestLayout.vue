<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link } from '@inertiajs/vue3';



import TestSidebar from '@/Components/Admin/TestSidebar.vue';
import MyDropdown from '@/Components/MyDropdown.vue';


const showingNavigationDropdown = ref(false);
const showingSidebar = ref(false)

const lgQuery = window.matchMedia('(min-width: 1024px)')

function handleScreenChange(e) {
    showingSidebar.value = e.matches
}

onMounted(() => {
    handleScreenChange(lgQuery)
    lgQuery.addEventListener('change', handleScreenChange)
})

onBeforeUnmount(() => {
    lgQuery.removeEventListener('change', handleScreenChange)
})

</script>

<template>

    <div class="h-screen bg-blue-800 dark:bg-gray-900 flex flex-col">


        <!-- Page Heading -->
        <header class="bg-white shadow dark:bg-gray-800" v-if="$slots.header">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <slot name="header" />
            </div>
        </header>

        <!-- Page Content -->
        <main class="h-full overflow-hidden bg-blue-200 flex relative">
            <!-- Sidebar with fixed positioning -->
            <TestSidebar />
            <!-- Main content with animated margin -->
            <div class="flex-1 transition-all duration-300 ease-out overflow-scroll"
                style="scrollbar-width: none; -ms-overflow-style: none;"
                :class="{ '': showingSidebar, 'ml-0': !showingSidebar }">
                <nav class="border-b border-gray-100 blue-grey-darken-4 bg-slate-700">
                    <!-- Primary Navigation Menu -->
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        <div class="flex h-16 justify-between items-center">
                            <div class="flex">
                                <!-- Logo -->
                                <!-- <div class="flex shrink-0 items-center">
                                    <Link :href="route('dashboard')">
                                    <ApplicationLogo
                                        class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                                    </Link>
                                </div> -->

                                <!-- Navigation Links -->
                                <div class="hidden space-x-8 sm:-my-px sm:flex sm:items-center">
                                    <p class="text-white">Dashboard</p>
                                </div>
                            </div>

                            <MyDropdown />
                            <!-- Hamburger -->
                            <div class="-me-2 flex items-center sm:hidden">
                                <button @click="
                                    showingNavigationDropdown =
                                    !showingNavigationDropdown
                                    "
                                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition duration-150 ease-in-out hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none dark:text-gray-500 dark:hover:bg-gray-900 dark:hover:text-gray-400 dark:focus:bg-gray-900 dark:focus:text-gray-400">
                                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                        <path :class="{
                                            hidden: showingNavigationDropdown,
                                            'inline-flex':
                                                !showingNavigationDropdown,
                                        }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 6h16M4 12h16M4 18h16" />
                                        <path :class="{
                                            hidden: !showingNavigationDropdown,
                                            'inline-flex':
                                                showingNavigationDropdown,
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
                    }" class="sm:hidden">
                        <div class="space-y-1 pb-3 pt-2">
                            <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                Dashboard
                            </ResponsiveNavLink>
                        </div>

                        <!-- Responsive Settings Options -->
                        <div class="border-t border-gray-200 pb-1 pt-4 dark:border-gray-600">
                            <div class="px-4">
                                <div class="text-base font-medium text-gray-800 dark:text-gray-200">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="text-sm font-medium text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>

                            <div class="mt-3 space-y-1">
                                <ResponsiveNavLink :href="route('profile.edit')">
                                    Profile
                                </ResponsiveNavLink>
                                <ResponsiveNavLink :href="route('logout')" method="post" as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </div>
                        </div>
                    </div>
                </nav>
                <slot />
            </div>
        </main>
    </div>
</template>
