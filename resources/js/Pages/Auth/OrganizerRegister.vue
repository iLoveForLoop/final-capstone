<script setup>
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
const goBack = () => {
    window.history.back()
}

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
</script>

<template>
    <div class="min-h-screen bg-gray-50">
        <div class="flex flex-col lg:flex-row h-screen">
            <!-- Left Side - Minimalist Branding (Fixed) -->
            <div
                class="hidden lg:flex lg:w-2/5 bg-slate-900 text-white p-12 flex-col justify-center items-center fixed left-0 top-0 bottom-0">
                <!-- Back Button - Positioned at top left -->
                <div class="absolute top-8 left-8">
                    <button @click="goBack"
                        class="flex items-center text-slate-300 hover:text-white transition-colors group">
                        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span class="text-sm font-medium">Back</span>
                    </button>
                </div>

                <!-- Main Content -->
                <div class="max-w-md text-center">
                    <!-- Logo and Brand -->
                    <div class="flex items-center justify-center mb-8">
                        <div
                            class="w-12 h-12 bg-white bg-opacity-10 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Eventory</h1>
                            <!-- <p class="text-slate-400 text-sm mt-1">Event Management Platform</p> -->
                        </div>
                    </div>

                    <!-- Description -->
                    <p class="text-lg text-slate-300 leading-relaxed mb-8">
                        Your all-in-one platform for seamless event management and vendor coordination.
                        Create unforgettable experiences with our powerful tools.
                    </p>



                    <!-- Footer -->
                    <div class="border-t border-slate-700 pt-6">
                        <p class="text-slate-400 text-sm mb-3">Already have an account?</p>
                        <a href="/login"
                            class="inline-flex items-center px-6 py-3 bg-white bg-opacity-10 hover:bg-opacity-20 text-white font-medium rounded-lg transition-all duration-200 backdrop-blur-sm border border-white border-opacity-20 hover:border-opacity-30">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                            </svg>
                            Sign in to your account
                        </a>
                    </div>
                </div>
            </div>

            <!-- Right Side - Registration Form (Scrollable) -->
            <div class="w-full lg:w-3/5 lg:ml-[40%] overflow-y-auto">
                <div class="flex items-center justify-center min-h-screen p-4 sm:p-5">
                    <div class="w-full max-w-xl">
                        <!-- Mobile Header with Back Button -->
                        <div class="lg:hidden mb-6">
                            <div class="flex items-center justify-center mb-4">

                                <div class="flex items-center">
                                    <div
                                        class="w-10 h-10 bg-slate-900 rounded-lg flex items-center justify-center mr-3">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                        </svg>
                                    </div>
                                    <h1 class="text-xl sm:text-2xl font-semibold text-slate-900">Eventory</h1>
                                </div>
                                <div class="w-10"></div> <!-- Spacer for balance -->
                            </div>
                        </div>



                        <!-- Header -->
                        <div class="mb-5">
                            <h2 class="text-xl sm:text-2xl font-semibold text-slate-900 mb-2">Create Organizer Account
                            </h2>
                            <p class="text-sm sm:text-base text-slate-600">Join our platform to get started</p>
                        </div>

                        <!-- Progress Indicator -->
                        <div class="mb-6 sm:mb-8">
                            <div class="flex justify-between items-center mb-3">
                                <span class="text-xs sm:text-sm text-slate-600">Step {{ currentStep }} of {{ totalSteps
                                }}</span>
                                <span class="text-xs sm:text-sm text-slate-900 font-medium">{{ Math.round((currentStep /
                                    totalSteps) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-1.5">
                                <div class="bg-slate-900 h-1.5 rounded-full transition-all duration-300"
                                    :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
                            </div>
                        </div>

                        <!-- Form Container -->
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
                            <!-- Step Header -->
                            <div class="px-4 sm:px-6 py-4 border-b border-gray-200">
                                <h3 class="text-sm sm:text-base font-medium text-slate-900">{{ stepTitle }}</h3>
                            </div>

                            <!-- Form Content -->
                            <div class="p-4 sm:p-6">
                                <!-- Step 1: Personal Information -->
                                <div v-show="currentStep === 1" class="space-y-4 sm:space-y-5">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="firstName"
                                                class="block text-sm font-medium text-slate-700 mb-1.5">First
                                                Name</label>
                                            <input id="firstName" v-model="formData.first_name" type="text"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                                :class="{ 'border-red-500': errors.first_name }" placeholder="John" />
                                            <div class="h-5 mt-1.5" v-if="errors.first_name">
                                                <p class="text-xs text-red-600">{{ errors.first_name }}</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="lastName"
                                                class="block text-sm font-medium text-slate-700 mb-1.5">Last
                                                Name</label>
                                            <input id="lastName" v-model="formData.last_name" type="text"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                                :class="{ 'border-red-500': errors.last_name }" placeholder="Doe" />
                                            <div class="h-5 mt-1.5" v-if="errors.last_name">
                                                <p class="text-xs text-red-600">{{ errors.last_name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label for="phoneNumber"
                                                class="block text-sm font-medium text-slate-700 mb-1.5">Phone
                                                Number</label>
                                            <input id="phoneNumber" v-model="formData.phoneNumber" type="tel"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                                :class="{ 'border-red-500': errors.phoneNumber }"
                                                placeholder="0912 345 6789" />
                                            <div class="h-5 mt-1.5" v-if="errors.phoneNumber">
                                                <p class="text-xs text-red-600">{{ errors.phoneNumber }}</p>
                                            </div>
                                        </div>

                                        <div>
                                            <label for="email"
                                                class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                                            <input id="email" v-model="formData.email" type="text"
                                                class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                                :class="{ 'border-red-500': errors.email }"
                                                placeholder="john@example.com" @input="handleEmailChange" />
                                            <div class="h-5 mt-1.5" v-if="errors.email">
                                                <p class="text-xs text-red-600">{{ errors.email }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="address"
                                            class="block text-sm font-medium text-slate-700 mb-1.5">Complete
                                            Address</label>
                                        <input id="address" v-model="formData.address" type="text"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.address }"
                                            placeholder="123 Main St, City, State, ZIP" />
                                        <div class="h-5 mt-1.5">
                                            <p v-if="errors.address" class="text-xs text-red-600">{{ errors.address }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 2: Account Security -->
                                <div v-show="currentStep === 2" class="space-y-4 sm:space-y-5">
                                    <div>
                                        <label for="password"
                                            class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                                        <input id="password" v-model="formData.password" type="password"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                            :class="{ 'border-red-500': errors.password }"
                                            placeholder="Create a strong password" @input="checkPasswordStrength" />

                                        <div class="h-16 mt-3">
                                            <div v-if="formData.password">
                                                <div class="flex items-center justify-between mb-2">
                                                    <span class="text-xs text-slate-600">Password Strength</span>
                                                    <span class="text-xs font-medium" :class="passwordStrengthClass">{{
                                                        passwordStrengthText }}</span>
                                                </div>
                                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full transition-all duration-300"
                                                        :class="passwordStrengthBarClass"
                                                        :style="{ width: `${(passwordStrength / 4) * 100}%` }"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="h-5" v-if="errors.password">
                                            <p class="text-xs text-red-600">{{ errors.password }}</p>
                                        </div>

                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-4 gap-y-1.5 mt-3">
                                            <div class="flex items-center text-xs"
                                                :class="passwordRequirements.length ? 'text-green-600' : 'text-slate-500'">
                                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path v-if="passwordRequirements.length"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                    <circle v-else cx="10" cy="10" r="3" />
                                                </svg>
                                                At least 8 characters
                                            </div>
                                            <div class="flex items-center text-xs"
                                                :class="passwordRequirements.uppercase ? 'text-green-600' : 'text-slate-500'">
                                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path v-if="passwordRequirements.uppercase"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                    <circle v-else cx="10" cy="10" r="3" />
                                                </svg>
                                                One uppercase letter
                                            </div>
                                            <div class="flex items-center text-xs"
                                                :class="passwordRequirements.lowercase ? 'text-green-600' : 'text-slate-500'">
                                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path v-if="passwordRequirements.lowercase"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                    <circle v-else cx="10" cy="10" r="3" />
                                                </svg>
                                                One lowercase letter
                                            </div>
                                            <div class="flex items-center text-xs"
                                                :class="passwordRequirements.number ? 'text-green-600' : 'text-slate-500'">
                                                <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path v-if="passwordRequirements.number"
                                                        d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" />
                                                    <circle v-else cx="10" cy="10" r="3" />
                                                </svg>
                                                One number
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label for="confirmPassword"
                                            class="block text-sm font-medium text-slate-700 mb-1.5">Confirm
                                            Password</label>
                                        <input id="confirmPassword" v-model="formData.password_confirmation"
                                            type="password"
                                            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent"
                                            :class="{
                                                'border-red-500': errors.password_confirmation || (formData.password_confirmation && !passwordsMatch),
                                                'border-green-500': formData.password_confirmation && passwordsMatch
                                            }" placeholder="Re-enter your password" />
                                        <div class="h-5 mt-1.5">
                                            <p v-if="errors.password_confirmation" class="text-xs text-red-600">{{
                                                errors.password_confirmation }}</p>
                                            <p v-else-if="formData.password_confirmation && !passwordsMatch"
                                                class="text-xs text-red-600">Passwords do not match</p>
                                            <p v-else-if="formData.password_confirmation && passwordsMatch"
                                                class="text-xs text-green-600">Passwords match</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Step 3: Email Verification - Dynamic UI based on verification status -->
                                <div v-show="currentStep === 3" class="space-y-6">
                                    <div class="text-center">
                                        <div class="w-12 h-12 sm:w-16 sm:h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                                            :class="isEmailVerified ? 'bg-green-100' : 'bg-blue-100'">
                                            <svg class="w-6 h-6 sm:w-8 sm:h-8"
                                                :class="isEmailVerified ? 'text-green-600' : 'text-blue-600'"
                                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path v-if="isEmailVerified" stroke-linecap="round"
                                                    stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                <path v-else stroke-linecap="round" stroke-linejoin="round"
                                                    stroke-width="2"
                                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-slate-900 mb-3">
                                            {{ isEmailVerified ? 'Email Verified' : 'Verify Your Email' }}
                                        </h3>
                                        <p class="text-sm text-slate-600 mb-2" v-if="!isEmailVerified">
                                            We've sent a 6-digit verification code to
                                        </p>
                                        <p class="text-sm text-slate-600 mb-2" v-else>
                                            Your email has been successfully verified
                                        </p>
                                        <p class="text-sm font-medium text-slate-900">
                                            {{ formData.email }}
                                        </p>
                                    </div>

                                    <!-- OTP Input Section - Only show if not verified -->
                                    <div v-if="!isEmailVerified">
                                        <label class="block text-sm font-medium text-slate-700 mb-3 text-center">
                                            Enter verification code
                                        </label>
                                        <div class="flex justify-center space-x-2 sm:space-x-3">
                                            <input v-for="n in 6" :key="n" v-model="otp[n - 1]"
                                                @input="handleOtpInput(n - 1, $event)"
                                                @keydown="handleOtpKeydown(n - 1, $event)" @paste="handleOtpPaste"
                                                :ref="el => otpInputs[n - 1] = el" type="text" inputmode="numeric"
                                                pattern="[0-9]*" maxlength="1"
                                                class="w-10 h-12 sm:w-12 sm:h-12 text-center text-lg font-semibold border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 focus:border-transparent transition-colors"
                                                :class="{ 'border-red-500': errors.otp }" />
                                        </div>
                                        <div class="h-5 mt-3 text-center">
                                            <p v-if="errors.otp" class="text-xs text-red-600">{{ errors.otp }}</p>
                                        </div>

                                        <!-- Resend Section -->
                                        <div class="text-center">
                                            <p class="text-xs text-slate-500">
                                                Didn't receive the code?
                                                <button @click="resendOtp" :disabled="resendCooldown > 0 || isLoading"
                                                    class="text-slate-900 font-medium hover:underline disabled:text-slate-400 disabled:cursor-not-allowed transition-colors">
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
                                <div v-show="currentStep === 4" class="space-y-4 sm:space-y-5">
                                    <div class="bg-gray-50 rounded-lg p-4 sm:p-5 border border-gray-200">
                                        <h3 class="text-sm font-medium text-slate-900 mb-4">Review Your Information</h3>

                                        <div class="space-y-3">
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-xs sm:text-sm text-slate-600">Name</span>
                                                <span class="text-xs sm:text-sm text-slate-900">{{ fullName }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-xs sm:text-sm text-slate-600">Email</span>
                                                <span class="text-xs sm:text-sm text-slate-900">{{ formData.email
                                                }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center py-2 border-b border-gray-200">
                                                <span class="text-xs sm:text-sm text-slate-600">Phone</span>
                                                <span class="text-xs sm:text-sm text-slate-900">{{ formData.phoneNumber
                                                }}</span>
                                            </div>
                                            <div class="flex justify-between items-start py-2">
                                                <span class="text-xs sm:text-sm text-slate-600">Address</span>
                                                <span class="text-xs sm:text-sm text-slate-900 text-right">{{
                                                    formData.address }}</span>
                                            </div>
                                            <div
                                                class="flex justify-between items-center py-2 border-t border-gray-200 mt-2">
                                                <span class="text-xs sm:text-sm text-slate-600">Email Status</span>
                                                <span class="text-xs sm:text-sm font-medium"
                                                    :class="isEmailVerified ? 'text-green-600' : 'text-red-600'">
                                                    {{ isEmailVerified ? '✓ Verified' : '✗ Not Verified' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <input id="terms" v-model="termsAccepted" type="checkbox"
                                            class="mt-0.5 w-4 h-4 text-slate-900 focus:ring-slate-900 rounded border-gray-300"
                                            :class="{ 'border-red-500': errors.terms }" />
                                        <label for="terms" class="ml-2 text-xs sm:text-sm text-slate-700">
                                            I agree to the <a href="#"
                                                class="text-slate-900 underline hover:no-underline">Terms of Service</a>
                                            and
                                            <a href="#" class="text-slate-900 underline hover:no-underline">Privacy
                                                Policy</a>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div
                                class="px-4 sm:px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between gap-3">
                                <button v-if="currentStep > 1" @click="prevStep" type="button"
                                    class="px-4 sm:px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors">
                                    Previous
                                </button>
                                <div v-else></div>

                                <button v-if="currentStep < totalSteps" @click="nextStep" type="button"
                                    :disabled="!isCurrentStepValid || isLoading" :class="[
                                        'px-4 sm:px-5 py-2.5 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors',
                                        (isLoading || !isCurrentStepValid)
                                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                            : 'bg-slate-900 text-white hover:bg-slate-800'
                                    ]">
                                    <svg v-if="isLoading"
                                        class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    {{ isLoading ? 'Please wait...' : (currentStep === 3 ? (isEmailVerified ? 'Continue'
                                        : 'Verify & Continue') : 'Next') }}
                                </button>

                                <button v-else @click="submitForm" :disabled="isLoading || !isCurrentStepValid"
                                    type="button"
                                    :class="['px-4 sm:px-5 py-2.5 text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-slate-900 transition-colors flex items-center justify-center',
                                        (isLoading || !isCurrentStepValid) ? 'bg-gray-300 text-gray-500 cursor-not-allowed' : 'bg-slate-900 text-white hover:bg-slate-800']">
                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
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
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Smooth transitions */
input,
button {
    transition: all 0.2s ease;
}

/* Improved mobile sizing */
@media (max-width: 640px) {

    input:not([type="text"].w-10),
    button {
        min-height: 42px;
    }

    .grid-cols-1>div {
        margin-bottom: 0.5rem;
    }
}
</style>
