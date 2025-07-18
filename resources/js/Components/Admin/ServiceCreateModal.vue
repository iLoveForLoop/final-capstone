<!-- components/ServiceCreateModal.vue -->
<script setup>
import { ref, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';

const props = defineProps({
    vendors: {
        type: Array
    },
})

const show = ref(false);
const selectedVendor = ref(null)

const form = useForm({
    vendor_id: '',
    service_category_id: '',
    name: '',
    description: '',
    price: '',
    is_available: true,
    image: null
});

const submit = () => {
    form.post(route('admin.services.store'), {
        preserveScroll: true,
        onSuccess: () => {
            show.value = false;
            form.reset();
        },
        forceFormData: true // Required for file uploads
    });
};

watch(() => form.vendor_id, (newVal) => {
    selectedVendor.value = props.vendors.find(v => v.id === newVal);
})

// Expose the show method to parent component
defineExpose({
    show: () => show.value = true
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
                                Add New Service
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
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="vendor_id"
                                            class="block text-sm font-medium text-gray-700">Vendor</label>
                                        <select id="vendor_id" v-model="form.vendor_id"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select Vendor</option>
                                            <!-- You'll need to populate this with actual vendors -->

                                            <option v-for="vendor in vendors" :key="vendor.id" :value="vendor.id">
                                                <!-- {{ console.log(vendor.serviceCategories) }} -->
                                                {{ vendor.business_name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.vendor_id" class="mt-1 text-sm text-red-600">{{
                                            form.errors.vendor_id }}</p>
                                    </div>

                                    <div>
                                        <label for="service_category_id"
                                            class="block text-sm font-medium text-gray-700">Category</label>
                                        <select id="service_category_id" v-model="form.service_category_id"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                            <option value="">Select Category</option>
                                            <option v-if="selectedVendor == null" value="">Select a vendor first
                                            </option>
                                            <!-- You'll need to populate this with actual categories -->
                                            <option v-for="category in selectedVendor?.service_categories"
                                                :key="category.id" :value="category.id">
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <p v-if="form.errors.service_category_id" class="mt-1 text-sm text-red-600">{{
                                            form.errors.service_category_id }}</p>
                                    </div>

                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700">Service
                                            Name</label>
                                        <input type="text" id="name" v-model="form.name"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name
                                        }}</p>
                                    </div>

                                    <div>
                                        <label for="price" class="block text-sm font-medium text-gray-700">Price</label>
                                        <input type="number" step="0.01" id="price" v-model="form.price"
                                            class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{
                                            form.errors.price }}</p>
                                    </div>
                                </div>

                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" v-model="form.description" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                    <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{
                                        form.errors.description }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Availability</label>
                                        <div class="mt-2 flex items-center">
                                            <input type="checkbox" id="is_available" v-model="form.is_available"
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="is_available" class="ml-2 block text-sm text-gray-700">
                                                Available
                                            </label>
                                        </div>
                                        <p v-if="form.errors.is_available" class="mt-1 text-sm text-red-600">{{
                                            form.errors.is_available }}</p>
                                    </div>

                                    <div>
                                        <label for="image" class="block text-sm font-medium text-gray-700">Service
                                            Image</label>
                                        <input type="file" id="image" @input="form.image = $event.target.files[0]"
                                            class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                        <p v-if="form.errors.image" class="mt-1 text-sm text-red-600">{{
                                            form.errors.image }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-75">
                                <span v-if="!form.processing">Add Service</span>
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
