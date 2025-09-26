<script setup>
import { usePage, Link } from '@inertiajs/vue3';
import { Calendar, Heart, Home, Search } from 'lucide-vue-next';


const page = usePage()
</script>

<template>
    <!-- Desktop Navigation - Always Centered -->
    <div class="hidden lg:flex flex-1 justify-center">
        <div class="flex items-center space-x-1">
            <!-- Core Navigation Links (Always visible) -->
            <Link :href="page.props.auth.user ? '/client' : '/'"
                class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                :class="{ 'text-gray-900 bg-gray-50': (page.url === '/client' || page.url === '/') }">
            <Home :size="16" class="mr-1.5" />
            <span>Home</span>
            </Link>

            <Link :href="page.props.auth.user ? '/client/services' : '/services'"
                class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                :class="{ 'text-gray-900 bg-gray-50': (page.url.startsWith('/client/services') || page.url.startsWith('/services')) }">
            <Search :size="16" class="mr-1.5" />
            <span>Services</span>
            </Link>


            <!-- Conditional Links -->
            <template v-if="page.props.auth.user">
                <Link href="/client/bookings"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                    :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/bookings') }">
                <Calendar :size="16" class="mr-1.5" />
                <span>Bookings</span>
                </Link>
                <Link href="/client/events"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                    :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/events') }">
                <Calendar :size="16" class="mr-1.5" />
                <span>Events</span>
                </Link>
                <Link href="/client/favorites"
                    class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-all duration-200 font-medium flex items-center"
                    :class="{ 'text-gray-900 bg-gray-50': page.url.startsWith('/client/favorites') }">
                <Heart :size="16" class="mr-1.5" />
                <span>Favorites</span>
                </Link>
            </template>
        </div>
    </div>
</template>

<style></style>
