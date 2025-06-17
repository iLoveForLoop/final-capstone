<script setup>

defineProps({
    services: {
        type: Array
    }
})
</script>

<template>

    <div class="flex flex-col items-center justify-center bg-blue-300 p-10">
        <ul>
            <li v-for="service in services" :key="service.id">{{ service.name }} - {{ service.price }}</li>
        </ul>
    </div>

</template>

<style></style>
