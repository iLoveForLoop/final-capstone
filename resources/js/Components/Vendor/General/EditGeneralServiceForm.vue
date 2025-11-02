<script setup>
import { ref, onMounted, computed, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import {
    Upload, X, Plus, Image as ImageIcon, Eye, Lightbulb,
    Tag, FileText, CheckCircle, Settings, Circle,
    Loader, Edit3, Clock, Zap
} from 'lucide-vue-next';
import { getCommonSpecifications } from '@/utils/getCommonSpecifications';
import { push } from 'notivue';

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

const toast = useToast();
const emit = defineEmits(['close', 'updated']);
const selectedImages = ref([]);
const existingImages = ref([]);
const imagesToDelete = ref([]);
const newSpecification = ref('');

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
            if (totalImageCount.value >= 8) {
                toast.warning('Maximum 8 images allowed');
                return;
            }

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
    form.post(route('vendor.services.update', props.service.id), {
        preserveScroll: true,
        onSuccess: () => {
            push.success(`${props.selectedCategory} service updated successfully`);
            emit('close');
            emit('updated');
        },
        onError: (errors) => {
            push.error(`Failed to update ${props.selectedCategory} service`);
        }
    });
};
</script>

<template>
    <div class="px-6 py-4">
        <div class="space-y-8">
            <!-- Basic Information Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Edit3 class="w-5 h-5 text-blue-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Basic Information</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Service Name -->
                    <div>
                        <label for="name" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <Tag class="w-4 h-4 text-gray-500" />
                            Service Name *
                        </label>
                        <input type="text" id="name" v-model="form.name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">
                            {{ form.errors.name }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label for="description" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <FileText class="w-4 h-4 text-gray-500" />
                            Description
                        </label>
                        <textarea id="description" v-model="form.description" rows="4"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                        <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">
                            {{ form.errors.description }}
                        </p>
                    </div>

                    <!-- Portfolio Images Section -->
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                            <ImageIcon class="w-4 h-4 text-gray-500" />
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
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                            <!-- Preview button -->
                                            <button type="button"
                                                class="p-2 bg-white rounded-lg text-gray-700 hover:text-blue-600 transition-colors"
                                                @click="$refs[`existingImageModal${image.id}`].showModal()">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <!-- Remove/Restore button -->
                                            <button type="button"
                                                @click="imagesToDelete.includes(image.id) ? restoreExistingImage(image.id) : removeExistingImage(image.id)"
                                                class="p-2 bg-white rounded-lg transition-colors" :class="imagesToDelete.includes(image.id)
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
                                        class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-lg">
                                        Cover
                                    </div>

                                    <!-- Marked for deletion overlay -->
                                    <div v-if="imagesToDelete.includes(image.id)"
                                        class="absolute inset-0 bg-red-500 bg-opacity-75 flex items-center justify-center">
                                        <span class="text-white text-sm font-medium">Will be deleted</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- New Images Upload Area -->
                        <div class="mb-4" v-if="totalImageCount < 8">
                            <h5 v-if="existingImages.length > 0" class="text-sm font-medium text-gray-600 mb-3">Add New
                                Images</h5>
                            <label class="cursor-pointer">
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-gray-100">
                                    <Upload class="mx-auto h-12 w-12 text-gray-400 mb-3" />
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-600">Click to upload</span>
                                        or drag and drop
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
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
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                            <!-- Preview button -->
                                            <button type="button"
                                                class="p-2 bg-white rounded-lg text-gray-700 hover:text-blue-600 transition-colors"
                                                @click="$refs[`newImageModal${index}`].showModal()">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <!-- Remove button -->
                                            <button type="button" @click="removeNewImage(index)"
                                                class="p-2 bg-white rounded-lg text-red-600 hover:text-red-700 transition-colors">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- New image badge -->
                                    <div
                                        class="absolute top-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded-lg">
                                        New
                                    </div>
                                </div>

                                <!-- Add more images button -->
                                <div v-if="totalImageCount < 8"
                                    class="relative group rounded-lg border-2 border-dashed border-gray-300 aspect-square flex items-center justify-center hover:border-blue-400 transition-colors">
                                    <label class="cursor-pointer w-full h-full flex items-center justify-center">
                                        <div class="text-center">
                                            <Plus class="mx-auto h-8 w-8 text-gray-400 group-hover:text-blue-500" />
                                            <span class="text-xs text-gray-500 group-hover:text-blue-600 mt-1">Add
                                                More</span>
                                        </div>
                                        <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*"
                                            multiple>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Images count and limit info -->
                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                            <span>{{ totalImageCount }} of 8 images
                                <span v-if="imagesToDelete.length > 0" class="text-red-600">({{ imagesToDelete.length }}
                                    marked for deletion)</span>
                            </span>
                            <span v-if="existingImages.length > 0 && !imagesToDelete.includes(existingImages[0]?.id)"
                                class="text-xs">
                                First remaining image will be the cover photo
                            </span>
                        </div>

                        <p v-if="form.errors.cover_images" class="mt-2 text-sm text-red-600">
                            {{ form.errors.cover_images }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Service Details Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Lightbulb class="w-5 h-5 text-amber-600" />
                    <h4 class="text-lg font-semibold text-gray-900">{{ selectedCategory }} Details</h4>
                </div>

                <!-- Dynamic fields based on category -->
                <template v-if="selectedCategory === 'Lights & Effects'">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- Setup Time -->
                        <div>
                            <label for="setup_time_hours"
                                class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Clock class="w-4 h-4 text-gray-500" />
                                Setup Time (Hours)
                            </label>
                            <input type="number" id="setup_time_hours" v-model="form.setup_time_hours" min="1" max="24"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <p class="mt-2 text-xs text-gray-500">
                                Estimated time required for setup
                            </p>
                            <p v-if="form.errors.setup_time_hours" class="mt-2 text-sm text-red-600">
                                {{ form.errors.setup_time_hours }}
                            </p>
                        </div>

                        <!-- Power Requirements -->
                        <div>
                            <label for="power_requirements"
                                class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                                <Zap class="w-4 h-4 text-gray-500" />
                                Power Requirements
                            </label>
                            <input type="text" id="power_requirements" v-model="form.power_requirements"
                                placeholder="e.g., 220V, 3-phase, dedicated circuit"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <p class="mt-2 text-xs text-gray-500">
                                Specify power requirements for your equipment
                            </p>
                            <p v-if="form.errors.power_requirements" class="mt-2 text-sm text-red-600">
                                {{ form.errors.power_requirements }}
                            </p>
                        </div>
                    </div>
                </template>

                <!-- Specifications -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                        <CheckCircle class="w-4 h-4 text-gray-500" />
                        Service Specifications
                    </label>

                    <!-- Custom specification input -->
                    <div class="flex gap-3 mb-4">
                        <input v-model="newSpecification" type="text" placeholder="Enter custom specification"
                            class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                            @keyup.enter="addCustomSpecification">
                        <button @click="addCustomSpecification"
                            class="px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center gap-2">
                            <Plus class="w-4 h-4" />
                            Add
                        </button>
                    </div>

                    <!-- Common specifications quick add -->
                    <div class="mb-4">
                        <p class="text-xs text-gray-500 mb-3">Quick add common specifications:</p>
                        <div class="flex flex-wrap gap-2">
                            <button v-for="spec in commonSpecifications" :key="spec" @click="addSpecification(spec)"
                                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border" :class="{
                                    'bg-blue-100 text-blue-800 border-blue-200': form.specifications.includes(spec),
                                    'bg-gray-100 text-gray-800 border-gray-200 hover:bg-gray-200': !form.specifications.includes(spec)
                                }">
                                {{ spec }}
                            </button>
                        </div>
                    </div>

                    <!-- Selected specifications -->
                    <div v-if="form.specifications.length > 0" class="mt-6">
                        <p class="text-xs text-gray-500 mb-3">Selected specifications:</p>
                        <div class="flex flex-wrap gap-2">
                            <span v-for="(spec, index) in form.specifications" :key="index"
                                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-800 border border-green-200">
                                <CheckCircle class="w-4 h-4" />
                                {{ spec }}
                                <button @click.stop="removeSpecification(index)"
                                    class="ml-1 text-green-600 hover:text-green-800 transition-colors">
                                    <X class="h-4 w-4" />
                                </button>
                            </span>
                        </div>
                    </div>
                    <p v-if="form.errors.specifications" class="mt-2 text-sm text-red-600">
                        {{ form.errors.specifications }}
                    </p>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Circle class="w-5 h-5 text-green-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Pricing</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Price *
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600 font-medium">₱</span>
                            <input type="number" id="price" v-model="form.price" required min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="0">
                        </div>
                        <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">
                            {{ form.errors.price }}
                        </p>
                    </div>

                    <!-- Max Price -->
                    <div>
                        <label for="max_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Price (Optional)
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600 font-medium">₱</span>
                            <input type="number" id="max_price" v-model="form.max_price" min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="0">
                        </div>
                        <p class="mt-2 text-xs text-gray-500">
                            For packages with variable pricing
                        </p>
                        <p v-if="form.errors.max_price" class="mt-2 text-sm text-red-600">
                            {{ form.errors.max_price }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Additional Notes -->
            <div>
                <label for="notes" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-3">
                    <FileText class="w-4 h-4 text-gray-500" />
                    Additional Notes
                </label>
                <textarea id="notes" v-model="form.notes" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                    :placeholder="`Any additional information about your ${selectedCategory} services...`"></textarea>
                <p v-if="form.errors.notes" class="mt-2 text-sm text-red-600">
                    {{ form.errors.notes }}
                </p>
            </div>
        </div>
    </div>

    <!-- Modal footer -->
    <div class="bg-gray-50 px-6 py-4 flex flex-col sm:flex-row gap-3 sm:justify-end border-t border-gray-200">
        <button type="button" @click="emit('close')"
            class="px-6 py-3 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
            Cancel
        </button>
        <button type="button" @click="submit" :disabled="form.processing"
            class="px-6 py-3 bg-gray-900 text-white rounded-lg text-sm font-medium transition-colors hover:bg-gray-800 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-2">
            <Loader v-if="form.processing" class="w-4 h-4 animate-spin" />
            <span>{{ form.processing ? 'Updating...' : 'Update Service' }}</span>
        </button>
    </div>
</template>
