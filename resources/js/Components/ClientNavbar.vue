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
    <nav class="sticky top-0 z-30 bg-[#bad6d8] backdrop-blur-md border-b border-slate-300/20">
        <div class="max-w-7xl mx-auto px-6 py-4">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <Link href="/" class="flex items-center space-x-2 group">
                <span
                    class="text-xl font-bold text-slate-800 group-hover:text-purple-700 transition-colors duration-300">
                    Eventory
                </span>
                </Link>

                <!-- Desktop Navigation -->
                <div class="hidden lg:flex items-center space-x-1">
                    <!-- Navigation Links -->
                    <Link href="/client"
                        class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url === '/client' }">
                    Home
                    </Link>

                    <Link href="/client/services"
                        class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/services') }">
                    Services
                    </Link>

                    <!-- Conditional Links -->
                    <template v-if="page.props.auth.user">
                        <Link href="/client/bookings"
                            class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/bookings') }">
                        Bookings
                        </Link>
                        <Link href="/client/events"
                            class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/events') }">
                        Events
                        </Link>
                        <Link href="/client/favorites"
                            class="px-4 py-2 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/favorites') }">
                        Favorites
                        </Link>

                    </template>


                </div>

                <!-- Right Side Actions -->
                <div class="hidden lg:flex items-center space-x-3">
                    <template v-if="page.props.auth.user">
                        <!-- Use MyDropdown Component -->
                        <MyDropdown />
                    </template>

                    <template v-else>
                        <!-- Login Button -->
                        <Link href="/login"
                            class="px-4 py-2 text-slate-700 hover:text-slate-900 transition-all duration-300 font-medium">
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
                        class="p-2 text-slate-700 hover:text-slate-900 hover:bg-white/20 rounded-lg transition-all duration-300">
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
            <div v-if="isMobileMenuOpen" class="lg:hidden mt-4 pb-4 border-t border-slate-300/20 pt-4">
                <div class="flex flex-col space-y-2">
                    <Link href="/client" @click="isMobileMenuOpen = false"
                        class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url === '/client' }">
                    Home
                    </Link>

                    <Link href="/client/services" @click="isMobileMenuOpen = false"
                        class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                        :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/services') }">
                    Services
                    </Link>

                    <template v-if="page.props.auth.user">
                        <Link href="/client/bookings" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/bookings') }">
                        Bookings
                        </Link>
                        <Link href="/client/events" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/events') }">
                        Events
                        </Link>
                        <Link href="/client/favorites" @click="isMobileMenuOpen = false"
                            class="px-4 py-3 rounded-lg text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium"
                            :class="{ 'text-slate-900 bg-white/20': page.url.startsWith('/client/favorites') }">
                        Favorites
                        </Link>

                    </template>



                    <!-- Mobile Auth Section -->
                    <div class="pt-4 border-t border-slate-300/20 mt-4">
                        <template v-if="page.props.auth.user">
                            <div
                                class="flex items-center space-x-3 px-4 py-3 text-slate-700 mb-2 bg-white/10 rounded-lg border border-slate-300/20">
                                <div
                                    class="w-8 h-8 rounded-full bg-slate-600 flex items-center justify-center text-white font-medium text-sm">
                                    {{page.props.auth.user.name.split(' ').map(word =>
                                        word.charAt(0)).join('').substring(0, 2).toUpperCase()}}
                                </div>
                                <div>
                                    <p class="font-medium text-slate-900">{{ page.props.auth.user.name }}</p>
                                    <p class="text-xs text-slate-600">{{ page.props.auth.user.email }}</p>
                                </div>
                            </div>

                            <Link :href="route('profile.edit')" @click="isMobileMenuOpen = false"
                                class="flex items-center px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg">
                            <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            Profile
                            </Link>

                            <Link href="/settings" @click="isMobileMenuOpen = false"
                                class="flex items-center px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg"
                                v-if="route().has && route().has('settings')">
                            <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Settings
                            </Link>

                            <hr class="border-slate-300/20 my-2">

                            <Link :href="route('logout')" method="post" as="button" @click="isMobileMenuOpen = false"
                                class="flex items-center w-full px-4 py-3 text-slate-700 hover:text-red-600 hover:bg-red-100/30 transition-all duration-300 font-medium rounded-lg">
                            <svg class="w-4 h-4 mr-3 text-slate-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Sign out
                            </Link>
                        </template>
                        <template v-else>
                            <Link href="/login" @click="isMobileMenuOpen = false"
                                class="block px-4 py-3 text-slate-700 hover:text-slate-900 hover:bg-white/20 transition-all duration-300 font-medium rounded-lg">
                            Log In
                            </Link>
                            <Link href="/register" @click="isMobileMenuOpen = false"
                                class="block mx-4 mt-2 px-6 py-3 bg-gradient-to-r from-purple-600 to-purple-700 text-white rounded-full hover:from-purple-500 hover:to-purple-600 transition-all duration-300 font-semibold text-center shadow-lg">
                            Get Started
                            </Link>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</template>
