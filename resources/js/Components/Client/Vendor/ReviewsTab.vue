<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Progress } from '@/Components/ui/progress';

import { Flag, MessageCircle, Star } from 'lucide-vue-next';

defineProps({
    vendor: {
        type: Object
    }
})

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}
</script>

<template>
    <!-- Reviews Summary -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-8 md:mb-12">
        <Card class="review-summary-card border-indigo-100">
            <CardContent class="pt-6 text-center">
                <div class="text-5xl md:text-6xl font-bold text-indigo-600 mb-3">{{
                    vendor.rating }}</div>
                <div class="flex justify-center gap-1 mb-3">
                    <Star v-for="n in 5" :key="n" fill="currentColor" :size="20"
                        :class="['text-yellow-400', n <= vendor.rating ? 'opacity-100' : 'opacity-30']" />
                </div>
                <p class="text-gray-600 font-medium text-sm md:text-base">{{ vendor.reviewCount
                }} total reviews</p>
            </CardContent>
        </Card>

        <div class="lg:col-span-2 space-y-4">
            <div v-for="(count, rating) in vendor.ratingBreakdown" :key="rating" class="rating-bar">
                <span class="rating-label text-sm md:text-base">{{ rating }} star{{ rating > 1 ?
                    's' : '' }}</span>
                <Progress :model-value="(count / vendor.reviewCount) * 100" class="rating-progress h-2 md:h-3" />
                <span class="rating-count text-sm md:text-base">{{ count }}</span>
            </div>
        </div>
    </div>

    <!-- Individual Reviews -->
    <div class="space-y-6">
        <div v-for="review in vendor.reviews" :key="review.id"
            class="review-card bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden transition-all hover:shadow-md">
            <!-- Review Header -->
            <div class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-start space-x-4">
                    <!-- Customer Avatar -->
                    <Avatar class="w-12 h-12 flex-shrink-0 ring-2 ring-gray-100">
                        <AvatarImage :src="review.avatar" :alt="review.name" />
                        <AvatarFallback class="bg-indigo-100 text-indigo-700 font-medium text-sm">
                            {{ review.name.charAt(0) }}
                        </AvatarFallback>
                    </Avatar>

                    <!-- Review Header Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ review.name }}
                            </h3>
                            <span class="text-sm text-gray-500 whitespace-nowrap ml-2">
                                {{ review.date }}
                            </span>
                        </div>

                        <div class="flex items-center mt-2">
                            <!-- Rating Stars -->
                            <div class="flex items-center space-x-1">
                                <Star v-for="n in 5" :key="n" :size="16" :class="[
                                    'transition-colors duration-150',
                                    n <= review.rating
                                        ? 'text-amber-400 fill-amber-400'
                                        : 'text-gray-300 fill-gray-300'
                                ]" />
                                <span class="ml-2 text-sm font-medium text-gray-700">
                                    {{ review.rating }}.0
                                </span>
                            </div>

                            <!-- Service Info -->
                            <span v-if="review.service"
                                class="ml-3 px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full">
                                {{ review.service }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Content -->
            <div class="px-6 py-5">
                <p class="text-gray-700 leading-relaxed text-base">
                    {{ review.comment }}
                </p>

                <!-- Review Photos -->
                <div v-if="review.photos && review.photos.length" class="review-photos mt-4 flex flex-wrap gap-3">
                    <div v-for="(photo, index) in review.photos" :key="index"
                        class="relative w-24 h-24 rounded-lg overflow-hidden border border-gray-200 cursor-pointer hover:shadow-md transition-shadow group">
                        <img :src="photo" alt="Review photo" class="w-full h-full object-cover" />
                        <div
                            class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all duration-200">
                        </div>
                    </div>
                </div>

                <!-- Vendor Response -->
                <div v-if="review.response" class="vendor-response mt-6 pt-6 border-t border-gray-100">
                    <div class="flex items-start space-x-3">
                        <Avatar class="w-10 h-10 flex-shrink-0 ring-2 ring-white shadow-sm">
                            <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                            <AvatarFallback class="bg-blue-100 text-blue-700 font-medium text-xs">
                                {{ vendor.name.charAt(0) }}
                            </AvatarFallback>
                        </Avatar>
                        <div class="flex-1">
                            <div class="flex flex-wrap items-center justify-between">
                                <div class="flex items-center">
                                    <h4 class="text-sm font-semibold text-gray-900">Response from {{ vendor.name }}</h4>
                                    <span
                                        class="ml-2 text-xs px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">Vendor</span>
                                </div>
                                <span class="text-xs text-gray-500">{{ review.responded_at }}</span>
                            </div>
                            <p class="text-gray-700 mt-2 text-sm bg-blue-50 rounded-lg p-3 border border-blue-100">
                                {{ review.response }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button
                        class="text-sm text-gray-600 hover:text-blue-600 transition-colors flex items-center py-1 px-2 rounded-lg hover:bg-gray-100">
                        <Star :size="14" class="mr-2 text-gray-400" />
                        <span>Helpful ({{ review.helpful_count || 0 }})</span>
                    </button>
                    <button
                        class="text-sm text-gray-600 hover:text-blue-600 transition-colors flex items-center py-1 px-2 rounded-lg hover:bg-gray-100">
                        <MessageCircle :size="14" class="mr-2 text-gray-400" />
                        <span>Comment</span>
                    </button>
                </div>
                <button
                    class="flex items-center text-sm text-gray-600 hover:text-red-600 transition-colors py-1 px-2 rounded-lg hover:bg-gray-100">
                    <Flag :size="14" class="mr-1 text-gray-400" />
                    Report
                </button>
            </div>
        </div>
    </div>
</template>



<style scoped>
/* Review Summary */
.review-summary-card {
    @apply bg-gradient-to-br from-indigo-50 to-purple-50;
}

.rating-bar {
    @apply flex items-center gap-3 md:gap-4;
}

.rating-label {
    @apply text-xs md:text-sm text-gray-600 w-16 md:w-20 flex-shrink-0;
}

.rating-count {
    @apply text-xs md:text-sm text-gray-600 w-6 md:w-8 flex-shrink-0;
}

/* Individual Reviews */
.review-photos {
    @apply flex gap-2 md:gap-3 mt-3 md:mt-4;
}

.review-photo {
    @apply w-20 h-20 md:w-24 md:h-24 rounded-lg object-cover cursor-pointer hover:scale-105 transition-transform duration-200;
}

.review-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.review-card:hover {
    transform: translateY(-2px);
}

/* Animation for vendor response */
.vendor-response {
    animation: fadeIn 0.5s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
