<script setup>
import VendorLayout from '@/Layouts/VendorLayout.vue'
import ServiceForm from '@/Components/Vendor/ServiceForm.vue';
import Services from '@/Components/Vendor/Services.vue'
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

defineProps({
    services: {
        type: Array,
        default: []
    }
})

const isAddingService = ref(false)

const toggle = () => {
    isAddingService.value = !isAddingService.value
}

</script>

<template>



    <VendorLayout>

        <h1>Dashboard</h1>

        <Modal :show="isAddingService" @close="toggle" :closeable="true">
            <ServiceForm @close-form="toggle" />
        </Modal>






    </VendorLayout>

</template>



<style></style>
