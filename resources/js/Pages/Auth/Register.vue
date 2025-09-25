<script setup>
import AccountSecurity from '@/Components/Register/AccountSecurity.vue'
import BusinessInformationForm from '@/Components/Register/BusinessInformationForm.vue'
import ContactAndPayment from '@/Components/Register/ContactAndPayment.vue'
import LegalAndCompliance from '@/Components/Register/LegalAndCompliance.vue'
import LocationForm from '@/Components/Register/LocationForm.vue'
import MediaAndPortfolio from '@/Components/Register/MediaAndPortfolio.vue'
import Review from '@/Components/Register/Review.vue'
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
                        <BusinessInformationForm :formData="formData" :errors="errors"
                            :vendorCategoryOptions="vendorCategoryOptions" />
                    </div>

                    <!-- Step 2: Location -->
                    <div v-show="currentStep === 2" class="space-y-6">
                        <LocationForm :formData="formData" :errors="errors"
                            :serviceCoverageOptions="serviceCoverageOptions" />
                    </div>

                    <!-- Step 3: Contact and Payment Preference -->
                    <div v-show="currentStep === 3" class="space-y-6">
                        <ContactAndPayment :formData="formData" :errors="errors" />
                    </div>

                    <!-- Step 4: Media & Portfolio -->
                    <div v-show="currentStep === 4" class="space-y-6">
                        <MediaAndPortfolio :errors="errors" :profilePhoto="profilePhoto"
                            :profilePhotoUrl="profilePhotoUrl" :servicePhotos="servicePhotos"
                            :servicePhotoUrls="servicePhotoUrls" @handle-file-upload="handleFileUpload"
                            @remove-file="removeFile" />
                    </div>

                    <!-- Step 5: Legal & Compliance -->
                    <div v-show="currentStep === 5" class="space-y-6">
                        <LegalAndCompliance :errors="errors" :permitFiles="permitFiles"
                            @handle-file-upload="handleFileUpload" @remove-file="removeFile" />
                    </div>

                    <!-- Step 6: Account Security -->
                    <div v-show="currentStep === 6" class="space-y-6">
                        <AccountSecurity :formData="formData" :errors="errors" :passwordStrength="passwordStrength"
                            :passwordRequirements="passwordRequirements" :passwordsMatch="passwordsMatch" />
                    </div>

                    <!-- Step 7: Review & Submit -->
                    <div v-show="currentStep === 7" class="space-y-6">
                        <Review :formData="formData" :errors="errors" v-model:termsAccepted="termsAccepted" />
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
