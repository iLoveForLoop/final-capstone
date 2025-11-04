<script setup>
import ClientLayout from '@/Layouts/ClientLayout.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { ref, nextTick, computed } from 'vue';
import useFlash from '@/Composables/useFlash';

const props = defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

useFlash()

const user = usePage().props.auth.user;
const client = usePage().props.auth.user.client || {};

// Profile Information Form
const profileForm = useForm({
    name: user.name,
    email: user.email,
    full_name: client.full_name || '',
    first_name: client.first_name || '',
    last_name: client.last_name || '',
    contact_number: client.contact_number || '',
    location: client.location || '',
    profile_image: null,
    remove_profile_image: false, // Added this flag
    _method: "PATCH"
});

// Password Update Form
const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

// Delete Account Form
const deleteForm = useForm({
    password: '',
});

// Modal state
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const currentPasswordInput = ref(null);
const avatarInput = ref(null);

// Computed property for profile image URL
const profileImageUrl = computed(() => {
    if (profileForm.profile_image) {
        return URL.createObjectURL(profileForm.profile_image);
    }

    // Check if user has media in Spatie Media Library
    if (user.media && user.media.length > 0) {
        const profileMedia = user.media.find(media => media.collection_name === 'avatar');
        if (profileMedia) {
            return profileMedia.original_url;
        }
    }

    return `https://ui-avatars.com/api/?name=${encodeURIComponent(user.name)}&color=7F9CF5&background=EBF4FF`;
});

// Check if user has profile picture
const hasProfilePicture = computed(() => {
    return user.media && user.media.some(media => media.collection_name === 'avatar');
});

// Handle profile_image file selection
const handleAvatarChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            profileForm.errors.profile_image = 'Please select a valid image file.';
            return;
        }

        // Validate file size (2MB)
        if (file.size > 2 * 1024 * 1024) {
            profileForm.errors.profile_image = 'Image size must be less than 2MB.';
            return;
        }

        profileForm.profile_image = file;
        profileForm.remove_profile_image = false; // Reset remove flag when uploading new image
        profileForm.errors.profile_image = null;

        // Auto-submit the form when a new profile_image is selected
        updateProfile();
    }
};

// Remove profile_image
const removeAvatar = () => {
    profileForm.profile_image = null;
    profileForm.remove_profile_image = true;
    updateProfile();
};

// Profile update method
const updateProfile = () => {
    const formData = new FormData();

    // Append all form data
    Object.keys(profileForm.data()).forEach(key => {
        if (profileForm[key] !== null && profileForm[key] !== undefined) {
            // Handle file separately
            if (key === 'profile_image' && profileForm.profile_image instanceof File) {
                formData.append(key, profileForm.profile_image);
            } else if (key === 'remove_profile_image') {
                // Convert boolean to string for form data
                formData.append(key, profileForm.remove_profile_image.toString());
            } else {
                formData.append(key, profileForm[key]);
            }
        }
    });

    profileForm.post(route('client.users.update', user.id), {
        preserveScroll: true,
        forceFormData: true,
        onSuccess: () => {
            if (avatarInput.value) {
                avatarInput.value.value = '';
            }
            // Reset remove flag after successful removal
            if (profileForm.remove_profile_image) {
                profileForm.remove_profile_image = false;
            }
        }
    });
};

// Password update method
const updatePassword = () => {
    passwordForm.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => {
            passwordForm.reset();
        },
        onError: () => {
            if (passwordForm.errors.password) {
                passwordForm.reset('password', 'password_confirmation');
                passwordInput.value?.focus();
            }
            if (passwordForm.errors.current_password) {
                passwordForm.reset('current_password');
                currentPasswordInput.value?.focus();
            }
        },
    });
};

// Email verification resend
const resendVerification = () => {
    useForm({}).post(route('verification.send'));
};

// Delete account methods
const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;
    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => deleteForm.reset(),
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;
    deleteForm.clearErrors();
    deleteForm.reset();
};
</script>

<template>
    <ClientLayout>
        <div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Profile Settings</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your account information and preferences</p>
                </div>

                <div class="space-y-6">
                    <!-- Profile Information Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Profile Information</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Update your account's profile information and contact details.
                            </p>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="updateProfile" class="space-y-6">
                                <!-- Profile Picture Section -->
                                <div class="flex items-center space-x-6">
                                    <div class="flex-shrink-0">
                                        <div class="relative">
                                            <img :src="profileImageUrl" alt="Profile picture"
                                                class="w-20 h-20 rounded-full object-cover border-2 border-gray-300 dark:border-gray-600" />
                                            <div v-if="profileForm.processing && profileForm.profile_image"
                                                class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center">
                                                <svg class="w-8 h-8 text-white animate-spin" fill="none"
                                                    viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                                        stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="space-y-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                                Profile Picture
                                            </label>
                                            <div class="flex items-center space-x-3">
                                                <label for="profile_image" class="cursor-pointer">
                                                    <span
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Change Photo
                                                    </span>
                                                    <input id="profile_image" ref="avatarInput" type="file"
                                                        class="hidden" accept="image/*" @change="handleAvatarChange" />
                                                </label>
                                                <button v-if="hasProfilePicture" type="button" @click="removeAvatar"
                                                    class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-red-600 dark:text-red-400 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                    Remove
                                                </button>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                JPG, PNG or GIF. Max size 2MB.
                                            </p>
                                            <p v-if="profileForm.errors.profile_image" class="text-sm text-red-600">
                                                {{ profileForm.errors.profile_image }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <!-- Basic Information -->
                                    <div class="space-y-4">
                                        <h3 class="text-md font-medium text-gray-900 dark:text-white">Basic Information
                                        </h3>

                                        <!-- <div>
                                            <label for="name"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Username
                                            </label>
                                            <input id="name" v-model="profileForm.name" type="text"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                :class="{ 'border-red-500': profileForm.errors.name }" required />
                                            <p v-if="profileForm.errors.name" class="mt-1 text-sm text-red-600">
                                                {{ profileForm.errors.name }}
                                            </p>
                                        </div> -->

                                        <div>
                                            <label for="email"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Email Address
                                            </label>
                                            <input id="email" v-model="profileForm.email" type="email"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                :class="{ 'border-red-500': profileForm.errors.email }" required />
                                            <p v-if="profileForm.errors.email" class="mt-1 text-sm text-red-600">
                                                {{ profileForm.errors.email }}
                                            </p>
                                        </div>

                                        <!-- Email Verification Status -->
                                        <div v-if="!user.email_verified_at"
                                            class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-md p-4">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0">
                                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20"
                                                        fill="currentColor">
                                                        <path fill-rule="evenodd"
                                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                                            clip-rule="evenodd" />
                                                    </svg>
                                                </div>
                                                <div class="ml-3">
                                                    <p class="text-sm text-yellow-700 dark:text-yellow-400">
                                                        Your email address is unverified.
                                                        <button type="button" @click="resendVerification"
                                                            class="font-medium underline hover:text-yellow-600 dark:hover:text-yellow-300 ml-1">
                                                            Click here to re-send the verification email.
                                                        </button>
                                                    </p>
                                                </div>
                                            </div>
                                            <div v-if="status === 'verification-link-sent'"
                                                class="mt-2 text-sm text-green-600 dark:text-green-400">
                                                A new verification link has been sent to your email address.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Personal Details -->
                                    <div class="space-y-4">
                                        <h3 class="text-md font-medium text-gray-900 dark:text-white">Personal Details
                                        </h3>

                                        <!-- <div>
                                            <label for="full_name"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Full Name
                                            </label>
                                            <input id="full_name" v-model="profileForm.full_name" type="text"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                                        </div> -->

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label for="first_name"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    First Name
                                                </label>
                                                <input id="first_name" v-model="profileForm.first_name" type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                                            </div>

                                            <div>
                                                <label for="last_name"
                                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Last Name
                                                </label>
                                                <input id="last_name" v-model="profileForm.last_name" type="text"
                                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                                            </div>
                                        </div>

                                        <div>
                                            <label for="contact_number"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Contact Number
                                            </label>
                                            <input id="contact_number" v-model="profileForm.contact_number" type="tel"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                                        </div>

                                        <div>
                                            <label for="location"
                                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Location
                                            </label>
                                            <input id="location" v-model="profileForm.location" type="text"
                                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white" />
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <button type="submit" :disabled="profileForm.processing"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg v-if="profileForm.processing && !profileForm.profile_image"
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Save Changes
                                        </button>
                                        <p v-if="profileForm.recentlySuccessful"
                                            class="ml-4 text-sm text-green-600 dark:text-green-400">
                                            Profile updated successfully.
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Password Update Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Update Password</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Ensure your account is using a long, random password to stay secure.
                            </p>
                        </div>
                        <div class="p-6">
                            <form @submit.prevent="updatePassword" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="current_password"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Current Password
                                        </label>
                                        <input id="current_password" ref="currentPasswordInput"
                                            v-model="passwordForm.current_password" type="password"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                            :class="{ 'border-red-500': passwordForm.errors.current_password }" />
                                        <p v-if="passwordForm.errors.current_password"
                                            class="mt-1 text-sm text-red-600">
                                            {{ passwordForm.errors.current_password }}
                                        </p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label for="password"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            New Password
                                        </label>
                                        <input id="password" ref="passwordInput" v-model="passwordForm.password"
                                            type="password"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                            :class="{ 'border-red-500': passwordForm.errors.password }" />
                                        <p v-if="passwordForm.errors.password" class="mt-1 text-sm text-red-600">
                                            {{ passwordForm.errors.password }}
                                        </p>
                                    </div>

                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Confirm New Password
                                        </label>
                                        <input id="password_confirmation" v-model="passwordForm.password_confirmation"
                                            type="password"
                                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                            :class="{ 'border-red-500': passwordForm.errors.password_confirmation }" />
                                        <p v-if="passwordForm.errors.password_confirmation"
                                            class="mt-1 text-sm text-red-600">
                                            {{ passwordForm.errors.password_confirmation }}
                                        </p>
                                    </div>
                                </div>

                                <div
                                    class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center">
                                        <button type="submit" :disabled="passwordForm.processing"
                                            class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed">
                                            <svg v-if="passwordForm.processing"
                                                class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            Update Password
                                        </button>
                                        <p v-if="passwordForm.recentlySuccessful"
                                            class="ml-4 text-sm text-green-600 dark:text-green-400">
                                            Password updated successfully.
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete Account Card -->
                    <div class="bg-white dark:bg-gray-800 shadow rounded-lg overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg font-medium text-gray-900 dark:text-white">Delete Account</h2>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                Permanently delete your account and all of its data.
                            </p>
                        </div>
                        <div class="p-6">
                            <div
                                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-md p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800 dark:text-red-400">
                                            Danger Zone
                                        </h3>
                                        <div class="mt-2 text-sm text-red-700 dark:text-red-300">
                                            <p>
                                                Once your account is deleted, all of its resources and data will be
                                                permanently deleted.
                                                Before deleting your account, please download any data or information
                                                that you wish to retain.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <button @click="confirmUserDeletion"
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    Delete Account
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Delete Account Confirmation Modal -->
            <div v-if="confirmingUserDeletion" class="fixed inset-0 z-50 overflow-y-auto">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
                    </div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div
                        class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 dark:bg-red-900/30 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">
                                    Delete Account
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Are you sure you want to delete your account? Once your account is deleted, all
                                        of its resources and data will be permanently deleted. Please enter your
                                        password to confirm you would like to permanently delete your account.
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <label for="delete_password" class="sr-only">Password</label>
                                    <input id="delete_password" ref="passwordInput" v-model="deleteForm.password"
                                        type="password"
                                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Enter your password"
                                        :class="{ 'border-red-500': deleteForm.errors.password }" />
                                    <p v-if="deleteForm.errors.password" class="mt-1 text-sm text-red-600">
                                        {{ deleteForm.errors.password }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="deleteUser" :disabled="deleteForm.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed">
                                Delete Account
                            </button>
                            <button type="button" @click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </ClientLayout>
</template>

<style scoped>
/* Add any custom styles here if needed */
</style>
