<script setup>
import Chart from "primevue/chart";
import SelectButton from "primevue/selectbutton";
import { ref, computed, onMounted } from "vue";

const props = defineProps({
    chartData: Object,
});

// Chart period options
const periodOptions = [
    { label: "Last 30 Days", value: "daily" },
    { label: "Last 12 Weeks", value: "weekly" },
    { label: "Last 12 Months", value: "monthly" },
    { label: "All Time", value: "alltime" },
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
                backgroundColor: "rgba(250, 202, 162, 0.3)",
                borderColor: "rgb(230, 126, 34)",
                borderWidth: 2,
                fill: true,
                data: data.map((item) => item.cumulativeRevenue),
                yAxisID: "y",
                tension: 0.4,
            },
            {
                type: "bar",
                label: "Period Revenue",
                backgroundColor: "rgba(100, 181, 246, 0.8)",
                borderColor: "rgb(79, 129, 189)",
                borderWidth: 1,
                data: data.map((item) => item.revenue),
                yAxisID: "y",
                barThickness: 25,
                borderRadius: 6,
            },
        ],
        // Store full payment details for custom tooltips
        allData: data,
    };
});

// Custom plugin to handle tooltip formatting
const paymentDetailsPlugin = {
    id: "paymentDetails",
    beforeRender: (chart) => {
        chart.paymentData = chartConfig.value.allData;
    },
};

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
                text: "Amount ($)",
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
    },
    plugins: {
        tooltip: {
            callbacks: {
                label: function (context) {
                    if (context.datasetIndex === 0) {
                        return `Cumulative Revenue: $${context.raw.toLocaleString(
                            undefined,
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }
                        )}`;
                    } else {
                        return `Period Revenue: $${context.raw.toLocaleString(
                            undefined,
                            {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            }
                        )}`;
                    }
                },
                afterBody: function (context) {
                    const periodIndex = context[0].dataIndex;
                    const chart = context[0].chart;

                    if (!chart.paymentData || !chart.paymentData[periodIndex])
                        return [];

                    // Get all payments for this period
                    const periodData = chart.paymentData[periodIndex];
                    const payments = periodData.payments;

                    if (payments.length === 0)
                        return ["No payments in this period"];

                    // Group payments by project
                    const projectGroups = {};

                    payments.forEach((payment) => {
                        if (!projectGroups[payment.projectId]) {
                            projectGroups[payment.projectId] = {
                                projectTitle: payment.projectTitle,
                                clientName: payment.clientName,
                                payments: [],
                                total: 0,
                            };
                        }

                        projectGroups[payment.projectId].payments.push(payment);
                        projectGroups[payment.projectId].total += parseFloat(
                            payment.amount
                        );
                    });

                    // Format project-based payment details
                    const lines = [];

                    lines.push(`Total Payments: ${payments.length}`);
                    lines.push("");

                    Object.values(projectGroups).forEach((group) => {
                        lines.push(
                            `—— ${group.projectTitle} (${group.clientName}) ——`
                        );
                        lines.push(
                            `Total: $${group.total.toLocaleString(undefined, {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2,
                            })}`
                        );
                        lines.push(`Payments: ${group.payments.length}`);

                        // Add details for each payment (limit to 3 per project for brevity)
                        const showPayments = group.payments.slice(0, 3);
                        showPayments.forEach((payment) => {
                            lines.push(
                                `• $${payment.amount.toLocaleString(undefined, {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2,
                                })} - ${payment.date}`
                            );
                            lines.push(
                                `  Work: ${payment.workDescription.substring(
                                    0,
                                    30
                                )}${
                                    payment.workDescription.length > 30
                                        ? "..."
                                        : ""
                                }`
                            );
                        });

                        if (group.payments.length > 3) {
                            lines.push(
                                `• ... and ${
                                    group.payments.length - 3
                                } more payment(s)`
                            );
                        }

                        lines.push("");
                    });

                    return lines;
                },
            },
        },
        legend: {
            labels: {
                font: {
                    weight: "bold",
                },
                color: "rgb(80, 60, 50)",
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
                Revenue & Payment Overview
            </h2>
            <SelectButton
                v-model="selectedPeriod"
                :options="periodOptions"
                optionLabel="label"
                optionValue="value"
                class="p-button-rounded p-button-sm overflow-x-auto"
            />
        </div>
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
