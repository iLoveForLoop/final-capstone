<script setup>
import AccountSecurity from '@/Components/Register/AccountSecurity.vue'
import BusinessInformationForm from '@/Components/Register/BusinessInformationForm.vue'
import ContactAndPayment from '@/Components/Register/ContactAndPayment.vue'
import LegalAndCompliance from '@/Components/Register/LegalAndCompliance.vue'
import LocationForm from '@/Components/Register/LocationForm.vue'
import MediaAndPortfolio from '@/Components/Register/MediaAndPortfolio.vue'
import Review from '@/Components/Register/Review.vue'
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { push } from 'notivue'
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
const totalSteps = 6
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
    return true
    // return profilePhoto.value !== null && servicePhotos.value.length > 0
})

const isStep4Valid = computed(() => {
    return true
    // return permitFiles.value.length > 0
})

const isStep5Valid = computed(() => {
    return formData.password.length >= 8 &&
        passwordStrength.value >= 3 &&
        passwordsMatch.value === true
})

const isStep6Valid = computed(() => {
    return termsAccepted.value === true
})

const isStep7Valid = computed(() => {
    return true
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
        // 3: 'Contact and Payment Preference',
        3: 'Media & Portfolio',
        4: 'Legal & Compliance',
        5: 'Account Security',
        6: 'Review & Submit'
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
            // if (!profilePhoto.value) {
            //     errors.profilePhoto = 'Profile photo is required'
            //     isValid = false
            // }
            // if (servicePhotos.value.length < 5) {
            //     errors.servicePhotos = 'At least 5 service photos are required'
            //     isValid = false
            // }
            break

        case 4:
            // if (permitFiles.value.length === 0) {
            //     errors.businessPermits = 'At least one business permit is required'
            //     isValid = false
            // }
            break

        case 5:
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

        case 6:
            if (!termsAccepted.value) {
                errors.terms = 'You must accept the terms and conditions'
                isValid = false
            }
            break

        case 7:

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
            // forceFormData: true,
            onFinish: () => {
                isLoading.value = false;


            },
            onError: (errors) => {
                console.log(errors);

                // errors is the same as form.errors
                // You can get the field names:
                const fields = Object.keys(errors) // ['full_name', 'email', 'password']

                // You can build a custom message
                const message = `Validation failed for: ${fields.join(', ')}`

                // push.error(message)

                // If you want to show the first message only:
                const firstField = fields[0]
                const firstMessage = errors[firstField]
                push.error(`${firstMessage}`)
            }
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
    <GuestLayout>
        <div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-4 sm:py-8">
            <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="text-center mb-6 sm:mb-10">
                    <div class="flex justify-center mb-3 sm:mb-4">
                        <div
                            class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-500 rounded-lg flex items-center justify-center shadow-sm">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                </path>
                            </svg>
                        </div>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-2">Vendor Registration</h1>
                    <p class="text-sm sm:text-base text-gray-600 px-2">Complete your profile to start accepting bookings
                    </p>
                </div>

                <!-- Progress Section -->
                <div class="mb-6 sm:mb-8 px-2 sm:px-0">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-xs sm:text-sm font-medium text-gray-700">Step {{ currentStep }} of {{
                            totalSteps
                            }}</span>
                        <span class="text-xs sm:text-sm font-medium text-purple-500">{{ Math.round((currentStep /
                            totalSteps)
                            * 100) }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5 sm:h-2 mb-2">
                        <div class="bg-purple-500 h-1.5 sm:h-2 rounded-full transition-all duration-300 ease-out shadow-sm"
                            :style="{ width: `${(currentStep / totalSteps) * 100}%` }"></div>
                    </div>
                    <div class="flex justify-between text-[10px] sm:text-xs text-gray-500 px-1">
                        <span class="truncate px-1">Business</span>
                        <span class="truncate px-1">Location</span>
                        <!-- <span class="truncate px-1">Contact</span> -->
                        <span class="truncate px-1">Portfolio</span>
                        <span class="truncate px-1">Legal</span>
                        <span class="truncate px-1">Security</span>
                        <span class="truncate px-1">Review</span>
                    </div>
                </div>

                <!-- Form Container -->
                <div
                    class="bg-white rounded-xl sm:rounded-xl shadow-md sm:shadow-lg border border-gray-100 overflow-hidden mx-2 sm:mx-0">
                    <!-- Step Header -->
                    <div class="border-b bg-purple-500 text-white px-4 sm:px-6 py-3 sm:py-4">
                        <h2 class="text-base sm:text-lg font-semibold">{{ stepTitle }}</h2>
                    </div>

                    <!-- Form Content -->
                    <div class="p-4 sm:p-6">
                        <!-- Step 1: Business Information -->
                        <div v-show="currentStep === 1" class="space-y-4 sm:space-y-6">
                            <BusinessInformationForm :formData="formData" :errors="errors"
                                :vendorCategoryOptions="vendorCategoryOptions" />
                        </div>

                        <!-- Step 2: Location -->
                        <div v-show="currentStep === 2" class="space-y-4 sm:space-y-6">
                            <LocationForm :formData="formData" :errors="errors"
                                :serviceCoverageOptions="serviceCoverageOptions" />
                        </div>

                        <!-- Step 3: Contact and Payment Preference -->
                        <!-- <div v-show="currentStep === 3" class="space-y-4 sm:space-y-6">
                        <ContactAndPayment :formData="formData" :errors="errors" />
                    </div> -->

                        <!-- Step 3: Media & Portfolio -->
                        <div v-show="currentStep === 3" class="space-y-4 sm:space-y-6">
                            <MediaAndPortfolio :errors="errors" :profilePhoto="profilePhoto"
                                :profilePhotoUrl="profilePhotoUrl" :servicePhotos="servicePhotos"
                                :servicePhotoUrls="servicePhotoUrls" @handle-file-upload="handleFileUpload"
                                @remove-file="removeFile" />
                        </div>

                        <!-- Step 4: Legal & Compliance -->
                        <div v-show="currentStep === 4" class="space-y-4 sm:space-y-6">
                            <LegalAndCompliance :errors="errors" :permitFiles="permitFiles"
                                @handle-file-upload="handleFileUpload" @remove-file="removeFile" />
                        </div>

                        <!-- Step 6: Account Security -->
                        <div v-show="currentStep === 5" class="space-y-4 sm:space-y-6">
                            <AccountSecurity :formData="formData" :errors="errors" :passwordStrength="passwordStrength"
                                :passwordRequirements="passwordRequirements" :passwordsMatch="passwordsMatch" />
                        </div>

                        <!-- Step 6: Review & Submit -->
                        <div v-show="currentStep === 6" class="space-y-4 sm:space-y-6">
                            <Review :formData="formData" :errors="errors" v-model:termsAccepted="termsAccepted" />
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
                                        ? 'bg-purple-500 text-white hover:bg-blue-700 shadow-sm'
                                        : 'bg-gray-200 text-gray-500 cursor-not-allowed'
                                ]">
                                Next
                                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 sm:ml-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5l7 7-7 7">
                                    </path>
                                </svg>
                            </button>
                            <button v-else @click="submitForm" :disabled="isLoading || !isCurrentStepValid"
                                type="button" :class="[
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
                                {{ isLoading ? 'Submitting...' : 'Submit' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Help Text -->
                <div class="mt-4 sm:mt-6 text-center text-xs sm:text-sm text-gray-500 px-2">
                    <p>Questions? <a href="#" class="text-purple-500 hover:text-blue-800 font-medium">Contact our
                            support
                            team</a></p>
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
