<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toastification';

const toast = useToast();

const show = ref(false);
const selected_img = ref(null);

const form = useForm({
    name: '',
    description: '',
    dish_image: null,
    dietary_type: '',
    course_type: '',
    is_available: true,
});

const dietaryTypes = [
    { value: '', label: 'None' },
    { value: 'vegetarian', label: 'Vegetarian' },
    { value: 'vegan', label: 'Vegan' },
    { value: 'halal', label: 'Halal' },
    { value: 'kosher', label: 'Kosher' },
    { value: 'gluten-free', label: 'Gluten Free' },
];

const courseTypes = [
    { value: '', label: 'None' },
    { value: 'appetizer', label: 'Appetizer' },
    { value: 'main', label: 'Main Course' },
    { value: 'dessert', label: 'Dessert' },
    { value: 'side', label: 'Side Dish' },
    { value: 'beverage', label: 'Beverage' },
];

const handleImageUpload = (event) => {
    const file = event.target.files[0];
    form.dish_image = file
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            selected_img.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('vendor.dishes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            toast.success('Dish created successfully');
            form.reset();
            show.value = false
            selected_img.value = null

        },
        onError: () => {
            toast.error('Failed to create dish');
        }
    });
};

const showModal = () => {
    show.value = true;
};

defineExpose({
    show: showModal
});
</script>

<template>
    <Transition enter-active-class="ease-out duration-300" enter-from-class="opacity-0" enter-to-class="opacity-100"
        leave-active-class="ease-in duration-200" leave-from-class="opacity-100" leave-to-class="opacity-0">
        <div v-if="show" class="fixed inset-0 z-50 overflow-y-auto">
            <Transition enter-active-class="ease-out duration-300"
                enter-from-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                enter-to-class="opacity-100 translate-y-0 sm:scale-100" leave-active-class="ease-in duration-200"
                leave-from-class="opacity-100 translate-y-0 sm:scale-100"
                leave-to-class="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="show = false"></div>
                    </div>

                    <!-- This element is to trick the browser into centering the modal contents. -->
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <!-- Modal container -->
                    <div
                        class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                        <!-- Modal header -->
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="flex justify-between items-start">
                                <h3 class="text-lg leading-6 font-medium text-gray-900">
                                    Add New Dish
                                </h3>
                                <button @click="show = false"
                                    class="text-gray-400 hover:text-gray-500 transition-colors duration-150">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Modal body -->
                        <div class="px-4 sm:px-6">
                            <div class="space-y-4">
                                <!-- Dish Name -->
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700">Dish Name
                                        *</label>
                                    <input type="text" id="name" v-model="form.name" required
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out">
                                    <p v-if="form.errors.name" class="mt-2 text-sm text-red-600">{{ form.errors.name }}
                                    </p>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description"
                                        class="block text-sm font-medium text-gray-700">Description</label>
                                    <textarea id="description" v-model="form.description" rows="3"
                                        class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm transition duration-150 ease-in-out"></textarea>
                                    <p v-if="form.errors.description" class="mt-2 text-sm text-red-600">{{
                                        form.errors.description }}
                                    </p>
                                </div>

                                <!-- Image Upload -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Dish Image</label>
                                    <div class="mt-1 flex items-center">
                                        <span v-if="selected_img"
                                            class="inline-block h-16 w-16 rounded-md overflow-hidden transition duration-150 ease-in-out">
                                            <img :src="selected_img" class="h-full w-full object-cover">
                                        </span>
                                        <span v-else
                                            class="inline-block h-16 w-16 rounded-md overflow-hidden bg-gray-100 flex items-center justify-center transition duration-150 ease-in-out">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </span>
                                        <label
                                            class="ml-5 bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 cursor-pointer transition duration-150 ease-in-out">
                                            <span>Upload</span>
                                            <input type="file" class="sr-only" @change="handleImageUpload"
                                                accept="image/*">
                                        </label>
                                    </div>
                                    <p v-if="form.errors.image_url" class="mt-2 text-sm text-red-600">{{
                                        form.errors.image_url }}
                                    </p>
                                </div>

                                <!-- Dietary Type -->
                                <div>
                                    <label for="dietary_type" class="block text-sm font-medium text-gray-700">Dietary
                                        Type</label>
                                    <select id="dietary_type" v-model="form.dietary_type"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md transition duration-150 ease-in-out">
                                        <option v-for="type in dietaryTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Course Type -->
                                <div>
                                    <label for="course_type" class="block text-sm font-medium text-gray-700">Course
                                        Type</label>
                                    <select id="course_type" v-model="form.course_type"
                                        class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md transition duration-150 ease-in-out">
                                        <option v-for="type in courseTypes" :key="type.value" :value="type.value">
                                            {{ type.label }}
                                        </option>
                                    </select>
                                </div>

                                <!-- Availability -->
                                <div class="flex items-center">
                                    <input id="is_available" v-model="form.is_available" type="checkbox"
                                        class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded transition duration-150 ease-in-out">
                                    <label for="is_available" class="ml-2 block text-sm text-gray-700">
                                        Available for order
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Modal footer -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="button" @click="submit" :disabled="form.processing"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50 disabled:cursor-not-allowed transition duration-150 ease-in-out">
                                <span v-if="form.processing">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                                <span v-else>
                                    Save Dish
                                </span>
                            </button>
                            <button type="button" @click="show = false"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition duration-150 ease-in-out">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Additional smooth transitions for interactive elements */
button,
input,
select,
textarea,
label {
    transition: all 0.15s ease;
}

/* Smooth hover transitions for buttons */
button:hover {
    transition: all 0.2s ease;
}

/* Image upload transition */
input[type="file"] {
    transition: all 0.2s ease;
}

/* Focus transitions */
input:focus,
select:focus,
textarea:focus {
    transition: all 0.2s ease;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.2);
}

/* Checkbox toggle transition */
input[type="checkbox"] {
    transition: background-color 0.2s ease, border-color 0.2s ease;
}

/* Modal backdrop transition */
.fixed.inset-0 {
    transition: opacity 0.3s ease;
}

/* Modal content transition */
.transform {
    transition: all 0.3s ease;
}
</style>
