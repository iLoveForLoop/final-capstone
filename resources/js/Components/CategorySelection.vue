<script setup>
const props = defineProps({
    categories: Array,
})

const selectedCategories = defineModel('selectedCategories');

const toggleCategory = (category) => {
    const index = selectedCategories.value.findIndex(c => c.id === category.id);
    if (index === -1) {
        selectedCategories.value.push(category);
    } else {
        selectedCategories.value.splice(index, 1);
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div>
            <h3 class="text-lg font-medium text-gray-900">Vendor Categories</h3>
            <p class="text-gray-500 text-sm mt-1">Select the services you need for your event</p>
        </div>

        <!-- Selection Status -->
        <div v-if="selectedCategories.length > 0" class="bg-[#E6F4F6] border border-[#239BA7]/30 rounded-lg px-3 py-2">
            <p class="text-xs text-[#239BA7]">
                <span class="font-medium">{{ selectedCategories.length }}</span>
                {{ selectedCategories.length === 1 ? 'category' : 'categories' }} selected
            </p>
        </div>

        <!-- Categories Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <button v-for="category in categories" :key="category.id" @click="toggleCategory(category)" :class="{
                'border-[#239BA7] bg-[#F5FBFB] ring-1 ring-[#239BA7]': selectedCategories.some(c => c.id === category.id),
                'border-gray-200 hover:border-[#239BA7]/50 hover:bg-gray-50': !selectedCategories.some(c => c.id === category.id)
            }" class="p-3 rounded-lg border transition-colors duration-150 flex flex-col items-center relative">
                <!-- Selection Indicator -->
                <div class="absolute top-2 right-2">
                    <div :class="{
                        'bg-[#239BA7] text-white': selectedCategories.some(c => c.id === category.id),
                        'bg-gray-200 text-transparent': !selectedCategories.some(c => c.id === category.id)
                    }" class="w-4 h-4 rounded-full flex items-center justify-center transition-colors">
                        <svg v-if="selectedCategories.some(c => c.id === category.id)" class="w-2.5 h-2.5"
                            fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>

                <!-- Category Icon -->
                <div :class="{
                    'bg-[#239BA7] text-white': selectedCategories.some(c => c.id === category.id),
                    'bg-gray-100 text-gray-500': !selectedCategories.some(c => c.id === category.id)
                }" class="w-10 h-10 rounded-full flex items-center justify-center mb-2 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                    </svg>
                </div>

                <!-- Category Name -->
                <span :class="{
                    'text-[#239BA7] font-medium': selectedCategories.some(c => c.id === category.id),
                    'text-gray-700': !selectedCategories.some(c => c.id === category.id)
                }" class="text-xs text-center">
                    {{ category.name }}
                </span>
            </button>
        </div>
    </div>
</template>

<style scoped>
/* Subtle interaction effects */
button {
    @apply transform transition-transform duration-100;
}

button:hover {
    @apply -translate-y-0.5;
}

button:active {
    @apply translate-y-0 scale-[0.98];
}
</style>
