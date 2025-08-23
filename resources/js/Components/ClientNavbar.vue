<script setup>
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import { ref } from 'vue';
import MyDropdown from './MyDropdown.vue';

const page = usePage()
const isDropdownShowing = ref(false)
const isMobileMenuOpen = ref(false)
</script>

<template>
    <nav class="sticky top-0 z-50 bg-black/95 backdrop-blur-md border-b border-white/10">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center space-x-2 group">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-700 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 7v10c0 5.55 3.84 9.739 9 11 5.16-1.261 9-5.45 9-11V7l-10-5z" />
                    </svg>
                </div>
                <span
                    class="text-xl font-bold text-white group-hover:text-purple-300 transition-colors duration-300">Eventory</span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-1">
                    <!-- Navigation Links -->
                    <Link href="/"
                        class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url === '/client' }">
                    Home
                    </Link>

                    <Link href="/services"
                        class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url.startsWith('/services') }">
                    Services
                    </Link>

                    <!-- Conditional Links -->
                    <template v-if="page.props.auth.user">
                        <Link href="/bookings"
                            class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                            :class="{ 'text-white bg-white/10': page.url.startsWith('/bookings') }">
                        Bookings
                        </Link>
                        <Link href="/favorites"
                            class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                            :class="{ 'text-white bg-white/10': page.url.startsWith('/favorites') }">
                        Favorites
                        </Link>
                    </template>

                    <Link href="/categories"
                        class="px-4 py-2 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url.startsWith('/categories') }">
                    Categories
                    </Link>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden lg:flex items-center space-x-3">
                    <template v-if="page.props.auth.user">
                        <!-- Profile Dropdown -->
                        <div class="relative">
                            <button @click="isDropdownShowing = !isDropdownShowing"
                                class="flex items-center space-x-3 px-3 py-2 rounded-lg hover:bg-white/10 transition-all duration-300 group">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ page.props.auth.user.name.charAt(0) }}
                                </div>
                                <span
                                    class="text-gray-300 group-hover:text-white transition-colors duration-300 font-medium">
                                    {{ page.props.auth.user.name }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-white transition-all duration-300"
                                    :class="{ 'rotate-180': isDropdownShowing }" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            <div v-if="isDropdownShowing" @click="isDropdownShowing = false"
                                class="absolute right-0 mt-2 w-48 bg-gray-900/95 backdrop-blur-md rounded-xl border border-white/10 shadow-2xl py-2 z-50">
                                <Link :href="route('profile.edit')"
                                    class="block px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z">
                                        </path>
                                    </svg>
                                    <span>My Profile</span>
                                </div>
                                </Link>
                                <Link :href="route('logout')" method="post" as="button"
                                    class="block w-full text-left px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">
                                <div class="flex items-center space-x-3">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                        </path>
                                    </svg>
                                    <span>Logout</span>
                                </div>
                                </Link>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <!-- Login Button -->
                        <Link href="/login"
                            class="px-4 py-2 text-gray-300 hover:text-white transition-all duration-300 font-medium">
                        Log In
                        </Link>
                        <!-- Get Started Button -->
                        <Link href="/register"
                            class="px-6 py-2 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full hover:from-purple-500 hover:to-purple-600 transition-all duration-300 font-semibold shadow-lg hover:shadow-purple-500/25">
                        Get Started
                        </Link>
                    </template>
                </div>

                <!-- Mobile Menu Button -->
                <div class="lg:hidden">
                    <button @click="isMobileMenuOpen = !isMobileMenuOpen"
                        class="p-2 text-gray-300 hover:text-white hover:bg-white/10 rounded-lg transition-all duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path v-if="!isMobileMenuOpen" stroke-linecap="round" stroke-linejoin="round"
                                stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                            <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-if="isMobileMenuOpen" class="lg:hidden mt-4 pb-4 border-t border-white/10 pt-4">
                <div class="flex flex-col space-y-2">
                    <Link href="/" @click="isMobileMenuOpen = false"
                        class="px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url === '/client' }">
                    Home
                    </Link>

                    <Link href="/services" @click="isMobileMenuOpen = false"
                        class="px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url.startsWith('/services') }">
                    Services
                    </Link>

                    <template v-if="page.props.auth.user">
                        <Link href="/bookings" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                            :class="{ 'text-white bg-white/10': page.url.startsWith('/bookings') }">
                        Bookings
                        </Link>
                        <Link href="/favorites" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                            :class="{ 'text-white bg-white/10': page.url.startsWith('/favorites') }">
                        Favorites
                        </Link>
                    </template>

                    <Link href="/categories" @click="isMobileMenuOpen = false"
                        class="px-4 py-3 rounded-lg text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium"
                        :class="{ 'text-white bg-white/10': page.url.startsWith('/categories') }">
                    Categories
                    </Link>

                    <!-- Mobile Auth Section -->
                    <div class="pt-4 border-t border-white/10 mt-4">
                        <template v-if="page.props.auth.user">
                            <div class="flex items-center space-x-3 px-4 py-3 text-gray-300">
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center text-white font-semibold text-sm">
                                    {{ page.props.auth.user.name.charAt(0) }}
                                </div>
                                <span class="font-medium">{{ page.props.auth.user.name }}</span>
                            </div>
                            <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false"
                                class="block px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">
                            My Profile
                            </Link>
                            <Link :href="route('logout')" method="post" as="button" @click="isMobileMenuOpen = false"
                                class="block w-full text-left px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">
                            Logout
                            </Link>
                        </template>
                        <template v-else>
                            <Link href="/login" @click="isMobileMenuOpen = false"
                                class="block px-4 py-3 text-gray-300 hover:text-white hover:bg-white/10 transition-all duration-300 font-medium">
                            Log In
                            </Link>
                            <Link href="/register" @click="isMobileMenuOpen = false"
                                class="block mx-4 mt-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full hover:from-purple-500 hover:to-purple-600 transition-all duration-300 font-semibold text-center">
                            Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
