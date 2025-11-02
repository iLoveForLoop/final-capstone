<script setup>
import { ref, watch } from 'vue'
import { Button } from '@/Components/ui/button'
import { Badge } from '@/Components/ui/badge'
import { Avatar, AvatarFallback, AvatarImage } from '@/Components/ui/avatar'
import { Dialog, DialogContent, DialogDescription, DialogHeader, DialogTitle, DialogFooter } from '@/Components/ui/dialog'
import { Label } from '@/Components/ui/label'
import { Input } from '@/Components/ui/input'
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/Components/ui/select'
import { Textarea } from '@/Components/ui/textarea'

// Props coming from parent
const props = defineProps({
    show: { type: Boolean, default: false },
    vendor: { type: Object, required: true },
    reportForm: { type: Object, required: true },
    isSubmitting: { type: Boolean, default: false }
})

// Emit events to parent
const emit = defineEmits(['update:show', 'submit', 'cancel'])

// Local reactive state to control open/close based on parent
const showReportModal = ref(props.show)

// Sync parent prop <-> local state
watch(
    () => props.show,
    (val) => (showReportModal.value = val)
)

watch(showReportModal, (val) => emit('update:show', val))

// Internal actions
const closeReportModal = () => {
    emit('cancel')
}
const submitReport = () => {
    emit('submit')
}
</script>

<template>
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
                <!-- Reason -->
                <div class="grid gap-2">
                    <Label for="reason" class="text-sm font-medium">Reason for report *</Label>
                    <Select v-model="reportForm.reason">
                        <SelectTrigger>
                            <SelectValue placeholder="Select a reason" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="Inappropriate behavior">Inappropriate Behavior</SelectItem>
                            <SelectItem value="Fake Profile">Fake Profile or Information</SelectItem>
                            <SelectItem value="Spam Scam">Spam or Scam</SelectItem>
                            <SelectItem value="Poor Service">Poor Service Quality</SelectItem>
                            <SelectItem value="Unprofessional">Unprofessional Conduct</SelectItem>
                            <SelectItem value="Inactivity">Inactivity</SelectItem>
                            <SelectItem value="other">Other</SelectItem>
                        </SelectContent>
                    </Select>
                </div>

                <!-- Description -->
                <div class="grid gap-2">
                    <Label for="description" class="text-sm font-medium">Detailed description *</Label>
                    <Textarea id="description" v-model="reportForm.description"
                        placeholder="Please provide specific details about your concern..." class="min-h-32" />
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
