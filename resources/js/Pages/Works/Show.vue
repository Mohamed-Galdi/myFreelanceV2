<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import { ref, computed } from "vue";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";
import WorkStatus from "@/Components/WorkStatus.vue";

const props = defineProps({
    work: Object,
    projects: Array,
    worksOfSameProject: Array,
});

defineOptions({
    layout: AppLayout,
});

const isMobile = computed(() => window.innerWidth <= 768);

const toast = useToast();
const confirm = useConfirm();

const work = props.work;

const projects = props.projects;

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

// ######################################## edit work
const openEditWorkDrawer = ref(false);

const workStatuses = ref(["Ongoing", "Completed", "Cancelled"]);

const editWorkForm = useForm({
    id: work.id,
    projectId: work.project_id,
    description: work.description ? work.description : null,
    price: work.price ? work.price : null,
    startDate: work.start_date ? work.start_date : null,
    endDate: work.end_date ? work.end_date : null,
    status: work.status,
});

function updateWork() {
    editWorkForm.post(route("admin.works.update", { work: editWorkForm.id }), {
        preserveState: false,
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Succes",
                detail: "Work updated successfully",
                life: 3000,
            });
            editWorkForm.reset();
            openEditWorkDrawer.value = false;
        },
        onError: () => {
            const errorMessage = Object.values(editWorkForm.errors)[0];
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}

// ######################################## delete work
const deleteWork = () => {
    confirm.require({
        group: "templating",
        message: "Are you sure you want to delete this work?",
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
            router.post(route("admin.works.delete", { work: work.id }));
            setTimeout(() => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Work deleted successfully",
                    life: 3000,
                });
            }, 500);
        },
    });
};
</script>

<template>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-100px)]"
    >
        <Toast position="top-center" />

        <!-- Edit Work Drawer -->
        <Drawer
            v-model:visible="openEditWorkDrawer"
            header="Edit Work"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="updateWork"
            >
                <Select
                    v-model="editWorkForm.status"
                    name="project_id"
                    :options="workStatuses"
                    placeholder="Work status"
                    class="w-full"
                />
                <Select
                    v-model="editWorkForm.projectId"
                    name="project_id"
                    :options="projects"
                    optionLabel="title"
                    optionValue="id"
                    placeholder="Project"
                    class="w-full"
                />
                <InputText
                    v-model="editWorkForm.description"
                    name="description"
                    type="text"
                    placeholder="Work Description"
                    autofocus
                    class="w-full"
                />
                <InputNumber
                    v-model="editWorkForm.price"
                    inputId="currency-us"
                    mode="currency"
                    currency="USD"
                    locale="en-US"
                    fluid
                    name="price"
                    placeholder="Price"
                    class="w-full"
                />
                <DatePicker
                    v-model="editWorkForm.startDate"
                    name="startDate"
                    placeholder="Start Date"
                    class="w-full"
                    dateFormat="dd/mm/yy"
                />
                <DatePicker
                    v-model="editWorkForm.endDate"
                    name="endDate"
                    placeholder="End Date"
                    class="w-full"
                    dateFormat="dd/mm/yy"
                />
                <button
                    class="w-full"
                    :class="editWorkForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="editWorkForm.processing"
                >
                    <span v-if="editWorkForm.processing">processing... </span>
                    <span v-else>Update Work</span>
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
                        <p>Are you sure you want to delete this work?</p>
                    </div>
                </div>
            </template>
        </ConfirmDialog>

        <!-- Header with back button and actions -->
        <div class="flex items-center justify-between mb-8">
            <!-- Back button -->
            <Link
                :href="route('works')"
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
            <!-- Edit / Delete buttons -->
            <div class="flex items-center gap-2">
                <!-- Edit button -->
                <button
                    @click="openEditWorkDrawer = true"
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
                    <p class="text-nowrap">Edit Work</p>
                </button>
                <!-- Delete button -->
                <button
                    @click="deleteWork()"
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
                    <p class="text-nowrap">Delete Work</p>
                </button>
            </div>
        </div>

        <!-- Main content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left column - Work details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Work card -->
                <div
                    class="bg-slate-100 rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-blue-50 flex justify-between items-center"
                    >
                        <h2 class="text-xl font-semibold text-gray-800">
                            Work Information
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="mb-6">
                            <div
                                class="flex items-center space-x-2 w-full justify-between"
                            >
                                <h3
                                    class="text-2xl font-bold text-gray-800 mb-1"
                                >
                                    {{ formatCurrency(work.price) }}
                                </h3>
                                <WorkStatus :status="work.status" />
                            </div>
                            <div class="flex items-center mt-2">
                                <Link
                                    :href="route('project', work.project.id)"
                                    class="text-gray-600 hover:text-blue-700 font-medium"
                                >
                                    {{ work.project.title }}
                                </Link>
                                <span class="mx-2 text-gray-400">•</span>
                                <Link
                                    :href="
                                        route('client', work.project.client.id)
                                    "
                                    class="text-gray-600 hover:text-blue-700"
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

                        <div class="grid grid-cols-3 gap-4 mb-6">
                            <div class="bg-white rounded-lg p-4">
                                <p class="text-xs text-gray-500">Start Date</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ formatDate(work.start_date) }}
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <p class="text-xs text-gray-500">End Date</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ formatDate(work.end_date) }}
                                </p>
                            </div>
                            <div class="bg-white rounded-lg p-4">
                                <p class="text-xs text-gray-500">Duration</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{
                                        totalDuration(
                                            work.start_date,
                                            work.end_date
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl p-4 flex flex-col">
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
                                <div
                                    class="w-full bg-gray-200 rounded-full h-2"
                                >
                                    <div
                                        class="h-2 rounded-full"
                                        :class="
                                            getProgressBarColor(
                                                getPaymentPercentage(work)
                                            )
                                        "
                                        :style="{
                                            width:
                                                getPaymentPercentage(work) +
                                                '%',
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <div
                                class="text-right text-sm"
                                :class="{
                                    'text-red-600':
                                        getPaymentPercentage(work) < 25,
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
                <!-- Payments card -->
                <div
                    class="bg-slate-100 rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div
                        class="px-6 py-4 border-b border-gray-100 bg-green-50 flex justify-between items-center"
                    >
                        <h2 class="text-xl font-semibold text-gray-800">
                            Payments
                        </h2>
                    </div>
                    <div class="p-6 space-y-3">
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
                                        <p class="text-gray-800 font-medium">
                                            Amount:
                                            <span
                                                class="text-blue-700 font-bold"
                                                >$ {{ payment.amount }}</span
                                            >
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            Date:
                                            {{
                                                formatDate(payment.payment_date)
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
                            <p class="text-sm text-gray-600 line-clamp-2 mb-4">
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
                            <div class="flex flex-wrap gap-2">
                                <span
                                    v-for="tech in work.project.tech_stack"
                                    :key="tech"
                                    class="px-2 py-1 bg-white text-blue-900 border border-blue-900 rounded-full text-xs"
                                >
                                    {{ tech }}
                                </span>
                            </div>
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

                        <div class="mt-4 space-y-3 flex justify-between">
                            <div v-if="work.project.client.source">
                                <p class="text-sm text-gray-500">Source</p>
                                <p class="text-sm text-gray-800">
                                    {{ work.project.client.source }}
                                </p>
                            </div>
                            <div v-if="work.project.client.contact">
                                <p class="text-sm text-gray-500">Contact</p>
                                <p class="text-sm text-gray-800">
                                    {{ work.project.client.contact }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Works Of The Same Project -->
        <div class="my-6">
            <p class="text-xl font-bold text-gray-800 mb-4 ms-2">
                Works Of The Same Project:
            </p>
            <!-- Empty state -->
            <div
                v-if="worksOfSameProject.length === 0"
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
                    This is the only work item for this project
                </h3>
            </div>

            <!-- Works grid -->
            <div
                v-else
                class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <div
                    v-for="work in worksOfSameProject"
                    :key="work.id"
                    class="bg-slate-100 rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition duration-200"
                >
                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <div>
                                <h3
                                    class="text-lg font-medium text-gray-800 line-clamp-1"
                                >
                                    {{ formatCurrency(work.price) }}
                                </h3>
                            </div>
                            <WorkStatus :status="work.status" />
                        </div>

                        <p
                            class="text-sm text-gray-600 bg-white line-clamp-2 my-5 border border-slate-200 rounded-md p-1 shadow-sm"
                        >
                            {{ work.description }}
                        </p>

                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div class="bg-white rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500">Start Date</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ formatDate(work.start_date) }}
                                </p>
                            </div>
                            <div class="bg-white rounded-lg px-3 py-2">
                                <p class="text-xs text-gray-500">End Date</p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{ formatDate(work.end_date) }}
                                </p>
                            </div>
                            <div
                                class="bg-white rounded-lg px-3 col-span-2 py-2 text-center"
                            >
                                <p class="text-xs text-gray-500">
                                    Total Duration
                                </p>
                                <p class="text-sm font-medium text-gray-800">
                                    {{
                                        totalDuration(
                                            work.start_date,
                                            work.end_date
                                        )
                                    }}
                                </p>
                            </div>
                        </div>

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
                                <div
                                    class="w-full bg-gray-200 rounded-full h-2"
                                >
                                    <div
                                        class="h-2 rounded-full"
                                        :class="
                                            getProgressBarColor(
                                                getPaymentPercentage(work)
                                            )
                                        "
                                        :style="{
                                            width:
                                                getPaymentPercentage(work) +
                                                '%',
                                        }"
                                    ></div>
                                </div>
                            </div>

                            <div
                                class="text-right text-sm"
                                :class="{
                                    'text-red-600':
                                        getPaymentPercentage(work) < 25,
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

                        <div
                            class="flex items-center justify-between pt-4 border-t border-gray-100"
                        >
                            <Link
                                :href="route('work', work.id)"
                                class="text-sm text-blue-600 hover:text-blue-700 font-bold"
                            >
                                View
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
