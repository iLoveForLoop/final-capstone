<script setup>
defineProps({
    formData: Object,
    errors: Object
})

const termsAccepted = defineModel('termsAccepted');

</script>

<template>
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <div class="flex items-start">
            <svg class="h-6 w-6 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-blue-900">Review Your Information</h3>
                <p class="mt-2 text-sm text-blue-700">Please review all the information you've
                    provided before submitting your registration. You can go back to any step to
                    make changes.</p>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
        <div class="bg-white border border-gray-200 rounded-lg p-4">
            <h4 class="font-medium text-gray-900 mb-3">Business Information</h4>
            <dl class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-600">Business Name:</dt>
                    <dd class="text-gray-900 font-medium">{{ formData.businessName || 'Not provided'
                        }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Address:</dt>
                    <dd class="text-gray-900 font-medium">{{ formData.address || 'Not provided'
                        }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Contact:</dt>
                    <dd class="text-gray-900">{{ formData.full_name || 'Not provided' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Email:</dt>
                    <dd class="text-gray-900">{{ formData.email || 'Not provided' }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-600">Categories:</dt>
                    <dd class="text-gray-900">{{ formData.vendorCategories.length }} selected
                    </dd>
                </div>
            </dl>
        </div>

        <!-- <div class="bg-white border border-gray-200 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Service Details</h4>
                                <dl class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Location:</dt>
                                        <dd class="text-gray-900">{{ formData.address ? 'Provided' : 'Not provided' }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Travel:</dt>
                                        <dd class="text-gray-900">{{ formData.willingToTravel ? 'Yes' : 'No' }}</dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Payment Methods:</dt>
                                        <dd class="text-gray-900">{{ formData.acceptedPaymentMethods.length }} methods
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Subscription:</dt>
                                        <dd class="text-gray-900">{{subscriptionPlans.find(p => p.value ===
                                            formData.subscriptionPlan)?.label || 'Not selected'}}</dd>
                                    </div>
                                </dl>
                            </div> -->
    </div>

    <!-- Terms and Conditions -->
    <div :class="['bg-gray-50 border rounded-lg p-4', errors.terms ? 'border-red-300' : 'border-gray-200']">
        <div class="flex items-start">
            <input v-model="termsAccepted" type="checkbox"
                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1">
            <div class="ml-3">
                <label class="text-sm text-gray-700">
                    I agree to Eventory's <a href="#" class="text-indigo-600 hover:text-indigo-500">Terms of Service</a>
                    and <a href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>. I
                    confirm that all information provided is accurate and up-to-date.
                </label>
            </div>
        </div>
        <p v-if="errors.terms" class="mt-1 text-sm text-red-600">{{ errors.terms }}</p>
    </div>
</template>

<style></style>
