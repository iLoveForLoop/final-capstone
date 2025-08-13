<template>
    <div class="relative">
        <canvas ref="chartCanvas" :width="width" :height="height"></canvas>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue'
import {
    Chart as ChartJS,
    CategoryScale,
    LinearScale,
    BarElement,
    BarController,
    Title,
    Tooltip,
    Legend
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    BarElement,
    BarController,
    Title,
    Tooltip,
    Legend
)

// Props
const props = defineProps({
    data: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && Array.isArray(value.labels) && Array.isArray(value.bookings)
        }
    },
    width: {
        type: Number,
        default: 400
    },
    height: {
        type: Number,
        default: 200
    },
    options: {
        type: Object,
        default: () => ({})
    }
})

// Template refs
const chartCanvas = ref(null)
let chartInstance = null

// Default chart options
const defaultOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        intersect: false,
        mode: 'index'
    },
    plugins: {
        legend: {
            display: false
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.8)',
            titleColor: '#fff',
            bodyColor: '#fff',
            borderColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
                label: function (context) {
                    const value = context.parsed.y
                    const suffix = value === 1 ? 'booking' : 'bookings'
                    return `${value} ${suffix}`
                }
            }
        }
    },
    scales: {
        x: {
            grid: {
                display: false
            },
            border: {
                display: false
            },
            ticks: {
                color: '#6B7280',
                font: {
                    size: 12
                }
            }
        },
        y: {
            grid: {
                color: 'rgba(0, 0, 0, 0.05)',
                drawBorder: false
            },
            border: {
                display: false
            },
            ticks: {
                color: '#6B7280',
                font: {
                    size: 12
                },
                beginAtZero: true,
                stepSize: 1,
                callback: function (value) {
                    if (Number.isInteger(value)) {
                        return value
                    }
                }
            }
        }
    }
}

// Create chart
const createChart = () => {
    if (!chartCanvas.value || !props.data) return

    const ctx = chartCanvas.value.getContext('2d')

    // Destroy existing chart
    if (chartInstance) {
        chartInstance.destroy()
    }

    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 300)
    gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)')
    gradient.addColorStop(1, 'rgba(59, 130, 246, 0.2)')

    const chartData = {
        labels: props.data.labels,
        datasets: [
            {
                label: 'Bookings',
                data: props.data.bookings,
                backgroundColor: gradient,
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
                barThickness: 40,
                maxBarThickness: 50
            }
        ]
    }

    const mergedOptions = {
        ...defaultOptions,
        ...props.options
    }

    chartInstance = new ChartJS(ctx, {
        type: 'bar',
        data: chartData,
        options: mergedOptions
    })
}

// Update chart data
const updateChart = () => {
    if (chartInstance && props.data) {
        chartInstance.data.labels = props.data.labels
        chartInstance.data.datasets[0].data = props.data.bookings
        chartInstance.update('none')
    }
}

// Watch for data changes
watch(() => props.data, () => {
    updateChart()
}, { deep: true })

// Lifecycle
onMounted(() => {
    createChart()
})

onUnmounted(() => {
    if (chartInstance) {
        chartInstance.destroy()
    }
})
</script>
