<template>
    <div>
        <CateringEditForm v-if="selectedCategory === 'Catering'" :service="service" @close="emit('close')"
            @updated="emit('updated')" :cateringService="service.catering_service" />

        <GeneralEditForm v-else :service="service" :category_id="category_id" :selectedCategory="selectedCategory"
            @close="emit('close')" @updated="emit('updated')" />
    </div>
</template>

<script setup>
import { computed } from 'vue'
import CateringEditForm from './CateringEditForm.vue';
import GeneralEditForm from './GeneralEditForm.vue';


const props = defineProps({
    service: Object,
    category_id: [String, Number],
    selectedCategory: String
})

const emit = defineEmits(['close', 'updated'])



</script>
