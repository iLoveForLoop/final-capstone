<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, reactive, computed, watch } from 'vue'
import { onUnmounted } from 'vue'


const page = usePage()
const vendorCategoryOptions = ref(page.props.categories)
// Form data structure
const formData = useForm({
    // Basic Business Information
    businessName: '',

    vendorCategories: [],

    businessDescription: '',

    // Password
    password: '',
    password_confirmation: '',

    // Contact Information
    full_name: '',

    phoneNumber: '',
    email: '',


    // Location & Service Area
    address: '',
    latitude: null,
    longitude: null,
    serviceCoverageAreas: [],


    // File uploads for Spatie Media Library
    profilePhoto: null,
    servicePhotos: [],
    permitFiles: [],

    is_vendor: true
})

// UI State
const currentStep = ref(1)
const totalSteps = 7
const isLoading = ref(false)
const profilePhoto = ref(null)
const servicePhotos = ref([])
const permitFiles = ref([])
const errors = reactive({})
const termsAccepted = ref(false)

// Password UI state
const showPassword = ref(false)
const showPasswordConfirm = ref(false)
const passwordStrength = ref(0)
const passwordRequirements = reactive({
    length: false,
    uppercase: false,
    lowercase: false,
    number: false
})

// Options




const subscriptionPlans = [
    { value: 'basic', label: 'Basic - ₱500/month' },
    { value: 'premium', label: 'Premium - ₱1,200/month' },
    { value: 'enterprise', label: 'Enterprise - ₱2,500/month' }
]

// Service coverage areas options
const serviceCoverageOptions = [
    'Tubigon', 'Calape', 'Clarin', 'Sagbayan', 'Anywhere in Bohol'
]

// Add computed properties for image URLs
const profilePhotoUrl = computed(() => {
    return profilePhoto.value ? URL.createObjectURL(profilePhoto.value) : ''
})

const servicePhotoUrls = computed(() => {
    return servicePhotos.value.map(photo => URL.createObjectURL(photo))
})

const passwordStrengthColor = computed(() => {
    if (passwordStrength.value < 2) return 'bg-red-500'
    if (passwordStrength.value < 4) return 'bg-yellow-500'
    return 'bg-green-500'
})

const passwordStrengthWidth = computed(() => {
    return `${(passwordStrength.value / 4) * 100}%`
})

const passwordStrengthText = computed(() => {
    if (passwordStrength.value === 0) return 'Very Weak'
    if (passwordStrength.value === 1) return 'Weak'
    if (passwordStrength.value === 2) return 'Fair'
    if (passwordStrength.value === 3) return 'Good'
    return 'Strong'
})

const passwordsMatch = computed(() => {
    if (!formData.password && !formData.password_confirmation) return null
    return formData.password === formData.password_confirmation
})

// Validation computed properties
const isStep1Valid = computed(() => {
    return formData.businessName.trim() !== '' &&
        formData.full_name.trim() !== '' &&
        formData.vendorCategories.length > 0 &&
        formData.phoneNumber.trim() !== '' &&
        formData.email.trim() !== '' &&
        /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)
})

const isStep2Valid = computed(() => {
    return formData.address.trim() !== '' &&
        formData.serviceCoverageAreas.length > 0
})

const isStep3Valid = computed(() => {
    return formData.preferred_contact_method !== undefined &&
        formData.preferred_contact_method !== ''
})

const isStep4Valid = computed(() => {
    return true
    // return profilePhoto.value !== null && servicePhotos.value.length > 0
})

const isStep5Valid = computed(() => {
    return true
    // return permitFiles.value.length > 0
})

const isStep6Valid = computed(() => {
    return formData.password.length >= 8 &&
        passwordStrength.value >= 3 &&
        passwordsMatch.value === true
})

const isStep7Valid = computed(() => {
    return termsAccepted.value === true
})

const isCurrentStepValid = computed(() => {
    switch (currentStep.value) {
        case 1: return isStep1Valid.value
        case 2: return isStep2Valid.value
        case 3: return isStep3Valid.value
        case 4: return isStep4Valid.value
        case 5: return isStep5Valid.value
        case 6: return isStep6Valid.value
        case 7: return isStep7Valid.value
        default: return false
    }
})

// Computed properties
const stepTitle = computed(() => {
    const titles = {
        1: 'Business Information',
        2: 'Location & Services',
        3: 'Contact and Payment Preference',
        4: 'Media & Portfolio',
        5: 'Legal & Compliance',
        6: 'Account Security',
        7: 'Review & Submit'
    }
    return titles[currentStep.value]
})

// Methods
const validateCurrentStep = () => {
    // Clear previous errors
    Object.keys(errors).forEach(key => delete errors[key])

    let isValid = true

    switch (currentStep.value) {
        case 1:
            if (!formData.businessName.trim()) {
                errors.businessName = 'Business name is required'
                isValid = false
            }
            if (!formData.full_name.trim()) {
                errors.full_name = 'Contact person is required'
                isValid = false
            }
            if (formData.vendorCategories.length === 0) {
                errors.vendorCategories = 'At least one vendor category is required'
                isValid = false
            }
            if (!formData.phoneNumber.trim()) {
                errors.phoneNumber = 'Phone number is required'
                isValid = false
            }
            if (!formData.email.trim()) {
                errors.email = 'Email is required'
                isValid = false
            } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(formData.email)) {
                errors.email = 'Please enter a valid email address'
                isValid = false
            }
            break

        case 2:
            if (!formData.address.trim()) {
                errors.address = 'Business address is required'
                isValid = false
            }
            // if (formData.serviceCoverageAreas.length === 0) {
            //     errors.serviceCoverageAreas = 'At least one service coverage area is required'
            //     isValid = false
            // }
            break

        case 3:
            if (!formData.preferred_contact_method) {
                errors.preferred_contact_method = 'Preferred contact method is required'
                isValid = false
            }
            break

        case 4:
            // if (!profilePhoto.value) {
            //     errors.profilePhoto = 'Profile photo is required'
            //     isValid = false
            // }
            // if (servicePhotos.value.length < 5) {
            //     errors.servicePhotos = 'At least 5 service photos are required'
            //     isValid = false
            // }
            break

        case 5:
            // if (permitFiles.value.length === 0) {
            //     errors.businessPermits = 'At least one business permit is required'
            //     isValid = false
            // }
            break

        case 6:
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

        case 7:
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

const handleFileUpload = (event, type) => {
    const files = Array.from(event.target.files)
    if (type === 'profile') {
        // Revoke previous URL to prevent memory leaks
        if (profilePhoto.value) {
            URL.revokeObjectURL(profilePhotoUrl.value)
        }
        profilePhoto.value = files[0]
        // Clear error when file is uploaded
        if (errors.profilePhoto) delete errors.profilePhoto
    } else if (type === 'service') {
        // Revoke previous URLs
        servicePhotoUrls.value.forEach(url => URL.revokeObjectURL(url))
        servicePhotos.value = [...servicePhotos.value, ...files]
        // Clear error when files are uploaded
        if (errors.servicePhotos && servicePhotos.value.length >= 5) delete errors.servicePhotos
    } else if (type === 'permits') {
        permitFiles.value = [...permitFiles.value, ...files]
        // Clear error when files are uploaded
        if (errors.businessPermits) delete errors.businessPermits
    }
}

const removeFile = (index, type) => {
    if (type === 'service') {
        // Revoke the URL before removing the file
        URL.revokeObjectURL(servicePhotoUrls.value[index])
        servicePhotos.value.splice(index, 1)
    } else if (type === 'permits') {
        permitFiles.value.splice(index, 1)
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

    formData.profilePhoto = profilePhoto.value;
    formData.servicePhotos = [...servicePhotos.value];
    formData.permitFiles = [...permitFiles.value];

    formData
        .transform((data) => {
            const fd = new FormData();
            Object.keys(data).forEach((key) => {
                if (key === 'vendorCategories') {
                    data.vendorCategories.forEach((category) => {
                        fd.append('vendorCategories[]', category);
                    });
                } else if (key === 'serviceCoverageAreas') {
                    data.serviceCoverageAreas.forEach((area) => {
                        fd.append('serviceCoverageAreas[]', area);
                    });
                } else if (key === 'servicePhotos') {
                    data.servicePhotos.forEach((file, index) => {
                        fd.append(`servicePhotos[${index}]`, file);
                    });
                } else if (key === 'permitFiles') {
                    data.permitFiles.forEach((file, index) => {
                        fd.append(`permitFiles[${index}]`, file);
                    });
                } else if (data[key] !== null) {
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


// Clean up URLs when component is unmounted


onUnmounted(() => {
    if (profilePhoto.value) {
        URL.revokeObjectURL(profilePhotoUrl.value)
    }
    servicePhotoUrls.value.forEach(url => URL.revokeObjectURL(url))
})

// Watch for password changes to update strength
watch(() => formData.password, checkPasswordStrength)
</script>

<template>
    <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl font-bold text-gray-900 mb-2">Join Eventory</h1>
                <p class="text-xl text-gray-600">Register as a vendor and grow your business</p>
            </div>

            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-sm font-medium text-gray-700">Step {{ currentStep }} of {{ totalSteps }}</span>
                    <span class="text-sm font-medium text-gray-700">{{ Math.round((currentStep / totalSteps) * 100) }}%
                        Complete</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
                </div>
            </div>

            <!-- Form Container -->
            <div class="bg-white rounded-lg shadow-xl overflow-hidden">
                <!-- Step Header -->
                <div class="bg-indigo-600 text-white p-6">
                    <h2 class="text-2xl font-bold">{{ stepTitle }}</h2>
                </div>

                <!-- Form Content -->
                <div class="p-6">
                    <!-- Step 1: Business Information -->
                    <div v-show="currentStep === 1" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Business Name *</label>
                                <input v-model="formData.businessName" type="text" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                    errors.businessName ? 'border-red-300' : 'border-gray-300']"
                                    placeholder="Enter your business name">
                                <p v-if="errors.businessName" class="mt-1 text-sm text-red-600">{{ errors.businessName
                                }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Owner/Contact Person
                                    *</label>
                                <input v-model="formData.full_name" type="text" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                    errors.full_name ? 'border-red-300' : 'border-gray-300']"
                                    placeholder="Primary contact name">
                                <p v-if="errors.full_name" class="mt-1 text-sm text-red-600">{{ errors.full_name
                                }}</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Vendor Categories *</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div v-for="category in vendorCategoryOptions" :key="category"
                                    class="flex items-center">
                                    <input :id="category.id" v-model="formData.vendorCategories" :value="category.id"
                                        type="checkbox"
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
                                    errors.phoneNumber ? 'border-red-300' : 'border-gray-300']"
                                    placeholder="+63 9XX XXX XXXX">
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
                                placeholder="Tell us about your business and services (500 characters max)"
                                maxlength="500"></textarea>
                            <p class="text-sm text-gray-500 mt-1">{{ formData.businessDescription.length }}/500
                                characters</p>
                        </div>
                    </div>

                    <!-- Step 2: Location & Services -->
                    <div v-show="currentStep === 2" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Address *</label>
                            <input v-model="formData.address" type="text" :class="['w-full px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                errors.address ? 'border-red-300' : 'border-gray-300']"
                                placeholder="Enter your business address">
                            <p v-if="errors.address" class="mt-1 text-sm text-red-600">{{ errors.address }}</p>
                        </div>

                        <!-- Map Container -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Location on Map</label>
                            <div class="border border-gray-300 rounded-md overflow-hidden">
                                <div id="map" class="h-64 bg-gray-100 flex items-center justify-center text-gray-500">
                                    <div class="text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="mt-2">Interactive Map</p>
                                        <p class="text-sm">Click to pin your exact location</p>
                                    </div>
                                </div>
                                <div class="p-4 bg-gray-50 flex gap-2">
                                    <button type="button"
                                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-sm">
                                        Use Current Location
                                    </button>
                                    <button type="button"
                                        class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 text-sm">
                                        Search Address
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Coverage Areas *</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                                <div v-for="area in serviceCoverageOptions" :key="area" class="flex items-center">
                                    <input :id="area" v-model="formData.serviceCoverageAreas" :value="area"
                                        type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                    <label :for="area" class="ml-2 text-sm text-gray-700">{{ area }}</label>
                                </div>
                            </div>
                            <p v-if="errors.serviceCoverageAreas" class="mt-1 text-sm text-red-600">{{
                                errors.serviceCoverageAreas }}</p>
                        </div>
                    </div>

                    <!-- Step 3: Contact and Payment Preference -->
                    <div v-show="currentStep === 3" class="space-y-6">
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
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    Phone Call
                                </label>
                                <label class="flex items-center">
                                    <input v-model="formData.preferred_contact_method" type="radio" value="email"
                                        class="mr-3 text-blue-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                    </svg>
                                    Email
                                </label>
                                <label class="flex items-center">
                                    <input v-model="formData.preferred_contact_method" type="radio" value="in-app"
                                        class="mr-3 text-blue-600">
                                    <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                                    </svg>
                                    In-App Messaging
                                </label>
                            </div>
                            <p v-if="errors.preferred_contact_method" class="mt-1 text-sm text-red-600">{{
                                errors.preferred_contact_method }}</p>
                        </div>
                    </div>

                    <!-- Step 4: Media & Portfolio -->
                    <div v-show="currentStep === 4" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Profile Photo/Logo (Optional but
                                recomended)</label>
                            <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
                                errors.profilePhoto ? 'border-red-300' : 'border-gray-300']">
                                <div class="space-y-1 text-center">
                                    <svg v-if="!profilePhoto" class="mx-auto h-12 w-12 text-gray-400"
                                        stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div v-if="profilePhoto" class="text-center">
                                        <img :src="profilePhotoUrl" alt="Profile preview"
                                            class="mx-auto h-20 w-20 object-cover rounded-full">
                                    </div>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="profile-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>{{ profilePhoto ? 'Change photo' : 'Upload a file' }}</span>
                                            <input id="profile-upload" @change="handleFileUpload($event, 'profile')"
                                                type="file" accept="image/*" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                                </div>
                            </div>
                            <p v-if="errors.profilePhoto" class="mt-1 text-sm text-red-600">{{ errors.profilePhoto }}
                            </p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Service Portfolio Photos
                                (Optional but
                                recomended)</label>
                            <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
                                errors.servicePhotos ? 'border-red-300' : 'border-gray-300']">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="service-photos"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload photos</span>
                                            <input id="service-photos" @change="handleFileUpload($event, 'service')"
                                                type="file" multiple accept="image/*" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PNG, JPG up to 5MB each (min 5 photos)</p>
                                    <p v-if="servicePhotos.length > 0" class="text-sm text-gray-600">
                                        {{ servicePhotos.length }} photos uploaded (minimum 5 required)
                                    </p>
                                </div>
                            </div>
                            <p v-if="errors.servicePhotos" class="mt-1 text-sm text-red-600">{{ errors.servicePhotos }}
                            </p>

                            <!-- Service Photos Preview -->
                            <div v-if="servicePhotos.length > 0" class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
                                <div v-for="(photo, index) in servicePhotos" :key="index" class="relative">
                                    <img :src="servicePhotoUrls[index]" alt="Service photo"
                                        class="h-24 w-full object-cover rounded-lg">
                                    <button @click="removeFile(index, 'service')" type="button"
                                        class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Video Portfolio
                                (Optional)</label>
                            <input type="url"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                placeholder="YouTube or Vimeo link">
                        </div>
                    </div>

                    <!-- Step 5: Legal & Compliance -->
                    <div v-show="currentStep === 5" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Business Permits (Optional but
                                recomended)</label>
                            <div :class="['mt-2 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md hover:border-indigo-400',
                                errors.businessPermits ? 'border-red-300' : 'border-gray-300']">
                                <div class="space-y-1 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                        viewBox="0 0 48 48">
                                        <path
                                            d="M9 12h6m6 0h6m-6 0v6m-6-6v6m6-6a3 3 0 013 3v6a3 3 0 01-3 3H9a3 3 0 01-3-3v-6a3 3 0 013-3" />
                                    </svg>
                                    <div class="flex text-sm text-gray-600">
                                        <label for="permits-upload"
                                            class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                            <span>Upload permits</span>
                                            <input id="permits-upload" @change="handleFileUpload($event, 'permits')"
                                                type="file" multiple accept=".pdf,.jpg,.jpeg,.png" class="sr-only">
                                        </label>
                                        <p class="pl-1">or drag and drop</p>
                                    </div>
                                    <p class="text-xs text-gray-500">PDF, PNG, JPG up to 10MB each</p>
                                    <p v-if="permitFiles.length > 0" class="text-sm text-gray-600">
                                        {{ permitFiles.length }} files uploaded
                                    </p>
                                </div>
                            </div>
                            <p v-if="errors.businessPermits" class="mt-1 text-sm text-red-600">{{ errors.businessPermits
                            }}</p>

                            <!-- Permits Preview -->
                            <div v-if="permitFiles.length > 0" class="mt-4 space-y-2">
                                <div v-for="(file, index) in permitFiles" :key="index"
                                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                    <div class="flex items-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                        <span class="ml-2 text-sm text-gray-700">{{ file.name }}</span>
                                    </div>
                                    <button @click="removeFile(index, 'permits')" type="button"
                                        class="text-red-500 hover:text-red-700">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Account Security -->
                    <div v-show="currentStep === 6" class="space-y-6">
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
                                        <input v-model="formData.password" :type="showPassword ? 'text' : 'password'"
                                            :class="['w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                                errors.password || errors.passwordStrength ? 'border-red-300' : 'border-gray-300']"
                                            placeholder="Create a strong password" @input="checkPasswordStrength">
                                        <button type="button" @click="showPassword = !showPassword"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg v-if="showPassword" class="h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464M21.536 15.536l-12-12" />
                                            </svg>
                                        </button>
                                    </div>
                                    <!-- Password Strength Indicator -->
                                    <div class="mt-2">
                                        <div class="flex items-center space-x-1">
                                            <div class="flex-1 h-2 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full transition-all duration-300"
                                                    :class="passwordStrengthColor"
                                                    :style="{ width: passwordStrengthWidth }"></div>
                                            </div>
                                            <span class="text-xs text-gray-600">{{ passwordStrengthText }}</span>
                                        </div>
                                        <ul class="text-xs text-gray-600 mt-2 space-y-1">
                                            <li
                                                :class="passwordRequirements.length ? 'text-green-600' : 'text-gray-400'">
                                                ✓ At least 8 characters
                                            </li>
                                            <li
                                                :class="passwordRequirements.uppercase ? 'text-green-600' : 'text-gray-400'">
                                                ✓ One uppercase letter
                                            </li>
                                            <li
                                                :class="passwordRequirements.lowercase ? 'text-green-600' : 'text-gray-400'">
                                                ✓ One lowercase letter
                                            </li>
                                            <li
                                                :class="passwordRequirements.number ? 'text-green-600' : 'text-gray-400'">
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
                                        <input v-model="formData.password_confirmation"
                                            :type="showPasswordConfirm ? 'text' : 'password'" :class="['w-full px-3 py-2 pr-10 border rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500',
                                                errors.password_confirmation ? 'border-red-300' : 'border-gray-300']"
                                            placeholder="Confirm your password">
                                        <button type="button" @click="showPasswordConfirm = !showPasswordConfirm"
                                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <svg v-if="showPasswordConfirm" class="h-5 w-5 text-gray-400" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <svg v-else class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
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
                    </div>

                    <!-- Step 7: Review & Submit -->
                    <div v-show="currentStep === 7" class="space-y-6">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-blue-600 mt-0.5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
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
                        <div
                            :class="['bg-gray-50 border rounded-lg p-4', errors.terms ? 'border-red-300' : 'border-gray-200']">
                            <div class="flex items-start">
                                <input v-model="termsAccepted" type="checkbox"
                                    class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mt-1">
                                <div class="ml-3">
                                    <label class="text-sm text-gray-700">
                                        I agree to Eventory's <a href="#"
                                            class="text-indigo-600 hover:text-indigo-500">Terms of Service</a> and <a
                                            href="#" class="text-indigo-600 hover:text-indigo-500">Privacy Policy</a>. I
                                        confirm that all information provided is accurate and up-to-date.
                                    </label>
                                </div>
                            </div>
                            <p v-if="errors.terms" class="mt-1 text-sm text-red-600">{{ errors.terms }}</p>
                        </div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <button v-if="currentStep > 1" @click="prevStep" type="button"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                            </path>
                        </svg>
                        Previous
                    </button>
                    <div v-else></div>

                    <div class="flex space-x-3">
                        <button v-if="currentStep < totalSteps" @click="nextStep" type="button"
                            :disabled="!isCurrentStepValid" :class="['inline-flex items-center px-4 py-2 border text-sm font-medium rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-offset-2',
                                isCurrentStepValid ? 'border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-indigo-500' :
                                    'border-gray-300 text-gray-700 bg-white opacity-50 cursor-not-allowed']">
                            Next
                            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                        <button v-else @click="submitForm" :disabled="isLoading || !isCurrentStepValid" type="button"
                            :class="['inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white focus:outline-none focus:ring-2 focus:ring-offset-2',
                                (isLoading || !isCurrentStepValid) ? 'bg-green-400 cursor-not-allowed' : 'bg-green-600 hover:bg-green-700 focus:ring-green-500']">
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            {{ isLoading ? 'Submitting...' : 'Submit Registration' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
