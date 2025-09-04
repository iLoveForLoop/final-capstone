<script setup>
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Progress } from '@/Components/ui/progress';
import { Star } from 'lucide-vue-next';

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
        <Card v-for="review in vendor.reviews" :key="review.id" class="review-card">
            <CardHeader class="px-6 py-5 border-b border-gray-100">
                <div class="flex items-start space-x-4">
                    <!-- Avatar -->
                    <Avatar class="w-12 h-12 flex-shrink-0 ring-2 ring-gray-100">
                        <AvatarImage :src="review.avatar" :alt="review.name" />
                        <AvatarFallback class="bg-indigo-100 text-indigo-700 font-medium text-sm">
                            {{ review.name.charAt(0) }}
                        </AvatarFallback>
                    </Avatar>

                    <!-- Review Header Info -->
                    <div class="flex-1 min-w-0">
                        <CardTitle class="text-base font-semibold text-gray-900 truncate">
                            {{ review.name }}
                        </CardTitle>

                        <div class="flex items-center justify-between mt-2">
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

                            <!-- Date -->
                            <span class="text-sm text-gray-500 whitespace-nowrap">
                                {{ formatDate(review.date) }}
                            </span>
                        </div>
                    </div>
                </div>
            </CardHeader>
            <CardContent>
                <p class="text-gray-700 leading-relaxed text-base md:text-lg mt-3">{{
                    review.comment
                }}</p>
                <div v-if="review.photos" class="review-photos mt-4">
                    <img v-for="photo in review.photos" :key="photo" :src="photo" alt="Review photo"
                        class="review-photo" />
                </div>
            </CardContent>
        </Card>
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
</style>
