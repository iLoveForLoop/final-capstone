<script setup>
defineProps({
    formData: Object,
    errors: Object,
    vendorCategoryOptions: Array
})
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
            <input v-model="formData.businessName" type="text" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                errors.businessName ? 'border-red-300' : 'border-gray-300']" placeholder="Enter your business name">
            <p v-if="errors.businessName" class="mt-1 text-sm text-red-600">{{ errors.businessName
            }}</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Owner/Contact Person
                *</label>
            <input v-model="formData.full_name" type="text" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                errors.full_name ? 'border-red-300' : 'border-gray-300']" placeholder="Primary contact name">
            <p v-if="errors.full_name" class="mt-1 text-sm text-red-600">{{ errors.full_name
            }}</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Categories *</label>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
            <div v-for="category in vendorCategoryOptions" :key="category" class="flex items-center">
                <input :id="category.id" v-model="formData.vendorCategories" :value="category.id" type="checkbox"
                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label :for="category" class="ml-2 text-sm text-gray-700">{{ category.name
                }}</label>
            </div>
        </div>
        <p v-if="errors.vendorCategories" class="mt-1 text-sm text-red-600">{{
            errors.vendorCategories }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
            <input v-model="formData.phoneNumber" type="tel" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                errors.phoneNumber ? 'border-red-300' : 'border-gray-300']" placeholder="+63 9XX XXX XXXX">
            <p v-if="errors.phoneNumber" class="mt-1 text-sm text-red-600">{{ errors.phoneNumber }}
            </p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input v-model="formData.email" type="email" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                errors.email ? 'border-red-300' : 'border-gray-300']" placeholder="your@email.com">
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
        <textarea v-model="formData.businessDescription" rows="4"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
            placeholder="Tell us about your business and services (500 characters max)" maxlength="500"></textarea>
        <p class="text-sm text-gray-500 mt-1">{{ formData.businessDescription.length }}/500
            characters</p>
    </div>
</template>

<style></style>
