<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-6 sm:mb-10">
                <div class="flex justify-center mb-3 sm:mb-4">
                    <div
                        class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Customer Registration</h1>
                <p class="text-sm sm:text-base text-gray-600 px-2">Create your account to start booking services</p>
            </div>

            <!-- Progress Section -->
            <div class="mb-6 sm:mb-8 px-2 sm:px-0">
                <div class="flex justify-between items-center mb-3">
                    <span class="text-xs sm:text-sm font-medium text-gray-700">Step {{ currentStep }} of {{ totalSteps
                        }}</span>
                    <span class="text-xs sm:text-sm font-medium text-blue-600">{{ Math.round((currentStep / totalSteps)
                        * 100) }}% Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2 mb-2">
                    <div class="bg-blue-600 h-1.5 sm:h-2 rounded-full transition-all duration-300 ease-out shadow-sm"
                        :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
                </div>
                <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 px-1">
                    <span class="truncate px-1">Personal</span>
                    <span class="truncate px-1">Contact</span>
                    <span class="truncate px-1">Security</span>
                    <span class="truncate px-1">Review</span>
                </div>
            </div>

            <!-- Form Container -->
            <div
                class="bg-white rounded-xl sm:rounded-xl shadow-md sm:shadow-lg border border-gray-100 overflow-hidden mx-2 sm:mx-0">
                <!-- Step Header -->
                <div class="border-b bg-blue-600 text-white px-4 sm:px-6 py-3 sm:py-4">
                    <h2 class="text-base sm:text-lg font-semibold">{{ stepTitle }}</h2>
                </div>

                <!-- Form Content -->
                <div class="p-4 sm:p-6">
                    <!-- Step 1: Personal Information -->
                    <div v-show="currentStep === 1" class="space-y-4 sm:space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                            <div>
                                <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name *
                                </label>
                                <input id="firstName" v-model="formData.first_name" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.first_name }" />
                                <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name }}
                                </p>
                            </div>

                            <div>
                                <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name *
                                </label>
                                <input id="lastName" v-model="formData.last_name" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.last_name }" />
                                <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}</p>
                            </div>
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email Address *
                            </label>
                            <input id="email" v-model="formData.email" type="email"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.email }" />
                            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                        </div>

                        <div>
                            <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">
                                Phone Number *
                            </label>
                            <input id="phoneNumber" v-model="formData.phoneNumber" type="tel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.phoneNumber }" />
                            <p v-if="errors.phoneNumber" class="mt-1 text-sm text-red-600">{{ errors.phoneNumber }}</p>
                        </div>
                    </div>

                    <!-- Step 2: Contact Information -->
                    <div v-show="currentStep === 2" class="space-y-4 sm:space-y-6">
                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                Address *
                            </label>
                            <textarea id="address" v-model="formData.address" rows="3"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.address }"
                                placeholder="Enter your complete address"></textarea>
                            <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                Preferred Contact Method *
                            </label>
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input v-model="formData.preferred_contact_method" type="radio" value="email"
                                        class="text-blue-600 focus:ring-blue-500" />
                                    <span class="ml-2 text-sm text-gray-700">Email</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="formData.preferred_contact_method" type="radio" value="phone"
                                        class="text-blue-600 focus:ring-blue-500" />
                                    <span class="ml-2 text-sm text-gray-700">Phone</span>
                                </label>
                                <label class="flex items-center">
                                    <input v-model="formData.preferred_contact_method" type="radio" value="both"
                                        class="text-blue-600 focus:ring-blue-500" />
                                    <span class="ml-2 text-sm text-gray-700">Both Email and Phone</span>
                                </label>
                            </div>
                            <p v-if="errors.preferred_contact_method" class="mt-1 text-sm text-red-600">{{
                                errors.preferred_contact_method }}</p>
                        </div>
                    </div>

                    <!-- Step 3: Account Security -->
                    <div v-show="currentStep === 3" class="space-y-4 sm:space-y-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password *
                            </label>
                            <input id="password" v-model="formData.password" type="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.password }" @input="checkPasswordStrength" />
                            <div v-if="formData.password" class="mt-2">
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs text-gray-600">Password Strength</span>
                                    <span class="text-xs font-medium" :class="passwordStrengthClass">
                                        {{ passwordStrengthText }}
                                    </span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1">
                                    <div class="h-1 rounded-full transition-all duration-300"
                                        :class="passwordStrengthBarClass"
                                        :style="{ width: `${(passwordStrength / 4) * 100}%` }"></div>
                                </div>
                            </div>
                            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>

                            <div class="mt-3 space-y-1">
                                <div class="flex items-center text-xs"
                                    :class="passwordRequirements.length ? 'text-green-600' : 'text-gray-500'">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            :d="passwordRequirements.length ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' : 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'" />
                                    </svg>
                                    At least 8 characters
                                </div>
                                <div class="flex items-center text-xs"
                                    :class="passwordRequirements.uppercase ? 'text-green-600' : 'text-gray-500'">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            :d="passwordRequirements.uppercase ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' : 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'" />
                                    </svg>
                                    One uppercase letter
                                </div>
                                <div class="flex items-center text-xs"
                                    :class="passwordRequirements.lowercase ? 'text-green-600' : 'text-gray-500'">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            :d="passwordRequirements.lowercase ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' : 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'" />
                                    </svg>
                                    One lowercase letter
                                </div>
                                <div class="flex items-center text-xs"
                                    :class="passwordRequirements.number ? 'text-green-600' : 'text-gray-500'">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            :d="passwordRequirements.number ? 'M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z' : 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'" />
                                    </svg>
                                    One number
                                </div>
                            </div>
                        </div>

                        <div>
                            <label for="confirmPassword" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirm Password *
                            </label>
                            <input id="confirmPassword" v-model="formData.password_confirmation" type="password"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                :class="{ 'border-red-500': errors.password_confirmation || (formData.password_confirmation && !passwordsMatch) }" />
                            <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{
                                errors.password_confirmation }}</p>
                            <p v-if="formData.password_confirmation && !passwordsMatch"
                                class="mt-1 text-sm text-red-600">Passwords do not match</p>
                            <p v-if="formData.password_confirmation && passwordsMatch"
                                class="mt-1 text-sm text-green-600">Passwords match</p>
                        </div>
                    </div>

                    <!-- Step 4: Review & Submit -->
                    <div v-show="currentStep === 4" class="space-y-4 sm:space-y-6">
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Review Your Information</h3>

                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Name:</span>
                                    <span class="text-sm font-medium">{{ formData.first_name }} {{ formData.last_name
                                        }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Email:</span>
                                    <span class="text-sm font-medium">{{ formData.email }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Phone:</span>
                                    <span class="text-sm font-medium">{{ formData.phoneNumber }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Address:</span>
                                    <span class="text-sm font-medium">{{ formData.address }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600">Contact Method:</span>
                                    <span class="text-sm font-medium capitalize">{{ formData.preferred_contact_method
                                        }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <input id="terms" v-model="termsAccepted" type="checkbox"
                                class="mt-1 text-blue-600 focus:ring-blue-500"
                                :class="{ 'border-red-500': errors.terms }" />
                            <label for="terms" class="ml-2 text-sm text-gray-700">
                                I agree to the <a href="#" class="text-blue-600 hover:text-blue-800">Terms of
                                    Service</a> and <a href="#" class="text-blue-600 hover:text-blue-800">Privacy
                                    Policy</a> *
                            </label>
                        </div>
                        <p v-if="errors.terms" class="text-sm text-red-600">{{ errors.terms }}</p>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between gap-2">
                    <button v-if="currentStep > 1" @click="prevStep" type="button"
                        class="px-3 sm:px-5 py-2 text-sm sm:text-base text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors flex-1 sm:flex-none">
                        <div class="flex items-center justify-center sm:justify-start">
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 19l-7-7 7-7"></path>
                            </svg>
                            Previous
                        </div>
                    </button>
                    <div v-else class="flex-1 sm:flex-none"></div>

                    <div class="flex space-x-2 sm:space-x-3 flex-1 sm:flex-none justify-end">
                        <button v-if="currentStep < totalSteps" @click="nextStep" type="button"
                            :disabled="!isCurrentStepValid" :class="[
                                'px-3 sm:px-5 py-2 text-sm sm:text-base rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 sm:focus:ring-offset-2 transition-colors flex items-center justify-center w-full',
                                isCurrentStepValid
                                    ? 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                                    : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                            ]">
                            Next
                            <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                        <button v-else @click="submitForm" :disabled="isLoading || !isCurrentStepValid" type="button"
                            :class="[
                                'px-4 sm:px-6 py-2 text-sm sm:text-base rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 sm:focus:ring-offset-2 transition-colors flex items-center justify-center w-full',
                                (isLoading || !isCurrentStepValid)
                                    ? 'bg-green-400 text-white cursor-not-allowed'
                                    : 'bg-green-600 text-white hover:bg-green-700 shadow-sm'
                            ]">
                            <svg v-if="isLoading"
                                class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ isLoading ? 'Creating Account...' : 'Create Account' }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Help Text -->
            <div class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-gray-500 px-2">
                <p>Already have an account? <a :href="route('login')"
                        class="text-blue-600 hover:text-blue-800 font-medium">Sign in here</a></p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, reactive, computed, watch } from 'vue'
import { onUnmounted } from 'vue'

const page = usePage()

// Form data structure for customer/organizer
const formData = useForm({
    // Personal Information
    first_name: '',
    last_name: '',
    email: '',
    phoneNumber: '',

    // Contact Information
    address: '',
    preferred_contact_method: '',

    // Password
    password: '',
    password_confirmation: '',

    // Role (customer/organizer)
    is_vendor: false
})

// UI State
const currentStep = ref(1)
const totalSteps = 4
const isLoading = ref(false)
const errors = reactive({})
const termsAccepted = ref(false)

// Password UI state
const passwordStrength = ref(0)
const passwordRequirements = reactive({
    length: false,
    uppercase: false,
    lowercase: false,
    number: false
})

// Computed properties
const passwordsMatch = computed(() => {
    if (!formData.password && !formData.password_confirmation) return null
    return formData.password === formData.password_confirmation
})

const stepTitle = computed(() => {
    const titles = {
        1: 'Personal Information',
        2: 'Contact Information',
        3: 'Account Security',
        4: 'Review & Submit'
    }
    return titles[currentStep.value]
})

// Password strength visual feedback
const passwordStrengthText = computed(() => {
    const strengths = ['Very Weak', 'Weak', 'Fair', 'Good', 'Strong']
    return strengths[passwordStrength.value] || 'Very Weak'
})

const passwordStrengthClass = computed(() => {
    const classes = {
        0: 'text-red-600',
        1: 'text-red-600',
        2: 'text-orange-600',
        3: 'text-yellow-600',
        4: 'text-green-600'
    }
    return classes[passwordStrength.value] || 'text-red-600'
})

const passwordStrengthBarClass = computed(() => {
    const classes = {
        0: 'bg-red-500',
        1: 'bg-red-500',
        2: 'bg-orange-500',
        3: 'bg-yellow-500',
        4: 'bg-green-500'
    }
    return classes[passwordStrength.value] || 'bg-red-500'
})

// Validation computed properties
const isStep1Valid = computed(() => {
    return formData.first_name.trim() !== '' &&
        formData.last_name.trim() !== '' &&
        formData.email.trim() !== '' &&
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email) &&
        formData.phoneNumber.trim() !== ''
})

const isStep2Valid = computed(() => {
    return formData.address.trim() !== '' &&
        formData.preferred_contact_method !== ''
})

const isStep3Valid = computed(() => {
    return formData.password.length >= 8 &&
        passwordStrength.value >= 3 &&
        passwordsMatch.value === true
})

const isStep4Valid = computed(() => {
    return termsAccepted.value === true
})

const isCurrentStepValid = computed(() => {
    switch (currentStep.value) {
        case 1: return isStep1Valid.value
        case 2: return isStep2Valid.value
        case 3: return isStep3Valid.value
        case 4: return isStep4Valid.value
        default: return false
    }
})

// Methods
const validateCurrentStep = () => {
    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key])

    let isValid = true

    switch (currentStep.value) {
        case 1:
            if (!formData.first_name.trim()) {
                errors.first_name = 'First name is required'
                isValid = false
            }
            if (!formData.last_name.trim()) {
                errors.last_name = 'Last name is required'
                isValid = false
            }
            if (!formData.email.trim()) {
                errors.email = 'Email is required'
                isValid = false
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                errors.email = 'Please enter a valid email address'
                isValid = false
            }
            if (!formData.phoneNumber.trim()) {
                errors.phoneNumber = 'Phone number is required'
                isValid = false
            }
            break

        case 2:
            if (!formData.address.trim()) {
                errors.address = 'Address is required'
                isValid = false
            }
            if (!formData.preferred_contact_method) {
                errors.preferred_contact_method = 'Preferred contact method is required'
                isValid = false
            }
            break

        case 3:
            if (formData.password.length < 8) {
                errors.password = 'Password must be at least 8 characters long'
                isValid = false
            }
            if (passwordStrength.value < 3) {
                errors.passwordStrength = 'Password is too weak'
                isValid = false
            }
            if (!passwordsMatch.value) {
                errors.password_confirmation = 'Passwords do not match'
                isValid = false
            }
            break

        case 4:
            if (!termsAccepted.value) {
                errors.terms = 'You must accept the terms and conditions'
                isValid = false
            }
            break
    }

    return isValid
}

const nextStep = () => {
    if (validateCurrentStep() && currentStep.value < totalSteps) {
        currentStep.value++
    }
}

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const checkPasswordStrength = () => {
    const password = formData.password

    // Check requirements
    passwordRequirements.length = password.length >= 8
    passwordRequirements.uppercase = /[A-Z]/.test(password)
    passwordRequirements.lowercase = /[a-z]/.test(password)
    passwordRequirements.number = /\d/.test(password)

    // Calculate strength (0-4)
    let strength = 0
    if (passwordRequirements.length) strength++
    if (passwordRequirements.uppercase) strength++
    if (passwordRequirements.lowercase) strength++
    if (passwordRequirements.number) strength++

    passwordStrength.value = strength
}

const submitForm = () => {
    if (!validateCurrentStep()) return;
    isLoading.value = true;

    formData
        .transform((data) => {
            const fd = new FormData();
            Object.keys(data).forEach((key) => {
                if (data[key] !== null) {
                    fd.append(key, data[key]);
                }
            });
            return fd;
        })
        .post(route('register'), {
            onFinish: () => {
                isLoading.value = false;
            },
        });
};

// Watch for password changes to update strength
watch(() => formData.password, checkPasswordStrength)
</script>

<style scoped>
/* Custom styles for better mobile experience */
@media (max-width: 640px) {
    .min-h-screen {
        min-height: 100vh;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
}
</style>
