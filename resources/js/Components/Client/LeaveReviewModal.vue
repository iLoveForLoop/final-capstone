<script setup>
import { ref, watch, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle
} from '@/Components/ui/dialog'
import { Button } from '@/Components/ui/button'
import { Card, CardContent } from '@/Components/ui/card'
import { Badge } from '@/Components/ui/badge'
import { Label } from '@/Components/ui/label'
import { Textarea } from '@/Components/ui/textarea'
import { Star, Loader2 } from 'lucide-vue-next'
import { useToast } from 'vue-toastification'

// Props
const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false
    },
    serviceData: {
        type: Object,
    }
})

const toast = useToast()

// Emits
const emit = defineEmits(['close', 'submit'])

console.log('vendor id: ', props.serviceData.vendorId);

// Reactive data
const rating = ref(0)
const hoverRating = ref(0)
const reviewText = ref('')
const isSubmitting = ref(false)

const ratingLabels = [
    'Poor - Very dissatisfied',
    'Fair - Below expectations',
    'Good - Met expectations',
    'Very Good - Exceeded expectations',
    'Excellent - Outstanding experience'
]

// Methods
const setRating = (value) => {
    rating.value = value
}

const handleOpenChange = (open) => {
    if (!open) {
        closeModal()
    }
}

const closeModal = () => {
    emit('close')
    resetForm()
}

const submitReview = async () => {
    if (rating.value === 0) return

    isSubmitting.value = true

    const reviewData = {
        booking_id: props.serviceData.bookingId,
        vendor_id: props.serviceData.vendorId,
        rating: rating.value,
        comment: reviewText.value.trim(),
    }



    try {
        await router.post(route('client.review.store'), reviewData, {
            preserveScroll: true,
            onSuccess: () => {
                emit('submit', reviewData)

                toast.success('Review submitted sucessfully')

                closeModal()
            },
            onError: (errors) => {
                console.error('Validation errors:', errors.message)
                alert('Failed to submit review. Please try again.')
            },
            onFinish: () => {
                isSubmitting.value = false
            }
        })
    } catch (error) {
        console.error('Error submitting review:', error)
        alert('Failed to submit review. Please try again.')
        isSubmitting.value = false
    }
}

const resetForm = () => {
    rating.value = 0
    hoverRating.value = 0
    reviewText.value = ''
    isSubmitting.value = false
}


</script>

<template>
    <Dialog :open="isOpen" @update:open="handleOpenChange">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Leave a Review</DialogTitle>
                <DialogDescription>
                    Share your experience with the service you booked
                </DialogDescription>
            </DialogHeader>

            <!-- Service Info Card -->
            <Card class="mb-4">
                <CardContent class="p-4">
                    <h4 class="font-medium text-gray-900">{{ serviceData.name }}</h4>
                    <p class="text-sm text-gray-600 mt-1">Provided by: {{ serviceData.provider }}</p>
                    <p class="text-sm text-gray-600 mt-1">Date: {{ serviceData.date }}</p>
                    <Badge variant="outline" class="mt-2">Booking ID: #{{ serviceData.bookingId }}</Badge>
                </CardContent>
            </Card>

            <!-- Rating Section -->
            <div class="mb-6">
                <Label class="text-sm font-medium mb-3 block">
                    How would you rate this service?
                </Label>
                <div class="flex items-center space-x-1 mb-2">
                    <button v-for="star in 5" :key="star" @click="setRating(star)" @mouseover="hoverRating = star"
                        @mouseleave="hoverRating = 0" class="p-1 transition-colors hover:scale-110 transform"
                        type="button">
                        <Star :size="32"
                            :class="star <= (hoverRating || rating) ? 'text-yellow-400 fill-yellow-400' : 'text-gray-300'" />
                    </button>
                </div>
                <div v-if="rating > 0" class="text-sm text-muted-foreground">
                    {{ ratingLabels[rating - 1] }}
                </div>
            </div>

            <!-- Review Text -->
            <div class="mb-6">
                <Label for="review" class="text-sm font-medium mb-2 block">
                    Share your experience (optional)
                </Label>
                <Textarea id="review" v-model="reviewText"
                    placeholder="Tell others about your experience with this service..." :maxlength="500" rows="4"
                    class="resize-none" />
                <div class="flex justify-end mt-1">
                    <span class="text-xs text-muted-foreground">
                        {{ reviewText.length }}/500 characters
                    </span>
                </div>
            </div>

            <!-- Action Buttons -->
            <DialogFooter class="flex-col sm:flex-row gap-2">
                <Button variant="outline" @click="closeModal" class="w-full sm:w-auto">
                    Cancel
                </Button>
                <Button @click="submitReview" :disabled="rating === 0 || isSubmitting"
                    class="w-full sm:w-auto bg-blue-500 text-white">
                    <Loader2 v-if="isSubmitting" class="mr-2 h-4 w-4 animate-spin" />
                    {{ isSubmitting ? 'Submitting...' : 'Submit Review' }}
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
