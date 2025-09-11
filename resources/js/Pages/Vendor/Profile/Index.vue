<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue';
import { ref, reactive } from 'vue';

// Form data - in real app, this would come from props or API
const formData = reactive({
    // Basic Information
    businessName: 'Elegant Events Co.',
    contactPerson: 'Sarah Johnson',
    email: 'sarah@elegantevents.com',
    phone: '+1 (555) 123-4567',
    website: 'https://elegantevents.com',
    profileImage: '', // Added missing profileImage property

    // Business Details
    businessDescription: 'We specialize in creating unforgettable experiences for weddings, corporate events, and special celebrations. With over 10 years of experience, we bring your vision to life with attention to detail and exceptional service.',
    yearsOfExperience: '10',
    teamSize: '15',

    // Location & Service Area
    businessAddress: '123 Event Plaza, Downtown District, City',
    serviceRadius: '50',

    // Specialties & Services
    specialties: ['Wedding Planning', 'Corporate Events', 'Birthday Parties', 'Anniversary Celebrations'],
    services: ['Full Event Planning', 'Day-of Coordination', 'Venue Decoration', 'Catering Services', 'Photography', 'Entertainment Booking'],

    // Pricing & Packages
    startingPrice: '500',
    currency: 'USD',
    packageTypes: ['Basic', 'Premium', 'Luxury'],

    // Social Media
    socialMedia: {
        instagram: '@elegantevents',
        facebook: 'ElegantEventsCo',
        twitter: '@elegant_events',
        linkedin: 'elegant-events-co'
    }
});

// Gallery and Media
const portfolioImages = ref([
    'https://images.unsplash.com/photo-1519225421980-715cb0215aed?w=300&h=200&fit=crop',
    'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?w=300&h=200&fit=crop',
    'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?w=300&h=200&fit=crop',
    'https://images.unsplash.com/photo-1470229538611-16ba8c7ffbd7?w=300&h=200&fit=crop'
]);

const showcaseVideos = ref([
    { id: 1, title: 'Wedding Highlight Reel', url: 'https://example.com/video1' },
    { id: 2, title: 'Corporate Event Setup', url: 'https://example.com/video2' }
]);

// UI State
const activeTab = ref('basic');
const isEditing = ref(false);
const showImageUpload = ref(false);
const showVideoModal = ref(false);
const showProfileImageModal = ref(false); // Added missing modal state
const newVideoTitle = ref('');
const newVideoUrl = ref('');
const newImageUrl = ref(''); // Added for image upload

// Methods
const toggleEdit = () => {
    isEditing.value = !isEditing.value;
};

const saveChanges = () => {
    // In real app, make API call to save changes
    console.log('Saving changes...', formData);
    isEditing.value = false;
    // Show success message
    alert('Changes saved successfully!');
};

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

const addVideo = () => {
    if (newVideoTitle.value && newVideoUrl.value) {
        showcaseVideos.value.push({
            id: Date.now(),
            title: newVideoTitle.value,
            url: newVideoUrl.value
        });
        newVideoTitle.value = '';
        newVideoUrl.value = '';
        showVideoModal.value = false;
    } else {
        alert('Please fill in both title and URL fields.');
    }
};

const removeVideo = (id) => {
    showcaseVideos.value = showcaseVideos.value.filter(video => video.id !== id);
};

const removeImage = (index) => {
    portfolioImages.value.splice(index, 1);
};

// Added missing methods for profile image handling
const handleProfileImageUpload = (event) => {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            formData.profileImage = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const removeProfileImage = () => {
    formData.profileImage = '';
    showProfileImageModal.value = false;
};

// Added method for adding portfolio images
const addPortfolioImage = () => {
    if (newImageUrl.value.trim()) {
        portfolioImages.value.push(newImageUrl.value);
        newImageUrl.value = '';
        showImageUpload.value = false;
    } else {
        alert('Please enter a valid image URL');
    }
};

// Added method to handle URL upload for portfolio images
const handlePortfolioImageUrlUpload = () => {
    const url = prompt('Enter image URL:');
    if (url && url.trim()) {
        portfolioImages.value.push(url.trim());
    }
};
</script>

<template>
    <VendorLayout>
        <div class="min-h-screen bg-gray-50 py-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="bg-white rounded-lg shadow-sm mb-8 p-6">
                    <div class="flex justify-between items-start">
                        <div class="flex items-start space-x-6">
                            <!-- Profile/Business Image -->
                            <div class="relative">
                                <div
                                    class="w-24 h-24 rounded-full overflow-hidden bg-gray-200 border-4 border-white shadow-lg">
                                    <img v-if="formData.profileImage" :src="formData.profileImage"
                                        alt="Business Logo/Profile" class="w-full h-full object-cover" />
                                    <div v-else class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4">
                                            </path>
                                        </svg>
                                    </div>
                                </div>
                                <!-- Edit Profile Image Button -->
                                <button v-if="isEditing" @click="showProfileImageModal = true"
                                    class="absolute -bottom-2 -right-2 bg-blue-600 text-white p-2 rounded-full hover:bg-blue-700 transition-colors shadow-lg">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </div>

                            <div>
                                <h1 class="text-3xl font-bold text-gray-900">{{ formData.businessName }}</h1>
                                <p class="text-gray-600 mt-1">{{ formData.contactPerson }}</p>
                                <p class="text-gray-500 mt-2">Manage your business information and showcase your
                                    services</p>
                            </div>
                        </div>
                        <div class="flex space-x-3">
                            <button @click="toggleEdit"
                                :class="isEditing ? 'bg-gray-600 hover:bg-gray-700' : 'bg-blue-600 hover:bg-blue-700'"
                                class="px-4 py-2 text-white rounded-lg font-medium transition-colors">
                                {{ isEditing ? 'Cancel' : 'Edit Profile' }}
                            </button>
                            <button v-if="isEditing" @click="saveChanges"
                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                                Save Changes
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="bg-white rounded-lg shadow-sm mb-8">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 px-6">
                            <button v-for="tab in [
                                { id: 'basic', label: 'Basic Info' },
                                { id: 'services', label: 'Services & Specialties' },
                                { id: 'portfolio', label: 'Portfolio' },
                                { id: 'media', label: 'Media' },
                                { id: 'pricing', label: 'Pricing' }
                            ]" :key="tab.id" @click="activeTab = tab.id"
                                :class="activeTab === tab.id ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700'"
                                class="py-4 px-1 border-b-2 font-medium text-sm transition-colors">
                                {{ tab.label }}
                            </button>
                        </nav>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="bg-white rounded-lg shadow-sm">

                    <!-- Basic Information Tab -->
                    <div v-show="activeTab === 'basic'" class="p-8">
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
                    </div>

                    <!-- Services & Specialties Tab -->
                    <div v-show="activeTab === 'services'" class="p-8">
                        <h2 class="text-2xl font-semibold mb-6">Services & Specialties</h2>

                        <!-- Specialties Section -->
                        <div class="mb-8">
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Event Specialties</h3>
                                <button v-if="isEditing" @click="addSpecialty"
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                    Add Specialty
                                </button>
                            </div>
                            <div class="flex flex-wrap gap-3">
                                <div v-for="(specialty, index) in formData.specialties" :key="index"
                                    class="flex items-center bg-blue-100 text-blue-800 px-4 py-2 rounded-full">
                                    <span>{{ specialty }}</span>
                                    <button v-if="isEditing" @click="removeSpecialty(index)"
                                        class="ml-2 text-blue-600 hover:text-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Services Section -->
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-lg font-medium">Services Offered</h3>
                                <button v-if="isEditing" @click="addService"
                                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                    Add Service
                                </button>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                <div v-for="(service, index) in formData.services" :key="index"
                                    class="flex items-center justify-between bg-gray-50 p-3 rounded-lg">
                                    <span>{{ service }}</span>
                                    <button v-if="isEditing" @click="removeService(index)"
                                        class="text-gray-400 hover:text-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Portfolio Tab -->
                    <div v-show="activeTab === 'portfolio'" class="p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold">Portfolio Gallery</h2>
                            <button v-if="isEditing" @click="handlePortfolioImageUrlUpload"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Upload Images
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            <div v-for="(image, index) in portfolioImages" :key="index" class="relative group">
                                <img :src="image" :alt="`Portfolio image ${index + 1}`"
                                    class="w-full h-48 object-cover rounded-lg shadow-sm" />
                                <div v-if="isEditing"
                                    class="absolute inset-0 bg-black bg-opacity-50 opacity-0 group-hover:opacity-100 transition-opacity rounded-lg flex items-center justify-center">
                                    <button @click="removeImage(index)"
                                        class="bg-red-600 text-white p-2 rounded-full hover:bg-red-700 transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Upload placeholder -->
                            <div v-if="isEditing" @click="handlePortfolioImageUrlUpload"
                                class="border-2 border-dashed border-gray-300 rounded-lg h-48 flex flex-col items-center justify-center text-gray-500 hover:border-blue-400 transition-colors cursor-pointer">
                                <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="font-medium">Add Image</span>
                            </div>
                        </div>
                    </div>

                    <!-- Media Tab -->
                    <div v-show="activeTab === 'media'" class="p-8">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-semibold">Showcase Videos</h2>
                            <button v-if="isEditing" @click="showVideoModal = true"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                Add Video
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div v-for="video in showcaseVideos" :key="video.id" class="bg-gray-50 rounded-lg p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="font-medium text-lg">{{ video.title }}</h3>
                                    <button v-if="isEditing" @click="removeVideo(video.id)"
                                        class="text-gray-400 hover:text-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                                <div class="bg-gray-200 rounded-lg h-32 flex items-center justify-center mb-3">
                                    <svg class="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                                <p class="text-sm text-gray-600 truncate">{{ video.url }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Tab -->
                    <div v-show="activeTab === 'pricing'" class="p-8">
                        <h2 class="text-2xl font-semibold mb-6">Pricing Information</h2>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Starting Price</label>
                                <div class="relative">
                                    <span
                                        class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">$</span>
                                    <input v-model="formData.startingPrice" :disabled="!isEditing"
                                        class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                                        type="number" />
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                                <select v-model="formData.currency" :disabled="!isEditing"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500">
                                    <option value="USD">USD ($)</option>
                                    <option value="EUR">EUR (€)</option>
                                    <option value="GBP">GBP (£)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Service Radius
                                    (miles)</label>
                                <input v-model="formData.serviceRadius" :disabled="!isEditing"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 disabled:bg-gray-50 disabled:text-gray-500"
                                    type="number" />
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-medium mb-4">Package Types</h3>
                            <div class="flex flex-wrap gap-3">
                                <div v-for="(packageType, index) in formData.packageTypes" :key="index"
                                    class="bg-green-100 text-green-800 px-4 py-2 rounded-full">
                                    {{ packageType }}
                                </div>
                            </div>
                        </div>
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
                                <label class="block text-sm font-medium text-gray-700 mb-2">Video URL</label>
                                <input v-model="newVideoUrl"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    type="url" placeholder="Enter video URL" />
                            </div>
                        </div>
                        <div class="flex justify-end space-x-3 mt-6">
                            <button @click="showVideoModal = false"
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
