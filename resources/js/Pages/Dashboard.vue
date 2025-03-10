<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Cards from "@/Components/Dashboard/Cards.vue";
import Chart from "@/Components/Dashboard/Chart.vue";
import WorkStatus from "@/Components/Dashboard/WorkStatus.vue";
import Deadlines from "@/Components/Dashboard/Deadlines.vue";

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    summary: Object,
    workStats: Object,
    paymentStats: Object,
    chartData: Object,
    recentProjects: Array,
    upcomingWorks: Array,
    overdueWorks: Array,
});
</script>

<template>
    <div class="max-w-7xl mx-auto p-4">
        
        <!-- Summary Cards -->
        <Cards :summary="summary" />

        <!-- Chart Section -->
        <Chart :chartData="chartData" />

        <!-- Work Stats & Recent Projects -->
        <WorkStatus
            :workStats="workStats"
            :paymentStats="paymentStats"
            :recentProjects="recentProjects"
        />

        <!-- Combined Work Deadlines Calendar -->
        <Deadlines
            :upcomingWorks="upcomingWorks"
            :overdueWorks="overdueWorks"
        />
    </div>
</template>

<style scoped>
</style>
