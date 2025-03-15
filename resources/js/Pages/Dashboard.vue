<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Cards from "@/Components/Dashboard/Cards.vue";
import Chart from "@/Components/Dashboard/Chart.vue";
import RecentWorks from "@/Components/Dashboard/RecentWorks.vue";

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    summary: Object,
    chartData: Object,
    recentWorks: Array,

});
</script>

<template>
    <div class="max-w-7xl mx-auto py-8 md:px-0 px-4">
        <!-- Summary Cards -->
        <Cards :summary="summary" />

        <!-- Chart Section -->
        <Chart :chartData="chartData" />

        <!-- Work Stats & Recent Projects -->
        <RecentWorks
            :recentWorks="recentWorks"
        />
    </div>
</template>

<style scoped></style>
