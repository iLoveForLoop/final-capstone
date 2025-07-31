<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const props = defineProps({
    category_id: {
        type: [String, Number], // or whatever type you expect
        default: null
    },
});

const toast = useToast();
const emit = defineEmits(['close', 'created']);
const show = ref(false);
const selectedImage = ref(null);
const newSpecification = ref('');
const newDeliverable = ref('');



const form = useForm({
    service_category_id: props.category_id,
    name: '',
    description: '',
    price: '',
    max_price: '',
    cover_image: null,
    min_pax: 1,
    max_pax: 1,
    delivery_fee: '',
    lead_time_days: 3,
    service_area: [],
    notes: '',

    // Photography specific fields
    coverage_type: 'wedding',
    hours_of_coverage: 8,
    delivery_time_days: 30,
    deliverables: [],
    number_of_photographers: 1,
    specifications: [],
    studio_shoot_available: false,
    _method: 'POST'
});

const coverageTypes = [
    { value: 'wedding', label: 'Wedding' },
    { value: 'corporate', label: 'Corporate' },
    { value: 'portrait', label: 'Portrait' },
    { value: 'event', label: 'Event' },
    { value: 'product', label: 'Product' },
    { value: 'fashion', label: 'Fashion' },
    { value: 'sports', label: 'Sports' },
    { value: 'real_estate', label: 'Real Estate' }
];

const commonDeliverables = [
    'Digital Files (USB)',
    'Online Gallery',
    'Photo Album',
    'Prints (8x10)',
    'Prints (5x7)',
    'Canvas Print',
    'Edited JPEGs',
    'RAW Files',
    'Slideshow'
];

const commonSpecifications = [
    'Engagement Session Included',
    'Second Photographer',
    'Photo Booth',
    'Drone Photography',
    '360° Photos',
    'Same-Day Edits',
    'Premium Album',
    'Unlimited Shots',
    'All-Day Coverage'
];

const commonServiceAreas = [
    'Tubigon',
    'Calape',
    'Tagbilaran',
    'Anywhere in Bohol'
];

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    form.cover_image = file;
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            selectedImage.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const addSpecification = (spec) => {
    if (!form.specifications.includes(spec)) {
        form.specifications = [...form.specifications, spec];
    }
};

const removeSpecification = (index) => {
    form.specifications = form.specifications.filter((_, i) => i !== index);
};

const toggleServiceArea = (area) => {
    const index = form.service_area.indexOf(area);
    if (index === -1) {
        form.service_area = [...form.service_area, area];
    } else {
        form.service_area = form.service_area.filter((_, i) => i !== index);
    }
};

const addCustomSpecification = () => {
    if (newSpecification.value.trim() && !form.specifications.includes(newSpecification.value.trim())) {
        form.specifications = [...form.specifications, newSpecification.value.trim()];
        newSpecification.value = '';
    }
};

const addDeliverable = (item) => {
    if (!form.deliverables.includes(item)) {
        form.deliverables = [...form.deliverables, item];
    }
};

const addCustomDeliverable = () => {
    if (newDeliverable.value.trim() && !form.deliverables.includes(newDeliverable.value.trim())) {
        form.deliverables = [...form.deliverables, newDeliverable.value.trim()];
        newDeliverable.value = '';
    }
};

const removeDeliverable = (index) => {
    form.deliverables = form.deliverables.filter((_, i) => i !== index);
};

const submit = () => {
    form.post(route('vendor.photography-services.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Service created successfully');
            form.reset();
            selectedImage.value = null;
            show.value = false;
            emit('close');
            emit('created');
        },
        onError: () => {
            toast.error('Failed to create service');
        }
    });
};
</script>

<template>
    <!-- Modal body -->
    <div class="px-6 py-4">
        <div class="space-y-6">
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Service Name *
                        </label>
                        <input type="text" id="name" v-model="form.name" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                            {{ form.errors.name }}</p>
                    </div>

                    <!-- Coverage Type -->
                    <div>
                        <label for="coverage_type" class="block text-sm font-medium text-gray-700 mb-1">
                            Coverage Type *
                        </label>
                        <select id="coverage_type" v-model="form.coverage_type" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            <option v-for="type in coverageTypes" :key="type.value" :value="type.value">
                                {{ type.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.coverage_type" class="mt-1 text-sm text-red-600">
                            {{ form.errors.coverage_type }}</p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                            Description
                        </label>
                        <textarea id="description" v-model="form.description" rows="3"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                            {{ form.errors.description }}</p>
                    </div>

                    <!-- Cover Image -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Cover Image
                        </label>
                        <div class="flex items-center space-x-4">
                            <div class="flex-shrink-0">
                                <div
                                    class="h-32 w-32 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center">
                                    <img v-if="selectedImage" :src="selectedImage" class="h-full w-full object-cover">
                                    <svg v-else class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <label class="cursor-pointer">
                                    <span
                                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Upload Image
                                    </span>
                                    <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*">
                                </label>
                                <p class="mt-1 text-xs text-gray-500">
                                    JPEG, PNG, or JPG (Max. 2MB)
                                </p>
                            </div>
                        </div>
                        <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">
                            {{ form.errors.cover_image }}</p>
                    </div>
                </div>
            </div>

            <!-- Photography Details Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Photography Details</h4>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Hours of Coverage -->
                    <div>
                        <label for="hours_of_coverage" class="block text-sm font-medium text-gray-700 mb-1">
                            Hours of Coverage
                        </label>
                        <input type="number" id="hours_of_coverage" v-model="form.hours_of_coverage" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.hours_of_coverage" class="mt-1 text-sm text-red-600">
                            {{ form.errors.hours_of_coverage }}</p>
                    </div>

                    <!-- Number of Photographers -->
                    <div>
                        <label for="number_of_photographers" class="block text-sm font-medium text-gray-700 mb-1">
                            Number of Photographers
                        </label>
                        <input type="number" id="number_of_photographers" v-model="form.number_of_photographers" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.number_of_photographers" class="mt-1 text-sm text-red-600">
                            {{ form.errors.number_of_photographers }}</p>
                    </div>

                    <!-- Delivery Time -->
                    <div>
                        <label for="delivery_time_days" class="block text-sm font-medium text-gray-700 mb-1">
                            Delivery Time (Days)
                        </label>
                        <input type="number" id="delivery_time_days" v-model="form.delivery_time_days" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.delivery_time_days" class="mt-1 text-sm text-red-600">
                            {{ form.errors.delivery_time_days }}</p>
                    </div>
                </div>

                <!-- Studio Shoot Available -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <input id="studio_shoot_available" v-model="form.studio_shoot_available" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="studio_shoot_available" class="ml-2 block text-sm text-gray-700">
                            Studio Shoot Available
                        </label>
                    </div>
                    <p v-if="form.errors.studio_shoot_available" class="mt-1 text-sm text-red-600">
                        {{ form.errors.studio_shoot_available }}</p>
                </div>
            </div>

            <!-- Deliverables Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Deliverables</h4>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selected Deliverables
                    </label>
                    <div v-if="form.deliverables.length > 0" class="flex flex-wrap gap-2 mb-4">
                        <span v-for="(item, index) in form.deliverables" :key="index"
                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                            {{ item }}
                            <button @click.stop="removeDeliverable(index)"
                                class="ml-1.5 inline-flex text-green-600 focus:outline-none">
                                <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </span>
                    </div>
                    <div v-else class="text-sm text-gray-500 mb-4">
                        No deliverables selected yet. Add deliverables below.
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Common Deliverables -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Common Deliverables
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            <button v-for="item in commonDeliverables" :key="item" @click="addDeliverable(item)" :class="{
                                'bg-indigo-100 text-indigo-800 border-indigo-300': form.deliverables.includes(item),
                                'bg-white text-gray-700 border-gray-300': !form.deliverables.includes(item)
                            }"
                                class="px-3 py-2 border rounded-md text-sm font-medium text-left focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ item }}
                            </button>
                        </div>
                    </div>

                    <!-- Custom Deliverable Input -->
                    <div>
                        <label for="newDeliverable" class="block text-sm font-medium text-gray-700 mb-1">
                            Add Custom Deliverable
                        </label>
                        <div class="flex gap-2">
                            <input id="newDeliverable" v-model="newDeliverable" type="text"
                                placeholder="Enter custom deliverable"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                @keyup.enter="addCustomDeliverable">
                            <button @click="addCustomDeliverable"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                Add
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Pricing</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Base Price (₱) *
                        </label>
                        <input type="number" id="price" v-model="form.price" required min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.price }}</p>
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Max Price (₱)
                        </label>
                        <input type="number" id="max_price" v-model="form.max_price" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.max_price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.max_price }}</p>
                    </div>

                    <!-- Delivery Fee -->
                    <div>
                        <label for="delivery_fee" class="block text-sm font-medium text-gray-700 mb-1">
                            Delivery Fee (₱)
                        </label>
                        <input type="number" id="delivery_fee" v-model="form.delivery_fee" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.delivery_fee" class="mt-1 text-sm text-red-600">
                            {{ form.errors.delivery_fee }}</p>
                    </div>
                </div>
            </div>

            <!-- Capacity Section -->
            <!-- <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Capacity</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                    <div>
                        <label for="min_pax" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Pax *
                        </label>
                        <input type="number" id="min_pax" v-model="form.min_pax" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.min_pax" class="mt-1 text-sm text-red-600">
                            {{ form.errors.min_pax }}</p>
                    </div>


                    <div>
                        <label for="max_pax" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Pax *
                        </label>
                        <input type="number" id="max_pax" v-model="form.max_pax" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.max_pax" class="mt-1 text-sm text-red-600">
                            {{ form.errors.max_pax }}</p>
                    </div>


                    <div>
                        <label for="lead_time_days" class="block text-sm font-medium text-gray-700 mb-1">
                            Lead Time (Days)
                        </label>
                        <input type="number" id="lead_time_days" v-model="form.lead_time_days" min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.lead_time_days" class="mt-1 text-sm text-red-600">
                            {{ form.errors.lead_time_days }}</p>
                    </div>
                </div>
            </div> -->

            <!-- Service Options Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Service Options</h4>

                <!-- Service Area -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Service Areas *
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="area in commonServiceAreas" :key="area" @click="toggleServiceArea(area)"
                            class="px-3 py-1 rounded-full text-sm font-medium transition-colors" :class="{
                                'bg-indigo-100 text-indigo-800 border border-indigo-200': form.service_area.includes(area),
                                'bg-gray-100 text-gray-800 border border-gray-200 hover:bg-gray-200': !form.service_area.includes(area)
                            }">
                            {{ area }}
                        </button>
                    </div>
                    <p v-if="form.errors.service_area" class="mt-1 text-sm text-red-600">
                        {{ form.errors.service_area }}</p>
                </div>

                <!-- Specifications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Specifications
                    </label>

                    <!-- Custom specification input -->
                    <div class="flex gap-2 mb-3">
                        <input v-model="newSpecification" type="text" placeholder="Enter custom specification"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            @keyup.enter="addCustomSpecification">
                        <button @click="addCustomSpecification"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Add
                        </button>
                    </div>

                    <!-- Common specifications quick add -->
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-2">Quick add common specifications:</p>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="spec in commonSpecifications" :key="spec" @click="addSpecification(spec)"
                                class="px-3 py-1 rounded-full text-sm font-medium transition-colors" :class="{
                                    'bg-indigo-100 text-indigo-800 border border-indigo-200': form.specifications.includes(spec),
                                    'bg-gray-100 text-gray-800 border border-gray-200 hover:bg-gray-200': !form.specifications.includes(spec)
                                }">
                                {{ spec }}
                            </button>
                        </div>
                    </div>

                    <!-- Selected specifications -->
                    <div v-if="form.specifications.length > 0" class="mt-3">
                        <p class="text-xs text-gray-500 mb-2">Selected specifications:</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(spec, index) in form.specifications" :key="index"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-indigo-100 text-indigo-800">
                                {{ spec }}
                                <button @click.stop="removeSpecification(index)"
                                    class="ml-1.5 inline-flex text-indigo-600 focus:outline-none">
                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </span>
                        </div>
                    </div>
                    <p v-if="form.errors.specifications" class="mt-1 text-sm text-red-600">
                        {{ form.errors.specifications }}</p>
                </div>
            </div>

            <!-- Additional Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Additional Notes
                </label>
                <textarea id="notes" v-model="form.notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                    {{ form.errors.notes }}</p>
            </div>
        </div>
    </div>

    <!-- Modal footer -->
    <div
        class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row-reverse sm:justify-start sm:space-x-reverse sm:space-x-3">
        <button type="button" @click="submit" :disabled="form.processing"
            class="w-full sm:w-auto px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed">
            <span v-if="form.processing" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Creating...
            </span>
            <span v-else>
                Create Service
            </span>
        </button>
        <button type="button" @click="emit('close')"
            class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </button>
    </div>
</template>

<style></style>
