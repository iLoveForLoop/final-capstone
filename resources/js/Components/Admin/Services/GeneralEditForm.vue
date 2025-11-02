<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import { Upload, X, Plus, Image as ImageIcon, Eye, Lightbulb } from 'lucide-vue-next';
import { getCommonSpecifications } from '@/utils/getCommonSpecifications';

const props = defineProps({
    service: {
        type: Object,
        required: true,
    },
    category_id: {
        type: [String, Number],
    },
    selectedCategory: String
});

console.log(props.service)

const toast = useToast();
const emit = defineEmits(['close', 'updated']);
const show = ref(false);
const selectedImages = ref([]);
const existingImages = ref([]);
const imagesToDelete = ref([]);
const newSpecification = ref('');

console.log('service data: ', props.service);
console.log('cat id: ', props.category_id);

const form = useForm({
    service_category_id: props.category_id || props.service.service_category_id,
    name: props.service.name || '',
    description: props.service.description || '',
    price: props.service.price || '',
    max_price: props.service.max_price || '',
    cover_images: [],
    notes: props.service.notes || '',
    specifications: props.service.specifications || [],
    equipment_available: props.service.equipment_available || [],
    power_requirements: props.service.power_requirements || '',
    setup_time_hours: props.service.setup_time_hours || 2,

    // For handling existing images
    delete_images: [],
    _method: 'PUT'
});

const commonSpecifications = ref([]);

// Watch for category changes to update specifications
watch(
    [() => props.category_id, () => props.selectedCategory],
    ([newId, newCategory]) => {
        if (newId) form.service_category_id = newId;
        if (newCategory) commonSpecifications.value = getCommonSpecifications(newCategory);
    },
    { immediate: true }
);

// Initialize existing images and form data on component mount
onMounted(() => {
    // Initialize images
    if (props.service.media) {
        existingImages.value = props.service.media.map(media => ({
            id: media.id,
            url: media.original_url,
            is_existing: true,
            is_primary: media.custom_properties?.is_primary || false,
            is_cover: media.custom_properties?.is_cover || false,
            is_portfolio: media.custom_properties?.is_portfolio || false,
        }));
    }

    // Initialize category-specific data
    if (props.selectedCategory) {
        commonSpecifications.value = getCommonSpecifications(props.selectedCategory);
    }
});

// Computed property for total image count
const totalImageCount = computed(() => {
    return existingImages.value.filter(img => !imagesToDelete.value.includes(img.id)).length + selectedImages.value.length;
});

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files);

    files.forEach(file => {
        if (file && file.type.startsWith('image/')) {
            // Check total image limit
            if (totalImageCount.value >= 8) {
                toast.warning('Maximum 8 images allowed');
                return;
            }

            // Check if image already exists
            const existsIndex = selectedImages.value.findIndex(img => img.file.name === file.name && img.file.size === file.size);
            if (existsIndex === -1) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    selectedImages.value.push({
                        file: file,
                        preview: e.target.result,
                        id: Date.now() + Math.random(),
                        is_existing: false
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

const removeNewImage = (index) => {
    selectedImages.value.splice(index, 1);
    updateFormImages();
};

const removeExistingImage = (imageId) => {
    if (!imagesToDelete.value.includes(imageId)) {
        imagesToDelete.value.push(imageId);
        form.delete_images = [...imagesToDelete.value];
    }
};

const restoreExistingImage = (imageId) => {
    const index = imagesToDelete.value.indexOf(imageId);
    if (index > -1) {
        imagesToDelete.value.splice(index, 1);
        form.delete_images = [...imagesToDelete.value];
    }
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

const addCustomSpecification = () => {
    if (newSpecification.value.trim() && !form.specifications.includes(newSpecification.value.trim())) {
        form.specifications = [...form.specifications, newSpecification.value.trim()];
        newSpecification.value = '';
    }
};

const submit = () => {
    console.log('Form data:', {
        ...form,
        delete_images: imagesToDelete.value,
        cover_images: form.cover_images
    });

    form.post(route('admin.services.update', props.service.id), {
        preserveScroll: true,
        onSuccess: () => {
            // toast.success(`${props.selectedCategory} service updated successfully`);
            emit('close');
            emit('updated');
        },
        onError: (errors) => {
            console.log('Errors:', errors);
            toast.error(`Failed to update ${props.selectedCategory} service`);
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

                        <!-- Existing Images Grid -->
                        <div v-if="existingImages.length > 0" class="mb-6">
                            <h5 class="text-sm font-medium text-gray-600 mb-3">Current Images</h5>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                                <div v-for="image in existingImages" :key="`existing-${image.id}`"
                                    class="relative group rounded-lg overflow-hidden bg-gray-100 aspect-square"
                                    :class="{ 'opacity-50': imagesToDelete.includes(image.id) }">
                                    <img :src="image.url" :alt="`Existing portfolio image`"
                                        class="w-full h-full object-cover">

                                    <!-- Image overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                        <div
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                            <!-- Preview button -->
                                            <button type="button"
                                                class="p-2 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors"
                                                @click="$refs[`existingImageModal${image.id}`].showModal()">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <!-- Remove/Restore button -->
                                            <button type="button"
                                                @click="imagesToDelete.includes(image.id) ? restoreExistingImage(image.id) : removeExistingImage(image.id)"
                                                class="p-2 bg-white rounded-full transition-colors" :class="imagesToDelete.includes(image.id)
                                                    ? 'text-green-600 hover:text-green-700'
                                                    : 'text-red-600 hover:text-red-700'">
                                                <Plus v-if="imagesToDelete.includes(image.id)"
                                                    class="w-4 h-4 rotate-45" />
                                                <X v-else class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Primary/Cover image badge -->
                                    <div v-if="image.is_primary || image.is_cover"
                                        class="absolute top-2 left-2 bg-indigo-600 text-white text-xs px-2 py-1 rounded-full">
                                        Cover
                                    </div>

                                    <!-- Marked for deletion overlay -->
                                    <div v-if="imagesToDelete.includes(image.id)"
                                        class="absolute inset-0 bg-red-500 bg-opacity-75 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">Will be deleted</span>
                                    </div>

                                    <!-- Image preview modal -->
                                    <dialog :ref="`existingImageModal${image.id}`"
                                        class="rounded-lg backdrop:bg-black backdrop:bg-opacity-50">
                                        <div class="p-0 max-w-3xl">
                                            <img :src="image.url" :alt="`Existing portfolio image`"
                                                class="w-full h-auto rounded-lg">
                                            <div class="p-4 flex justify-between items-center">
                                                <span class="text-sm text-gray-600">Existing Image</span>
                                                <button @click="$refs[`existingImageModal${image.id}`].close()"
                                                    class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </dialog>
                                </div>
                            </div>
                        </div>

                        <!-- New Images Upload Area -->
                        <div class="mb-4" v-if="totalImageCount < 8">
                            <h5 v-if="existingImages.length > 0" class="text-sm font-medium text-gray-600 mb-3">Add New
                                Images</h5>
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
                                        PNG, JPG, JPEG up to 2MB each (Max 8 images total)
                                    </p>
                                </div>
                                <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*" multiple
                                    :disabled="totalImageCount >= 8">
                            </label>
                        </div>

                        <!-- New Selected Images Grid -->
                        <div v-if="selectedImages.length > 0">
                            <h5 class="text-sm font-medium text-gray-600 mb-3">New Images to Upload</h5>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                                <div v-for="(image, index) in selectedImages" :key="image.id"
                                    class="relative group rounded-lg overflow-hidden bg-gray-100 aspect-square">
                                    <img :src="image.preview" :alt="`New portfolio image ${index + 1}`"
                                        class="w-full h-full object-cover">

                                    <!-- Image overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                        <div
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex space-x-2">
                                            <!-- Preview button -->
                                            <button type="button"
                                                class="p-2 bg-white rounded-full text-gray-700 hover:text-indigo-600 transition-colors"
                                                @click="$refs[`newImageModal${index}`].showModal()">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <!-- Remove button -->
                                            <button type="button" @click="removeNewImage(index)"
                                                class="p-2 bg-white rounded-full text-red-600 hover:text-red-700 transition-colors">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- New image badge -->
                                    <div
                                        class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded-full">
                                        New
                                    </div>

                                    <!-- Image preview modal -->
                                    <dialog :ref="`newImageModal${index}`"
                                        class="rounded-lg backdrop:bg-black backdrop:bg-opacity-50">
                                        <div class="p-0 max-w-3xl">
                                            <img :src="image.preview" :alt="`New portfolio image ${index + 1}`"
                                                class="w-full h-auto rounded-lg">
                                            <div class="p-4 flex justify-between items-center">
                                                <span class="text-sm text-gray-600">New Image {{ index + 1 }}</span>
                                                <button @click="$refs[`newImageModal${index}`].close()"
                                                    class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                                                    Close
                                                </button>
                                            </div>
                                        </div>
                                    </dialog>
                                </div>

                                <!-- Add more images button -->
                                <div v-if="totalImageCount < 8"
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
                        </div>

                        <!-- Images count and limit info -->
                        <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                            <span>{{ totalImageCount }} of 8 images
                                <span v-if="imagesToDelete.length > 0" class="text-red-600">({{ imagesToDelete.length }}
                                    marked for deletion)</span>
                            </span>
                            <span v-if="existingImages.length > 0 && !imagesToDelete.includes(existingImages[0]?.id)"
                                class="text-xs">
                                First remaining image will be the cover photo
                            </span>
                        </div>

                        <p v-if="form.errors.cover_images" class="mt-1 text-sm text-red-600">
                            {{ form.errors.cover_images }}</p>
                    </div>
                </div>
            </div>

            <!-- Service Details Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">{{ selectedCategory }} Details</h4>

                <!-- Dynamic fields based on category -->
                <template v-if="selectedCategory === 'Lights & Effects'">
                    <!-- Setup Time -->
                    <div class="mb-6">
                        <label for="setup_time_hours" class="block text-sm font-medium text-gray-700 mb-1">
                            Setup Time (Hours)
                        </label>
                        <input type="number" id="setup_time_hours" v-model="form.setup_time_hours" min="1" max="24"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">
                            Estimated time required for setup
                        </p>
                        <p v-if="form.errors.setup_time_hours" class="mt-1 text-sm text-red-600">
                            {{ form.errors.setup_time_hours }}</p>
                    </div>

                    <!-- Power Requirements -->
                    <div class="mb-6">
                        <label for="power_requirements" class="block text-sm font-medium text-gray-700 mb-1">
                            Power Requirements
                        </label>
                        <input type="text" id="power_requirements" v-model="form.power_requirements"
                            placeholder="e.g., 220V, 3-phase, dedicated circuit"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="mt-1 text-xs text-gray-500">
                            Specify power requirements for your equipment
                        </p>
                        <p v-if="form.errors.power_requirements" class="mt-1 text-sm text-red-600">
                            {{ form.errors.power_requirements }}</p>
                    </div>
                </template>

                <!-- Add more category-specific fields here as needed -->

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

            <!-- Additional Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                    Additional Notes
                </label>
                <textarea id="notes" v-model="form.notes" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                    :placeholder="`Any additional information about your ${selectedCategory} services...`"></textarea>
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
                Updating...
            </span>
            <span v-else>
                Update Service
            </span>
        </button>
        <button type="button" @click="emit('close')"
            class="mt-3 sm:mt-0 w-full sm:w-auto px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            Cancel
        </button>
    </div>
</template>

<style></style>
