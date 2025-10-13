<script setup>
defineProps({
    service: Object,
    form: Object,
    selectedDishes: Object,
    totalPrice: Number
})

const acceptedTerms = defineModel('acceptedTerms')

const formatPrice = (price) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP'
    }).format(price);
};
</script>

<template>
    <h3 class="text-xl font-semibold text-gray-900 mb-4">Review Your Booking</h3>

    <!-- Service Details -->
    <div class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h4 class="font-semibold text-lg text-gray-900 mb-4">Service Details</h4>
        <div class="flex items-start space-x-4">
            <!-- Service Image -->
            <div class="flex-shrink-0 w-24 h-24 rounded-lg overflow-hidden border border-gray-200">
                <img v-if="service.image_url" :src="service.image_url" :alt="service.name"
                    class="w-full h-full object-cover">
                <div v-else class="w-full h-full bg-gray-100 flex items-center justify-center">
                    <svg class="w-10 h-10 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
            </div>

            <!-- Service Info -->
            <div class="flex-1">
                <h5 class="text-xl font-bold text-gray-900">{{ service.name }}</h5>
                <p class="text-sm text-gray-600 mb-3">{{ service.category_name }}</p>

                <!-- Catering Service Details -->
                <div v-if="service.category_name === 'Catering' && service.catering_service"
                    class="text-sm text-gray-700 space-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Base Price: {{ formatPrice(service.catering_service.price)
                            }}
                            per person</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Menu Type: {{ service.catering_service.is_customizable ?
                            'Customizable' : 'Fixed' }}</span>
                    </div>
                </div>

                <!-- Photography Service Details -->
                <div v-else-if="service.category_name === 'Photography' && service.photography_service"
                    class="text-sm text-gray-700 space-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Duration: {{ service.photography_service.duration }}
                            hours</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Style: {{ service.photography_service.style }}</span>
                    </div>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Equipment: {{ service.photography_service.equipment ?
                            'Included' : 'Not Included' }}</span>
                    </div>
                </div>

                <!-- General Service Details -->
                <div v-else class="text-sm text-gray-700 space-y-2">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                                clip-rule="evenodd" />
                        </svg>
                        <span>Base Price: {{ formatPrice(service.price) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selected Dishes (for catering) -->
    <div v-if="service.category_name === 'Catering' && Object.keys(selectedDishes).some(category => selectedDishes[category].length > 0)"
        class="bg-white border border-gray-200 rounded-lg p-6 shadow-sm">
        <h4 class="font-semibold text-lg text-gray-900 mb-4">Selected Menu Items</h4>
        <div class="space-y-4">
            <div v-for="(dishes, category) in selectedDishes" :key="category">
                <h5 class="text-base font-medium text-gray-700 mb-2">{{ category }}</h5>
                <div class="flex flex-wrap gap-2">
                    <span v-for="dish in dishes" :key="dish"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 border border-blue-200">
                        {{ dish }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Summary -->
    <div class="bg-gray-50 p-6 rounded-lg border border-gray-200">
        <h4 class="font-semibold text-lg text-gray-900 mb-4">Event Summary</h4>
        <div class="space-y-3 text-sm">
            <div class="flex justify-between">
                <span class="text-gray-600">Event Name:</span>
                <span class="font-medium text-gray-900">{{ form.name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Location:</span>
                <span class="font-medium text-gray-900">{{ form.location }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-600">Date & Time:</span>
                <span class="font-medium text-gray-900">{{ form.event_date }} at {{
                    form.event_time
                    }}</span>
            </div>
            <div v-if="form.pax" class="flex justify-between">
                <span class="text-gray-600">Number of Guests:</span>
                <span class="font-medium text-gray-900">{{ form.pax }} people</span>
            </div>
            <div v-if="form.description" class="flex justify-between">
                <span class="text-gray-600">Description:</span>
                <span class="font-medium text-gray-900 text-right max-w-xs">{{
                    form.description
                    }}</span>
            </div>
        </div>
    </div>

    <!-- Price Breakdown -->
    <div class="bg-blue-50 p-6 rounded-lg border border-blue-200">
        <h4 class="text-lg font-semibold text-gray-900 mb-4">Price Breakdown</h4>

        <div class="space-y-3 text-sm mb-4">
            <div v-if="service.category_name === 'Catering'" class="flex justify-between">
                <span class="text-gray-600">Base price ({{
                    formatPrice(service.catering_service?.price || service.price) }} ×
                    {{
                        form.pax || 0 }} guests)</span>
                <span class="font-medium text-gray-900">{{
                    formatPrice((service.catering_service?.price ||
                        service.price) * (form.pax || 0)) }}</span>
            </div>
            <div v-else class="flex justify-between">
                <span class="text-gray-600">Service fee</span>
                <span class="font-medium text-gray-900">{{
                    formatPrice(service.photography_service?.price
                        || service.price) }}</span>
            </div>

            <!-- Additional charges could be added here -->
            <div class="flex justify-between text-green-600">
                <span>Discount</span>
                <span>-{{ formatPrice(0) }}</span>
            </div>
        </div>

        <div class="border-t border-blue-200 pt-3">
            <div class="flex justify-between items-center">
                <span class="text-lg font-semibold text-gray-900">Total Estimated
                    Cost:</span>
                <span class="text-2xl font-bold text-blue-600">{{
                    formatPrice(totalPrice)
                    }}</span>
            </div>
        </div>

        <p class="text-sm text-gray-600 mt-3">Final pricing may vary based on specific
            requirements</p>
    </div>

    <!-- Terms and Conditions -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
        <div class="flex items-start">
            <input id="terms" v-model="acceptedTerms" type="checkbox"
                class="mt-1 mr-3 h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" />
            <label for="terms" class="text-sm text-gray-700">
                <span class="font-medium">I agree to the Terms and Conditions</span>
                <p class="mt-1 text-gray-600">
                    By submitting this booking request, you agree to our terms of
                    service.
                    A confirmation email will be sent upon acceptance of your request.
                    Payment details and final confirmation will be provided by the
                    service provider.
                </p>
            </label>
        </div>
    </div>
</template>

<style></style>
