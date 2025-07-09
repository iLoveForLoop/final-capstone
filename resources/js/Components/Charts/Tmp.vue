<template>
    <div class="h-full w-full">
        <!-- Stats Preview -->
        <div class="grid grid-cols-2 gap-3 mb-4">
            <!-- <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-2 border border-blue-100">
                <div class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total</div>
                <div class="text-sm font-bold text-blue-800">{{ totalValue }}</div>
            </div> -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-lg p-2 border border-green-100">
                <div class="text-xs text-green-600 font-semibold uppercase tracking-wide">Peak</div>
                <div class="text-sm font-bold text-green-800">{{ maxValue }}</div>
            </div>
            <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-2 border border-purple-100">
                <div class="text-xs text-purple-600 font-semibold uppercase tracking-wide">Avg</div>
                <div class="text-sm font-bold text-purple-800">{{ avgValue }}</div>
            </div>
        </div>

        <!-- Chart Container -->
        <div class="relative h-40">
            <!-- Loading Overlay -->
            <div v-if="loading"
                class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center rounded-xl z-10">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce"></div>
                    <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-3 h-3 bg-indigo-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>

            <!-- Chart Canvas -->
            <div class="bg-gradient-to-br from-gray-50 to-white rounded-lg p-3 border border-gray-100 h-full">
                <canvas ref="canvasEl" class="w-full h-full"></canvas>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="flex items-center justify-between mt-3 pt-2 border-t border-gray-100">
            <div class="flex items-center space-x-2">
                <div class="w-2 h-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full animate-pulse"></div>
                <span class="text-xs text-gray-600">Live Data</span>
            </div>
            <div class="text-xs text-gray-400">
                {{ formatTime(new Date()) }}
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const props = defineProps({
    labels: Array,
    data: Array,
    chartLabel: {
        type: String,
        default: 'Bookings'
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const canvasEl = ref(null)
let chartInstance = null

// Computed values for stats
const totalValue = computed(() => {
    return props.data ? props.data.reduce((sum, val) => sum + val, 0) : 0
})

const maxValue = computed(() => {
    return props.data ? Math.max(...props.data) : 0
})

const avgValue = computed(() => {
    return props.data ? Math.round(totalValue.value / props.data.length) : 0
})

onMounted(() => {
    renderChart()
})

watch(() => props.data, () => {
    if (chartInstance) {
        chartInstance.destroy()
    }
    renderChart()
})

function formatTime(date) {
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    })
}

function renderChart() {
    if (!canvasEl.value || !props.data) return

    // Create gradient
    const ctx = canvasEl.value.getContext('2d')
    const gradient = ctx.createLinearGradient(0, 0, 0, 160)
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.3)')
    gradient.addColorStop(0.5, 'rgba(99, 102, 241, 0.1)')
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.0)')

    chartInstance = new Chart(canvasEl.value, {
        type: 'line',
        data: {
            labels: props.labels,
            datasets: [
                {
                    label: props.chartLabel,
                    data: props.data,
                    fill: true,
                    borderColor: '#6366f1',
                    backgroundColor: gradient,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#6366f1',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#6366f1',
                    pointHoverBorderColor: '#ffffff',
                    pointHoverBorderWidth: 2,
                    borderWidth: 2
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            animation: {
                duration: 1500,
                easing: 'easeInOutQuart'
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    titleColor: '#ffffff',
                    bodyColor: '#ffffff',
                    borderColor: '#6366f1',
                    borderWidth: 1,
                    cornerRadius: 6,
                    displayColors: false,
                    padding: 8,
                    titleFont: {
                        size: 12,
                        weight: 'bold'
                    },
                    bodyFont: {
                        size: 11
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [3, 3]
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 10,
                            weight: 500
                        }
                    },
                    border: {
                        display: false
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        display: true,
                        color: 'rgba(0, 0, 0, 0.05)',
                        borderDash: [3, 3]
                    },
                    ticks: {
                        color: '#6b7280',
                        font: {
                            size: 10,
                            weight: 500
                        },
                        padding: 8
                    },
                    border: {
                        display: false
                    }
                }
            },
            elements: {
                point: {
                    hoverRadius: 6
                }
            }
        }
    })
}
</script>
