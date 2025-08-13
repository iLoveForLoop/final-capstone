<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    isCollapsed: {
        type: Boolean,
        default: false
    },
    notificationCount: {
        type: Number,
        default: 0
    }
});
</script>

<template>
    <div class="relative">
        <Link :href="href" :class="[
            active
                ? 'bg-indigo-900/50 text-white border-l-4 border-indigo-500'
                : 'text-gray-300 hover:bg-gray-800 hover:text-white',
            isCollapsed ? 'flex flex-col items-center justify-center py-3' : 'flex items-center px-3 py-2.5',
            'group text-sm font-medium rounded-md transition-colors duration-200 mx-2 relative'
        ]">
        <span :class="[
            active
                ? 'text-indigo-400'
                : 'text-gray-400 group-hover:text-gray-300',
            isCollapsed ? '' : 'mr-3',
            'flex-shrink-0 h-5 w-5 flex justify-center items-center transition-colors duration-200 relative'
        ]" aria-hidden="true">
            <slot name="icon" />
            <span v-if="notificationCount > 0 && isCollapsed"
                class="absolute -top-1 -right-1 bg-red-500 text-white text-xs h-4 w-4 flex items-center justify-center rounded-full">
                {{ notificationCount > 9 ? '9+' : notificationCount }}
            </span>
        </span>
        <span class="truncate flex items-center" :class="{ 'sr-only': isCollapsed }">
            <slot />
            <span v-if="notificationCount > 0 && !isCollapsed"
                class="ml-2 bg-red-500 text-white text-xs px-1.5 py-0.5 rounded-full min-w-[20px] flex justify-center">
                {{ notificationCount > 9 ? '9+' : notificationCount }}
            </span>
        </span>
        </Link>
    </div>
</template>
