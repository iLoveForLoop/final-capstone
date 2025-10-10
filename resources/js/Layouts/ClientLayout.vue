<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
</script>

<template>
    <!-- Navigation -->
    <ClientNavbar class="relative z-20" />
    <main>
        <slot />
    </main>
</template>

<style></style>
