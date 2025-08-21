<script setup>
import { onMounted, ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();
const emit = defineEmits(['close', 'created']);
const show = ref(false);
const selectedImage = ref(null);
const newSpecification = ref('');
const dishSearch = ref('');

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
    cover_image: null,
    min_pax: 50,
    max_pax: 100,
    package_price: '',
    service_area: [],
    is_customizable: false,
    delivery_fee: '',
    specifications: [],
    dishes: {},
    dish_selection_limits: {}, // Separate object for selection limits
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

const submit = () => {
    console.log('dishes:', form.dishes);
    console.log('dish_selection_limits:', form.dish_selection_limits);
    console.log('specifications:', form.specifications);
    console.log('service_area:', form.service_area);

    form.post(route('vendor.catering-services.store'), {
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
    // Remove category if it becomes empty
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

            <!-- Dishes Section -->
            <div class="border-b border-gray-200 pb-6">
                <!-- Customizable -->
                <div class="mb-6">
                    <div class="flex items-center">
                        <input id="is_customizable" v-model="form.is_customizable" type="checkbox"
                            class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        <label for="is_customizable" class="ml-2 block text-sm text-gray-700">
                            Customizable Menu Options
                        </label>
                    </div>
                    <p class="mt-1 text-xs text-gray-500">
                        When enabled, customers can select specific dishes from each category during booking.
                    </p>
                </div>

                <h4 class="text-lg font-medium text-gray-900 mb-4">Menu Selection</h4>

                <!-- Display Selected Categories and Dishes -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Selected Menu
                    </label>

                    <div v-if="Object.keys(form.dishes).length > 0" class="space-y-4">
                        <div v-for="(dishes, category) in form.dishes" :key="category"
                            class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center space-x-3">
                                    <h5 class="text-md font-medium text-gray-800">{{ category }}</h5>
                                    <span v-if="form.is_customizable"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Max {{ form.dish_selection_limits[category] || 1 }} selectable
                                    </span>
                                </div>
                                <button @click="removeCategory(category)"
                                    class="text-red-600 hover:text-red-800 focus:outline-none">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Selection Limit Input (only show if customizable) -->
                            <div v-if="form.is_customizable" class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1">
                                    Maximum dishes customers can select from this category:
                                </label>
                                <input type="number" :value="form.dish_selection_limits[category] || 1"
                                    @input="updateSelectionLimit(category, parseInt($event.target.value))" min="1"
                                    :max="dishes.length"
                                    class="w-20 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <span class="ml-2 text-xs text-gray-500">out of {{ dishes.length }} available
                                    dishes</span>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <span v-for="(dish, index) in dishes" :key="index"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                    {{ dish }}
                                    <button @click.stop="removeDish(category, index)"
                                        class="ml-1.5 inline-flex text-green-600 focus:outline-none">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                clip-rule="evenodd" />
                                        </svg>
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
                            <div class="flex gap-2">
                                <input v-model="newCategory" type="text"
                                    placeholder="Enter category name (e.g., Appetizers, Main Course)"
                                    class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                    @keyup.enter="addCategory">
                                <button @click="addCategory"
                                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Add Category
                                </button>
                            </div>

                            <!-- Selection limit for new category (only show if customizable) -->
                            <div v-if="form.is_customizable" class="flex items-center space-x-3">
                                <label class="text-sm text-gray-600">
                                    Max selectable dishes for this category:
                                </label>
                                <input v-model="categorySelectionLimit" type="number" min="1"
                                    class="w-16 px-2 py-1 border border-gray-300 rounded text-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
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
                                    'bg-indigo-100 text-indigo-800 border-indigo-300': selectedCategory === category,
                                    'bg-white text-gray-700 border-gray-300': selectedCategory !== category
                                }"
                                class="px-3 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                {{ category }}
                                <span v-if="form.is_customizable" class="ml-1 text-xs opacity-75">
                                    ({{ form.dish_selection_limits[category] || 1 }})
                                </span>
                            </button>
                        </div>
                    </div>

                    <!-- Add Dish to Selected Category -->
                    <div v-if="selectedCategory">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Add Dish to "{{ selectedCategory }}"
                        </label>
                        <div class="flex gap-2">
                            <input v-model="newDish" type="text" placeholder="Enter dish name"
                                class="flex-1 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                                @keyup.enter="addDish">
                            <button @click="addDish"
                                class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                Add Dish
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
                            Per Pax Price (if per pax)
                        </label>
                        <input type="number" id="price" v-model="form.price" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="₱">
                        <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.price }}</p>
                    </div>

                    <!-- Package Price -->
                    <div>
                        <label for="package_price" class="block text-sm font-medium text-gray-700 mb-1">
                            Package Price (if package)
                        </label>
                        <input type="number" id="package_price" v-model="form.package_price" min="0"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                            placeholder="₱">
                        <p v-if="form.errors.package_price" class="mt-1 text-sm text-red-600">
                            {{ form.errors.package_price }}</p>
                    </div>
                </div>
            </div>

            <!-- Capacity Section -->
            <div class="border-b border-gray-200 pb-6">
                <h4 class="text-lg font-medium text-gray-900 mb-4">Capacity</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Min Pax -->
                    <div>
                        <label for="min_pax" class="block text-sm font-medium text-gray-700 mb-1">
                            Minimum Pax *
                        </label>
                        <input type="number" id="min_pax" v-model="form.min_pax" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.min_pax" class="mt-1 text-sm text-red-600">
                            {{ form.errors.min_pax }}</p>
                    </div>

                    <!-- Max Pax -->
                    <div>
                        <label for="max_pax" class="block text-sm font-medium text-gray-700 mb-1">
                            Maximum Pax *
                        </label>
                        <input type="number" id="max_pax" v-model="form.max_pax" required min="1"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        <p v-if="form.errors.max_pax" class="mt-1 text-sm text-red-600">
                            {{ form.errors.max_pax }}</p>
                    </div>
                </div>
            </div>

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
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
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
