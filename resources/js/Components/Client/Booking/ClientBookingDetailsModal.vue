<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import {
    X, FileText, User, Calendar, Utensils,
    MapPin, Mail, Phone, Clock, Users, CreditCard, Package, Printer, Download
} from 'lucide-vue-next'
import html2canvas from 'html2canvas'

const isOpen = ref(false)
const booking = ref(null)

const open = (bookingData = {}) => {
    booking.value = bookingData
    isOpen.value = true
}

const close = () => {
    isOpen.value = false
}

defineExpose({
    open,
    close
})

const emit = defineEmits(['accept-booking', 'decline-booking', 'complete-booking', 'cancel-booking'])

const getStatusBadgeClass = (status) => {
    if (!status) return 'bg-gray-100 text-gray-700 border border-gray-300'

    const classes = {
        pending: 'bg-amber-50 text-amber-700 border border-amber-200',
        confirmed: 'bg-green-50 text-green-700 border border-green-200',
        completed: 'bg-blue-50 text-blue-700 border border-blue-200',
        cancelled: 'bg-red-50 text-red-700 border border-red-200'
    }
    return classes[status] || 'bg-gray-100 text-gray-700 border border-gray-300'
}

const formatDate = (date) => {
    if (!date) return 'N/A'
    try {
        return new Date(date).toLocaleDateString('en-US', {
            weekday: 'long',
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        })
    } catch (e) {
        return 'Invalid Date'
    }
}

const formatTime = (time) => {
    if (!time) return 'N/A'
    try {
        return new Date(`2000-01-01T${time}`).toLocaleTimeString('en-US', {
            hour: 'numeric',
            minute: '2-digit',
            hour12: true
        })
    } catch (e) {
        return 'Invalid Time'
    }
}

const formatCurrency = (amount) => {
    if (!amount && amount !== 0) return '₱0'
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 2
    }).format(amount)
}

const getServicePriceRange = () => {
    const price = booking.value?.service?.price
    const maxPrice = booking.value?.service?.max_price

    if (!price) return 'N/A'

    if (maxPrice && maxPrice > price) {
        return `${formatCurrency(price)} - ${formatCurrency(maxPrice)}`
    }

    return formatCurrency(price)
}

const isCateringService = () => {
    return booking.value?.category?.name === 'Catering'
}

const getCateringDishesByCategory = () => {
    if (!booking.value?.selected_catering_dishes) return {}

    let dishes = booking.value.selected_catering_dishes
    if (typeof dishes === 'string') {
        try {
            dishes = JSON.parse(dishes)
        } catch (e) {
            console.error('Error parsing catering dishes:', e)
            return {}
        }
    }

    if (typeof dishes === 'object' && !Array.isArray(dishes)) {
        const result = {}
        Object.entries(dishes).forEach(([category, dishData]) => {
            result[category] = Array.isArray(dishData) ? dishData : [dishData]
        })
        return result
    }

    return {}
}

const calculateTotalPrice = () => {
    const basePrice = parseFloat(booking.value?.service?.price) || 0
    const pax = booking.value?.pax || 1
    return basePrice * pax
}

const getBookingAge = () => {
    if (!booking.value?.created_at) return ''
    const created = new Date(booking.value.created_at)
    const now = new Date()
    const diffTime = Math.abs(now - created)
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24))

    if (diffDays === 1) return '1 day ago'
    if (diffDays < 7) return `${diffDays} days ago`
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`
    return `${Math.floor(diffDays / 30)} months ago`
}

// Generate the printable HTML content (used for both print and image)
const generatePrintableContent = () => {
    const cateringDishes = getCateringDishesByCategory()
    const hasCateringDishes = Object.keys(cateringDishes).length > 0

    return `
        <div class="printable-content" style="width: 794px; min-height: 1123px; background: white; padding: 40px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-size: 12px; line-height: 1.4; color: #374151;">
            <div style="max-width: 714px; margin: 0 auto;">
                <!-- Header -->
                <div style="border-bottom: 2px solid #e5e7eb; padding-bottom: 24px; margin-bottom: 32px;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px;">
                        <div>
                            <h1 style="font-size: 24px; font-weight: 700; color: #111827; margin: 0 0 8px 0;">Booking Details</h1>
                            <p style="font-size: 14px; color: #6b7280; margin: 0;">Booking ID: #${booking.value?.id || 'N/A'}</p>
                        </div>
                        <div style="text-align: right;">
                            <p style="font-size: 12px; color: #6b7280; margin: 0 0 4px 0;">Generated on</p>
                            <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 600;">${new Date().toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })}</p>
                        </div>
                    </div>
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="display: inline-block; padding: 6px 16px; border-radius: 6px; font-size: 12px; font-weight: 600; text-transform: capitalize; ${getStatusBadgeClass(booking.value?.status).replace('bg-', 'background-color: ').replace('text-', 'color: ').replace('border border-', 'border: 1px solid ')}">${booking.value?.status || 'N/A'}</span>
                        <p style="font-size: 12px; color: #6b7280; margin: 0;">Booked ${getBookingAge()}</p>
                    </div>
                </div>

                <!-- Content Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px;">
                    <!-- Left Column -->
                    <div>
                        <!-- Client Information -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Client Information</h2>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Full Name</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.user?.client?.full_name || booking.value?.client || 'N/A'}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Contact Number</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.user?.client?.contact_number || 'N/A'}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Email Address</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.user?.email || booking.value?.client_email || 'N/A'}</p>
                            </div>
                        </div>

                        <!-- Event Details -->
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Event Details</h2>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Event Date</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${formatDate(booking.value?.event?.event_date || booking.value?.event_date)}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Event Time</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.time || formatTime(booking.value?.event?.event_time)}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Event Venue</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.event?.location || booking.value?.event_location || 'N/A'}</p>
                            </div>
                            ${(booking.value?.event?.description || booking.value?.notes) ? `
                            <div>
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Additional Notes</p>
                                <p style="font-size: 13px; color: #374151; margin: 0; background-color: #f9fafb; padding: 12px; border-radius: 6px; line-height: 1.5;">${booking.value?.event?.description || booking.value?.notes}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div>
                        <!-- Service Information -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Service Information</h2>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Service Name</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.service?.name || 'N/A'}</p>
                            </div>
                            <div style="margin-bottom: 12px;">
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Service Category</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${booking.value?.category?.name || 'N/A'}</p>
                            </div>
                            <div>
                                <p style="font-size: 11px; color: #6b7280; margin: 0 0 4px 0; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500;">Service Price</p>
                                <p style="font-size: 14px; color: #111827; margin: 0; font-weight: 500;">${getServicePriceRange()}</p>
                            </div>
                        </div>

                        ${isCateringService() && booking.value?.pax ? `
                        <!-- Guest Count -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Guest Information</h2>
                            </div>
                            <div style="text-align: center; padding: 16px; background-color: #f8fafc; border-radius: 8px;">
                                <p style="font-size: 32px; font-weight: 700; color: #1e40af; margin: 0 0 4px 0;">${booking.value.pax}</p>
                                <p style="font-size: 14px; color: #6b7280; margin: 0; font-weight: 500;">Total Guests</p>
                            </div>
                        </div>
                        ` : ''}

                        ${hasCateringDishes ? `
                        <!-- Menu Selection -->
                        <div style="margin-bottom: 32px;">
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Menu Selection</h2>
                            </div>
                            ${Object.entries(cateringDishes).map(([category, dishes]) => `
                                <div style="margin-bottom: 16px;">
                                    <p style="font-size: 12px; color: #374151; margin: 0 0 8px 0; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px;">${category}</p>
                                    <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                        ${dishes.map(dishName => `
                                            <span style="display: inline-block; padding: 4px 10px; font-size: 11px; font-weight: 500; background-color: #fef3c7; color: #92400e; border: 1px solid #fbbf24; border-radius: 4px;">${dishName}</span>
                                        `).join('')}
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                        ` : ''}

                        <!-- Payment Details -->
                        <div>
                            <div style="display: flex; align-items: center; margin-bottom: 16px; padding-bottom: 8px; border-bottom: 1px solid #f3f4f6;">
                                <h2 style="font-size: 16px; font-weight: 600; color: #111827; margin: 0;">Payment Details</h2>
                            </div>
                            ${isCateringService() && booking.value?.pax ? `
                                <div style="margin-bottom: 8px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
                                        <span style="font-size: 13px; color: #6b7280;">Price per person</span>
                                        <span style="font-size: 13px; color: #111827; font-weight: 500;">${formatCurrency(booking.value?.service?.price)}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 12px;">
                                        <span style="font-size: 13px; color: #6b7280;">Number of guests</span>
                                        <span style="font-size: 13px; color: #111827; font-weight: 500;">× ${booking.value?.pax}</span>
                                    </div>
                                </div>
                                <div style="padding-top: 12px; border-top: 2px solid #e5e7eb; display: flex; justify-content: space-between; align-items: center;">
                                    <span style="font-size: 16px; font-weight: 700; color: #111827;">Total Amount</span>
                                    <span style="font-size: 20px; font-weight: 700; color: #059669;">${formatCurrency(calculateTotalPrice())}</span>
                                </div>
                            ` : `
                                <div style="text-align: center; padding: 16px; background-color: #f0fdf4; border-radius: 8px;">
                                    <p style="font-size: 20px; font-weight: 700; color: #059669; margin: 0;">${formatCurrency(booking.value?.service?.price || booking.value?.raw_amount)}</p>
                                    <p style="font-size: 12px; color: #6b7280; margin: 4px 0 0 0;">Total Amount</p>
                                </div>
                            `}
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div style="margin-top: 48px; padding-top: 24px; border-top: 1px solid #e5e7eb; text-align: center;">
                    <p style="font-size: 11px; color: #9ca3af; margin: 0;">This is a system-generated booking confirmation document</p>
                </div>
            </div>
        </div>
    `
}

const handlePrint = () => {
    const printContent = generatePrintableContent()

    // Create a print window
    const printWindow = window.open('', '_blank', 'width=794,height=1123')
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Booking Details - #${booking.value?.id || 'N/A'}</title>
            <style>
                @media print {
                    @page {
                        size: A4;
                        margin: 0;
                    }
                    body {
                        margin: 0;
                        padding: 0;
                    }
                }
                body {
                    margin: 0;
                    padding: 0;
                    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                }
            </style>
        </head>
        <body>
            ${printContent}
            <script>
                window.onload = function() {
                    window.print();
                    setTimeout(() => {
                        window.close();
                    }, 500);
                }
            <\/script>
        </body>
        </html>
    `)
    printWindow.document.close()
}

const saveAsImage = async () => {
    try {
        const saveButton = document.querySelector('[data-save-image]')
        const originalText = saveButton.innerHTML
        saveButton.innerHTML = 'Saving...'
        saveButton.disabled = true

        const printContent = generatePrintableContent()

        // Create a temporary div for image capture
        const printDiv = document.createElement('div')
        printDiv.innerHTML = printContent
        document.body.appendChild(printDiv)

        // Wait for fonts and styles to load
        await new Promise(resolve => setTimeout(resolve, 100))

        const canvas = await html2canvas(printDiv.firstElementChild, {
            backgroundColor: '#ffffff',
            scale: 2,
            useCORS: true,
            logging: false,
            width: 794,
            height: printDiv.firstElementChild.scrollHeight,
            windowWidth: 794,
            windowHeight: printDiv.firstElementChild.scrollHeight
        })

        document.body.removeChild(printDiv)

        const link = document.createElement('a')
        link.download = `booking-${booking.value?.id || 'details'}-${new Date().getTime()}.png`
        link.href = canvas.toDataURL('image/png')
        link.click()

        saveButton.innerHTML = originalText
        saveButton.disabled = false

    } catch (error) {
        console.error('Error saving image:', error)
        alert('Failed to save image. Please try again.')

        const saveButton = document.querySelector('[data-save-image]')
        if (saveButton) {
            saveButton.innerHTML = 'Save as Image'
            saveButton.disabled = false
        }
    }
}

const handleEscape = (event) => {
    if (event.key === 'Escape' && isOpen.value) {
        close()
    }
}

const acceptBooking = (booking) => {
    emit('accept-booking', booking)
}

const declineBooking = (booking) => {
    emit('decline-booking', booking)
}

const cancelBooking = (booking) => {
    emit('cancel-booking', booking)
}

const completeBooking = (booking) => {
    emit('complete-booking', booking)
}

onMounted(() => {
    window.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
    window.removeEventListener('keydown', handleEscape)
})
</script>

<template>
    <Transition name="fade">
        <div v-if="isOpen" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50" aria-labelledby="modal-title"
            role="dialog" aria-modal="true" @click="close">

            <div class="flex items-center justify-center min-h-screen p-4" @click.stop>
                <Transition name="scale">
                    <div
                        class="relative bg-white rounded-lg shadow-xl w-full max-w-4xl mx-auto max-h-[92vh] overflow-hidden flex flex-col">

                        <!-- Header -->
                        <div class="px-6 py-5 border-b border-gray-200">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900" id="modal-title">
                                        Booking Details
                                    </h3>
                                    <p class="text-sm text-gray-500 mt-0.5">ID: #{{ booking?.id || 'N/A' }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button @click="saveAsImage" data-save-image
                                        class="flex items-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        <Download :size="14" class="mr-2" />
                                        Save as Image
                                    </button>
                                    <button @click="handlePrint"
                                        class="flex items-center px-3 py-2 text-sm text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                        <Printer :size="14" class="mr-2" />
                                        Print
                                    </button>
                                    <button @click="close"
                                        class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors">
                                        <X :size="20" />
                                    </button>
                                </div>
                            </div>

                            <!-- Status Badge -->
                            <div class="mt-4 flex items-center justify-between">
                                <span :class="getStatusBadgeClass(booking?.status)"
                                    class="inline-flex items-center px-3 py-1 rounded-md text-xs font-medium capitalize">
                                    {{ booking?.status || 'N/A' }}
                                </span>
                                <div class="text-sm text-gray-500 flex items-center">
                                    <Clock :size="14" class="mr-1.5" />
                                    Booked {{ getBookingAge() }}
                                </div>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-6">
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                                    <!-- Left Column -->
                                    <div class="space-y-6">
                                        <!-- Client Information -->
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <User :size="16" class="mr-2 text-gray-600" />
                                                Client Information
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <!-- {{ console.log('CLIENT', booking.user) }} -->
                                                    <label class="text-xs text-gray-500">Name</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.user?.client?.full_name || booking?.client.full_name
                                                            || 'N/A'
                                                        }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Contact Number</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.client.contact_number || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Email</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.client_email || 'N/A' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Event Details -->
                                        <div class="pt-6 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <Calendar :size="16" class="mr-2 text-gray-600" />
                                                Event Details
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs text-gray-500">Date</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ formatDate(booking?.event?.event_date || booking?.event_date)
                                                        }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Time</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.time || formatTime(booking?.event?.event_time) }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Venue</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.event?.location || booking?.event_location || 'N/A'
                                                        }}
                                                    </p>
                                                </div>
                                                <div v-if="booking?.event?.description || booking?.notes">
                                                    <label class="text-xs text-gray-500">Notes</label>
                                                    <p class="text-sm text-gray-700 mt-1 bg-gray-50 p-3 rounded">
                                                        {{ booking?.event?.description || booking?.notes }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Right Column -->
                                    <div class="space-y-6">
                                        <!-- Service Information -->
                                        <div>
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <Package :size="16" class="mr-2 text-gray-600" />
                                                Service Information
                                            </h4>
                                            <div class="space-y-3">
                                                <div>
                                                    <label class="text-xs text-gray-500">Service</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.service?.name || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Category</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ booking?.category?.name || 'N/A' }}
                                                    </p>
                                                </div>
                                                <div>
                                                    <label class="text-xs text-gray-500">Service Price</label>
                                                    <p class="text-sm text-gray-900 mt-1">
                                                        {{ getServicePriceRange() }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Catering Specific -->
                                        <template v-if="isCateringService()">
                                            <div v-if="booking?.pax" class="pt-6 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                    <Users :size="16" class="mr-2 text-gray-600" />
                                                    Guest Count
                                                </h4>
                                                <p class="text-2xl font-semibold text-gray-900">{{ booking.pax }} <span
                                                        class="text-sm font-normal text-gray-500">guests</span></p>
                                            </div>

                                            <div v-if="Object.keys(getCateringDishesByCategory()).length > 0"
                                                class="pt-6 border-t border-gray-200">
                                                <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                    <Utensils :size="16" class="mr-2 text-gray-600" />
                                                    Menu Selection
                                                </h4>
                                                <div class="space-y-4">
                                                    <div v-for="(dishes, category) in getCateringDishesByCategory()"
                                                        :key="category">
                                                        <p class="text-xs font-medium text-gray-700 mb-2">{{ category }}
                                                        </p>
                                                        <div class="flex flex-wrap gap-2">
                                                            <span v-for="(dishName, index) in dishes" :key="index"
                                                                class="inline-block px-2.5 py-1 text-xs bg-gray-100 text-gray-700 rounded">
                                                                {{ dishName }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- Payment Details -->
                                        <div class="pt-6 border-t border-gray-200">
                                            <h4 class="text-sm font-semibold text-gray-900 mb-4 flex items-center">
                                                <CreditCard :size="16" class="mr-2 text-gray-600" />
                                                Payment Details
                                            </h4>

                                            <template v-if="isCateringService() && booking?.pax">
                                                <div class="space-y-2">
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600">Price per person</span>
                                                        <span class="text-gray-900">{{
                                                            formatCurrency(booking?.service?.price) }}</span>
                                                    </div>
                                                    <div class="flex justify-between text-sm">
                                                        <span class="text-gray-600">Number of guests</span>
                                                        <span class="text-gray-900">× {{ booking?.pax }}</span>
                                                    </div>
                                                    <div
                                                        class="pt-2 mt-2 border-t border-gray-200 flex justify-between">
                                                        <span class="text-sm font-semibold text-gray-900">Total
                                                            Amount</span>
                                                        <span class="text-lg font-semibold text-gray-900">
                                                            {{ formatCurrency(calculateTotalPrice()) }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <p class="text-lg font-semibold text-gray-900">
                                                    {{ formatCurrency(booking?.service?.price || booking?.raw_amount) }}
                                                </p>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer -->
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                            <div class="flex items-center justify-end space-x-3">
                                <button @click="close"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                                    Close
                                </button>
                            </div>
                        </div>
                    </div>
                </Transition>
            </div>
        </div>
    </Transition>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

.scale-enter-active,
.scale-leave-active {
    transition: all 0.2s ease;
}

.scale-enter-from,
.scale-leave-to {
    opacity: 0;
    transform: scale(0.98);
}

::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f9fafb;
}

::-webkit-scrollbar-thumb {
    background: #d1d5db;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #9ca3af;
}
</style>
