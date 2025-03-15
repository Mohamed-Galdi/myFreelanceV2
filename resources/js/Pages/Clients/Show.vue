<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, useForm, router } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import WorkStatus from "@/Components/WorkStatus.vue";

const props = defineProps({
    client: Object,
    metrics: Object,
});

defineOptions({
    layout: AppLayout,
});

const toast = useToast();
const confirm = useConfirm();

const client = props.client;

const activeTab = ref("projects");

const isMobile = computed(() => window.innerWidth <= 768);

const sources = ref(["Upwork", "Recommendation", "Baaeed", "Other"]);

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

function totalDuration(startDate, endDate) {
    if (!startDate || !endDate) return "N/A";
    if (startDate === endDate) return "1 day";

    const start = new Date(startDate);
    const end = new Date(endDate);

    let years = end.getFullYear() - start.getFullYear();
    let months = end.getMonth() - start.getMonth();
    let days = end.getDate() - start.getDate();

    // Adjust for negative days (if end day is before start day)
    if (days < 0) {
        months--; // Reduce one month
        let lastMonth = new Date(end.getFullYear(), end.getMonth(), 0); // Last day of the previous month
        days += lastMonth.getDate();
    }

    // Adjust for negative months (if end month is before start month in the same year)
    if (months < 0) {
        years--;
        months += 12;
    }

    // Formatting the result
    let result = [];
    if (years > 0) result.push(`${years} ${years === 1 ? "year" : "years"}`);
    if (months > 0)
        result.push(`${months} ${months === 1 ? "month" : "months"}`);
    if (days > 0) result.push(`${days} ${days === 1 ? "day" : "days"}`);

    return result.length > 0 ? result.join(" and ") : "0 days";
}

// ######################################## edit client
const openEditClientDrawer = ref(false);

const editClientForm = useForm({
    id: client.id,
    name: client.name,
    contact: client.contact,
    source: client.source,
});

function updateClient() {
    editClientForm.post(
        route("admin.clients.update", { client: editClientForm.id }),
        {
            preserveState: false,
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

// ######################################## delete client
function deleteClient() {
    confirm.require({
        group: "templating",
        message: "Are you sure you want to delete this client?",
        header: "Confirm Deletion",
        rejectProps: {
            label: "Cancel",
            severity: "secondary",
            outlined: true,
        },
        acceptProps: {
            label: "Delete",
            severity: "danger",
        },
        accept: () => {
            router.post(route("admin.clients.delete", { client: client.id }));
            setTimeout(() => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Client deleted successfully",
                    life: 3000,
                });
            }, 500);
        },
    });
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

        <!-- Confirm Dialog -->
        <ConfirmDialog group="templating" class="w-full md:w-1/2 lg:w-1/3 mx-8">
            <template #message="slotProps">
                <div class="flex flex-col items-center justify-center w-full">
                    <div class="bg-rose-500 rounded-full p-3 mb-4">
                        <svg
                            class="h-8 w-8 text-white"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                            />
                        </svg>
                    </div>
                    <div class="w-full">
                        <h2
                            class="text-xl font-bold text-gray-800 mb-4 text-center"
                        >
                            Confirm Deletion
                        </h2>
                        <p>Are you sure you want to delete this client?</p>
                    </div>
                </div>
            </template>
        </ConfirmDialog>

        <!-- Back button and header -->
        <div class="mb-6">
            <Link
                :href="route('clients')"
                class="inline-flex items-center justify-center p-2 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition duration-150"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5 text-gray-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M15 19l-7-7 7-7"
                    />
                </svg>
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
                <!-- Edit / Delete buttons -->
                <div class="flex items-center gap-2">
                    <!-- Edit button -->
                    <button
                        @click="openEditClientDrawer = true"
                        class="w-full flex items-center justify-center px-4 py-2 bg-white border border-amber-300 text-amber-700 rounded-lg shadow-sm hover:bg-amber-50 transition duration-150"
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
                        <p class="text-nowrap">Edit Project</p>
                    </button>
                    <!-- Delete button -->
                    <button
                        @click="deleteClient()"
                        class="w-full flex items-center justify-center px-4 py-2 bg-white border border-red-300 text-red-700 rounded-lg shadow-sm hover:bg-red-50 transition duration-150"
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
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                            />
                        </svg>
                        <p class="text-nowrap">Delete Project</p>
                    </button>
                </div>
            </div>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div
                class="bg-white shadow-md rounded-2xl p-5 flex flex-col items-center text-center border border-gray-200"
            >
                <i class="pi pi-desktop text-4xl text-amber-500 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-700">Projects</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ metrics.totalProjects }}
                </p>
            </div>

            <div
                class="bg-white shadow-md rounded-2xl p-5 flex flex-col items-center text-center border border-gray-200"
            >
                <i class="pi pi-file text-4xl text-blue-500 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-700">Works</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ metrics.totalWorks }}
                </p>
            </div>

            <div
                class="bg-white shadow-md rounded-2xl p-5 flex flex-col items-center text-center border border-gray-200"
            >
                <i class="pi pi-wallet text-4xl text-green-500 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-700">Payments</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ formatCurrency(metrics.totalRevenue) }}
                </p>
            </div>

            <div
                class="bg-white shadow-md rounded-2xl p-5 flex flex-col items-center text-center border border-gray-200"
            >
                <i class="pi pi-clock text-4xl text-yellow-500 mb-3"></i>
                <h3 class="text-lg font-semibold text-gray-700">Pending</h3>
                <p class="text-2xl font-bold text-gray-900">
                    {{ formatCurrency(metrics.pendingAmount) }}
                </p>
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
                <button
                    @click="activeTab = 'payments'"
                    :class="[
                        'py-4 px-1 text-center border-b-2 font-medium text-sm',
                        activeTab === 'payments'
                            ? 'border-rose-500 text-rose-600'
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                    ]"
                >
                    Payments
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
                        v-if="project.image"
                        class="h-40 bg-gray-100 flex items-center justify-center"
                    >
                        <img
                            :src="`/${project.image}`"
                            :alt="project.title"
                            class="h-40 object-cover object-top w-full"
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
                            <Link
                                :href="route('project', project.id)"
                                class="text-sm text-rose-600 hover:text-rose-700 font-medium"
                            >
                                View Details
                            </Link>
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
                                        <p
                                            class="text-sm font-medium text-rose-600 truncate"
                                        >
                                            {{ project.title }}
                                        </p>
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
                                        <div>
                                            <span
                                                class="text-sm text-gray-500 me-1"
                                                >Duration:</span
                                            >
                                            <span
                                                class="text-sm text-gray-800"
                                                >{{
                                                    totalDuration(
                                                        work.start_date,
                                                        work.end_date
                                                    )
                                                }}</span
                                            >
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
                                        <p
                                            v-if="work.remaining_amount > 0"
                                            class="text-orange-500 text-sm"
                                        >
                                            remaining:
                                            {{
                                                formatCurrency(
                                                    work.remaining_amount
                                                )
                                            }}
                                        </p>
                                        <p
                                            v-else
                                            class="text-green-700 text-sm font-medium"
                                        >
                                            paid
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Tab content - Payments -->
        <div v-if="activeTab === 'payments'">
            <div v-if="client.projects.length === 0" class="text-center py-8">
                <div class="mx-auto h-12 w-12 text-gray-400">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 640 512"
                        class="text-[#9da3af]"
                    >
                        <path class="fill-current"
                            d="M535 41c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.4 33.9 0l64 64c4.5 4.5 7 10.6 7 17s-2.5 12.5-7 17l-64 64c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l23-23L384 112c-13.3 0-24-10.7-24-24s10.7-24 24-24l174.1 0L535 41zM105 377l-23 23L256 400c13.3 0 24 10.7 24 24s-10.7 24-24 24L81.9 448l23 23c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0L7 441c-4.5-4.5-7-10.6-7-17s2.5-12.5 7-17l64-64c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9zM96 64l241.9 0c-3.7 7.2-5.9 15.3-5.9 24c0 28.7 23.3 52 52 52l117.4 0c-4 17 .6 35.5 13.8 48.8c20.3 20.3 53.2 20.3 73.5 0L608 169.5 608 384c0 35.3-28.7 64-64 64l-241.9 0c3.7-7.2 5.9-15.3 5.9-24c0-28.7-23.3-52-52-52l-117.4 0c4-17-.6-35.5-13.8-48.8c-20.3-20.3-53.2-20.3-73.5 0L32 342.5 32 128c0-35.3 28.7-64 64-64zm64 64l-64 0 0 64c35.3 0 64-28.7 64-64zM544 320c-35.3 0-64 28.7-64 64l64 0 0-64zM320 352a96 96 0 1 0 0-192 96 96 0 1 0 0 192z"
                        />
                    </svg>
                </div>
                <h3 class="mt-2 text-sm font-medium text-gray-900">
                    No payments yet
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    Get started by creating a new project for this client.
                </p>
            </div>
            <div v-else>
                <div v-for="project in client.projects" :key="project.id">
                    <h2 class="text-xl font-bold text-blue-900 mt-8">
                        {{ project.title }}
                    </h2>

                    <div
                        v-for="work in project.works"
                        :key="work.id"
                        class="mt-3 p-4 bg-blue-50 rounded-lg shadow-md border border-blue-200"
                    >
                        <h3 class="text-lg font-semibold text-gray-800">
                            {{ work.description }}
                        </h3>
                        <p class="text-gray-600">
                            Total Price:
                            <span class="font-bold text-blue-600"
                                >$ {{ work.price }}</span
                            >
                        </p>
                        <p class="text-gray-600">
                            Status:
                            <span
                                :class="
                                    work.status === 'Completed'
                                        ? 'text-green-600'
                                        : 'text-yellow-600'
                                "
                            >
                                {{ work.status }}
                            </span>
                        </p>

                        <div class="mt-3 space-y-3">
                            <div
                                v-for="payment in work.payments"
                                :key="payment.id"
                                class="p-4 bg-white rounded-lg shadow hover:shadow-md transition-all border border-gray-200"
                            >
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <i
                                            class="pi pi-wallet text-blue-500 text-lg"
                                        ></i>
                                        <div>
                                            <p
                                                class="text-gray-800 font-medium"
                                            >
                                                Amount:
                                                <span
                                                    class="text-blue-700 font-bold"
                                                    >$
                                                    {{ payment.amount }}</span
                                                >
                                            </p>
                                            <p class="text-sm text-gray-500">
                                                Date:
                                                {{
                                                    formatDate(
                                                        payment.payment_date
                                                    )
                                                }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm text-gray-500">
                                            Method:
                                            <span class="font-semibold">{{
                                                payment.payment_method
                                            }}</span>
                                        </p>
                                        <p
                                            v-if="payment.note"
                                            class="text-xs text-gray-600 italic"
                                        >
                                            Note: "{{ payment.note }}"
                                        </p>
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
