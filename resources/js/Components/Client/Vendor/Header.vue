<script setup>
import { ref } from 'vue'
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

const props = defineProps({
    vendor: {
        type: Object,
    },
})

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

const reportForm = useForm({
    reason: '',
    details: '',
})

const showReportModal = ref(false)
const isSubmittingReport = ref(false)

const closeReportModal = () => {
    showReportModal.value = false
    reportForm.value = {
        reason: '',
        details: '',
        email: '',
        attachments: []
    }
    isSubmittingReport.value = false
}

const reportVendor = () => {
    showReportModal.value = true
}

const submitReport = async () => {
    if (!reportForm.value.reason || !reportForm.value.details) {
        alert('Please fill in all required fields')
        return
    }

    isSubmittingReport.value = true

    try {
        const response = await fetch('/api/reports', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                vendor_id: props.vendor.id,
                reason: reportForm.value.reason,
                details: reportForm.value.details,
                email: reportForm.value.email,
                attachments: reportForm.value.attachments
            })
        })

        if (response.ok) {
            const result = await response.json()
            alert('Report submitted successfully! We will review it within 24 hours.')
            closeReportModal()
        } else {
            throw new Error('Failed to submit report')
        }
    } catch (error) {
        console.error('Error submitting report:', error)
        alert('Failed to submit report. Please try again.')
    } finally {
        isSubmittingReport.value = false
    }
}



const goBack = () => {
    if (window.history.length > 1) {
        window.history.back()
    } else {
        router.visit('/client')
    }
}


const chatVendor = () => {
    emitter.emit('chat-vendor', props.vendor.user_id)
}


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
                <div class="lg:col-span-3 flex justify-center lg:justify-start">
                    <div class="relative">
                        <Avatar class="w-32 h-32 md:w-40 md:h-40 border-4 border-white/30 shadow-2xl">
                            <AvatarImage :src="vendor.avatar" :alt="vendor.name" />
                            <AvatarFallback>SS</AvatarFallback>
                        </Avatar>
                        <!-- Verified Badge -->
                        <Badge v-if="vendor.verified"
                            class="absolute -bottom-2 -right-2 bg-green-500 text-white rounded-full w-10 h-10 md:w-12 md:h-12 flex items-center justify-center border-4 border-slate-900 shadow-lg p-0">
                            <Check :size="16" />
                        </Badge>
                        <!-- Status Indicator -->
                        <Badge class="absolute -top-2 -left-2 px-3 py-1 text-xs font-medium"
                            :class="vendor.available ? 'bg-green-500' : 'bg-red-500'">
                            {{ vendor.available ? 'Available' : 'Busy' }}
                        </Badge>
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
                        <span class="flex items-center px-4 py-2 bg-white/10 text-white text-sm rounded-full">
                            <Clock class="h-4 w-4 mr-2" />
                            {{ vendor.responseTime }} response
                        </span>
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
                    <Button @click="chatVendor" size="lg"
                        class="w-full bg-blue-500 text-white hover:bg-blue-600 font-semibold transition-colors duration-200">
                        <MessageCircle class="h-5 w-5 mr-2" />
                        Message
                    </Button>
                    <Button @click="reportVendor" variant="ghost"
                        class="w-full bg-white/10 text-white hover:bg-white/20 border-0 transition-colors duration-200">
                        <TriangleAlert class="h-5 w-5 mr-2" />
                        Report
                    </Button>
                </div>
            </div>
        </div>

        <!-- Report Dialog -->
        <Dialog :open="showReportModal" @update:open="showReportModal = $event">
            <DialogContent class="sm:max-w-md">
                <DialogHeader>
                    <DialogTitle>Report Vendor</DialogTitle>
                    <DialogDescription>
                        Please provide details about your concern regarding {{ vendor.name }}.
                        We take all reports seriously and will review them within 24 hours.
                    </DialogDescription>
                </DialogHeader>

                <div class="grid gap-4 py-4">
                    <!-- Reason Selection -->
                    <div class="grid gap-2">
                        <Label for="reason" class="text-sm font-medium">Reason for report *</Label>
                        <Select v-model="reportForm.reason">
                            <SelectTrigger>
                                <SelectValue placeholder="Select a reason" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="inappropriate_behavior">Inappropriate Behavior</SelectItem>
                                <SelectItem value="fake_profile">Fake Profile or Information</SelectItem>
                                <SelectItem value="spam_scam">Spam or Scam</SelectItem>
                                <SelectItem value="poor_service">Poor Service Quality</SelectItem>
                                <SelectItem value="unprofessional">Unprofessional Conduct</SelectItem>
                                <SelectItem value="safety_concerns">Safety Concerns</SelectItem>
                                <SelectItem value="other">Other</SelectItem>
                            </SelectContent>
                        </Select>
                    </div>

                    <!-- Details -->
                    <div class="grid gap-2">
                        <Label for="details" class="text-sm font-medium">Detailed description *</Label>
                        <Textarea id="details" v-model="reportForm.details"
                            placeholder="Please provide specific details about your concern..." class="min-h-32" />
                    </div>
                </div>

                <DialogFooter class="flex flex-col sm:flex-row gap-2">
                    <Button variant="outline" @click="closeReportModal" :disabled="isSubmittingReport">
                        Cancel
                    </Button>
                    <Button @click="submitReport"
                        :disabled="isSubmittingReport || !reportForm.reason || !reportForm.details"
                        class="bg-red-600 hover:bg-red-700">
                        <span v-if="isSubmittingReport">Submitting...</span>
                        <span v-else>Submit Report</span>
                    </Button>
                </DialogFooter>
            </DialogContent>
        </Dialog>
    </div>
</template>

<style>
/* Additional styling if needed */
</style>
