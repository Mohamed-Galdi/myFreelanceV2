<script setup>
import Chart from "primevue/chart";
import SelectButton from "primevue/selectbutton";
import { ref, computed, watch } from "vue";

const props = defineProps({
    chartData: Object,
});

// Chart period options
const periodOptions = [
    { label: "Last 30 Days", value: "daily" },
    { label: "Last 12 Weeks", value: "weekly" },
    { label: "Last 12 Months", value: "monthly" },
    { label: "All Time", value: "alltime" }, // New option
];
const selectedPeriod = ref("monthly");

// Prepare chart data
const chartConfig = computed(() => {
    const data = props.chartData[selectedPeriod.value] || [];

    return {
        labels: data.map((item) => item.period),
        datasets: [
            {
                type: "line",
                label: "Cumulative Revenue",
                backgroundColor: "rgba(250, 202, 162, 0.3)", // Soft peach
                borderColor: "rgb(230, 126, 34)", // Warm orange-brown
                borderWidth: 2,
                fill: true,
                data: data.map((item) => item.cumulativeRevenue),
                yAxisID: "y",
                tension: 0.4,
            },
            {
                type: "bar",
                label: "Works",
                backgroundColor: "rgba(100, 181, 246, 0.8)", // Soft sky blue
                borderColor: "rgb(79, 129, 189)", // Muted deep blue
                borderWidth: 1,
                data: data.map((item) => item.workCount),
                yAxisID: "y1",
                barThickness: 25,
                borderRadius: 6,
            },
        ],
    };
});

const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    interaction: {
        mode: "index",
        intersect: false,
    },
    scales: {
        x: {
            grid: { display: false },
        },
        y: {
            type: "linear",
            display: true,
            position: "left",
            title: {
                display: true,
                text: "Cumulative Revenue ($)",
                font: { weight: "bold" },
            },
            ticks: {
                callback: function (value) {
                    return "$" + value.toLocaleString();
                },
            },
            grid: {
                color: "rgba(230, 230, 230, 0.4)",
            },
        },
        y1: {
            type: "linear",
            display: true,
            position: "right",
            title: {
                display: true,
                text: "Work Count",
                font: { weight: "bold" },
            },
            ticks: {
                stepSize: 1,
                precision: 0,
            },
            grid: {
                drawOnChartArea: false,
            },
        },
    },
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context) {
                    let label = context.dataset.label || "";
                    if (label) {
                        label += ": ";
                    }
                    if (context.datasetIndex === 0) {
                        label += "$" + context.raw.toFixed(2);
                    } else {
                        label += context.raw;
                    }
                    return label;
                },
            },
        },
        legend: {
            labels: {
                font: {
                    weight: "bold",
                },
                color: "rgb(80, 60, 50)", // Warm earthy brown
            },
        },
    },
};
</script>

<template>
    <div
        class="card-soft mb-8 p-6 rounded-2xl bg-gradient-to-br from-white to-blue-50 shadow-lg border border-gray-200"
    >
        <div
            class="flex md:flex-row flex-col md:gap-0 gap-2 justify-between items-center mb-6"
        >
            <h2 class="text-xl font-bold text-gray-800">
                Revenue & Work Overview
            </h2>
            <SelectButton
                v-model="selectedPeriod"
                :options="periodOptions"
                optionLabel="label"
                optionValue="value"
                class="p-button-rounded p-button-sm overflow-x-auto"
            />
        </div>
        <!-- Scroll on mobile, full width on desktop -->
        <div class="overflow-x-auto md:overflow-hidden w-full">
            <div class="min-w-[600px] md:w-full h-96">
                <Chart
                    type="bar"
                    :data="chartConfig"
                    :options="chartOptions"
                    class="revenue-chart h-full"
                />
            </div>
        </div>
    </div>
</template>
