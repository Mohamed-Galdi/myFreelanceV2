<script setup>
import { computed } from "vue";

const props = defineProps({
    workStats: Object,
    paymentStats: Object,
    recentProjects: Array,
});

const workTotal = computed(() => Math.max(1, props.workStats.total));
const workPercentages = computed(() => ({
    completed: (props.workStats.completed / workTotal.value) * 100,
    ongoing: (props.workStats.ongoing / workTotal.value) * 100,
    cancelled: (props.workStats.cancelled / workTotal.value) * 100,
}));

const getInitials = (title) => {
    return title
        .split(" ")
        .map((word) => word.charAt(0))
        .join("")
        .substring(0, 2)
        .toUpperCase();
};
</script>

<template>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Work Status Card -->
        <div
            class="p-6 rounded-xl bg-gradient-to-br from-white to-orange-50 shadow-lg border border-orange-200"
        >
            <h2 class="text-xl font-bold text-orange-900 mb-6">Work Status</h2>
            <div
                v-for="(percentage, key) in workPercentages"
                :key="key"
                class="mb-6"
            >
                <div class="flex justify-between mb-2">
                    <span class="font-medium capitalize text-orange-800">
                        {{ key }} ({{ workStats[key] }})
                    </span>
                    <span class="font-medium text-orange-600"
                        >{{ percentage.toFixed(0) }}%</span
                    >
                </div>
                <div
                    class="h-3 w-full bg-orange-100 rounded-full overflow-hidden"
                >
                    <div
                        class="h-full rounded-full transition-all"
                        :class="{
                            'bg-emerald-500': key === 'completed',
                            'bg-amber-500': key === 'ongoing',
                            'bg-red-400': key === 'cancelled',
                        }"
                        :style="`width: ${percentage}%`"
                    ></div>
                </div>
            </div>
            <!-- Payment Status -->
            <div class="mt-8 pt-6 border-t border-orange-300">
                <h3 class="text-lg font-bold text-orange-900 mb-4">
                    Payment Status
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div
                        v-for="(count, status) in paymentStats"
                        :key="status"
                        class="flex flex-col items-center bg-opacity-75 p-3 rounded-lg shadow-md"
                        :class="{
                            'bg-emerald-100 text-emerald-700':
                                status === 'paid',
                            'bg-amber-100 text-amber-700': status === 'pending',
                            'bg-sky-100 text-sky-700': status === 'refunded',
                            'bg-red-100 text-red-700': status === 'cancelled',
                        }"
                    >
                        <span class="font-medium capitalize">{{ status }}</span>
                        <span class="text-2xl font-bold">{{ count }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Projects Section -->
        <div
            class="p-6 rounded-xl bg-gradient-to-br from-white to-blue-50 shadow-lg border border-blue-200 lg:col-span-2"
        >
            <h2 class="text-xl font-bold text-blue-900 mb-6">
                Recent Projects
            </h2>
            <div v-if="recentProjects.length" class="space-y-4">
                <div
                    v-for="project in recentProjects"
                    :key="project.id"
                    class="p-5 bg-white rounded-lg shadow hover:shadow-md transition-all border border-blue-200"
                >
                    <div class="flex items-start gap-4">
                        <!-- Project Logo -->
                        <div v-if="project.logo" class="flex-shrink-0">
                            <img
                                :src="project.logo"
                                alt="Logo"
                                class="h-12 w-12 object-contain rounded-lg shadow"
                            />
                        </div>
                        <div
                            v-else
                            class="flex-shrink-0 h-12 w-12 rounded-lg flex items-center justify-center text-white font-bold bg-blue-400"
                        >
                            {{ getInitials(project.title) }}
                        </div>
                        <!-- Project Details -->
                        <div class="flex-grow">
                            <div class="flex justify-between">
                                <h3 class="font-bold text-lg text-blue-900">
                                    {{ project.title }}
                                </h3>
                                <span
                                    class="text-lg font-bold text-emerald-600"
                                >
                                    ${{
                                        project.total_revenue.toLocaleString()
                                    }}
                                </span>
                            </div>
                            <div class="text-sm text-gray-600">
                                {{ project.client.name }}
                            </div>
                            <div class="mt-2 flex justify-between items-center">
                                <span
                                    class="text-xs bg-sky-100 text-sky-700 px-2 py-1 rounded-full"
                                >
                                    {{ project.work_count }} works
                                </span>
                                <div
                                    class="text-xs text-gray-500 flex items-center"
                                >
                                    <i class="pi pi-calendar mr-1"></i>
                                    <span>{{
                                        new Date(
                                            project.updated_at ||
                                                project.created_at
                                        ).toLocaleDateString()
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- No Projects Found -->
            <div
                v-else
                class="flex flex-col items-center justify-center py-12 text-gray-500"
            >
                <i class="pi pi-folder-open text-4xl mb-4"></i>
                <p>No projects found</p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.text-orange-900 {
    color: #8a4b08;
}

.text-blue-900 {
    color: #1e3a5f;
}
</style>
