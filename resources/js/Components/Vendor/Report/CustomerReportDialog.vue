<script setup>
import { ref, watch } from 'vue'
import { useForm, usePage } from '@inertiajs/vue3'
import { Button } from '@/Components/ui/button'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'
import { push } from 'notivue'

const props = defineProps({
    show: { type: Boolean, default: false },
    customer: { type: Object, required: true },
})

const emit = defineEmits(['update:show'])

const showReportModal = ref(props.show)
watch(() => props.show, (val) => (showReportModal.value = val))
watch(showReportModal, (val) => emit('update:show', val))

// Local form + state
const isSubmitting = ref(false)
const page = usePage()

const reportForm = useForm({
    reported_id: props.customer.id, // or props.customer.id
    reported_type: 'client',
    reason: '',
    description: '',
})

// Close modal
const closeReportModal = () => {
    showReportModal.value = false
    reportForm.reset()
    isSubmitting.value = false
}

// Submit report
const submitReport = () => {
    if (!reportForm.reason || !reportForm.description) {
        alert('Please fill out all required fields.')
        return
    }

    isSubmitting.value = true
    reportForm.post('/reports', {
        preserveScroll: true,
        onSuccess: () => {
            // console.log('FLASH: ', page.props.flash.error.description[0]);
            const flash = page.props.flash
            if (flash?.success) {
                push.success(flash.success)
                closeReportModal()
            }
            if (flash?.error) {
                push.error(flash.error)
            }


        },
        onError: (errors) => {
            alert('Failed to submit report. Please try again.')
            console.error(errors)
        },
        onFinish: () => {
            isSubmitting.value = false
        },
    })
}
</script>

<template>
    <Dialog :open="showReportModal" @update:open="showReportModal = $event">
        <DialogContent class="sm:max-w-md">
            <DialogHeader>
                <DialogTitle>Report Customer</DialogTitle>
                <DialogDescription>
                    Please provide details about your concern regarding {{ customer.name }}.
                    We will review all reports within 24 hours.
                </DialogDescription>
            </DialogHeader>

            <div class="grid gap-4 py-4">
                <!-- Reason -->
                <div class="grid gap-2">
                    <Label for="reason" class="text-sm font-medium">Reason for report *</Label>
                    <Select v-model="reportForm.reason">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a reason" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Abusive behavior">Abusive Behavior</SelectItem>
                            <SelectItem value="Spam or Scam">Spam or Scam</SelectItem>
                            <SelectItem value="No-show">No-show or Late</SelectItem>
                            <SelectItem value="Inappropriate Requests">Inappropriate Requests</SelectItem>
                            <SelectItem value="Payment Issues">Payment Issues</SelectItem>
                            <SelectItem value="Unprofessional">Unprofessional Conduct</SelectItem>
                            <SelectItem value="Other">Other</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Description -->
                <div class="grid gap-2">
                    <Label for="description" class="text-sm font-medium">Detailed description *</Label>
                    <Textarea id="description" v-model="reportForm.description"
                        placeholder="Describe the issue you experienced..." class="min-h-32" />
                </div>
            </div>

            <DialogFooter class="flex flex-col sm:flex-row gap-2">
                <Button variant="outline" @click="closeReportModal" :disabled="isSubmitting">
                    Cancel
                </Button>
                <Button @click="submitReport" :disabled="isSubmitting || !reportForm.reason || !reportForm.description"
                    class="bg-red-600 hover:bg-red-700">
                    <span v-if="isSubmitting">Submitting...</span>
                    <span v-else>Submit Report</span>
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
