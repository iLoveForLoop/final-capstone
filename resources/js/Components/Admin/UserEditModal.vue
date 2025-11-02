<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { push } from 'notivue'

const show = ref(false);
const selectedRole = ref('client');
const currentUserId = ref(null);
const currentUser = ref(null);
const categoryDropdownOpen = ref(false);

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    full_name: '',
    email: '',
    password: '',
    password_confirmation: '',
    profile_image: null,
    location: '',
    contact_number: '',
    business_name: '',
    description: '',
    selected_role: 'client',
    service_categories: [],
    _method: 'PUT',
});

const roleOptions = [
    { value: 'admin', label: 'Admin' },
    { value: 'vendor', label: 'Vendor' },
    { value: 'client', label: 'Client' }
];

const showVendorFields = computed(() => selectedRole.value === 'vendor');
const showClientFields = computed(() => selectedRole.value === 'client');

const open = (user) => {
    show.value = true;
    currentUser.value = user;
    currentUserId.value = user.id;

    // console.log('Client Number: ', user)

    // Update form with user data
    form.full_name = user.name;
    form.email = user.email;
    form.location = user.vendor?.location || user.client?.location || '';
    form.contact_number = user.vendor?.contact_number || user.client?.contact_number || '';
    form.business_name = user.vendor?.business_name || '';
    form.description = user.vendor?.description || '';
    form.selected_role = user.roles[0]?.name || 'client';
    form.service_categories = user.service_categories?.map(c => c.id) || [];
    selectedRole.value = user.roles[0]?.name || 'client';
};

const toggleCategoryDropdown = () => {
    categoryDropdownOpen.value = !categoryDropdownOpen.value;
};

const toggleCategory = (categoryId) => {
    if (form.service_categories.includes(categoryId)) {
        form.service_categories = form.service_categories.filter(id => id !== categoryId);
    } else {
        form.service_categories = [...form.service_categories, categoryId];
    }
};

const removeCategory = (categoryId) => {
    form.service_categories = form.service_categories.filter(id => id !== categoryId);
};

const getCategoryName = (categoryId) => {
    const category = props.categories.find(c => c.id === categoryId);
    return category ? category.name : '';
};

const handleFileUpload = (event) => {
    form.profile_image = event.target.files[0];
};

const submit = () => {
    // console.log('full_name: ', form.name)
    form.selected_role = selectedRole.value;
    form.post(route('admin.users.update', currentUserId.value), {
        preserveScroll: true,
        onSuccess: () => {
            show.value = false;
            form.reset();
            push.success({
                // title: 'Success',
                message: 'User Edited Successfully'
            })
        }
    });
};

defineExpose({
    show: open
});

</script>

<template>
    <transition enter-active-class="transition-opacity ease-out duration-300" enter-from-class="opacity-0"
        enter-to-class="opacity-100" leave-active-class="transition-opacity ease-in duration-200"
        leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Backdrop -->
                <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                    <div class="absolute inset-0 bg-gray-500 opacity-75" @click="show = false"></div>
                </div>

                <!-- Modal Container -->
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <transition enter-active-class="transition-all ease-out duration-300"
                    enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    enter-to-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-active-class="transition-all ease-in duration-200"
                    leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                    leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                        <!-- Modal Header -->
                        <div class="bg-indigo-600 px-4 py-3 sm:px-6 sm:flex sm:items-center sm:justify-between">
                            <h3 class="text-lg leading-6 font-medium text-white">
                                Edit User: {{ currentUser?.name }}
                            </h3>
                            <button @click="show = false" class="text-indigo-100 hover:text-white focus:outline-none">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="space-y-6">
                                <!-- Role Selection -->
                                <!-- <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">User Role</label>
                                    <div class="grid grid-cols-3 gap-3">
                                        <div v-for="option in roleOptions" :key="option.value"
                                            class="flex items-center">
                                            <input :id="`role-${option.value}`" v-model="selectedRole"
                                                :value="option.value" type="radio"
                                                class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300">
                                            <label :for="`role-${option.value}`"
                                                class="ml-2 block text-sm text-gray-700">
                                                {{ option.label }}
                                            </label>
                                        </div>
                                    </div>
                                </div> -->

                                <!-- Common Fields -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="full_name" class="block text-sm font-medium text-gray-700">Full
                                            Name</label>
                                        <input type="text" id="full_name" v-model="form.full_name"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <p v-if="form.errors.full_name" class="mt-1 text-sm text-red-600">{{
                                            form.errors.full_name
                                            }}</p>
                                    </div>
                                    <div>
                                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                        <input type="email" id="email" v-model="form.email"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{
                                            form.errors.email }}</p>
                                    </div>
                                    <div>
                                        <label for="password" class="block text-sm font-medium text-gray-700">Password
                                            (leave blank to keep current)</label>
                                        <input type="password" id="password" v-model="form.password"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">{{
                                            form.errors.password }}</p>
                                    </div>
                                    <div>
                                        <label for="password_confirmation"
                                            class="block text-sm font-medium text-gray-700">Confirm Password</label>
                                        <input type="password" id="password_confirmation"
                                            v-model="form.password_confirmation"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="profile_image"
                                            class="block text-sm font-medium text-gray-700">Profile
                                            Image</label>
                                        <input type="file" id="profile_image" @change="handleFileUpload"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p v-if="form.errors.profile_image" class="mt-1 text-sm text-red-600">{{
                                            form.errors.profile_image }}</p>
                                        <div v-if="currentUser?.image_url" class="mt-2 flex items-center">
                                            <span class="text-xs text-gray-500 mr-2">Current:</span>
                                            <img :src="currentUser.image_url" class="h-8 w-8 rounded-full object-cover">
                                        </div>
                                    </div>
                                </div>

                                <!-- Vendor Fields -->
                                <transition enter-active-class="transition-all ease-out duration-300"
                                    enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-[500px]"
                                    leave-active-class="transition-all ease-in duration-200"
                                    leave-from-class="opacity-100 max-h-[500px]" leave-to-class="opacity-0 max-h-0">
                                    <div v-if="showVendorFields" class="space-y-4 overflow-hidden">
                                        <h4 class="text-sm font-medium text-gray-500 border-b pb-2">Vendor Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="business_name"
                                                    class="block text-sm font-medium text-gray-700">Business
                                                    Name</label>
                                                <input type="text" id="business_name" v-model="form.business_name"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <p v-if="form.errors.business_name" class="mt-1 text-sm text-red-600">{{
                                                    form.errors.business_name }}</p>
                                            </div>

                                            <!-- Service Categories Multi-Select -->
                                            <div class="md:col-span-2">
                                                <label class="block text-sm font-medium text-gray-700">Service
                                                    Categories</label>
                                                <div class="mt-1 relative">
                                                    <div @click="toggleCategoryDropdown"
                                                        class="cursor-pointer relative border border-gray-300 rounded-md shadow-sm pl-3 pr-10 py-2 text-left focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                        <span v-if="form.service_categories.length === 0"
                                                            class="text-gray-400">Select categories...</span>
                                                        <div v-else class="flex flex-wrap gap-1">
                                                            <span v-for="categoryId in form.service_categories"
                                                                :key="categoryId"
                                                                class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-800">
                                                                {{ getCategoryName(categoryId) }}
                                                                <button type="button"
                                                                    @click.stop="removeCategory(categoryId)"
                                                                    class="ml-1.5 inline-flex text-indigo-400 hover:text-indigo-500 focus:outline-none">
                                                                    <svg class="h-3 w-3" fill="currentColor"
                                                                        viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd"
                                                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                                            clip-rule="evenodd" />
                                                                    </svg>
                                                                </button>
                                                            </span>
                                                        </div>
                                                        <span
                                                            class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                                            <svg class="h-5 w-5 text-gray-400"
                                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                                fill="currentColor">
                                                                <path fill-rule="evenodd"
                                                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                                                    clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>

                                                    <!-- Category Dropdown -->
                                                    <transition enter-active-class="transition ease-out duration-100"
                                                        enter-from-class="transform opacity-0 scale-95"
                                                        enter-to-class="transform opacity-100 scale-100"
                                                        leave-active-class="transition ease-in duration-75"
                                                        leave-from-class="transform opacity-100 scale-100"
                                                        leave-to-class="transform opacity-0 scale-95">
                                                        <div v-show="categoryDropdownOpen"
                                                            class="absolute z-10 mt-1 w-full rounded-md bg-white shadow-lg max-h-60 overflow-auto">
                                                            <ul class="py-1">
                                                                <li v-for="category in props.categories"
                                                                    :key="category.id"
                                                                    class="px-3 py-2 text-sm text-gray-900 hover:bg-indigo-100 cursor-pointer flex items-center"
                                                                    @click="toggleCategory(category.id)">
                                                                    <input type="checkbox"
                                                                        :checked="form.service_categories.includes(category.id)"
                                                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded mr-2">
                                                                    <span>{{ category.name }}</span>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </transition>
                                                </div>
                                                <p v-if="form.errors.service_categories"
                                                    class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.service_categories }}
                                                </p>
                                            </div>

                                            <div>
                                                <label for="contact_number"
                                                    class="block text-sm font-medium text-gray-700">Contact
                                                    Number</label>
                                                <input type="text" id="contact_number" v-model="form.contact_number"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <p v-if="form.errors.contact_number" class="mt-1 text-sm text-red-600">
                                                    {{
                                                        form.errors.contact_number }}</p>
                                            </div>
                                            <div>
                                                <label for="location"
                                                    class="block text-sm font-medium text-gray-700">Location</label>
                                                <input type="text" id="location" v-model="form.location"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.location }}</p>
                                            </div>
                                            <div class="md:col-span-2">
                                                <label for="description"
                                                    class="block text-sm font-medium text-gray-700">Business
                                                    Description</label>
                                                <textarea id="description" v-model="form.description" rows="3"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{
                                                    form.errors.description
                                                    }}</p>
                                            </div>
                                            <!-- <div>
                                                <label for="profile_image"
                                                    class="block text-sm font-medium text-gray-700">Profile
                                                    Image</label>
                                                <input type="file" id="profile_image" @change="handleFileUpload"
                                                    class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                <p v-if="form.errors.profile_image" class="mt-1 text-sm text-red-600">{{
                                                    form.errors.profile_image }}</p>
                                                <div v-if="currentUser?.image_url" class="mt-2 flex items-center">
                                                    <span class="text-xs text-gray-500 mr-2">Current:</span>
                                                    <img :src="currentUser.image_url"
                                                        class="h-8 w-8 rounded-full object-cover">
                                                </div>
                                            </div> -->
                                        </div>
                                    </div>
                                </transition>

                                <!-- Client Fields -->
                                <transition enter-active-class="transition-all ease-out duration-300"
                                    enter-from-class="opacity-0 max-h-0" enter-to-class="opacity-100 max-h-[500px]"
                                    leave-active-class="transition-all ease-in duration-200"
                                    leave-from-class="opacity-100 max-h-[500px]" leave-to-class="opacity-0 max-h-0">
                                    <div v-if="showClientFields" class="space-y-4 overflow-hidden">
                                        <h4 class="text-sm font-medium text-gray-500 border-b pb-2">Client Information
                                        </h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="contact_number"
                                                    class="block text-sm font-medium text-gray-700">Contact
                                                    Number</label>
                                                <input type="text" id="contact_number" v-model="form.contact_number"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <p v-if="form.errors.contact_number" class="mt-1 text-sm text-red-600">
                                                    {{
                                                        form.errors.contact_number }}</p>
                                            </div>
                                            <div>
                                                <label for="location"
                                                    class="block text-sm font-medium text-gray-700">Location</label>
                                                <input type="text" id="location" v-model="form.location"
                                                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                <p v-if="form.errors.location" class="mt-1 text-sm text-red-600">
                                                    {{ form.errors.location }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </transition>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-75">
                                <span v-if="!form.processing">Update User</span>
                                <span v-else class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Processing...
                                </span>
                            </button>
                            <button type="button" @click="show = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </div>
                </transition>
            </div>
        </div>
    </transition>
</template>

<style scoped>
/* Line clamp for description */
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
