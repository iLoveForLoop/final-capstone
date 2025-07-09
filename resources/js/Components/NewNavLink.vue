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
    }
});

// console.log('is collapsed: ', props.isCollapsed)


</script>

<template>
    <Link :href="href" :class="[
        active
            ? 'bg-indigo-50 text-indigo-700'
            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
        isCollapsed ? 'flex flex-col' : 'flex',
        'groups items-center px-3 py-2 text-sm font-medium rounded-md'
    ]">
    <span :class="[
        active ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500',
        isCollapsed ? '' : 'mr-3',
        'flex-shrink-0 h-6 w-6 flex justify-center items-center'
    ]" aria-hidden="true">
        <slot name="icon" />
    </span>
    <span class="flex-1">
        <slot />
    </span>
    </Link>
</template>
