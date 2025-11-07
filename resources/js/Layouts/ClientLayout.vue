<script setup>
import ClientNavbar from '@/Components/ClientNavbar.vue';
import AIChatbot from '@/Components/AIChatbot.vue';
import {
    Notivue,
    Notification,
    // FIX: This must be NotificationProgress
    NotificationProgress,
    pastelTheme
} from 'notivue';
import 'notivue/notification-progress.css';
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
</script>

<template>
    <ConfirmDialog />
    <!-- Navigation -->
    <ClientNavbar class="relative z-20" />
    <AIChatbot />
    <!-- Notivue container -->
    <Notivue v-slot="item">
        <Notification :item="item" :theme="pastelTheme">
            <NotificationProgress :item="item" />

        </Notification>
    </Notivue>
    <main>
        <slot />
    </main>
</template>

<style></style>
