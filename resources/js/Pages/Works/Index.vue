<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
    works: Object,
    totalWorks: Number,
    totalRevenue: Number,
    pendingRevenue: Number,
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

function showWorkDetails(work) {
    router.get(route("work", { work: work.id }));
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
                <h1 class="text-3xl font-bold text-gray-800">Works</h1>
                <p class="text-gray-500 mt-1">Manage your work and income</p>
            </div>

            <div class="flex items-center gap-4 self-stretch sm:self-auto">
                <div
                    class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100"
                >
                    <p class="text-sm text-gray-500">Total Works</p>
                    <p class="text-2xl font-semibold text-gray-800">
                        {{ totalWorks }}
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
                    class="inline-flex items-center justify-center px-4 py-2 bg-blue-500 text-white rounded-lg shadow-sm hover:bg-blue-600 transition duration-150"
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
                    New Work
                </Link>
            </div>
        </div>

        <!-- Search bar -->
        <div class="mb-8">
            <div class="relative max-w-md">
                <input
                    type="text"
                    placeholder="Search works..."
                    class="w-full pl-10 pr-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-blue-200 focus:border-blue-400 focus:outline-none transition duration-150"
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
            v-if="works.data.length === 0"
            class="bg-blue-50 rounded-xl p-12 text-center shadow-sm border border-blue-100"
        >
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-blue-100"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-blue-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                    />
                </svg>
            </div>
            <h3 class="mt-6 text-xl font-medium text-gray-900">
                No works found
            </h3>
            <p class="mt-2 text-gray-500">
                Get started by creating your first work item.
            </p>
            <div class="mt-6">
                <Link
                    href="#"
                    class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow-sm hover:bg-blue-600 transition duration-150"
                >
                    Create Your First Work
                </Link>
            </div>
        </div>

        <!-- Works grid -->
        <div
            v-else
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="work in works.data"
                :key="work.id"
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition duration-200"
            >
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <span
                                :class="[
                                    getStatusColor(work.project_status),
                                    'px-2.5 py-0.5 rounded-full text-xs font-medium inline-block mb-2',
                                ]"
                            >
                                {{
                                    work.project_status
                                        .charAt(0)
                                        .toUpperCase() +
                                    work.project_status.slice(1)
                                }}
                            </span>
                            <h3
                                class="text-lg font-medium text-gray-800 line-clamp-1"
                            >
                                {{ formatCurrency(work.price) }}
                            </h3>
                        </div>
                        <div
                            class="h-8 w-8 flex items-center justify-center rounded-full bg-blue-50 text-blue-600 ml-2 flex-shrink-0"
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
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="mb-3">
                        <span
                            :class="[
                                getStatusColor(work.payment_status),
                                'px-2.5 py-0.5 rounded-full text-xs font-medium mr-2',
                            ]"
                        >
                            {{
                                work.payment_status.charAt(0).toUpperCase() +
                                work.payment_status.slice(1)
                            }}
                        </span>
                        <span
                            v-if="work.payment_method"
                            class="text-xs bg-gray-100 text-gray-800 px-2.5 py-0.5 rounded-full"
                        >
                            {{
                                work.payment_method
                                    .replace("_", " ")
                                    .charAt(0)
                                    .toUpperCase() +
                                work.payment_method.replace("_", " ").slice(1)
                            }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <Link
                            :href="route('projects', work.project.id)"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            {{ work.project.title }}
                        </Link>
                        <p class="text-xs text-gray-500 mt-1">
                            Client: {{ work.project.client.name }}
                        </p>
                    </div>

                    <p class="text-sm text-gray-600 line-clamp-2 mb-4">
                        {{ work.description }}
                    </p>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">Start Date</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ formatDate(work.start_date) }}
                            </p>
                        </div>
                        <div class="bg-gray-50 rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">End Date</p>
                            <p class="text-sm font-medium text-gray-800">
                                {{ formatDate(work.end_date) }}
                            </p>
                        </div>
                    </div>

                    <div class="relative">
                        <button
                            @click="work.showPayments = !work.showPayments"
                            class="text-sm text-gray-600 hover:text-gray-800 flex items-center"
                        >
                            Payment History
                            <svg
                                class="w-4 h-4 ml-1"
                                xmlns="http://www.w3.org/2000/svg"
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
                        </button>

                        <div
                            v-if="work.showPayments"
                            class="absolute bg-white shadow-lg rounded-lg p-3 w-56 mt-2 border border-gray-100"
                        >
                            <div v-if="work.payments.length > 0">
                                <div
                                    v-for="payment in work.payments"
                                    :key="payment.id"
                                    class="border-b py-2 last:border-none"
                                >
                                    <p
                                        class="text-sm font-medium text-gray-800"
                                    >
                                        {{ formatCurrency(payment.amount) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ formatDate(payment.paid_at) }} ·
                                        <span
                                            :class="[
                                                getStatusColor(payment.status),
                                            ]"
                                            class="px-2 py-0.5 rounded-full text-xs"
                                        >
                                            {{ payment.status }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <p v-else class="text-gray-500 text-sm">
                                No payments recorded.
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-4 border-t border-gray-100"
                    >
                        <button
                            @click="showWorkDetails(work)"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            View Details
                        </button>
                        <div class="flex space-x-2">
                            <button
                                class="p-1 rounded-full text-gray-400 hover:text-gray-600 transition duration-150"
                                title="Mark as Paid"
                                v-if="work.payment_status === 'pending'"
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
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                                    />
                                </svg>
                            </button>
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

        <!-- Payment Details Dropdown -->

        <!-- Additional stats card (for pending revenue) -->
        <div
            v-if="works.data.length > 0"
            class="mt-8 bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
        >
            <div class="p-6 bg-blue-50 border-b border-blue-100">
                <h2 class="text-xl font-semibold text-blue-800">
                    Pending Revenue
                </h2>
            </div>
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <p class="text-gray-600">Total pending payments</p>
                    <p class="text-2xl font-bold text-amber-600">
                        {{ formatCurrency(pendingRevenue) }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <nav v-if="works.data.length > 0" class="mt-10 flex justify-center">
            <div class="flex items-center space-x-1 text-sm">
                <template v-for="(link, index) in works.links" :key="index">
                    <Link
                        v-if="link.url"
                        :preserve-scroll="true"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-md transition-colors"
                        :class="{
                            'bg-blue-500 text-white font-medium': link.active,
                            'bg-white text-gray-700 hover:bg-blue-50 hover:text-blue-600':
                                !link.active,
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === works.links.length - 1,
                        }"
                    />
                    <p
                        v-else
                        v-html="link.label"
                        class="px-4 py-2 text-gray-500 bg-slate-200 rounded-md"
                        :class="{
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === works.links.length - 1,
                        }"
                    />
                </template>
            </div>
        </nav>
    </div>
</template>
