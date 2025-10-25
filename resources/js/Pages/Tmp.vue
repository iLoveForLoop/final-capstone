<script setup>
import axios from 'axios'

const sendOtp = async () => {
    try {
        const res = await axios.post('/email/send-otp', { email: 'jefbay110@gmail.com' })
        console.log('OTP RES:', res.data.message)
    } catch (err) {
        console.error('Error sending OTP:', err)
    }
}
</script>

<template>
    <button class="py-2 px-5 bg-blue-500 rounded" @click="sendOtp">
        Test
    </button>
</template>

<style></style>
