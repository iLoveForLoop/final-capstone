<script setup>
import AIChatbot from '@/Components/AIChatbot.vue';
import {
    Notivue,
    Notification,
    // FIX: This must be NotificationProgress
    NotificationProgress,
    pastelTheme
} from 'notivue';
import 'notivue/notification-progress.css';
</script>

<template>
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
