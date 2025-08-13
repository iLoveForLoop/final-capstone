<script setup>
import { ref, onMounted, watch, nextTick } from 'vue'
import {
    Chart,
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    Filler
} from 'chart.js'

Chart.register(
    LineController,
    LineElement,
    PointElement,
    CategoryScale,
    LinearScale,
    Title,
    Tooltip,
    Legend,
    Filler
)

const props = defineProps({
    data: {
        type: Object,
        required: true,
        validator: data => (
            Array.isArray(data?.labels) &&
            Array.isArray(data?.values)
        )
    },
    lineColor: {
        type: String,
        default: '#6366f1'
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
        type: 'line',
        data: {
            labels: props.data.labels,
            datasets: [{
                label: 'Bookings',
                data: props.data.values,
                borderColor: props.lineColor,
                backgroundColor: 'rgba(99, 102, 241, 0.1)',
                borderWidth: 2,
                pointRadius: 4,
                pointHoverRadius: 6,
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1f2937',
                    titleColor: '#f9fafb',
                    bodyColor: '#f9fafb',
                    padding: 12,
                    usePointStyle: true
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f5f9'
                    },
                    ticks: {
                        color: '#64748b'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#64748b'
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
