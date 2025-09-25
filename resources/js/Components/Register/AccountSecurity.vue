<script setup>
import { computed, ref } from 'vue'

const props = defineProps({
    errors: Object,
    formData: Object,
    passwordStrength: Number,
    passwordRequirements: Object,
    passwordsMatch: Boolean
})

const showPassword = ref(false)
const showPasswordConfirm = ref(false)

const passwordStrengthColor = computed(() => {
    if (props.passwordStrength < 2) return 'bg-red-500'
    if (props.passwordStrength < 4) return 'bg-yellow-500'
    return 'bg-green-500'
})

const passwordStrengthWidth = computed(() => {
    return `${(props.passwordStrength / 4) * 100}%`
})

const passwordStrengthText = computed(() => {
    if (props.passwordStrength === 0) return 'Very Weak'
    if (props.passwordStrength === 1) return 'Weak'
    if (props.passwordStrength === 2) return 'Fair'
    if (props.passwordStrength === 3) return 'Good'
    return 'Strong'
})
</script>

<template>
    <!-- Password Section -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
        <h4 class="font-medium text-blue-900 mb-4 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
            </svg>
            Account Security
        </h4>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password *</label>
                <div class="relative">
                    <input v-model="formData.password" :type="showPassword ? 'text' : 'password'" :class="['w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                        errors.password || errors.passwordStrength ? 'border-red-300' : 'border-gray-300']"
                        placeholder="Create a strong password" @input="checkPasswordStrength">
                    <button type="button" @click="showPassword = !showPassword"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg v-if="showPassword" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M21.536 15.536l-12-12" />
                        </svg>
                    </button>
                </div>
                <!-- Password Strength Indicator -->
                <div class="mt-2">
                    <div class="flex items-center space-x-1">
                        <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full transition-all duration-300" :class="passwordStrengthColor"
                                :style="{ width: passwordStrengthWidth }"></div>
                        </div>
                        <span class="text-xs text-gray-600">{{ passwordStrengthText }}</span>
                    </div>
                    <ul class="text-xs text-gray-600 mt-2 space-y-1">
                        <li :class="passwordRequirements.length ? 'text-green-600' : 'text-gray-400'">
                            ✓ At least 8 characters
                        </li>
                        <li :class="passwordRequirements.uppercase ? 'text-green-600' : 'text-gray-400'">
                            ✓ One uppercase letter
                        </li>
                        <li :class="passwordRequirements.lowercase ? 'text-green-600' : 'text-gray-400'">
                            ✓ One lowercase letter
                        </li>
                        <li :class="passwordRequirements.number ? 'text-green-600' : 'text-gray-400'">
                            ✓ One number
                        </li>
                    </ul>
                </div>
                <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}
                </p>
                <p v-if="errors.passwordStrength" class="mt-1 text-sm text-red-600">{{
                    errors.passwordStrength }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password
                    *</label>
                <div class="relative">
                    <input v-model="formData.password_confirmation" :type="showPasswordConfirm ? 'text' : 'password'"
                        :class="['w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                            errors.password_confirmation ? 'border-red-300' : 'border-gray-300']"
                        placeholder="Confirm your password">
                    <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg v-if="showPasswordConfirm" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M21.536 15.536l-12-12" />
                        </svg>
                    </button>
                </div>
                <div v-if="passwordsMatch === false" class="mt-1 text-sm text-red-600">
                    Passwords do not match
                </div>
                <div v-else-if="passwordsMatch === true" class="mt-1 text-sm text-green-600">
                    Passwords match ✓
                </div>
                <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{
                    errors.password_confirmation }}</p>
            </div>
        </div>
    </div>
</template>

<style></style>
