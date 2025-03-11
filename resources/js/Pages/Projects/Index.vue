<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
    projects: Object,
    totalProjects: Number,
    totalRevenue: Number,
});

defineOptions({
    layout: AppLayout,
});

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value || 0);
};

function showProjectDetails(project) {
    router.get(route('project', { project: project.id }));
}
</script>

<template>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-100px)]"
    >
        <!-- Header with title, stats and create button -->
        <div
            class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4"
        >
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Projects</h1>
                <p class="text-gray-500 mt-1">Manage your project portfolio</p>
            </div>

            <div class="flex items-center gap-4 self-stretch sm:self-auto">
                <div
                    class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100"
                >
                    <p class="text-sm text-gray-500">Total Projects</p>
                    <p class="text-2xl font-semibold text-gray-800">
                        {{ totalProjects }}
                    </p>
                </div>
                <div
                    class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100"
                >
                    <p class="text-sm text-gray-500">Total Revenue</p>
                    <p class="text-2xl font-semibold text-emerald-600">
                        {{ formatCurrency(totalRevenue) }}
                    </p>
                </div>
                <Link
                    href="#"
                    class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white rounded-lg shadow-sm hover:bg-amber-600 transition duration-150"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-2"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                    New Project
                </Link>
            </div>
        </div>

        <!-- Search bar -->
        <div class="mb-8">
            <div class="relative max-w-md">
                <input
                    type="text"
                    placeholder="Search projects..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-amber-200 focus:border-amber-400 focus:outline-none transition duration-150"
                />
                <div
                    class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"
                >
                    <svg
                        class="h-5 w-5 text-gray-400"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                        />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Empty state -->
        <div
            v-if="projects.data.length === 0"
            class="bg-amber-50 rounded-xl p-12 text-center shadow-sm border border-amber-100"
        >
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-amber-100"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-amber-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                    />
                </svg>
            </div>
            <h3 class="mt-6 text-xl font-medium text-gray-900">
                No projects found
            </h3>
            <p class="mt-2 text-gray-500">
                Get started by creating your first project.
            </p>
            <div class="mt-6">
                <Link
                    href="#"
                    class="px-4 py-2 bg-amber-500 text-white rounded-lg shadow-sm hover:bg-amber-600 transition duration-150"
                >
                    Create Your First Project
                </Link>
            </div>
        </div>

        <!-- Projects grid -->
        <div
            v-else
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="project in projects.data"
                :key="project.id"
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition duration-200"
            >
                <div
                    v-if="project.logo"
                    class="h-40 bg-gray-50 flex items-center justify-center"
                >
                    <img
                        :src="project.logo"
                        :alt="project.title"
                        class="max-h-full max-w-full object-contain"
                    />
                </div>
                <div
                    v-else
                    class="h-40 bg-gray-50 flex items-center justify-center"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-16 w-16 text-gray-300"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
                        />
                    </svg>
                </div>

                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <h3
                            class="text-lg font-medium text-gray-800 line-clamp-1"
                        >
                            {{ project.title }}
                        </h3>
                        <div
                            class="h-8 w-8 flex items-center justify-center rounded-full bg-amber-50 text-amber-600 ml-2 flex-shrink-0"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="mb-3">
                        <Link
                            :href="route('clients', project.client.id)"
                            class="text-sm text-amber-600 hover:text-amber-700 font-medium"
                        >
                            {{ project.client.name }}
                        </Link>
                    </div>

                    <p
                        v-if="project.description"
                        class="text-sm text-gray-600 line-clamp-2 mb-4"
                    >
                        {{ project.description }}
                    </p>

                    <div class="flex flex-wrap gap-2 mb-4">
                        <span
                            v-for="(tech, index) in project.tech_stack"
                            :key="index"
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                        >
                            {{ tech }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">Works</p>
                            <p class="text-base font-medium text-gray-800">
                                {{ project.work_count }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">Revenue</p>
                            <p class="text-base font-medium text-emerald-600">
                                {{ formatCurrency(project.total_revenue) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-4 border-t border-gray-100"
                    >
                        <button
                            @click="showProjectDetails(project)"
                            class="text-sm text-amber-600 hover:text-amber-700 font-medium"
                        >
                            View Details
                        </button>
                        <div class="flex space-x-2">
                            <a
                                v-if="project.github_repo"
                                :href="project.github_repo"
                                target="_blank"
                                class="p-1 rounded-full text-gray-500 hover:text-gray-700 transition duration-150"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"
                                    />
                                </svg>
                            </a>
                            <a
                                v-if="project.live_link"
                                :href="project.live_link"
                                target="_blank"
                                class="p-1 rounded-full text-gray-500 hover:text-gray-700 transition duration-150"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"
                                    />
                                </svg>
                            </a>
                            <button
                                class="p-1 rounded-full text-gray-400 hover:text-gray-600 transition duration-150"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pagination -->
       <nav v-if="projects.data.length > 0" class="mt-10 flex justify-center">
            <div class="flex items-center space-x-1 text-sm">
                <template v-for="(link, index) in projects.links" :key="index">
                    <Link
                        v-if="link.url"
                        :preserve-scroll="true"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-md transition-colors"
                        :class="{
                            'bg-amber-500 text-white font-medium': link.active,
                            'bg-white text-gray-700 hover:bg-amber-50 hover:text-amber-600':
                                !link.active,
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === projects.links.length - 1,
                        }"
                    />
                    <p
                        v-else
                        v-html="link.label"
                        class="px-4 py-2 text-gray-500 bg-slate-200 rounded-md"
                        :class="{
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === projects.links.length - 1,
                        }"
                    />
                </template>
            </div>
        </nav>
    </div>
</template>
