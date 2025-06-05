<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ServiceForm from '@/Components/Vendor/ServiceForm.vue';
import Modal from '@/Components/Modal.vue';
import { ref } from 'vue';

const isAddingService = ref(false)

const toggle = () => {
    isAddingService.value = !isAddingService.value
}
</script>

<template>



    <AuthenticatedLayout>
        <!-- <button class=" px-3 py-2 bg-blue-200" @click="toggle" @close="isAddingService = false">Add</button> -->
        <div class="w-full h-full flex items-center justify-center">
            <h1 class="text-white text-4xl">This is the vendor page</h1>
        </div>


        <ServiceForm />





    </AuthenticatedLayout>

</template>



<style></style>
