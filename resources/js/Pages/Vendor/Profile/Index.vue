<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { ref, reactive, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Header from '@/Components/Vendor/Profile/Header.vue';
import NavigationTabs from '@/Components/Vendor/Profile/NavigationTabs.vue';
import BasicInfo from '@/Components/Vendor/Profile/BasicInfo.vue';
import Specialties from '@/Components/Vendor/Profile/Specialties.vue';
import Portfolio from '@/Components/Vendor/Profile/Portfolio.vue';
import MediaTab from '@/Components/Vendor/Profile/MediaTab.vue';

const props = defineProps({
    vendor: {
        type: Object
    },
    portfolioImages: {
        type: Array,
        default: () => []
    },
    showcaseVideos: {
        type: Array,
        default: () => []
    }
})



// Create a reactive copy of the original vendor data
const formData = useForm({
    businessName: props.vendor.business_name ?? '',
    contactPerson: props.vendor.full_name ?? '',
    email: props.vendor.user.email ?? '',
    phone: props.vendor.contact_number ?? '',
    website: props.vendor.website ?? '',
    profileImage: props.vendor.avatar ?? null,
    profileImageFile: null,
    businessDescription: props.vendor.description ?? '',
    yearsOfExperience: props.vendor.years_of_experience ?? '',
    teamSize: props.vendor.team_size ?? '',
    businessAddress: props.vendor.location ?? '',
    serviceRadius: props.vendor.service_radius ?? '',
    specialties: props.vendor.specialties || [],
    services: props.vendor.services || [],
    startingPrice: props.vendor.starting_price ?? '',
    currency: props.vendor.currency ?? 'USD',
    packageTypes: props.vendor.package_types || [],
    socialMedia: props.vendor.social_media || {
        instagram: '',
        facebook: '',
        twitter: '',
        linkedin: ''
    },
    portfolioImages: [],
    showcaseVideos: [],
    removedImageIds: [],
    removedVideoIds: [],
    _method: 'PUT'
});



// Gallery and Media - using existing media if available
const portfolioImages = ref(props.portfolioImages);
const removedImageIds = ref([]) // for tracking deleted ones

const originalPortfolioImages = [...portfolioImages.value];

const showcaseVideos = ref(props.showcaseVideos);

const originalShowcaseVideos = [...showcaseVideos.value];

// UI State
const activeTab = ref('basic');
const isEditing = ref(false);
const showImageUpload = ref(false);
const showVideoModal = ref(false);
const showProfileImageModal = ref(false);
const newVideoTitle = ref('');
const newVideoFile = ref(null); // Changed from URL to file
const newVideoPreview = ref(null);
const newImageFiles = ref([]); // For multiple image uploads
const newImagePreviews = ref([]);

// Backup of form data for cancel functionality
let formDataBackup = null;

// Methods
const toggleEdit = () => {
    if (!isEditing.value) {
        // Entering edit mode - create a backup of current form data
        formDataBackup = {
            businessName: formData.businessName,
            contactPerson: formData.contactPerson,
            email: formData.email,
            phone: formData.phone,
            website: formData.website,
            profileImage: formData.profileImage,
            businessDescription: formData.businessDescription,
            yearsOfExperience: formData.yearsOfExperience,
            teamSize: formData.teamSize,
            businessAddress: formData.businessAddress,
            serviceRadius: formData.serviceRadius,
            specialties: [...formData.specialties],
            services: [...formData.services],
            startingPrice: formData.startingPrice,
            currency: formData.currency,
            packageTypes: [...formData.packageTypes],
            socialMedia: { ...formData.socialMedia },
            portfolioImages: [...portfolioImages.value],
            showcaseVideos: [...showcaseVideos.value]
        };
    } else {
        // Canceling edit mode - restore from backup
        if (formDataBackup) {
            formData.businessName = formDataBackup.businessName;
            formData.contactPerson = formDataBackup.contactPerson;
            formData.email = formDataBackup.email;
            formData.phone = formDataBackup.phone;
            formData.website = formDataBackup.website;
            formData.profileImage = formDataBackup.profileImage;
            formData.businessDescription = formDataBackup.businessDescription;
            formData.yearsOfExperience = formDataBackup.yearsOfExperience;
            formData.teamSize = formDataBackup.teamSize;
            formData.businessAddress = formDataBackup.businessAddress;
            formData.serviceRadius = formDataBackup.serviceRadius;
            formData.specialties = [...formDataBackup.specialties];
            formData.services = [...formDataBackup.services];
            formData.startingPrice = formDataBackup.startingPrice;
            formData.currency = formDataBackup.currency;
            formData.packageTypes = [...formDataBackup.packageTypes];
            formData.socialMedia = { ...formDataBackup.socialMedia };

            // Restore portfolio images and videos
            portfolioImages.value = [...formDataBackup.portfolioImages];
            showcaseVideos.value = [...formDataBackup.showcaseVideos];

            // Clear any uploaded files
            formData.profileImageFile = null;
            formData.portfolioImages = [];
            formData.showcaseVideos = [];
        }
    }

    isEditing.value = !isEditing.value;
};

const saveChanges = () => {
    const submitData = new FormData()

    // Append all scalar fields
    Object.keys(formData).forEach((key) => {
        if (!['portfolioImages', 'showcaseVideos', 'socialMedia'].includes(key)) {
            if (formData[key] !== null && formData[key] !== undefined) {
                submitData.append(key, formData[key])
            }
        }
    })

    // Social media as JSON
    submitData.append('socialMedia', JSON.stringify(formData.socialMedia))

    // Append new images
    formData.portfolioImages.forEach((file, index) => {
        submitData.append(`portfolioImages[${index}]`, file)
    })

    // Append removed ids
    formData.removedImageIds.forEach((id, index) => {
        submitData.append(`removedImageIds[${index}]`, id)
    })

    // Submit to backend
    formData.post(route('vendor.profile-setting.update'), {
        data: submitData,
    })

    isEditing.value = false
}

const addSpecialty = () => {
    const newSpecialty = prompt('Enter new specialty:');
    if (newSpecialty && newSpecialty.trim()) {
        formData.specialties.push(newSpecialty.trim());
    }
};

const removeSpecialty = (index) => {
    formData.specialties.splice(index, 1);
};

const addService = () => {
    const newService = prompt('Enter new service:');
    if (newService && newService.trim()) {
        formData.services.push(newService.trim());
    }
};

const removeService = (index) => {
    formData.services.splice(index, 1);
};

const handleVideoFileUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Check if file is a video
        if (!file.type.includes('video/')) {
            alert('Please select a video file.');
            return;
        }

        newVideoFile.value = file;

        // Create preview
        newVideoPreview.value = URL.createObjectURL(file);
    }
};

const addVideo = () => {
    if (newVideoTitle.value && newVideoFile.value) {
        // Add to form data for submission
        formData.showcaseVideos.push({
            title: newVideoTitle.value,
            file: newVideoFile.value
        });

        // Add to UI list
        showcaseVideos.value.push({
            id: Date.now(),
            title: newVideoTitle.value,
            url: newVideoPreview.value,
            file: newVideoFile.value
        });

        newVideoTitle.value = '';
        newVideoFile.value = null;
        newVideoPreview.value = null;
        showVideoModal.value = false;
    } else {
        alert('Please fill in both title and select a video file.');
    }
};

const removeVideo = (id) => {


    const index = showcaseVideos.value.findIndex(video => video.id === id);
    if (index !== -1) {
        showcaseVideos.value.splice(index, 1);
        // Also remove from formData if it exists there
        if (formData.showcaseVideos.length > index) {
            formData.showcaseVideos.splice(index, 1);
        }
    }
};

const handleImageFilesUpload = (event) => {
    const files = Array.from(event.target.files);

    // Filter only image files
    const imageFiles = files.filter(file => file.type.includes('image/'));

    if (imageFiles.length === 0) {
        alert('Please select valid image files.');
        return;
    }

    // Add to form data
    imageFiles.forEach(file => {
        formData.portfolioImages.push(file);
    });

    // Create previews
    imageFiles.forEach(file => {
        const reader = new FileReader();
        reader.onload = (e) => {
            portfolioImages.value.push({ url: e.target.result });
            newImagePreviews.value.push(e.target.result);
        };
        reader.readAsDataURL(file);
    });

    showImageUpload.value = false;
};

const removeImage = (index) => {
    const image = portfolioImages.value[index]

    // If it's an existing image (from backend, has id)
    if (image.id) {
        formData.removedImageIds.push(image.id)
    }

    // console.log(formData.removedImageIds)

    // Remove from UI preview
    portfolioImages.value.splice(index, 1)

    // Also remove from new uploads if it was added there
    if (formData.portfolioImages.length > index) {
        formData.portfolioImages.splice(index, 1)
    }
}

const handleProfileImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Check if file is an image
        if (!file.type.includes('image/')) {
            alert('Please select an image file.');
            return;
        }

        formData.profileImageFile = file;

        const reader = new FileReader();
        reader.onload = (e) => {
            formData.profileImage = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeProfileImage = () => {
    formData.profileImage = originalData.profileImage;
    formData.profileImageFile = null;
    showProfileImageModal.value = false;
};
</script>

<template>
    <VendorLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <Header :formData="formData" :vendor="vendor" :isEditing="isEditing"
                    @show-profile-modal="showProfileImageModal = true" @toggle-edit="toggleEdit"
                    @save-changes="saveChanges" />


                <!-- Navigation Tabs -->
                <NavigationTabs v-model:activeTab="activeTab" />

                <!-- Tab Content -->
                <div class="bg-white rounded-lg shadow-sm">

                    <!-- Basic Information Tab -->
                    <div v-show="activeTab === 'basic'" class="p-8">
                        <BasicInfo :formData="formData" :isEditing="isEditing" />
                    </div>

                    <!-- Services & Specialties Tab -->
                    <div v-show="activeTab === 'services'" class="p-8">
                        <Specialties :formData="formData" :isEditing="isEditing" @add-specialty="addSpecialty"
                            @remove-specialty="removeSpecialty" />

                    </div>

                    <!-- Portfolio Tab -->
                    <div v-show="activeTab === 'portfolio'" class="p-8">
                        <Portfolio v-model:portfolioImages="portfolioImages" :isEditing="isEditing"
                            @show-image-upload="showImageUpload = true" @remove-image="removeImage" />

                    </div>

                    <!-- Media Tab -->
                    <div v-show="activeTab === 'media'" class="p-8">
                        <MediaTab :isEditing="isEditing" v-model:showcaseVideos="showcaseVideos"
                            @show-video-modal="showVideoModal = true" @remove-video="removeVideo" />
                    </div>
                </div>

                <!-- Video Modal -->
                <div v-if="showVideoModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Add Video</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Video Title</label>
                                <input v-model="newVideoTitle"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    type="text" placeholder="Enter video title" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Video File</label>
                                <input type="file" @change="handleVideoFileUpload" accept="video/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <p class="text-xs text-gray-500 mt-1">Supported formats: MP4, MOV, AVI, etc.</p>
                            </div>

                            <!-- Video Preview -->
                            <div v-if="newVideoPreview" class="mt-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preview</label>
                                <video :src="newVideoPreview" class="w-full h-32 object-cover rounded-lg"
                                    controls></video>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button @click="showVideoModal = false; newVideoPreview = null; newVideoFile = null;"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                            <button @click="addVideo"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Add Video
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Image Upload Modal -->
                <div v-if="showImageUpload"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Upload Portfolio Images</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Images</label>
                                <input type="file" @change="handleImageFilesUpload" accept="image/*" multiple
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <p class="text-xs text-gray-500 mt-1">You can select multiple images</p>
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button @click="showImageUpload = false"
                                class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Profile Image Modal -->
                <div v-if="showProfileImageModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                    <div class="bg-white rounded-lg p-6 w-full max-w-md">
                        <h3 class="text-lg font-semibold mb-4">Update Profile Image</h3>

                        <!-- Current Image Preview -->
                        <div class="flex justify-center mb-4">
                            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-200 border-4 border-gray-300">
                                <img v-if="formData.profileImage" :src="formData.profileImage" alt="Current Profile"
                                    class="w-full h-full object-cover" />
                                <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                        </path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <!-- Upload New Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Image</label>
                                <input type="file" @change="handleProfileImageUpload" accept="image/*"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" />
                                <p class="text-xs text-gray-500 mt-1">Recommended: Square image, at least 200x200px</p>
                            </div>
                        </div>

                        <div class="flex justify-between mt-6">
                            <!-- Remove Image Button -->
                            <button v-if="formData.profileImage" @click="removeProfileImage"
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                Remove Image
                            </button>
                            <div v-else></div>

                            <!-- Action Buttons -->
                            <div class="flex space-x-3">
                                <button @click="showProfileImageModal = false"
                                    class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Cancel
                                </button>
                                <button @click="showProfileImageModal = false"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Done
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </VendorLayout>
</template>

<style scoped>
/* Custom scrollbar for better UX */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Smooth transitions */
* {
    transition: all 0.2s ease-in-out;
}
</style>
