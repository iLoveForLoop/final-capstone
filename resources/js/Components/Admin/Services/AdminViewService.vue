<script setup>
import ViewCateringService from '@/Components/Vendor/Catering/ViewCateringService.vue';
import ViewGeneralService from '@/Components/Vendor/General/ViewGeneralService.vue';

const props = defineProps({
    selectedService: Object,
    showViewModal: Boolean
})

const emit = defineEmits(['close'])

console.log('Selected', props.selectedService.category.name);

</script>

<template>
    <ViewCateringService v-if="showViewModal && selectedService.category.name === 'Catering'" :service="selectedService"
        :catering-service="selectedService.catering_service" @close="emit('close')" />
    <ViewGeneralService v-if="showViewModal && selectedService.category.name !== 'Catering'" :service="selectedService"
        @close="emit('close')" />
</template>

<style></style>
