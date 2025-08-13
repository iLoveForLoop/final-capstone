<script setup>
import { router } from '@inertiajs/vue3';

defineProps({
    data: {
        type: Object,
        default: () => ({ data: [] })
    }
})
</script>

<template>
    <div v-if="data.links?.length > 3" class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
        <div class="text-sm text-gray-700">
            Showing <span class="font-medium">{{ data.from }}</span> to <span class="font-medium">{{
                data.to }}</span> of <span class="font-medium">{{ data.total }}</span> data
        </div>
        <div class="flex space-x-1">
            <template v-for="(link, index) in data.links">
                <button v-if="index === 0" :key="'prev'" @click="router.get(link.url)" :disabled="!link.url"
                    class="px-3 py-1 border rounded text-sm font-medium" :class="{
                        'border-gray-300 text-gray-500 hover:bg-gray-50': link.url,
                        'border-gray-200 text-gray-300 cursor-not-allowed': !link.url
                    }">
                    &laquo; Previous
                </button>
                <button v-else-if="index === data.links.length - 1" :key="'next'" @click="router.get(link.url)"
                    :disabled="!link.url" class="px-3 py-1 border rounded text-sm font-medium" :class="{
                        'border-gray-300 text-gray-500 hover:bg-gray-50': link.url,
                        'border-gray-200 text-gray-300 cursor-not-allowed': !link.url
                    }">
                    Next &raquo;
                </button>
                <button v-else :key="index" @click="router.get(link.url)"
                    class="px-3 py-1 border rounded text-sm font-medium" :class="{
                        'border-indigo-500 bg-indigo-50 text-indigo-600': link.active,
                        'border-gray-300 text-gray-500 hover:bg-gray-50': !link.active
                    }">
                    {{ link.label }}
                </button>
            </template>
        </div>
    </div>
</template>

<style></style>
