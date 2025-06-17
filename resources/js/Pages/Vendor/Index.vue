<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
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



    <AuthenticatedLayout>

        <div class="w-full h-full flex flex-col items-center justify-center ">
            <h1 class="text-white text-4xl">This is the vendor page</h1>
            <button class=" px-3 py-2 bg-blue-200" @click="toggle" @close="isAddingService = false">Add</button>

            <Services :services="services" />

        </div>

        <Modal :show="isAddingService" @close="toggle" :closeable="true">
            <ServiceForm @close-form="toggle" />
        </Modal>






    </AuthenticatedLayout>

</template>



<style></style>
