<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { Upload, X, Plus, Image as ImageIcon, Eye } from 'lucide-vue-next';

const props = defineProps({
    category_id: {
        type: [String, Number],
    },
});

const toast = useToast();
const emit = defineEmits(['close', 'created']);
const show = ref(false);
const selectedImages = ref([]);
const newSpecification = ref('');
const newDeliverable = ref('');

console.log('cat id: ', props.category_id)

const form = useForm({
    service_category_id: props.category_id,
    name: '',
    description: '',
    price: '',
    max_price: '',
    cover_images: [],
    delivery_fee: '',
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
    const files = Array.from(event.target.files);

    files.forEach(file => {
        if (file && file.type.startsWith('image/')) {
            // Check if image already exists
            const existsIndex = selectedImages.value.findIndex(img => img.file.name === file.name && img.file.size === file.size);
            if (existsIndex === -1) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    selectedImages.value.push({
                        file: file,
                        preview: e.target.result,
                        id: Date.now() + Math.random()
                    });
                    updateFormImages();
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Clear the input
    event.target.value = '';
};

const removeImage = (index) => {
    selectedImages.value.splice(index, 1);
    updateFormImages();
};

const updateFormImages = () => {
    form.cover_images = selectedImages.value.map(img => img.file);
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



const submit = () => {
    console.log('cover_images:', form.cover_images);

    form.post(route('vendor.photography-services.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Service created successfully');
            form.reset();
            selectedImages.value = [];
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

                    <!-- Cover Images Section -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-3">
                            <ImageIcon class="inline w-4 h-4 mr-1" />
                            Portfolio Images
                        </label>

                        <!-- Image Upload Area -->
                        <div class="mb-4">
                            <label class="cursor-pointer">
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition-colors duration-200 bg-gray-50 hover:bg-gray-100">
                                    <Upload class="mx-auto h-12 w-12 text-gray-400 mb-2" />
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-indigo-600 hover:text-indigo-500">Click to
                                            upload</span>
                                        or drag and drop
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">
                                        PNG, JPG, JPEG up to 2MB each (Max 8 images)
                                    </p>
                                </div>
                                <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*" multiple
                                    :disabled="selectedImages.length >= 8">
                            </label>
                        </div>

                        <!-- Selected Images Grid -->
                        <div v-if="selectedImages.length > 0"
                            class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            <div v-for="(image, index) in selectedImages" :key="image.id"
                                class="relative group rounded-lg overflow-hidden bg-gray-100 aspect-square">
                                <img :src="image.preview" :alt="`Portfolio image ${index + 1}`"
                                    class="w-full h-full object-cover">

                                <!-- Image overlay -->
                                <div
                                    class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                    <div
                                        class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                        <!-- Preview button -->
                                        <button type="button"
                                            class="p-2 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors"
                                            @click="$refs[`imageModal${index}`].showModal()">
                                            <Eye class="w-4 h-4" />
                                        </button>
                                        <!-- Remove button -->
                                        <button type="button" @click="removeImage(index)"
                                            class="p-2 bg-white rounded-full text-red-600 hover:text-red-700 transition-colors">
                                            <X class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                <!-- Primary image badge -->
                                <div v-if="index === 0"
                                    class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">
                                    Cover
                                </div>

                                <!-- Image preview modal -->
                                <dialog :ref="`imageModal${index}`"
                                    class="rounded-lg backdrop:bg-black backdrop:bg-opacity-50">
                                    <div class="p-0 max-w-3xl">
                                        <img :src="image.preview" :alt="`Portfolio image ${index + 1}`"
                                            class="w-full h-auto rounded-lg">
                                        <div class="p-4 flex justify-between items-center">
                                            <span class="text-sm text-gray-600">Image {{ index + 1 }} of {{
                                                selectedImages.length }}</span>
                                            <button @click="$refs[`imageModal${index}`].close()"
                                                class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                                Close
                                            </button>
                                        </div>
                                    </div>
                                </dialog>
                            </div>

                            <!-- Add more images button -->
                            <div v-if="selectedImages.length < 8"
                                class="relative group rounded-lg border-2 border-dashed border-gray-300 aspect-square flex items-center justify-center hover:border-indigo-400 transition-colors">
                                <label class="cursor-pointer w-full h-full flex items-center justify-center">
                                    <div class="text-center">
                                        <Plus class="mx-auto h-8 w-8 text-gray-400 group-hover:text-indigo-500" />
                                        <span class="text-xs text-gray-500 group-hover:text-indigo-600 mt-1">Add
                                            More</span>
                                    </div>
                                    <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*"
                                        multiple>
                                </label>
                            </div>
                        </div>

                        <!-- Images count and limit info -->
                        <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                            <span>{{ selectedImages.length }} of 8 images selected</span>
                            <span v-if="selectedImages.length > 0" class="text-xs">
                                First image will be used as the cover photo
                            </span>
                        </div>

                        <p v-if="form.errors.cover_images" class="mt-1 text-sm text-red-600">
                            {{ form.errors.cover_images }}</p>
                    </div>
                </div>
            </div>

            <!-- Photography Details Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Photography Details</h4>

                <!-- Studio Shoot Available -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input id="studio_shoot_available" v-model="form.studio_shoot_available" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="studio_shoot_available" class="ml-2 block text-sm text-gray-700">
                            Studio Shoot Available
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        Check if you offer studio photography sessions
                    </p>
                    <p v-if="form.errors.studio_shoot_available" class="mt-1 text-sm text-red-600">
                        {{ form.errors.studio_shoot_available }}</p>
                </div>

                <!-- Specifications -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Service Specifications
                    </label>

                    <!-- Custom specification input -->
                    <div class="flex gap-2 mb-3">
                        <input v-model="newSpecification" type="text" placeholder="Enter custom specification"
                            class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            @keyup.enter="addCustomSpecification">
                        <button @click="addCustomSpecification"
                            class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <Plus class="w-4 h-4" />
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
                                    <X class="h-3.5 w-3.5" />
                                </button>
                            </span>
                        </div>
                    </div>
                    <p v-if="form.errors.specifications" class="mt-1 text-sm text-red-600">
                        {{ form.errors.specifications }}</p>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Pricing</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-1">
                            Price *
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">₱</span>
                            <input type="number" id="price" v-model="form.price" required min="0"
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0.00">
                        </div>
                        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.price }}</p>
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Price (Optional)
                        </label>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-gray-500">₱</span>
                            <input type="number" id="max_price" v-model="form.max_price" min="0"
                                class="w-full pl-8 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0.00">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            For packages with variable pricing
                        </p>
                        <p v-if="form.errors.max_price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.max_price }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Options Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Service Options</h4>

                <!-- Service Area -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Service Areas
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="area in commonServiceAreas" :key="area" @click="toggleServiceArea(area)"
                            class="px-3 py-2 rounded-lg text-sm font-medium transition-colors" :class="{
                                'bg-indigo-100 text-indigo-800 border border-indigo-200': form.service_area.includes(area),
                                'bg-gray-100 text-gray-800 border border-gray-200 hover:bg-gray-200': !form.service_area.includes(area)
                            }">
                            {{ area }}
                        </button>
                    </div>
                    <p v-if="form.errors.service_area" class="mt-1 text-sm text-red-600">
                        {{ form.errors.service_area }}</p>
                </div>
            </div>

            <!-- Additional Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Additional Notes
                </label>
                <textarea id="notes" v-model="form.notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    placeholder="Any additional information about your photography services..."></textarea>
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
