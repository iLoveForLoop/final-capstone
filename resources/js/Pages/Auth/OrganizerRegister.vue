<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import axios from 'axios'
import { ref, reactive, computed, watch } from 'vue'

const page = usePage()

// Form data structure for customer/organizer
const formData = useForm({
    // Personal Information - will be combined into full_name for backend
    first_name: '',
    last_name: '',
    email: '',
    phoneNumber: '',
    address: '',

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

// Email verification tracking
const verifiedEmail = ref('')
const isEmailVerified = ref(false)
const originalEmail = ref('')

// OTP State
const otp = ref(['', '', '', '', '', ''])
const otpInputs = ref([])
const resendCooldown = ref(0)
const isOtpSent = ref(false)

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

const fullName = computed(() => {
    return `${formData.first_name} ${formData.last_name}`.trim()
})

const otpCode = computed(() => {
    return otp.value.join('')
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

const stepTitle = computed(() => {
    const titles = {
        1: 'Personal Information',
        2: 'Account Security',
        3: 'Email Verification',
        4: 'Review & Submit'
    }
    return titles[currentStep.value]
})

// Validation computed properties
const isStep1Valid = computed(() => {
    return formData.first_name.trim() !== '' &&
        formData.last_name.trim() !== '' &&
        formData.email.trim() !== '' &&
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email) &&
        formData.phoneNumber.trim() !== '' &&
        formData.address.trim() !== ''
})

const isStep2Valid = computed(() => {
    return formData.password.length >= 8 &&
        passwordStrength.value >= 3 &&
        passwordsMatch.value === true
})

const isStep3Valid = computed(() => {
    // If email is already verified, step is always valid
    if (isEmailVerified.value) return true
    // Otherwise, require OTP verification
    return otpCode.value.length === 6 && /^\d+$/.test(otpCode.value)
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
const handleEmailChange = () => {
    // If email changes and it was previously verified, reset verification
    if (isEmailVerified.value && formData.email !== verifiedEmail.value) {
        isEmailVerified.value = false
        verifiedEmail.value = ''
        resetOtp()
    }
}

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
            if (!formData.address.trim()) {
                errors.address = 'Address is required'
                isValid = false
            }
            break

        case 2:
            if (formData.password.length < 8) {
                errors.password = 'Password must be at least 8 characters long'
                isValid = false
            }
            if (passwordStrength.value < 3) {
                errors.password = 'Password is too weak'
                isValid = false
            }
            if (!passwordsMatch.value) {
                errors.password_confirmation = 'Passwords do not match'
                isValid = false
            }
            break

        case 3:
            // Only validate OTP if email is not already verified
            if (!isEmailVerified.value) {
                if (otpCode.value.length !== 6) {
                    errors.otp = 'Please enter the 6-digit verification code'
                    isValid = false
                } else if (!/^\d+$/.test(otpCode.value)) {
                    errors.otp = 'Verification code must contain only numbers'
                    isValid = false
                }
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

const nextStep = async () => {
    if (!validateCurrentStep()) return

    // If moving to OTP step and email is not verified, send the OTP
    if (currentStep.value === 2 && currentStep.value + 1 === 3 && !isEmailVerified.value) {
        await sendOtp()
    }

    // If moving from OTP step and email is not verified, verify the OTP
    if (currentStep.value === 3 && currentStep.value + 1 === 4 && !isEmailVerified.value) {
        const verified = await verifyOtp()
        if (!verified) return
    }

    if (currentStep.value < totalSteps) {
        currentStep.value++
    }
}

const prevStep = () => {
    if (currentStep.value > 1) {
        currentStep.value--
    }
}

const sendOtp = async () => {
    if (isLoading.value) return // ✅ prevent multiple clicks while loading
    isLoading.value = true

    try {
        const res = await axios.post('/email/send-otp', { email: formData.email })
        console.log('Response Data: ', res.data)
        isOtpSent.value = true
        resendCooldown.value = 30
        errors.otp = null
        startResendCooldown()
    } catch (error) {
        console.log('Error Sending OTP: ', error.response?.data?.message || error.message)
        errors.otp = error.response?.data?.message || 'Error sending OTP.'
    } finally {
        isLoading.value = false // ✅ always reset loading
    }
}

const verifyOtp = async () => {
    // Simulate OTP verification
    isLoading.value = true
    console.log('CODE: ', otpCode.value)

    try {
        const res = await axios.post('/email/verify-otp', { email: formData.email, otp_code: otpCode.value })
        console.log("VERIFICATION RES: ", res.data)
        // OTP verified successfully
        isEmailVerified.value = true
        verifiedEmail.value = formData.email
        isLoading.value = false
        return true
    } catch (error) {
        console.log('Error Verifying OTP: ', error.message)
        errors.otp = error.response.data.message
        isLoading.value = false
        return false
    }
}

const resendOtp = async () => {
    if (resendCooldown.value > 0) return

    await sendOtp()
}

const resetOtp = () => {
    otp.value = ['', '', '', '', '', '']
    isOtpSent.value = false
    resendCooldown.value = 0
}

const startResendCooldown = () => {
    const interval = setInterval(() => {
        resendCooldown.value--
        if (resendCooldown.value <= 0) {
            clearInterval(interval)
        }
    }, 1000)
}

const handleOtpInput = (index, event) => {
    const value = event.target.value

    // Only allow numbers
    if (value && !/^\d$/.test(value)) {
        otp.value[index] = ''
        return
    }

    otp.value[index] = value

    // Auto-focus next input
    if (value && index < 5) {
        otpInputs.value[index + 1]?.focus()
    }
}

const handleOtpKeydown = (index, event) => {
    // Handle backspace
    if (event.key === 'Backspace') {
        if (!otp.value[index] && index > 0) {
            otpInputs.value[index - 1]?.focus()
        }
        otp.value[index] = ''
    }

    // Handle arrow keys
    if (event.key === 'ArrowLeft' && index > 0) {
        otpInputs.value[index - 1]?.focus()
    }
    if (event.key === 'ArrowRight' && index < 5) {
        otpInputs.value[index + 1]?.focus()
    }
}

const handleOtpPaste = (event) => {
    event.preventDefault()
    const pasteData = event.clipboardData.getData('text').trim()

    if (/^\d{6}$/.test(pasteData)) {
        const digits = pasteData.split('')
        digits.forEach((digit, index) => {
            if (index < 6) {
                otp.value[index] = digit
            }
        })
        otpInputs.value[5]?.focus()
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
    if (!validateCurrentStep()) return
    isLoading.value = true

    // Combine first_name and last_name into full_name for backend
    const submissionData = {
        ...formData.data(),
        full_name: fullName.value,
        is_vendor: false
    }

    // Remove first_name and last_name from the data since we're using full_name
    delete submissionData.first_name
    delete submissionData.last_name

    formData
        .transform(() => {
            const fd = new FormData()
            Object.keys(submissionData).forEach((key) => {
                if (submissionData[key] !== null && submissionData[key] !== undefined) {
                    fd.append(key, submissionData[key])
                }
            })
            return fd
        })
        .post(route('register'), {
            onFinish: () => {
                isLoading.value = false
            },
        })
}

// Watch for password changes to update strength
watch(() => formData.password, checkPasswordStrength)

// Auto-send OTP when reaching step 3 and email is not verified
watch(() => currentStep.value, (newStep) => {
    if (newStep === 3 && !isOtpSent.value && !isEmailVerified.value) {
        sendOtp()
    }
})

const goBack = () => {
    // router.visit('/'); // Go back to home page, or use router.get('/previous-route') for specific route
    // Alternatively, you can use window.history.back() for browser back:
    window.history.back();
};
</script>

<template>
    <GuestLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
                <!-- Header -->
                <!-- Header with Back Button -->
                <div class="text-center mb-6 sm:mb-10 relative px-2">
                    <!-- Back Button -->
                    <button @click="goBack"
                        class="absolute left-2 sm:left-0 top-1/2 transform -translate-y-1/2 flex items-center text-gray-600 hover:text-gray-900 transition-colors group">
                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-1 sm:mr-2 group-hover:-translate-x-1 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span class="text-sm font-medium hidden sm:inline">Back</span>
                        <span class="text-sm font-medium sm:hidden">Back</span>
                    </button>

                    <div class="flex justify-center mb-3 sm:mb-4">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-600 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mb-2 px-8 sm:px-0">Organizer
                        Registration</h1>
                    <p class="text-xs sm:text-sm lg:text-base text-gray-600 px-2">Create your account to start managing
                        events</p>
                </div>

                <!-- Progress Section -->
                <div class="mb-6 sm:mb-8 px-2 sm:px-0">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-700">Step {{ currentStep }} of {{
                            totalSteps
                        }}</span>
                        <span class="text-xs sm:text-sm font-medium text-blue-600">{{ Math.round((currentStep /
                            totalSteps)
                            * 100) }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2 mb-2">
                        <div class="bg-blue-600 h-1.5 sm:h-2 rounded-full transition-all duration-300 ease-out shadow-sm"
                            :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
                    </div>
                    <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 px-1">
                        <span class="truncate px-1">Personal Info</span>
                        <span class="truncate px-1">Security</span>
                        <span class="truncate px-1">Verification</span>
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
                                    <label for="firstName" class="block text-sm font-medium text-gray-700 mb-2">First
                                        Name
                                        *</label>
                                    <input id="firstName" v-model="formData.first_name" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': errors.first_name }" />
                                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">{{ errors.first_name
                                    }}
                                    </p>
                                </div>

                                <div>
                                    <label for="lastName" class="block text-sm font-medium text-gray-700 mb-2">Last Name
                                        *</label>
                                    <input id="lastName" v-model="formData.last_name" type="text"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': errors.last_name }" />
                                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">{{ errors.last_name }}
                                    </p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                                <div>
                                    <label for="phoneNumber" class="block text-sm font-medium text-gray-700 mb-2">Phone
                                        Number *</label>
                                    <input id="phoneNumber" v-model="formData.phoneNumber" type="tel"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': errors.phoneNumber }" />
                                    <p v-if="errors.phoneNumber" class="mt-1 text-sm text-red-600">{{ errors.phoneNumber
                                    }}
                                    </p>
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                        Address
                                        *</label>
                                    <input id="email" v-model="formData.email" type="email"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                        :class="{ 'border-red-500': errors.email }" @input="handleEmailChange" />
                                    <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
                                </div>
                            </div>

                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address
                                    *</label>
                                <input id="address" v-model="formData.address" type="text"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.address }"
                                    placeholder="Enter your complete address" />
                                <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                            </div>
                        </div>

                        <!-- Step 2: Account Security -->
                        <div v-show="currentStep === 2" class="space-y-4 sm:space-y-6">
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password
                                    *</label>
                                <input id="password" v-model="formData.password" type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{ 'border-red-500': errors.password }" @input="checkPasswordStrength" />
                                <div v-if="formData.password" class="mt-2">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs text-gray-600">Password Strength</span>
                                        <span class="text-xs font-medium" :class="passwordStrengthClass">{{
                                            passwordStrengthText }}</span>
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
                                <label for="confirmPassword"
                                    class="block text-sm font-medium text-gray-700 mb-2">Confirm
                                    Password *</label>
                                <input id="confirmPassword" v-model="formData.password_confirmation" type="password"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    :class="{
                                        'border-red-500': errors.password_confirmation || (formData.password_confirmation && !passwordsMatch),
                                        'border-green-500': formData.password_confirmation && passwordsMatch
                                    }" />
                                <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{
                                    errors.password_confirmation }}</p>
                                <p v-if="formData.password_confirmation && !passwordsMatch"
                                    class="mt-1 text-sm text-red-600">Passwords do not match</p>
                                <p v-if="formData.password_confirmation && passwordsMatch"
                                    class="mt-1 text-sm text-green-600">Passwords match</p>
                            </div>
                        </div>

                        <!-- Step 3: Email Verification - Dynamic UI based on verification status -->
                        <div v-show="currentStep === 3" class="space-y-6">
                            <div class="text-center">
                                <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                                    :class="isEmailVerified ? 'bg-green-100' : 'bg-blue-100'">
                                    <svg class="w-6 h-6 sm:w-8 sm:h-8"
                                        :class="isEmailVerified ? 'text-green-600' : 'text-blue-600'" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path v-if="isEmailVerified" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                        <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-3">
                                    {{ isEmailVerified ? 'Email Verified' : 'Verify Your Email' }}
                                </h3>
                                <p class="text-sm text-gray-600 mb-2" v-if="!isEmailVerified">
                                    We've sent a 6-digit verification code to
                                </p>
                                <p class="text-sm text-gray-600 mb-2" v-else>
                                    Your email has been successfully verified
                                </p>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ formData.email }}
                                </p>
                            </div>

                            <!-- OTP Input Section - Only show if not verified -->
                            <div v-if="!isEmailVerified">
                                <label class="block text-sm font-medium text-gray-700 mb-3 text-center">
                                    Enter verification code
                                </label>
                                <div class="flex justify-center space-x-2 sm:space-x-3">
                                    <input v-for="n in 6" :key="n" v-model="otp[n - 1]"
                                        @input="handleOtpInput(n - 1, $event)"
                                        @keydown="handleOtpKeydown(n - 1, $event)" @paste="handleOtpPaste"
                                        :ref="el => otpInputs[n - 1] = el" type="text" inputmode="numeric"
                                        pattern="[0-9]*" maxlength="1"
                                        class="w-10 h-12 sm:w-12 sm:h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors"
                                        :class="{ 'border-red-500': errors.otp }" />
                                </div>
                                <div class="h-5 mt-3 text-center">
                                    <p v-if="errors.otp" class="text-xs text-red-600">{{ errors.otp }}</p>
                                </div>

                                <!-- Resend Section -->
                                <div class="text-center">
                                    <p class="text-xs text-gray-500">
                                        Didn't receive the code?
                                        <button @click="resendOtp" :disabled="resendCooldown > 0 || isLoading"
                                            class="text-blue-600 font-medium hover:underline disabled:text-gray-400 disabled:cursor-not-allowed transition-colors">
                                            {{ isLoading ? 'Sending...' : (resendCooldown > 0 ? `Resend in
                                            ${resendCooldown}s` : 'Resend Code') }}
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <!-- Already Verified Message -->
                            <div v-else class="text-center">
                                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                    <p class="text-sm text-green-700">
                                        ✓ Your email has been verified. You can proceed to the next step.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Review & Submit -->
                        <div v-show="currentStep === 4" class="space-y-4 sm:space-y-6">
                            <div class="bg-gray-50 rounded-lg p-4 sm:p-5 border border-gray-200">
                                <h3 class="text-sm font-medium text-gray-900 mb-4">Review Your Information
                                </h3>

                                <div class="space-y-3">
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-600">Name</span>
                                        <span class="text-xs sm:text-sm text-gray-900">{{ fullName
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-600">Email</span>
                                        <span class="text-xs sm:text-sm text-gray-900">{{ formData.email
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-b border-gray-200">
                                        <span class="text-xs sm:text-sm text-gray-600">Phone</span>
                                        <span class="text-xs sm:text-sm text-gray-900">{{
                                            formData.phoneNumber
                                        }}</span>
                                    </div>
                                    <div class="flex justify-between items-start py-2">
                                        <span class="text-xs sm:text-sm text-gray-600">Address</span>
                                        <span class="text-xs sm:text-sm text-gray-900 text-right">{{
                                            formData.address }}</span>
                                    </div>
                                    <div class="flex justify-between items-center py-2 border-t border-gray-200 mt-2">
                                        <span class="text-xs sm:text-sm text-gray-600">Email Status</span>
                                        <span class="text-xs sm:text-sm font-medium"
                                            :class="isEmailVerified ? 'text-green-600' : 'text-red-600'">
                                            {{ isEmailVerified ? '✓ Verified' : '✗ Not Verified' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-start">
                                <input id="terms" v-model="termsAccepted" type="checkbox"
                                    class="mt-0.5 w-4 h-4 text-blue-600 focus:ring-blue-500 rounded border-gray-300"
                                    :class="{ 'border-red-500': errors.terms }" />
                                <label for="terms" class="ml-2 text-xs sm:text-sm text-gray-700">
                                    I agree to the <a href="#" class="text-blue-600 underline hover:no-underline">Terms
                                        of
                                        Service</a>
                                    and
                                    <a href="#" class="text-blue-600 underline hover:no-underline">Privacy
                                        Policy</a>
                                </label>
                            </div>
                            <p v-if="errors.terms" class="text-sm text-red-600">{{ errors.terms }}</p>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between gap-2">
                        <button v-if="currentStep > 1" @click="prevStep" type="button"
                            class="px-3 sm:px-5 py-2 text-sm sm:text-base text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-colors flex-1 sm:flex-none">
                            Previous
                        </button>
                        <div v-else class="flex-1 sm:flex-none"></div>

                        <div class="flex space-x-2 sm:space-x-3 flex-1 sm:flex-none justify-end">
                            <button v-if="currentStep < totalSteps" @click="nextStep" type="button"
                                :disabled="!isCurrentStepValid || isLoading" :class="[
                                    'px-3 sm:px-5 py-2 text-sm sm:text-base rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 sm:focus:ring-offset-2 transition-colors flex items-center justify-center w-full',
                                    (isLoading || !isCurrentStepValid)
                                        ? 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                        : 'bg-blue-600 text-white hover:bg-blue-700 shadow-sm'
                                ]">
                                <svg v-if="isLoading"
                                    class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                {{ isLoading ? 'Please wait...' : (currentStep === 3 ? (isEmailVerified ?
                                    'Continue'
                                    : 'Verify & Continue') : 'Next') }}
                            </button>

                            <button v-else @click="submitForm" :disabled="isLoading || !isCurrentStepValid"
                                type="button"
                                :class="['px-4 sm:px-6 py-2 text-sm sm:text-base rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1 sm:focus:ring-offset-2 transition-colors flex items-center justify-center w-full',
                                    (isLoading || !isCurrentStepValid) ? 'bg-gray-400 text-white cursor-not-allowed' : 'bg-green-600 text-white hover:bg-green-700 shadow-sm']">
                                <svg v-if="isLoading"
                                    class="animate-spin -ml-1 mr-1 sm:mr-2 h-3 w-3 sm:h-4 sm:w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                {{ isLoading ? 'Creating...' : 'Create Account' }}
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
    </GuestLayout>
</template>



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
