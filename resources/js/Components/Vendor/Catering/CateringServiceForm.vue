<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';
import {
    Upload, X, Plus, Image as ImageIcon, Eye,
    Tag, FileText, CheckCircle, Settings, Circle,
    Loader, Edit3, Utensils, Users, MapPin, Package
} from 'lucide-vue-next';
import { push } from 'notivue';

const emit = defineEmits(['close', 'created']);
const selectedImages = ref([]);
const newSpecification = ref('');

const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
    category_id: {
        type: [String, Number],
        default: null
    },
});

const form = useForm({
    service_category_id: props.category_id,
    name: '',
    description: '',
    price: '',
    max_price: '',
    cover_images: [],
    min_pax: 50,
    max_pax: 100,
    package_price: '',
    service_area: [],
    is_customizable: false,
    delivery_fee: '',
    specifications: [],
    dishes: {},
    dish_selection_limits: {},
    notes: '',
    _method: 'POST'
});

const commonSpecifications = [
    '2 Main Dishes',
    '3 Main Dishes',
    'Appetizers',
    'Salad Bar',
    'Dessert Bar',
    'Drinks Included',
    'Table Setup',
    'Service Staff',
];

const commonServiceAreas = [
    'Tubigon',
    'Calape',
    'Anywhere in Bohol'
];

const handleImageUpload = (event) => {
    const files = Array.from(event.target.files);

    files.forEach(file => {
        if (file && file.type.startsWith('image/')) {
            if (selectedImages.value.length >= 5) {
                push.warning('Maximum 5 images allowed');
                return;
            }

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
    form.post(route('vendor.catering-services.store'), {
        preserveScroll: true,
        onSuccess: () => {
            push.success('Catering service created successfully');
            form.reset();
            selectedImages.value = [];
            emit('close');
            emit('created');
        },
        onError: () => {
            push.error('Failed to create catering service');
        }
    });
};

// Reactive variables for dish management
const newCategory = ref('');
const newDish = ref('');
const selectedCategory = ref('');
const categorySelectionLimit = ref(1);

// Functions for dish management
const addCategory = () => {
    if (newCategory.value.trim() && !form.dishes.hasOwnProperty(newCategory.value.trim())) {
        const categoryName = newCategory.value.trim();
        form.dishes[categoryName] = [];
        form.dish_selection_limits[categoryName] = categorySelectionLimit.value;
        selectedCategory.value = categoryName;
        newCategory.value = '';
        categorySelectionLimit.value = 1;
    }
};

const addDish = () => {
    if (newDish.value.trim() && selectedCategory.value) {
        const dishName = newDish.value.trim();
        if (!form.dishes[selectedCategory.value].includes(dishName)) {
            form.dishes[selectedCategory.value].push(dishName);
            newDish.value = '';
        }
    }
};

const removeDish = (category, index) => {
    form.dishes[category].splice(index, 1);
    if (form.dishes[category].length === 0) {
        delete form.dishes[category];
        delete form.dish_selection_limits[category];
        if (selectedCategory.value === category) {
            selectedCategory.value = '';
        }
    }
};

const removeCategory = (category) => {
    delete form.dishes[category];
    delete form.dish_selection_limits[category];
    if (selectedCategory.value === category) {
        selectedCategory.value = '';
    }
};

const updateSelectionLimit = (category, limit) => {
    form.dish_selection_limits[category] = Math.max(1, limit);
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

                    <!-- Service Images Section -->
                    <div class="md:col-span-2">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                            <ImageIcon class="w-4 h-4 text-gray-500" />
                            Service Images
                        </label>

                        <!-- Image Upload Area -->
                        <div class="mb-6">
                            <label class="cursor-pointer">
                                <div
                                    class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition-colors duration-200 bg-gray-50 hover:bg-gray-100">
                                    <Upload class="mx-auto h-12 w-12 text-gray-400 mb-3" />
                                    <div class="text-sm text-gray-600">
                                        <span class="font-medium text-blue-600">Click to upload</span>
                                        or drag and drop
                                    </div>
                                    <p class="text-xs text-gray-500 mt-2">
                                        PNG, JPG, JPEG up to 2MB each (Max 5 images)
                                    </p>
                                </div>
                                <input type="file" class="sr-only" @change="handleImageUpload" accept="image/*" multiple
                                    :disabled="selectedImages.length >= 5">
                            </label>
                        </div>

                        <!-- Selected Images Grid -->
                        <div v-if="selectedImages.length > 0">
                            <h5 class="text-sm font-medium text-gray-600 mb-3">Selected Images</h5>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                                <div v-for="(image, index) in selectedImages" :key="image.id"
                                    class="relative group rounded-lg overflow-hidden bg-gray-100 aspect-square">
                                    <img :src="image.preview" :alt="`Service image ${index + 1}`"
                                        class="w-full h-full object-cover">

                                    <!-- Image overlay -->
                                    <div
                                        class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-40 transition-all duration-200 flex items-center justify-center">
                                        <div
                                            class="opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex gap-2">
                                            <!-- Preview button -->
                                            <button type="button"
                                                class="p-2 bg-white rounded-lg text-gray-700 hover:text-blue-600 transition-colors"
                                                @click="$refs[`imageModal${index}`].showModal()">
                                                <Eye class="w-4 h-4" />
                                            </button>
                                            <!-- Remove button -->
                                            <button type="button" @click="removeImage(index)"
                                                class="p-2 bg-white rounded-lg text-red-600 hover:text-red-700 transition-colors">
                                                <X class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Primary image badge -->
                                    <div v-if="index === 0"
                                        class="absolute top-2 left-2 bg-blue-600 text-white text-xs px-2 py-1 rounded-lg">
                                        Primary
                                    </div>
                                </div>

                                <!-- Add more images button -->
                                <div v-if="selectedImages.length < 5"
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
                            <span>{{ selectedImages.length }} of 5 images selected</span>
                            <span v-if="selectedImages.length > 0" class="text-xs">
                                First image will be used as the primary cover
                            </span>
                        </div>

                        <p v-if="form.errors.cover_images" class="mt-2 text-sm text-red-600">
                            {{ form.errors.cover_images }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Menu Selection Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Utensils class="w-5 h-5 text-purple-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Menu Selection</h4>
                </div>

                <!-- Customizable Toggle -->
                <div class="mb-8">
                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                        <div class="flex items-center gap-3">
                            <input id="is_customizable" v-model="form.is_customizable" type="checkbox"
                                class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded transition-colors">
                            <label for="is_customizable" class="block text-sm font-medium text-gray-700">
                                Customizable Menu Options
                            </label>
                        </div>
                        <p class="text-xs text-gray-500">
                            When enabled, customers can select specific dishes from each category during booking.
                        </p>
                    </div>
                </div>

                <!-- Display Selected Categories and Dishes -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                        <Package class="w-4 h-4 text-gray-500" />
                        Selected Menu
                    </label>

                    <div v-if="Object.keys(form.dishes).length > 0" class="space-y-4">
                        <div v-for="(dishes, category) in form.dishes" :key="category"
                            class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-3">
                                    <h5 class="text-md font-medium text-gray-800">{{ category }}</h5>
                                    <span v-if="form.is_customizable"
                                        class="inline-flex items-center px-2 py-1 rounded-lg text-xs font-medium bg-blue-100 text-blue-800">
                                        Max {{ form.dish_selection_limits[category] || 1 }} selectable
                                    </span>
                                </div>
                                <button @click="removeCategory(category)"
                                    class="text-red-600 hover:text-red-800 transition-colors">
                                    <X class="h-4 w-4" />
                                </button>
                            </div>

                            <!-- Selection Limit Input -->
                            <div v-if="form.is_customizable" class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Maximum dishes customers can select from this category:
                                </label>
                                <input type="number" :value="form.dish_selection_limits[category] || 1"
                                    @input="updateSelectionLimit(category, parseInt($event.target.value))" min="1"
                                    :max="dishes.length"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <span class="ml-2 text-xs text-gray-500">out of {{ dishes.length }} available
                                    dishes</span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span v-for="(dish, index) in dishes" :key="index"
                                    class="inline-flex items-center gap-2 px-3 py-2 rounded-lg text-sm font-medium bg-green-50 text-green-800 border border-green-200">
                                    <CheckCircle class="w-4 h-4" />
                                    {{ dish }}
                                    <button @click.stop="removeDish(category, index)"
                                        class="ml-1 text-green-600 hover:text-green-800 transition-colors">
                                        <X class="h-4 w-4" />
                                    </button>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div v-else class="text-sm text-gray-500 mb-4">
                        No categories or dishes added yet.
                    </div>
                </div>

                <div class="space-y-6">
                    <!-- Add New Category -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h5 class="text-md font-medium text-gray-800 mb-3">Add New Category</h5>
                        <div class="space-y-3">
                            <div class="flex gap-3">
                                <input v-model="newCategory" type="text"
                                    placeholder="Enter category name (e.g., Appetizers, Main Course)"
                                    class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    @keyup.enter="addCategory">
                                <button @click="addCategory"
                                    class="px-6 py-3 bg-gray-900 text-white rounded-lg font-medium hover:bg-gray-800 transition-colors flex items-center gap-2">
                                    <Plus class="w-4 h-4" />
                                    Add
                                </button>
                            </div>

                            <!-- Selection limit for new category -->
                            <div v-if="form.is_customizable" class="flex items-center gap-3">
                                <label class="text-sm text-gray-600">
                                    Max selectable dishes for this category:
                                </label>
                                <input v-model="categorySelectionLimit" type="number" min="1"
                                    class="w-20 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                    </div>

                    <!-- Select Category to Add Dishes -->
                    <div v-if="Object.keys(form.dishes).length > 0">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Select Category to Add Dishes
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mb-4">
                            <button v-for="category in Object.keys(form.dishes)" :key="category"
                                @click="selectedCategory = category" :class="{
                                    'bg-blue-100 text-blue-800 border-blue-300': selectedCategory === category,
                                    'bg-white text-gray-700 border-gray-300': selectedCategory !== category
                                }"
                                class="px-4 py-3 border rounded-lg text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                {{ category }}
                                <span v-if="form.is_customizable" class="ml-1 text-xs opacity-75">
                                    ({{ form.dish_selection_limits[category] || 1 }})
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Add Dish to Selected Category -->
                    <div v-if="selectedCategory">
                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-2">
                            <Utensils class="w-4 h-4 text-gray-500" />
                            Add Dish to "{{ selectedCategory }}"
                        </label>
                        <div class="flex gap-3">
                            <input v-model="newDish" type="text" placeholder="Enter dish name"
                                class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                @keyup.enter="addDish">
                            <button @click="addDish"
                                class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition-colors flex items-center gap-2">
                                <Plus class="w-4 h-4" />
                                Add Dish
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Circle class="w-5 h-5 text-green-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Pricing</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Base Price -->
                    <div>
                        <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                            Per Pax Price
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600 font-medium">₱</span>
                            <input type="number" id="price" v-model="form.price" min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="0">
                        </div>
                        <p v-if="form.errors.price" class="mt-2 text-sm text-red-600">
                            {{ form.errors.price }}
                        </p>
                    </div>

                    <!-- Package Price -->
                    <div>
                        <label for="package_price" class="block text-sm font-medium text-gray-700 mb-2">
                            Package Price
                        </label>
                        <div class="relative">
                            <span
                                class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-600 font-medium">₱</span>
                            <input type="number" id="package_price" v-model="form.package_price" min="0"
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                placeholder="0">
                        </div>
                        <p v-if="form.errors.package_price" class="mt-2 text-sm text-red-600">
                            {{ form.errors.package_price }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Capacity Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Users class="w-5 h-5 text-orange-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Capacity</h4>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Min Pax -->
                    <div>
                        <label for="min_pax" class="block text-sm font-medium text-gray-700 mb-2">
                            Minimum Pax *
                        </label>
                        <input type="number" id="min_pax" v-model="form.min_pax" required min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p v-if="form.errors.min_pax" class="mt-2 text-sm text-red-600">
                            {{ form.errors.min_pax }}
                        </p>
                    </div>

                    <!-- Max Pax -->
                    <div>
                        <label for="max_pax" class="block text-sm font-medium text-gray-700 mb-2">
                            Maximum Pax *
                        </label>
                        <input type="number" id="max_pax" v-model="form.max_pax" required min="1"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                        <p v-if="form.errors.max_pax" class="mt-2 text-sm text-red-600">
                            {{ form.errors.max_pax }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Service Options Section -->
            <div class="border-b border-gray-200 pb-8">
                <div class="flex items-center gap-3 mb-6">
                    <Settings class="w-5 h-5 text-indigo-600" />
                    <h4 class="text-lg font-semibold text-gray-900">Service Options</h4>
                </div>

                <!-- Service Area -->
                <div class="mb-6">
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                        <MapPin class="w-4 h-4 text-gray-500" />
                        Service Areas *
                    </label>
                    <div class="flex flex-wrap gap-2">
                        <button v-for="area in commonServiceAreas" :key="area" @click="toggleServiceArea(area)"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors border" :class="{
                                'bg-blue-100 text-blue-800 border-blue-200': form.service_area.includes(area),
                                'bg-gray-100 text-gray-800 border-gray-200 hover:bg-gray-200': !form.service_area.includes(area)
                            }">
                            {{ area }}
                        </button>
                    </div>
                    <p v-if="form.errors.service_area" class="mt-2 text-sm text-red-600">
                        {{ form.errors.service_area }}
                    </p>
                </div>

                <!-- Specifications -->
                <div>
                    <label class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-4">
                        <CheckCircle class="w-4 h-4 text-gray-500" />
                        Specifications
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

            <!-- Additional Notes -->
            <div>
                <label for="notes" class="flex items-center gap-2 text-sm font-medium text-gray-700 mb-3">
                    <FileText class="w-4 h-4 text-gray-500" />
                    Additional Notes
                </label>
                <textarea id="notes" v-model="form.notes" rows="4"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
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
            <span>{{ form.processing ? 'Creating...' : 'Create Service' }}</span>
        </button>
    </div>
</template>
