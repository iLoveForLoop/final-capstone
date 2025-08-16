<script setup>
import { computed } from 'vue';

const props = defineProps({
    title: String,
    value: [String, Number],
    unit: {
        type: String,
        default: ''
    },
    icon: String // 'users', 'clock', or 'truck'
});

const icons = {
    users: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>`,
    clock: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>`,
    truck: `<svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" /></svg>`
};

const iconClasses = {
    users: 'bg-blue-100 text-blue-600',
    clock: 'bg-orange-100 text-orange-600',
    truck: 'bg-green-100 text-green-600'
};
</script>

<template>
    <div class="rounded-lg bg-white p-3 shadow-sm ring-1 ring-slate-100">
        <div class="flex items-center gap-3">
            <div :class="iconClasses[icon]" class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-lg">
                <div v-html="icons[icon]"></div>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-500">{{ title }}</p>
                <p class="text-base font-bold text-slate-900 sm:text-lg">
                    {{ value }} <span v-if="unit" class="text-sm font-medium text-slate-500">{{ unit }}</span>
                </p>
            </div>
        </div>
    </div>
</template>
