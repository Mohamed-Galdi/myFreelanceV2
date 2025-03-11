<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";


const props = defineProps({
    client: Object,
    metrics: Object,
});

defineOptions({
    layout: AppLayout,
});

const toast = useToast();

const activeTab = ref("projects");

const isMobile = computed(() => window.innerWidth <= 768);

const sources = ref([
    { name: "Upwork", value: "upwork" },
    { name: "Recommendation", value: "recommendation" },
    { name: "Baaeed", value: "baaeed" },
    { name: "Other", value: "other" },
]);

const projectStatuses = computed(() => {
    const statuses = {
        ongoing: 0,
        completed: 0,
        cancelled: 0,
    };

    props.client.projects.forEach((project) => {
        const ongoingWorks = project.works.filter(
            (work) => work.project_status === "ongoing"
        ).length;
        const completedWorks = project.works.filter(
            (work) => work.project_status === "completed"
        ).length;
        const cancelledWorks = project.works.filter(
            (work) => work.project_status === "cancelled"
        ).length;

        // Determine project status based on its works
        if (cancelledWorks === project.works.length) {
            statuses.cancelled++;
        } else if (ongoingWorks > 0) {
            statuses.ongoing++;
        } else if (completedWorks === project.works.length) {
            statuses.completed++;
        }
    });

    return statuses;
});

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value);
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

// ######################################## edit client
const openEditClientDrawer = ref(false);

const editClientForm = useForm({
    id: null,
    name: "",
    contact: "",
    source: "",
});

function showEditClientDetails(client) {
    editClientForm.id = client.id;
    editClientForm.name = client.name;
    editClientForm.contact = client.contact;
    editClientForm.source = client.source;
    openEditClientDrawer.value = true;
}

function updateClient() {
    editClientForm.post(
        route("admin.clients.update", { client: editClientForm.id }),
        {
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Client updated successfully",
                    life: 3000,
                });
                editClientForm.reset();
                openEditClientDrawer.value = false;
            },
            onError: () => {
                const errorMessage = Object.values(editClientForm.errors)[0];
                toast.add({
                    severity: "error",
                    summary: "Erreur",
                    detail: errorMessage,
                    life: 3000,
                });
            },
        }
    );
}
</script>

<template>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-100px)]"
    >
        <Toast position="top-center" />
         <!-- Edit Client Drawer -->
        <Drawer
            v-model:visible="openEditClientDrawer"
            header="Edit Client"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="updateClient"
            >
                <InputText
                    v-model="editClientForm.name"
                    name="name"
                    type="text"
                    placeholder="Client Name"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="editClientForm.contact"
                    name="contact"
                    type="text"
                    placeholder="Client Contact"
                    autofocus
                    class="w-full"
                />
                <Select
                    v-model="editClientForm.source"
                    name="source"
                    :options="sources"
                    optionLabel="name"
                    optionValue="value"
                    placeholder="Client Source"
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="editClientForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="editClientForm.processing"
                >
                    <span v-if="editClientForm.processing">processing... </span>
                    <span v-else>Update Client</span>
                </button>
            </form>
        </Drawer>
        <!-- Back button and header -->
        <div class="flex items-center mb-6">
            <Link
                :href="route('clients')"
                class="flex items-center text-gray-600 hover:text-gray-900"
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
                Back to Clients
            </Link>
        </div>

        <!-- Client header -->
        <div
            class="bg-white rounded-xl shadow-sm p-6 mb-8 border border-gray-100"
        >
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">
                        {{ client.name }}
                    </h1>
                    <p v-if="client.contact" class="text-gray-600 mt-1">
                        {{ client.contact }}
                    </p>
                    <p v-if="client.source" class="text-rose-600 text-sm mt-1">
                        Source: {{ client.source }}
                    </p>
                </div>
                <div class="mt-4 sm:mt-0 flex space-x-3">
                    <button
                        @click="showEditClientDetails(client)"
                        class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition duration-150"
                    >
                        Edit Client
                    </button>
                    <button
                        class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition duration-150"
                    >
                        New Project
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-rose-50 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-rose-500"
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
                        <div class="ml-5 w-0 flex-1">
                            <dt
                                class="text-sm font-medium text-gray-500 truncate"
                            >
                                Total Projects
                            </dt>
                            <dd class="flex items-baseline">
                                <div
                                    class="text-2xl font-semibold text-gray-900"
                                >
                                    {{ client.projects.length }}
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="p-5">
                    <div class="flex items-center">
                        <div
                            class="flex-shrink-0 bg-emerald-50 rounded-full p-3"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-emerald-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt
                                class="text-sm font-medium text-gray-500 truncate"
                            >
                                Total Revenue
                            </dt>
                            <dd class="flex items-baseline">
                                <div
                                    class="text-2xl font-semibold text-gray-900"
                                >
                                    {{ formatCurrency(client.total_revenue) }}
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-50 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-blue-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"
                                />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt
                                class="text-sm font-medium text-gray-500 truncate"
                            >
                                Paid Works
                            </dt>
                            <dd class="flex items-baseline">
                                <div
                                    class="text-2xl font-semibold text-gray-900"
                                >
                                    {{ metrics.totalPaidWorks }}
                                </div>
                                <div
                                    class="ml-2 text-sm font-medium text-emerald-600"
                                >
                                    {{
                                        formatCurrency(metrics.totalPaidRevenue)
                                    }}
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-amber-50 rounded-full p-3">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6 text-amber-500"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dt
                                class="text-sm font-medium text-gray-500 truncate"
                            >
                                Pending Works
                            </dt>
                            <dd class="flex items-baseline">
                                <div
                                    class="text-2xl font-semibold text-gray-900"
                                >
                                    {{ metrics.totalPendingWorks }}
                                </div>
                                <div
                                    class="ml-2 text-sm font-medium text-amber-600"
                                >
                                    {{
                                        formatCurrency(
                                            metrics.totalPendingRevenue
                                        )
                                    }}
                                </div>
                            </dd>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Project Status Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="px-5 py-4 bg-blue-50">
                    <h3 class="text-blue-700 text-lg font-medium">Ongoing</h3>
                </div>
                <div class="px-5 py-5">
                    <div class="text-3xl font-bold text-gray-800">
                        {{ projectStatuses.ongoing }}
                    </div>
                    <div class="text-sm text-gray-500">
                        projects in progress
                    </div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="px-5 py-4 bg-emerald-50">
                    <h3 class="text-emerald-700 text-lg font-medium">
                        Completed
                    </h3>
                </div>
                <div class="px-5 py-5">
                    <div class="text-3xl font-bold text-gray-800">
                        {{ projectStatuses.completed }}
                    </div>
                    <div class="text-sm text-gray-500">projects completed</div>
                </div>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
            >
                <div class="px-5 py-4 bg-red-50">
                    <h3 class="text-red-700 text-lg font-medium">Cancelled</h3>
                </div>
                <div class="px-5 py-5">
                    <div class="text-3xl font-bold text-gray-800">
                        {{ projectStatuses.cancelled }}
                    </div>
                    <div class="text-sm text-gray-500">projects cancelled</div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-8">
                <button
                    @click="activeTab = 'projects'"
                    :class="[
                        'py-4 px-1 text-center border-b-2 font-medium text-sm',
                        activeTab === 'projects'
                            ? 'border-rose-500 text-rose-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                    ]"
                >
                    Projects
                </button>
                <button
                    @click="activeTab = 'works'"
                    :class="[
                        'py-4 px-1 text-center border-b-2 font-medium text-sm',
                        activeTab === 'works'
                            ? 'border-rose-500 text-rose-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                    ]"
                >
                    Recent Works
                </button>
            </nav>
        </div>

        <!-- Tab content - Projects -->
        <div v-if="activeTab === 'projects'">
            <div v-if="client.projects.length === 0" class="text-center py-8">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12"
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    No projects yet
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Get started by creating a new project for this client.
                </p>
                <div class="mt-6">
                    <button
                        class="px-4 py-2 bg-rose-500 text-white rounded-lg hover:bg-rose-600 transition duration-150"
                    >
                        New Project
                    </button>
                </div>
            </div>

            <div
                v-else
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6"
            >
                <div
                    v-for="project in client.projects"
                    :key="project.id"
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition duration-200"
                >
                    <div
                        v-if="project.logo"
                        class="h-40 bg-gray-100 flex items-center justify-center"
                    >
                        <img
                            :src="project.logo"
                            :alt="project.title"
                            class="max-h-full max-w-full object-contain"
                        />
                    </div>
                    <div
                        v-else
                        class="h-40 bg-gray-100 flex items-center justify-center"
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
                        <h3 class="font-medium text-lg text-gray-800">
                            {{ project.title }}
                        </h3>
                        <p
                            v-if="project.description"
                            class="mt-1 text-gray-500 text-sm line-clamp-2"
                        >
                            {{ project.description }}
                        </p>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span
                                v-for="(tech, index) in project.tech_stack"
                                :key="index"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800"
                            >
                                {{ tech }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">Works</p>
                                <p class="text-base font-medium">
                                    {{ project.works_count }}
                                </p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Revenue</p>
                                <p
                                    class="text-base font-medium text-emerald-600"
                                >
                                    {{ formatCurrency(project.total_revenue) }}
                                </p>
                            </div>
                        </div>

                        <div
                            class="mt-5 pt-4 border-t border-gray-100 flex justify-between items-center"
                        >
                            <button
                                class="text-sm text-rose-600 hover:text-rose-700 font-medium"
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab content - Works -->
        <div v-if="activeTab === 'works'">
            <div
                v-if="!client.projects.some((p) => p.works.length > 0)"
                class="text-center py-8"
            >
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="h-12 w-12"
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
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    No works found
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    No work has been added to any project for this client yet.
                </p>
            </div>

            <div v-else>
                <div
                    class="overflow-hidden bg-white shadow-sm border border-gray-100 sm:rounded-lg"
                >
                    <ul class="divide-y divide-gray-200">
                        <li
                            v-for="project in client.projects"
                            :key="project.id"
                        >
                            <div
                                v-for="work in project.works"
                                :key="work.id"
                                class="px-6 py-5"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center mb-1">
                                            <p
                                                class="text-sm font-medium text-rose-600 truncate"
                                            >
                                                {{ project.title }}
                                            </p>
                                            <span class="mx-2 text-gray-300"
                                                >•</span
                                            >
                                            <span
                                                :class="[
                                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                                    work.project_status ===
                                                    'ongoing'
                                                        ? 'bg-blue-100 text-blue-800'
                                                        : work.project_status ===
                                                          'completed'
                                                        ? 'bg-green-100 text-green-800'
                                                        : 'bg-red-100 text-red-800',
                                                ]"
                                            >
                                                {{ work.project_status }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-900">
                                            {{ work.description }}
                                        </p>

                                        <div
                                            class="mt-2 flex items-center text-sm text-gray-500"
                                        >
                                            <span
                                                v-if="
                                                    work.start_date &&
                                                    work.end_date
                                                "
                                            >
                                                {{
                                                    formatDate(work.start_date)
                                                }}
                                                -
                                                {{ formatDate(work.end_date) }}
                                            </span>
                                            <span v-else-if="work.start_date">
                                                Started:
                                                {{
                                                    formatDate(work.start_date)
                                                }}
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="flex-shrink-0 ml-4 flex flex-col items-end"
                                    >
                                        <p
                                            class="text-lg font-medium text-gray-900"
                                        >
                                            {{ formatCurrency(work.price) }}
                                        </p>
                                        <span
                                            :class="[
                                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium mt-1',
                                                work.payment_status === 'paid'
                                                    ? 'bg-green-100 text-green-800'
                                                    : work.payment_status ===
                                                      'pending'
                                                    ? 'bg-amber-100 text-amber-800'
                                                    : work.payment_status ===
                                                      'refunded'
                                                    ? 'bg-blue-100 text-blue-800'
                                                    : 'bg-red-100 text-red-800',
                                            ]"
                                        >
                                            {{ work.payment_status }}
                                        </span>
                                        <p
                                            v-if="work.payment_method"
                                            class="mt-1 text-xs text-gray-500"
                                        >
                                            via
                                            {{
                                                work.payment_method.replace(
                                                    "_",
                                                    " "
                                                )
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
