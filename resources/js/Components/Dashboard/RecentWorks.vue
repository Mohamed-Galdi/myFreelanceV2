<script setup>
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import WorkStatus from "@/Components/WorkStatus.vue";

const props = defineProps({
    recentWorks: Array,
});

const getInitials = (title) => {
    return title
        .split(" ")
        .map((word) => word.charAt(0))
        .join("")
        .substring(0, 2)
        .toUpperCase();
};

const getPaymentPercentage = (work) => {
    return ((parseFloat(work.paid) / parseFloat(work.price)) * 100).toFixed(0);
};

const getProgressBarColor = (percentage) => {
    const percent = parseInt(percentage);
    if (percent < 25) return "bg-red-500";
    if (percent < 50) return "bg-yellow-500";
    if (percent < 75) return "bg-blue-500";
    return "bg-green-500";
};

const totalDuration = (startDate, endDate) => {
    if (!startDate || !endDate) return "N/A";
    const start = new Date(startDate);
    const end = new Date(endDate);
    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    if (days < 0) {
        months--;
        let lastMonth = new Date(end.getFullYear(), end.getMonth(), 0);
        days += lastMonth.getDate();
    }

    if (months < 0) {
        years--;
        months += 12;
    }

    let result = [];
    if (years > 0) result.push(`${years} ${years === 1 ? "year" : "years"}`);
    if (months > 0)
        result.push(`${months} ${months === 1 ? "month" : "months"}`);
    if (days > 0) result.push(`${days} ${days === 1 ? "day" : "days"}`);
    return result.length > 0 ? result.join(" and ") : "0 days";
};

const formatDate = (dateString) => {
    if (!dateString) return "—";
    const date = new Date(dateString);
    return date.toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
};
</script>

<template>
    <div
        class="bg-slate-50 rounded-xl shadow-lg overflow-hidden border border-gray-100"
    >
        <div
            class="px-6 py-5 flex justify-between items-center bg-gradient-to-r to-sky-100 from-blue-100"
        >
            <h2 class="text-xl font-bold text-blue-800">Recent Works</h2>
            <span
                class="bg-white text-blue-600 text-xs px-3 py-1 rounded-full shadow-sm font-medium"
                >Last 5</span
            >
        </div>

        <div v-if="recentWorks.length">
            <div
                v-for="work in recentWorks"
                :key="work.id"
                class="m-4 p-4 rounded-md shadow-sm hover:shadow-md hover:scale-[1.001] hover:border-blue-300 border border-slate-300 bg-white transition-all duration-300 ease-in-out"
            >
                <!-- Top section: Title, Status, Client, Project -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div
                            v-if="work.logo"
                            class="h-12 w-12 shadow-sm flex-shrink-0 "
                        >
                            <img
                                :src="work.logo"
                                alt="Logo"
                                class="h-full w-full object-cover border border-blue-500 rounded-lg "
                            />
                        </div>
                        <div
                            v-else
                            class="h-12 w-12 rounded-lg flex items-center justify-center text-white font-bold bg-gradient-to-br from-blue-400 to-blue-600 flex-shrink-0 shadow-sm"
                        >
                            {{ getInitials(work.title) }}
                        </div>

                        <div>
                            <Link
                                :href="route('work', work.id)"
                                class="font-semibold text-lg text-gray-800 hover:text-blue-600 transition-colors line-clamp-1"
                            >
                                {{ work.title }}
                            </Link>

                            <div
                                class="flex items-center text-sm text-gray-500 mt-0.5 gap-2"
                            >
                                <Link
                                    :href="route('project', 1)"
                                    class="hover:text-blue-600 transition-colors"
                                >
                                    {{ work.project }}
                                </Link>
                                <span class="text-gray-300">•</span>
                                <span>{{ work.client }}</span>
                            </div>
                        </div>
                    </div>

                    <WorkStatus :status="work.status" />
                </div>

                <!-- Bottom section: Grid with Payment, Dates, Duration -->
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <!-- Payment info -->
                    <div class="bg-gray-50 rounded-xl p-4 flex flex-col">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-gray-500 text-sm font-medium"
                                >Payment</span
                            >
                            <div>
                                <span class="font-bold text-gray-900"
                                    >${{ work.paid }}</span
                                >
                                <span class="text-gray-400 text-sm">/</span>
                                <span class="text-gray-600"
                                    >${{ work.price }}</span
                                >
                            </div>
                        </div>

                        <div class="mb-1">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div
                                    class="h-2 rounded-full"
                                    :class="
                                        getProgressBarColor(
                                            getPaymentPercentage(work)
                                        )
                                    "
                                    :style="{
                                        width: getPaymentPercentage(work) + '%',
                                    }"
                                ></div>
                            </div>
                        </div>

                        <div
                            class="text-right text-sm"
                            :class="{
                                'text-red-600': getPaymentPercentage(work) < 25,
                                'text-yellow-600':
                                    getPaymentPercentage(work) >= 25 &&
                                    getPaymentPercentage(work) < 50,
                                'text-blue-600':
                                    getPaymentPercentage(work) >= 50 &&
                                    getPaymentPercentage(work) < 75,
                                'text-green-600':
                                    getPaymentPercentage(work) >= 75,
                            }"
                        >
                            {{ getPaymentPercentage(work) }}% Complete
                        </div>
                    </div>

                    <!-- Date info -->
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div
                            class="flex justify-between text-sm text-gray-500 font-medium mb-2"
                        >
                            <span>Start Date</span>
                            <span>End Date</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-medium text-gray-800">{{
                                formatDate(work.startDate)
                            }}</span>
                            <span
                                class="font-medium"
                                :class="
                                    work.endDate
                                        ? 'text-gray-800'
                                        : 'text-blue-500'
                                "
                            >
                                {{
                                    work.endDate
                                        ? formatDate(work.endDate)
                                        : "In Progress"
                                }}
                            </span>
                        </div>
                    </div>

                    <!-- Duration -->
                    <div
                        class="bg-gray-50 rounded-xl p-4 flex items-center justify-between"
                    >
                        <span class="text-gray-500 text-sm font-medium"
                            >Duration</span
                        >
                        <span
                            class="font-medium text-gray-800"
                        >
                            {{ totalDuration(work.startDate, work.endDate ?? new Date()) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div
            v-else
            class="flex flex-col items-center justify-center py-16 px-6 text-center"
        >
            <div
                class="mb-4 h-20 w-20 bg-blue-50 rounded-full flex items-center justify-center"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-10 w-10 text-blue-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="1.5"
                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                    />
                </svg>
            </div>
            <h3 class="text-gray-800 font-medium text-lg">No works found</h3>
            <p class="text-gray-500 mt-1 max-w-sm">
                Your recent works will appear here once you create them
            </p>
        </div>
    </div>
</template>
