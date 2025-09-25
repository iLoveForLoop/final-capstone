<script setup>
defineProps({
    formData: Object,
    errors: Object
})
</script>

<template>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Payment Instructions
        </label>
        <textarea v-model="formData.payment_instructions" rows="4"
            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            placeholder="Describe how clients can pay you (e.g., GCash: 09XX-XXX-XXXX, Bank transfer: BPI Account XXXXXXXXX, Cash payment accepted, etc.)"></textarea>
    </div>
    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">
            Preferred Contact Method <span class="text-red-500">*</span>
        </label>
        <div class="space-y-3">
            <label class="flex items-center">
                <input v-model="formData.preferred_contact_method" type="radio" value="phone"
                    class="mr-3 text-blue-600">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                </svg>
                Phone Call
            </label>
            <label class="flex items-center">
                <input v-model="formData.preferred_contact_method" type="radio" value="email"
                    class="mr-3 text-blue-600">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Email
            </label>
            <label class="flex items-center">
                <input v-model="formData.preferred_contact_method" type="radio" value="in-app"
                    class="mr-3 text-blue-600">
                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                In-App Messaging
            </label>
        </div>
        <p v-if="errors.preferred_contact_method" class="mt-1 text-sm text-red-600">{{
            errors.preferred_contact_method }}</p>
    </div>
</template>

<style></style>
