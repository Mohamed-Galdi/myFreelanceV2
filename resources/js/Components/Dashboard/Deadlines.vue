<script setup>
import { computed } from "vue";
import { Calendar, Clock, AlertCircle } from "lucide-vue-next";

const props = defineProps({
    upcomingWorks: Array,
    overdueWorks: Array,
});

const allDeadlineWorks = computed(() => {
    const allWorks = [...props.upcomingWorks, ...props.overdueWorks];
    return allWorks.sort((a, b) => new Date(a.end_date) - new Date(b.end_date));
});

const isOverdue = (endDate) => new Date(endDate) < new Date();

const getDaysFromNow = (endDate) => {
    const diffTime = new Date(endDate).getTime() - new Date().getTime();
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

const groupedDeadlines = computed(() => {
    const grouped = {};
    allDeadlineWorks.value.forEach((work) => {
        const date = new Date(work.end_date);
        const month = date.toLocaleString("default", { month: "long" });
        const year = date.getFullYear();
        const day = date.getDate();
        if (!grouped[month]) grouped[month] = {};
        if (!grouped[month][day]) grouped[month][day] = [];
        grouped[month][day].push(work);
    });
    return grouped;
});
</script>

<template>
    <div class="p-6 bg-white shadow-lg rounded-2xl">
        <!-- Header -->
        <div class="flex justify-between items-center mb-6">
            <h2
                class="text-2xl font-bold text-gray-800 flex items-center gap-2"
            >
                <Calendar class="w-6 h-6 text-sky-600" /> Deadlines Calendar
            </h2>
            <div class="flex gap-4 items-center">
                <span class="flex items-center text-sm text-gray-700">
                    <span class="w-3 h-3 rounded-full bg-sky-500 mr-2"></span>
                    Upcoming
                </span>
                <span class="flex items-center text-sm text-gray-700">
                    <span class="w-3 h-3 rounded-full bg-rose-500 mr-2"></span>
                    Overdue
                </span>
            </div>
        </div>

        <!-- Empty State -->
        <div
            v-if="allDeadlineWorks.length === 0"
            class="flex flex-col items-center justify-center py-12 text-gray-500"
        >
            <AlertCircle class="w-12 h-12 text-gray-400 mb-4" />
            <p>No upcoming or overdue deadlines</p>
        </div>

        <!-- Deadlines List -->
        <div v-else>
            <div
                v-for="(monthData, month, year) in groupedDeadlines"
                :key="month"
                class="mb-8"
            >
                <h3 class="text-xl font-bold text-gray-800 mb-4 border-b pb-2">
                    {{ month }}
                </h3>
                <div
                    class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"
                >
                    <div
                        v-for="(dayWorks, day) in monthData"
                        :key="`${month}-${day}`"
                        class="bg-gray-50 p-4 rounded-xl shadow-sm hover:shadow-md transition"
                    >
                        <div class="flex justify-between items-center mb-3">
                            <h4 class="text-xl font-bold text-gray-700">
                                {{ day }}
                            </h4>
                            <div class="flex gap-1">
                                <span
                                    v-if="
                                        dayWorks.some(
                                            (w) => !isOverdue(w.end_date)
                                        )
                                    "
                                    class="bg-sky-100 text-sky-600 px-2 py-1 text-xs rounded-full"
                                >
                                    {{
                                        dayWorks.filter(
                                            (w) => !isOverdue(w.end_date)
                                        ).length
                                    }}
                                    Upcoming
                                </span>
                                <span
                                    v-if="
                                        dayWorks.some((w) =>
                                            isOverdue(w.end_date)
                                        )
                                    "
                                    class="bg-rose-100 text-rose-600 px-2 py-1 text-xs rounded-full"
                                >
                                    {{
                                        dayWorks.filter((w) =>
                                            isOverdue(w.end_date)
                                        ).length
                                    }}
                                    Overdue
                                </span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div
                                v-for="work in dayWorks"
                                :key="work.id"
                                class="p-3 rounded-lg border-l-4"
                                :class="
                                    isOverdue(work.end_date)
                                        ? 'border-rose-500 bg-rose-50'
                                        : 'border-sky-500 bg-sky-50'
                                "
                            >
                                <div class="font-semibold text-gray-800">
                                    {{ work.project.title }}
                                </div>
                                <div class="text-xs text-gray-600">
                                    {{ work.project.client.name }}
                                </div>
                                <div
                                    class="flex justify-between items-center mt-2 text-sm font-medium"
                                >
                                    <div class="text-gray-700">
                                        ${{ work.price.toLocaleString() }}
                                    </div>
                                    <div
                                        class="flex items-center gap-1 text-xs px-2 py-1 rounded-full"
                                        :class="
                                            isOverdue(work.end_date)
                                                ? 'bg-rose-100 text-rose-700'
                                                : 'bg-sky-100 text-sky-700'
                                        "
                                    >
                                        <Clock class="w-4 h-4" />
                                        {{
                                            isOverdue(work.end_date)
                                                ? `Overdue by ${Math.abs(
                                                      getDaysFromNow(
                                                          work.end_date
                                                      )
                                                  )} days`
                                                : `Due in ${getDaysFromNow(
                                                      work.end_date
                                                  )} days`
                                        }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.card {
    transition: all 0.3s;
}
.card:hover {
    transform: translateY(-3px);
}
</style>
