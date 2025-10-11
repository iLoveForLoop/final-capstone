<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import AIChatbot from '@/Components/AIChatbot.vue';
</script>

<template>
    <!-- Navigation -->
    <ClientNavbar class="relative z-20" />
    <AIChatbot />
    <main>
        <slot />
    </main>
</template>

<style></style>
