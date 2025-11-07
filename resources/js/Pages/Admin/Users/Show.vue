<script setup>
import TestLayout from '@/Layouts/TestLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { useUserActions } from '@/Composables/useUserActions';
import StatusAction from '@/Components/Admin/StatusAction.vue';

const toast = useToast();
const props = defineProps({
    user: Object
});

const activeTab = ref('overview');
const showSuspendModal = ref(false);
const showResetPasswordModal = ref(false);

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString(undefined, options);
};

const suspendUser = () => {
    router.put(route('admin.users.suspend', props.user.id), {
        onSuccess: () => {
            toast.success('User suspended successfully');
            showSuspendModal.value = false;
        },
        onError: () => {
            toast.error('Failed to suspend user');
        }
    });
};

const resetPassword = () => {
    router.post(route('admin.users.reset-password', props.user.id), {
        onSuccess: () => {
            toast.success('Password reset email sent');
            showResetPasswordModal.value = false;
        },
        onError: () => {
            toast.error('Failed to reset password');
        }
    });
};

const {
    showActionModal,
    currentUser,
    currentAction,
    suspendForm,
    banForm,
    deleteForm,
    minDateTime,
    availableActions,
    openActionModal,
    closeActionModal,
    performSuspend,
    performBan,
    performActivate,
    performDelete,
    isSuspended
} = useUserActions();


</script>

<template>
    <TestLayout>


        <div v-if="showActionModal" class="fixed inset-0 bg-black bg-opacity-50 transition-opacity duration-300 z-40"
            @click="closeActionModal">
        </div>

        <transition enter-active-class="transform transition ease-in-out duration-300"
            enter-from-class="translate-x-full" enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in-out duration-300" leave-from-class="translate-x-0"
            leave-to-class="translate-x-full">
            <div v-if="showActionModal" class="fixed inset-y-0 right-0 pl-10 max-w-full flex z-50">
                <StatusAction :currentUser="currentUser" :availableActions="availableActions"
                    @close-action-modal="closeActionModal" @perform-suspend="performSuspend"
                    @perform-delete="performDelete" @perform-ban="performBan" v-model:suspendForm="suspendForm"
                    v-model:banForm="banForm" v-model:currentAction="currentAction" v-model:deleteForm="deleteForm" />
            </div>
        </transition>


        <div class="p-6 space-y-6 bg-gray-50 min-h-screen">
            <!-- Header -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">User Profile</h1>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-sm text-gray-500">Viewing details for:</span>
                        <span class="text-sm font-medium">{{ user.name }}</span>
                    </div>
                </div>
                <div class="flex gap-3">
                    <!-- <button @click="showResetPasswordModal = true"
                        class="px-4 py-2 text-sm font-medium rounded-lg border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 transition">
                        Reset Password
                    </button> -->
                    <button @click="openActionModal(user)"
                        class="px-4 py-2 text-sm font-medium rounded-lg bg-red-600 text-white hover:bg-red-700 transition">
                        Update User Status
                    </button>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column - Profile Card -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="p-6 flex flex-col items-center text-center">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-4 shadow" :class="{
                                    'border-green-500': user.status === 'active',
                                    'border-red-500': user.status === 'banned',
                                    'border-yello-500': user.status === 'suspended',
                                }">
                                    <!-- {{ console.log(user.image_url) }} -->
                                    <template v-if="user.image_url">
                                        <img :src="user.image_url" :alt="user.name" class="w-full h-full object-cover">
                                    </template>
                                    <template v-else>
                                        <div
                                            class="w-full h-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-3xl font-bold">
                                            {{ user.name.charAt(0).toUpperCase() }}
                                        </div>
                                    </template>
                                </div>

                            </div>
                            <h2 class="mt-4 text-xl font-semibold text-gray-800">{{ user.name }}</h2>
                            <p class="text-sm text-gray-500">{{ user.email }}</p>

                            <div class="mt-3 flex flex-wrap justify-center gap-2">
                                <span v-for="role in user.roles" :key="role.id" :class="{
                                    'bg-purple-100 text-purple-800': role.name === 'admin',
                                    'bg-green-100 text-green-800': role.name === 'vendor',
                                    'bg-blue-100 text-blue-800': role.name === 'client'
                                }" class="px-3 py-1 text-xs font-medium rounded-full capitalize">
                                    {{ role.name }}
                                </span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 px-6 py-4">
                            <div class="grid grid-cols-2 gap-4 text-center">
                                <div>
                                    <p class="text-sm text-gray-500">Joined</p>
                                    <p class="text-sm font-medium">{{ formatDate(user.created_at) }}</p>
                                </div>
                                <div>
                                    <p class="text-sm text-gray-500">Last Login</p>
                                    <p class="text-sm font-medium">{{ user.last_login_at ?
                                        formatDate(user.last_login_at) : 'Never' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <!-- <div class="bg-white rounded-xl shadow-sm p-6 space-y-4">
                        <h3 class="text-lg font-semibold text-gray-800">Quick Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Total Bookings</span>
                                <span class="text-sm font-medium">{{ user.bookings?.length || 0 }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Reviews Given</span>
                                <span class="text-sm font-medium">{{ user.reviews?.length || 0 }}</span>
                            </div>
                            <div v-if="user.vendor" class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Vendor Rating</span>
                                <span class="text-sm font-medium">{{ user.vendor.average_rating || 'N/A' }} ({{
                                    user.vendor.review_count ||
                                    0 }})</span>
                            </div>
                        </div>
                    </div> -->
                </div>

                <!-- Right Column - Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Tabs -->
                    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
                        <div class="border-b border-gray-200">
                            <nav class="flex -mb-px">
                                <button @click="activeTab = 'overview'" :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'overview',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'overview'
                                }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                                    Overview
                                </button>
                                <button v-if="user.vendor" @click="activeTab = 'vendor'" :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'vendor',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'vendor'
                                }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                                    Vendor Profile
                                </button>
                                <button @click="activeTab = 'activity'" :class="{
                                    'border-indigo-500 text-indigo-600': activeTab === 'activity',
                                    'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'activity'
                                }" class="whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm">
                                    Activity Log
                                </button>
                            </nav>
                        </div>
                        <div class="p-6">
                            <!-- Overview Tab -->
                            <div v-if="activeTab === 'overview'" class="space-y-4">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500">Basic Information</h4>
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Full Name</p>
                                            <p class="text-sm font-medium">{{ user.name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Email</p>
                                            <p class="text-sm font-medium">{{ user.email }}</p>
                                        </div>
                                        {{ console.log('Number: ', user) }}
                                        <div v-if="user.client">
                                            <p class="text-xs text-gray-500">Contact Number</p>
                                            <p class="text-sm font-medium">{{ user.client.contact_number }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Status</p>
                                            <p class="text-sm font-medium" :class="{
                                                'text-green-600': user.status === 'active',
                                                'text-red-600': user.status !== 'active'
                                            }">{{ user.status }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Registered</p>
                                            <p class="text-sm font-medium">{{ formatDate(user.created_at) }}</p>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="user.vendor">
                                    <h4 class="text-sm font-medium text-gray-500">Vendor Summary</h4>
                                    <div class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">Business Name</p>
                                            <p class="text-sm font-medium">{{ user.vendor.business_name }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Approval Status</p>
                                            <p class="text-sm font-medium" :class="{
                                                'text-green-600': user.vendor.is_approved,
                                                'text-yellow-600': !user.vendor.is_approved
                                            }">{{ user.vendor.is_approved ? 'Approved' : 'Pending' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Vendor Tab -->
                            <div v-if="activeTab === 'vendor' && user.vendor" class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-xs text-gray-500">Business Name</p>
                                        <p class="text-sm font-medium">{{ user.vendor.business_name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Contact Number</p>
                                        <p class="text-sm font-medium">{{ user.vendor.contact_number || 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Location</p>
                                        <p class="text-sm font-medium">{{ user.vendor.location || 'N/A' }}</p>
                                    </div>

                                </div>

                                <div>
                                    <p class="text-xs text-gray-500">Service Categories</p>
                                    <div class="mt-2">
                                        <div v-if="user.vendor.service_categories?.length" class="flex flex-wrap gap-2">
                                            <span v-for="category in user.vendor.service_categories" :key="category.id"
                                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                                {{ category.name }}
                                            </span>
                                        </div>
                                        <p v-else class="text-sm text-gray-400">No service categories selected</p>
                                    </div>
                                </div>



                                <div>
                                    <p class="text-xs text-gray-500">Business Description</p>
                                    <p class="mt-1 text-sm text-gray-800">{{ user.vendor.description }}</p>
                                </div>
                            </div>

                            <!-- Activity Tab -->
                            <div v-if="activeTab === 'activity'" class="space-y-4">

                                <div class="space-y-4">
                                    <div v-for="(activity, index) in user.activities" :key="index"
                                        class="flex items-start">
                                        <div class="flex-shrink-0 mt-1">
                                            <!-- <div class="h-2 w-2 rounded-full bg-indigo-500"></div> -->
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium">{{ activity.description }}</p>
                                            <p class="text-xs text-gray-500">{{ formatDate(activity.created_at) }}</p>
                                        </div>
                                    </div>
                                    <p v-if="!user.activities?.length" class="text-sm text-gray-500">No recent activity
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Suspend User Modal -->
        <!-- <div v-if="showSuspendModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showSuspendModal = false"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Suspend User</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">Are you sure you want to suspend this user? They
                                        will lose
                                        access to their account.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="suspendUser" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Suspend
                        </button>
                        <button @click="showSuspendModal = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div> -->

        <!-- Reset Password Modal -->
        <div v-if="showResetPasswordModal" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="showResetPasswordModal = false"></div>
                </div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div
                    class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">Reset Password</h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">This will send a password reset link to the user's
                                        email
                                        address: <strong>{{ user.email }}</strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button @click="resetPassword" type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Send Reset Link
                        </button>
                        <button @click="showResetPasswordModal = false" type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Cancel
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </TestLayout>
</template>

<style scoped>
/* Custom transitions */
.slide-fade-enter-active {
    transition: all 0.3s ease-out;
}

.slide-fade-leave-active {
    transition: all 0.2s cubic-bezier(1, 0.5, 0.8, 1);
}

.slide-fade-enter-from,
.slide-fade-leave-to {
    transform: translateY(10px);
    opacity: 0;
}
</style>
