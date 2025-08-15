<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref } from 'vue';
import MyDropdown from './MyDropdown.vue';

const page = usePage()
const isDropdownShowing = ref(false)
</script>

<template>
    <nav class="sticky top-0 z-30 bg-white shadow-sm">
        <div class="container mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="text-2xl font-bold text-[#239BA7]">Eventory</Link>

                <!-- Desktop Navigation -->
                <div class="hidden md:flex items-center space-x-8">
                    <!-- Common Links -->
                    <Link href="/" class="text-gray-700 hover:text-[#239BA7] transition"
                        :class="{ 'font-semibold text-[#239BA7]': page.url === '/client' }">Home</Link>
                    <Link href="/services" class="text-gray-700 hover:text-[#239BA7] transition"
                        :class="{ 'font-semibold text-[#239BA7]': page.url.startsWith('/services') }">Browse
                    Services</Link>

                    <!-- Conditional Links -->
                    <template v-if="page.props.auth.user">
                        <Link href="/bookings" class="text-gray-700 hover:text-[#239BA7] transition"
                            :class="{ 'font-semibold text-[#239BA7]': page.url.startsWith('/bookings') }">My
                        Bookings</Link>
                        <Link href="/favorites" class="text-gray-700 hover:text-[#239BA7] transition"
                            :class="{ 'font-semibold text-[#239BA7]': page.url.startsWith('/favorites') }">
                        Favorites</Link>
                    </template>

                    <Link href="/categories" class="text-gray-700 hover:text-[#239BA7] transition"
                        :class="{ 'font-semibold text-[#239BA7]': page.url.startsWith('/categories') }">
                    Categories
                    </Link>
                    <!-- <Link href="#how-it-works" class="text-gray-700 hover:text-[#239BA7] transition">How It Works
                        </Link> -->

                    <!-- Search Bar -->
                    <!-- <div class="relative ml-4">
                            <input type="text" placeholder="Search services..."
                                class="pl-4 pr-10 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-[#239BA7] focus:border-transparent w-48">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5 text-gray-400 absolute right-3 top-2.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div> -->

                    <!-- Auth Links -->

                    <template v-if="page.props.auth.user">
                        <!-- Profile Dropdown -->
                        <MyDropdown />
                        <!-- <div class="relative ml-4">
                            <button class="flex items-center space-x-2 focus:outline-none"
                                @click="isDropdownShowing = !isDropdownShowing">
                                <div
                                    class="w-8 h-8 rounded-full bg-[#239BA7] flex items-center justify-center text-[#239BA7] font-semibold">
                                    {{ page.props.auth.user.name.charAt(0) }}
                                </div>
                            </button>
                            <div v-if="isDropdownShowing"
                                class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 group-hover:block">
                                <Link :href="route('profile.edit')"
                                    class="block px-4 py-2 text-gray-700 hover:bg-[#239BA7]>
                                My Profile</Link>
                                <Link :href="route('logout')" method="post" as="button"
                                    class="block w-full text-left px-4 py-2 text-gray-700 hover:bg-[#239BA7]>
                                Logout</Link>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">

                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button"
                                                class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-500 transition duration-150 ease-in-out hover:text-gray-700 focus:outline-none dark:bg-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                                {{ $page.props.auth.user.name }}

                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                                    viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd"
                                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>

<template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Log Out
                                        </DropdownLink>
                                    </template>
</Dropdown>
</div>
</div> -->
                    </template>

                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-gray-700 focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </nav>
</template>

<style></style>
