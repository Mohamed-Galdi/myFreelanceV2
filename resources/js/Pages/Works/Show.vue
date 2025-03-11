<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    work: Object,
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

// Format date
const formatDate = (dateString) => {
    if (!dateString) return "N/A";
    const date = new Date(dateString);
    return new Intl.DateTimeFormat("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    }).format(date);
};

// Get status color class
const getStatusColor = (status) => {
    switch (status) {
        case "ongoing":
            return "bg-blue-100 text-blue-800";
        case "completed":
            return "bg-green-100 text-green-800";
        case "cancelled":
            return "bg-red-100 text-red-800";
        case "paid":
            return "bg-emerald-100 text-emerald-800";
        case "pending":
            return "bg-amber-100 text-amber-800";
        case "refunded":
            return "bg-purple-100 text-purple-800";
        default:
            return "bg-gray-100 text-gray-800";
    }
};

// Format duration between start and end date
const formatDuration = () => {
    if (!props.work.start_date || !props.work.end_date) return "N/A";

    const start = new Date(props.work.start_date);
    const end = new Date(props.work.end_date);
    const diffTime = Math.abs(end - start);
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));

    return diffDays === 1 ? "1 day" : `${diffDays} days`;
};
</script>

<template>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-100px)]"
    >
        <!-- Header with back button and actions -->
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center">
                <Link
                    :href="route('works')"
                    class="flex items-center text-gray-600 hover:text-blue-600 mr-4"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-5 w-5 mr-1"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />
                    </svg>
                    Back to Works
                </Link>
                <h1 class="text-2xl font-bold text-gray-800 ml-2">
                    Work Details
                </h1>
            </div>
            <div class="flex space-x-3">
                <Link
                    :href="route('works', { work: work.id })"
                    class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150"
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
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"
                        />
                    </svg>
                    Edit
                </Link>
                <button
                    v-if="work.payment_status === 'pending'"
                    class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white rounded-lg hover:bg-emerald-600 transition duration-150"
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
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                    Mark as Paid
                </button>
            </div>
        </div>

        <!-- Main content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left column - Work details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Work card -->
                <div
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center"
                    >
                        <h2 class="text-xl font-semibold text-gray-800">
                            Work Information
                        </h2>
                        <div class="flex items-center space-x-2">
                            <span
                                :class="[
                                    getStatusColor(work.project_status),
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                ]"
                            >
                                {{
                                    work.project_status
                                        .charAt(0)
                                        .toUpperCase() +
                                    work.project_status.slice(1)
                                }}
                            </span>
                            <span
                                :class="[
                                    getStatusColor(work.payment_status),
                                    'px-3 py-1 rounded-full text-sm font-medium',
                                ]"
                            >
                                {{
                                    work.payment_status
                                        .charAt(0)
                                        .toUpperCase() +
                                    work.payment_status.slice(1)
                                }}
                            </span>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-1">
                                {{ formatCurrency(work.price) }}
                            </h3>
                            <div class="flex items-center mt-2">
                                <Link
                                    :href="route('projects', work.project.id)"
                                    class="text-blue-600 hover:text-blue-700 font-medium"
                                >
                                    {{ work.project.title }}
                                </Link>
                                <span class="mx-2 text-gray-400">•</span>
                                <Link
                                    :href="
                                        route('clients', work.project.client.id)
                                    "
                                    class="text-gray-600 hover:text-gray-800"
                                >
                                    {{ work.project.client.name }}
                                </Link>
                            </div>
                        </div>

                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">
                                Description
                            </h4>
                            <p class="text-gray-700 whitespace-pre-line">
                                {{ work.description }}
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 mb-2"
                                >
                                    Date Information
                                </h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Start Date
                                            </p>
                                            <p
                                                class="text-sm font-medium text-gray-800"
                                            >
                                                {{
                                                    formatDate(work.start_date)
                                                }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                End Date
                                            </p>
                                            <p
                                                class="text-sm font-medium text-gray-800"
                                            >
                                                {{ formatDate(work.end_date) }}
                                            </p>
                                        </div>
                                        <div class="col-span-2">
                                            <p class="text-xs text-gray-500">
                                                Duration
                                            </p>
                                            <p
                                                class="text-sm font-medium text-gray-800"
                                            >
                                                {{ formatDuration() }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <h4
                                    class="text-sm font-medium text-gray-500 mb-2"
                                >
                                    Payment Information
                                </h4>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="col-span-2">
                                            <p class="text-xs text-gray-500">
                                                Amount
                                            </p>
                                            <p
                                                class="text-lg font-bold"
                                                :class="
                                                    work.payment_status ===
                                                    'paid'
                                                        ? 'text-emerald-600'
                                                        : 'text-amber-600'
                                                "
                                            >
                                                {{ formatCurrency(work.price) }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">
                                                Status
                                            </p>
                                            <p
                                                class="text-sm font-medium"
                                                :class="
                                                    work.payment_status ===
                                                    'paid'
                                                        ? 'text-emerald-600'
                                                        : 'text-amber-600'
                                                "
                                            >
                                                {{
                                                    work.payment_status
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    work.payment_status.slice(1)
                                                }}
                                            </p>
                                        </div>
                                        <div v-if="work.payment_method">
                                            <p class="text-xs text-gray-500">
                                                Method
                                            </p>
                                            <p
                                                class="text-sm font-medium text-gray-800"
                                            >
                                                {{
                                                    work.payment_method
                                                        .replace("_", " ")
                                                        .charAt(0)
                                                        .toUpperCase() +
                                                    work.payment_method
                                                        .replace("_", " ")
                                                        .slice(1)
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-100 pt-4 mt-6">
                            <div
                                class="flex justify-between items-center text-sm text-gray-500"
                            >
                                <div>
                                    Created: {{ formatDate(work.created_at) }}
                                </div>
                                <div>
                                    Last Updated:
                                    {{ formatDate(work.updated_at) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right column - Related info -->
            <div class="space-y-6">
                <!-- Project card -->
                <div
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Project Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-4">
                            <h3 class="font-medium text-lg text-gray-800 mb-1">
                                {{ work.project.title }}
                            </h3>
                            <p class="text-sm text-gray-600 line-clamp-2">
                                {{ work.project.description }}
                            </p>
                        </div>

                        <div
                            v-if="
                                work.project.tech_stack &&
                                work.project.tech_stack.length > 0
                            "
                            class="mb-4"
                        >
                            <h4 class="text-sm font-medium text-gray-500 mb-2">
                                Tech Stack
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tech in work.project.tech_stack"
                                    :key="tech"
                                    class="px-2 py-1 bg-blue-50 text-blue-700 rounded-full text-xs"
                                >
                                    {{ tech }}
                                </span>
                            </div>
                        </div>

                        <div class="flex space-x-3 mt-4">
                            <Link
                                :href="route('projects', work.project.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150 text-sm"
                            >
                                View Project
                            </Link>
                            <a
                                v-if="work.project.github_repo"
                                :href="work.project.github_repo"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-lg hover:bg-gray-700 transition duration-150 text-sm"
                            >
                                <svg
                                    class="h-4 w-4 mr-2"
                                    fill="currentColor"
                                    viewBox="0 0 24 24"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        clip-rule="evenodd"
                                        d="M12 2C6.477 2 2 6.477 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.91-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.87 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0012 2z"
                                    ></path>
                                </svg>
                                GitHub
                            </a>
                            <a
                                v-if="work.project.live_link"
                                :href="work.project.live_link"
                                target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition duration-150 text-sm"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 mr-2"
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
                                Live Site
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Client card -->
                <div
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Client Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <h3 class="font-medium text-lg text-gray-800 mb-1">
                            {{ work.project.client.name }}
                        </h3>

                        <div class="mt-4 space-y-3">
                            <div v-if="work.project.client.contact">
                                <p class="text-sm text-gray-500">Contact</p>
                                <p class="text-sm text-gray-800">
                                    {{ work.project.client.contact }}
                                </p>
                            </div>
                            <div v-if="work.project.client.source">
                                <p class="text-sm text-gray-500">Source</p>
                                <p class="text-sm text-gray-800">
                                    {{ work.project.client.source }}
                                </p>
                            </div>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500">
                                        Projects
                                    </p>
                                    <p
                                        class="text-lg font-semibold text-blue-600"
                                    >
                                        {{ work.project.client.projects_count }}
                                    </p>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3">
                                    <p class="text-xs text-gray-500">
                                        Total Revenue
                                    </p>
                                    <p
                                        class="text-lg font-semibold text-emerald-600"
                                    >
                                        {{
                                            formatCurrency(
                                                work.project.client
                                                    .total_revenue
                                            )
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <Link
                                :href="route('clients', work.project.client.id)"
                                class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150 text-sm"
                            >
                                View Client
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related works section -->
        <div class="mt-8">
            <Link
                :href="route('works', { project: work.project.id })"
                class="flex items-center text-blue-600 hover:text-blue-700 mb-4"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 mr-1"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M19 9l-7 7-7-7"
                    />
                </svg>
                View all works for this project
            </Link>
        </div>
    </div>
</template>
