<script setup>
import { ref } from 'vue';

const props = defineProps({
    service: {
        type: Object,
        required: true
    },
    isSelected: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['select', 'view'])

const showDetails = ref(false);

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(price);
};

const showPriceRange = (service) => {
    if (service.max_price) {
        return `${formatPrice(service.price)} - ${formatPrice(service.max_price)}`;
    }
    return formatPrice(service.price);
};

const toggleDetails = () => {
    showDetails.value = !showDetails.value;
};

const handleSelect = () => {
    emit('select', props.service.id)
}

const handleView = () => {
    emit('view', props.service.id)
}
</script>

<template>
    <div class="bg-white rounded-xl border border-gray-200 p-5 space-y-5 hover:shadow-sm transition-shadow">
        <!-- Header with Price and Coverage Type -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex flex-wrap items-center gap-2">
                <div
                    class="bg-[#E6F4F6] text-[#239BA7] px-3 py-1.5 rounded-lg font-semibold text-base border border-[#239BA7]/30">
                    {{ showPriceRange(service) }}
                </div>

                <span v-if="service.photography_service?.coverage_type"
                    class="inline-flex items-center text-xs font-medium text-[#239BA7] bg-[#E6F4F6] px-2.5 py-1 rounded-lg border border-[#239BA7]/30">
                    {{ service.photography_service.coverage_type }}
                </span>
            </div>

            <div v-if="service.photography_service?.studio_shoot_available" class="flex-shrink-0">
                <span
                    class="inline-flex items-center text-xs px-2.5 py-1 bg-[#F5FBFB] text-[#239BA7] rounded-lg font-medium border border-[#239BA7]/30">
                    🏢 Studio Available
                </span>
            </div>
        </div>

        <!-- Service Title and Description -->
        <div>
            <h3 class="text-lg font-bold text-gray-900 mb-1.5">{{ service.name }}</h3>
            <p class="text-gray-600 text-sm leading-relaxed line-clamp-3">{{ service.description }}</p>
        </div>

        <!-- Key Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <!-- Photographers -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Photographers</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ service.photography_service?.number_of_photographers || 1 }}
                            <span class="text-xs font-normal text-gray-500">photographer{{
                                service.photography_service?.number_of_photographers > 1 ? 's' : '' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Coverage Hours -->
            <div v-if="service.photography_service?.hours_of_coverage"
                class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Coverage</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ service.photography_service.hours_of_coverage }}
                            <span class="text-xs font-normal text-gray-500">hours</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Time -->
            <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Delivery Time</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ service.photography_service?.delivery_time_days || 30 }}
                            <span class="text-xs font-normal text-gray-500">days</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delivery Fee -->
            <div v-if="service.delivery_fee" class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                <div class="flex items-center">
                    <div class="w-6 h-6 bg-[#E6F4F6] rounded-lg flex items-center justify-center mr-2">
                        <svg class="h-3.5 w-3.5 text-[#239BA7]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs text-gray-500 font-medium">Delivery Fee</div>
                        <div class="text-base font-semibold text-gray-900">
                            {{ formatPrice(service.delivery_fee) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Toggle Button -->
        <button @click="toggleDetails"
            class="w-full flex items-center justify-between text-sm font-medium text-[#239BA7]">
            <span>{{ showDetails ? 'Hide details' : 'Show more details' }}</span>
            <svg class="w-4 h-4 transition-transform duration-200" :class="{ 'rotate-180': showDetails }" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <!-- Collapsible Content -->
        <div v-show="showDetails" class="space-y-4 pt-2">
            <!-- Deliverables -->
            <div v-if="service.photography_service?.deliverables?.length" class="space-y-2">
                <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Deliverables
                    <span class="text-xs bg-gray-100 text-gray-600 px-1.5 py-0.5 rounded-full ml-1.5">
                        {{ service.photography_service.deliverables.length }} items
                    </span>
                </h4>
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-100">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                        <div v-for="(item, index) in service.photography_service.deliverables" :key="index"
                            class="flex items-center justify-between py-1 px-2 hover:bg-gray-100 rounded">
                            <span class="text-xs font-medium text-gray-700">{{ item }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Specifications -->
            <div v-if="service.photography_service?.specifications?.length" class="space-y-2">
                <h4 class="text-xs font-semibold text-gray-700 flex items-center">
                    <svg class="w-3.5 h-3.5 mr-1.5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    Features
                </h4>
                <div class="bg-gray-50 rounded-lg p-2 border border-gray-100">
                    <ul class="space-y-1.5">
                        <li v-for="(spec, index) in service.photography_service.specifications" :key="index"
                            class="flex items-start text-xs text-gray-700">
                            <span
                                class="inline-block w-1.5 h-1.5 bg-[#239BA7] rounded-full mt-1.5 mr-2 flex-shrink-0"></span>
                            <span>{{ spec }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 pt-4 border-t border-gray-100">
            <button @click="handleView"
                class="flex-1 px-4 py-2.5 bg-gray-50 text-gray-700 font-medium rounded-lg hover:bg-gray-100 transition-colors text-sm">
                View
            </button>

            <button @click="handleSelect" :class="{
                'bg-[#239BA7] text-white hover:bg-[#1D8E99]': !isSelected,
                'bg-green-600 text-white hover:bg-green-700': isSelected
            }"
                class="flex-1 px-4 py-2.5 font-medium rounded-lg transition-colors flex items-center justify-center text-sm">
                <span v-if="isSelected" class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                            clip-rule="evenodd"></path>
                    </svg>
                    Selected
                </span>
                <span v-else>Select</span>
            </button>
        </div>
    </div>
</template>

<style scoped>
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.rotate-180 {
    transform: rotate(180deg);
}
</style>
