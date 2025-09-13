<script setup>
defineProps({
    formData: Object,
    isEditing: Boolean
})
</script>

<template>
    <h2 class="text-2xl font-semibold mb-6">Basic Information</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Business Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
            <input v-model="formData.businessName" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="text" />
        </div>

        <!-- Contact Person -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Person *</label>
            <input v-model="formData.contactPerson" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="text" />
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address *</label>
            <input v-model="formData.email" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="email" />
        </div>

        <!-- Phone -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
            <input v-model="formData.phone" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="tel" />
        </div>

        <!-- Website -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Website</label>
            <input v-model="formData.website" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="url" />
        </div>

        <!-- Years of Experience -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
            <input v-model="formData.yearsOfExperience" :disabled="!isEditing"
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                type="number" />
        </div>
    </div>

    <!-- Business Description -->
    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Description</label>
        <textarea v-model="formData.businessDescription" :disabled="!isEditing" rows="4"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
            placeholder="Tell potential clients about your business..."></textarea>
    </div>

    <!-- Business Address -->
    <div class="mt-6">
        <label class="block text-sm font-medium text-gray-700 mb-2">Business Address</label>
        <textarea v-model="formData.businessAddress" :disabled="!isEditing" rows="2"
            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"></textarea>
    </div>

    <!-- Social Media Links -->
    <div class="mt-8">
        <h3 class="text-lg font-medium mb-4">Social Media</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div v-for="(value, platform) in formData.socialMedia" :key="platform">
                <label class="block text-sm font-medium text-gray-700 mb-2 capitalize">{{ platform
                }}</label>
                <input v-model="formData.socialMedia[platform]" :disabled="!isEditing"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                    type="text" />
            </div>
        </div>
    </div>
</template>

<style></style>
