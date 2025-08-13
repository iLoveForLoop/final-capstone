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
    PointElement,
    LineElement,
    LineController,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js'

// Register Chart.js components
ChartJS.register(
    CategoryScale,
    LinearScale,
    PointElement,
    LineElement,
    LineController,
    Title,
    Tooltip,
    Legend,
    Filler
)

// Props
const props = defineProps({
    data: {
        type: Object,
        required: true,
        validator: (value) => {
            return value && Array.isArray(value.labels) && Array.isArray(value.revenue)
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

// Format currency for tooltips
const formatCurrency = (value) => {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: 'PHP',
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value)
}

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
            borderColor: 'rgba(99, 102, 241, 0.2)',
            borderWidth: 1,
            cornerRadius: 8,
            displayColors: false,
            callbacks: {
                label: function (context) {
                    return `Revenue: ${formatCurrency(context.parsed.y)}`
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
                callback: function (value) {
                    return formatCurrency(value)
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
    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.1)')
    gradient.addColorStop(1, 'rgba(99, 102, 241, 0.01)')

    const chartData = {
        labels: props.data.labels,
        datasets: [
            {
                label: 'Revenue',
                data: props.data.revenue,
                borderColor: 'rgb(99, 102, 241)',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointHoverBorderWidth: 3,
                pointHoverBorderColor: 'rgb(99, 102, 241)',
                pointHoverBackgroundColor: '#fff'
            }
        ]
    }

    const mergedOptions = {
        ...defaultOptions,
        ...props.options
    }

    chartInstance = new ChartJS(ctx, {
        type: 'line',
        data: chartData,
        options: mergedOptions
    })
}

// Update chart data
const updateChart = () => {
    if (chartInstance && props.data) {
        chartInstance.data.labels = props.data.labels
        chartInstance.data.datasets[0].data = props.data.revenue
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
