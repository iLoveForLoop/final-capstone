<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import {
    Chart,
    BarController,
    LineController,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
} from 'chart.js'

// Register Chart.js components
Chart.register(
    BarController,
    LineController,
    BarElement,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend
)

const props = defineProps({
    data: {
        type: Object,
        required: true,
        validator: data => (
            Array.isArray(data?.labels) &&
            Array.isArray(data?.bookings) &&
            Array.isArray(data?.revenue)
        )
    },
    bookingsColor: {
        type: String,
        default: '#6366f1' // indigo-500
    },
    revenueColor: {
        type: String,
        default: '#10b981' // emerald-500
    },
    height: {
        type: Number,
        default: 300
    }
})

const chartRef = ref(null)
const chartInstance = ref(null)

const initChart = () => {
    if (chartInstance.value) {
        chartInstance.value.destroy()
    }

    const ctx = chartRef.value.getContext('2d')

    chartInstance.value = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: props.data.labels,
            datasets: [
                {
                    label: 'Bookings',
                    data: props.data.bookings,
                    backgroundColor: props.bookingsColor,
                    borderRadius: 4,
                    yAxisID: 'y'
                },
                {
                    label: 'Revenue (₱)',
                    data: props.data.revenue,
                    borderColor: props.revenueColor,
                    backgroundColor: 'transparent',
                    type: 'line',
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        padding: 20,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#f9fafb',
                    padding: 12,
                    usePointStyle: true,
                    callbacks: {
                        label: (context) => {
                            let label = context.dataset.label || ''
                            if (label) label += ': '
                            if (context.datasetIndex === 0) {
                                label += context.raw
                            } else {
                                label += '₱' + context.raw.toLocaleString()
                            }
                            return label
                        }
                    }
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    title: {
                        display: true,
                        text: 'Bookings'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Revenue'
                    },
                    grid: {
                        drawOnChartArea: false
                    },
                    ticks: {
                        callback: (value) => {
                            if (value >= 1000000) return '₱' + (value / 1000000).toFixed(1) + 'M'
                            if (value >= 1000) return '₱' + (value / 1000).toFixed(0) + 'k'
                            return '₱' + value
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    })
}

onMounted(() => {
    nextTick(() => initChart())
})

watch(() => props.data, () => {
    nextTick(() => initChart())
}, { deep: true })
</script>

<template>
    <div class="chart-container" :style="{ height: `${height}px` }">
        <canvas ref="chartRef"></canvas>
    </div>
</template>

<style scoped>
.chart-container {
    position: relative;
    width: 100%;
}
</style>
