<script setup>
import { ref, onMounted } from 'vue';
import { useToast } from 'vue-toastification';
// import CateringServiceForm from './Catering/CateringServiceForm.vue';
// import PhotographyServiceForm from './Photograpy/PhotographyServiceForm.vue';

import CateringServiceForm from './Catering/CateringServiceForm.vue';
import PhotographyServiceForm from './Photograpy/PhotographyServiceForm.vue';
import VideographyServiceForm from './Videography/VideographyServiceForm.vue';
import GeneralServiceForm from './General/GeneralServiceForm.vue';



const props = defineProps({
    categories: {
        type: Array,
        default: () => []
    },
});

const selectedCategory = ref()
const selectedCategoryId = ref(props.categories[0].id)

const toast = useToast();
const emit = defineEmits(['close', 'created']);
const show = ref(false);

const handleCategoryChange = (category) => {
    selectedCategory.value = category.name
    selectedCategoryId.value = category.id
}

onMounted(() => {
    if (props.categories.length > 0) {
        selectedCategory.value = props.categories[0].name;
    }
});

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
                <div class="flex items-center justify-center min-h-screen p-4 sm:p-6 text-center">
                    <!-- Background overlay -->
                    <div class="fixed inset-0 transition-opacity" aria-hidden="true">
                        <div class="absolute inset-0 bg-gray-500 opacity-75" @click="show = false"></div>
                    </div>

                    <!-- Modal container -->
                    <div
                        class="inline-block w-full max-w-4xl bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all">
                        <!-- Modal header -->
                        <div class="bg-white px-6 pt-6 pb-2">
                            <div class="flex justify-between items-center">
                                <h3 class="text-xl font-semibold text-gray-900">
                                    Add New {{ selectedCategory }} Service
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

                        <div class="bg-white px-6 pt-6 pb-2">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Select Category
                                </label>

                                <div class="grid grid-cols-2 md:grid-cols-5 gap-2">
                                    <button v-for="category in categories" :key="category.id"
                                        @click="handleCategoryChange(category)" :class="{
                                            'bg-indigo-100 text-indigo-800 border-indigo-300': selectedCategory === category.name,
                                            'bg-white text-gray-700 border-gray-300': selectedCategory !== category.name
                                        }"
                                        class="px-3 py-2 border rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        {{ category.name }}

                                    </button>
                                </div>
                            </div>
                        </div>

                        <CateringServiceForm @close="show = false" v-if="selectedCategory === 'Catering'"
                            :category_id="selectedCategoryId" />

                        <PhotographyServiceForm @close="show = false" v-if="selectedCategory === 'Photography'"
                            :category_id="selectedCategoryId" />

                        <VideographyServiceForm @close="show = false" v-if="selectedCategory === 'Videography'"
                            :category_id="selectedCategoryId" />

                        <GeneralServiceForm @close="show = false" v-else :category_id="selectedCategoryId"
                            :selectedCategory="selectedCategory" />

                    </div>

                </div>
            </Transition>
        </div>
    </Transition>
</template>

<style scoped>
/* Smooth transitions */
button,
input,
select,
textarea,
label {
    transition: all 0.15s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    height: 6px;
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #a1a1a1;
}

/* Animation for dish selection */
@keyframes pulse {
    0% {
        transform: scale(1);
    }

    50% {
        transform: scale(1.05);
    }

    100% {
        transform: scale(1);
    }
}

.dish-selected {
    animation: pulse 0.3s ease;
}
</style>
