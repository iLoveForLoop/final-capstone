<script setup>
import { computed, ref } from 'vue'
// Import shadcn-vue components
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import { router, useForm } from '@inertiajs/vue3'
import VendorReportDialog from './VendorReportDialog.vue'
import { useUIStore } from '@/store/ui'
import { usePage } from '@inertiajs/vue3'
import useFlash from '@/Composables/useFlash'



const props = defineProps({
    vendor: {
        type: Object,
    },
})

const ui = useUIStore()

import {
    ArrowLeft,
    Star,
    MapPin,
    Clock,
    Calendar,
    CalendarCheck,
    Shield,
    Heart,
    MessageCircle,
    Check,
    Mail,
    TriangleAlert,
} from 'lucide-vue-next'
import emitter from '@/utils/eventBus'
import { push } from 'notivue'

const reportForm = useForm({
    reported_id: props.vendor.user_id,
    reported_type: 'vendor',
    reason: '',
    description: '',
})

const showReportModal = ref(false)
const isSubmittingReport = ref(false)

const closeReportModal = () => {
    showReportModal.value = false
    reportForm.value = {
        reason: '',
        description: '',
        email: '',
        attachments: []
    }
    isSubmittingReport.value = false
}

const reportVendor = () => {
    showReportModal.value = true
}



const page = usePage()
useFlash()

const submitReport = () => {
    console.log(reportForm)

    reportForm.post('/reports', {
        preserveScroll: true,
        onSuccess: () => {
            closeReportModal()
            reportForm.reset()
        },
        onError: (errors) => {
            // Access flash from the page composable
            if (page.props.flash.error) {
                // alert(page.props.flash.error)
            }
            // Handle validation errors
            else if (errors.reason || errors.description || errors.reported_id) {
                const errorMessages = Object.values(errors).flat().join(', ')
                // alert(`Please fix the following errors: ${errorMessages}`)
            }
            else {
                // alert('Failed to submit report. Please check the form and try again.')
            }
            // console.error('Report submission errors:', errors)
        },
        onFinish: () => {
            isSubmittingReport.value = false
        }
    })
}


const goBack = () => {
    if (window.history.length > 1) {
        window.history.back()
    } else {
        router.visit('/client')
    }
}

const isMessaging = ref(false)

const chatVendor = () => {

    if (isMessaging.value) return // prevent multiple clicks

    isMessaging.value = true

    // Emit the event (you might be opening a chat modal or redirecting)
    emitter.emit('chat-vendor', props.vendor.user_id)

    // Simulate loading duration or wait for your actual chat logic
    setTimeout(() => {
        isMessaging.value = false
    }, 1000)
}


// Compute initials (handles 1 or multiple names)
const initials = computed(() => {
    if (!props.vendor.name) return '?'
    const words = props.vendor.name.trim().split(' ')
    if (words.length === 1) return words[0].charAt(0).toUpperCase()
    return (words[0].charAt(0) + words[words.length - 1].charAt(0)).toUpperCase()
})


</script>

<template>
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-slate-900 to-slate-800"></div>

        <!-- Header Content -->
        <div class="relative max-w-6xl mx-auto px-4 sm:px-6 py-8">
            <!-- Back Button -->
            <Button @click="goBack"
                class="mb-10 text-white bg-white/10 hover:bg-white/20 border-0 transition-colors duration-200">
                <ArrowLeft class="mr-2 h-4 w-4" />
                Back
            </Button>

            <!-- Vendor Info Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start text-white">
                <!-- Avatar Section -->
                <div class="lg:col-span-3 flex justify-center lg:justify-start  ">
                    <div class="relative">
                        <Avatar class="w-32 h-32 md:w-40 md:h-40 border-4 border-white/30 shadow-2xl">
                            <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                            <AvatarFallback>{{ initials }}</AvatarFallback>
                        </Avatar>

                    </div>
                </div>

                <!-- Vendor Details -->
                <div class="lg:col-span-7 text-center lg:text-left space-y-6">
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold">{{ vendor.name }}</h1>
                        <p class="text-white/80 text-lg mt-2">
                            {{ vendor.categories.join(", ") }}
                        </p>
                    </div>

                    <!-- Rating & Location -->
                    <div
                        class="flex flex-col sm:flex-row gap-4 items-center lg:items-start justify-center lg:justify-start">
                        <div class="flex items-center gap-3">
                            <div class="flex gap-1">
                                <Star v-for="n in 5" :key="n"
                                    :class="['h-5 w-5 text-yellow-400', n <= vendor.rating ? 'fill-current' : 'opacity-30']" />
                            </div>
                            <span class="font-medium text-lg">{{ vendor.rating }}</span>
                            <span class="text-white/60">({{ vendor.reviewCount }} reviews)</span>
                        </div>
                        <div class="flex items-center gap-2 text-white/80">
                            <MapPin class="h-4 w-4" />
                            <span>{{ vendor.location }}</span>
                        </div>
                    </div>

                    <!-- Simple Stats -->
                    <div class="flex flex-wrap gap-3 justify-center lg:justify-start">
                        <!-- <span class="flex items-center px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <Clock class="h-4 w-4 mr-2" />
                            {{ vendor.responseTime }} response
                        </span> -->
                        <span class="flex items-center px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <CalendarCheck class="h-4 w-4 mr-2" />
                            {{ vendor.completedEvents }}+ Events
                        </span>
                        <span v-if="vendor.verified"
                            class="flex items-center px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <Shield class="h-4 w-4 mr-2" />
                            Verified Vendor
                        </span>
                    </div>
                </div>

                <!-- Action Buttons - Simplified -->
                <div class="lg:col-span-2 flex flex-col gap-4">
                    <Button @click="chatVendor" :disabled="isMessaging || ui.isInMessage"
                        class="w-full bg-blue-500 hover:bg-blue-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center">
                        <MessageCircle class="h-5 w-5 mr-1" />
                        Message
                    </Button>

                    <Button @click="reportVendor" variant="ghost"
                        class="w-full bg-white/10 text-white hover:bg-white/20 border-0 transition-colors duration-200">
                        <TriangleAlert class="h-5 w-5 mr-1" />
                        Report
                    </Button>
                </div>
            </div>
        </div>

        <!-- Report Dialog -->
        <VendorReportDialog v-model:show="showReportModal" :vendor="vendor" :report-form="reportForm"
            :isSubmitting="isSubmittingReport" @submit="submitReport" @cancel="closeReportModal" />
    </div>
</template>

<style>
/* Additional styling if needed */
</style>
