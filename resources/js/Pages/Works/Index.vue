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
    works: Object,
    totalWorks: Number,
    totalRevenue: String,
    pendingRevenue: String,
    projects: Array,
});

defineOptions({
    layout: AppLayout,
});

const isMobile = computed(() => window.innerWidth <= 768);

const toast = useToast();
const confirm = useConfirm();

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

function totalDuration(startDate, endDate) {
    if (!startDate || !endDate) return "N/A";

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

function showWorkDetails(work) {
    router.get(route("work", { work: work.id }));
}

// ######################################## create work
const openNewWorkDrawer = ref(false);

const newWorkForm = useForm({
    projectId: "",
    description: "",
    price: "",
    startDate: "",
    endDate: "",
});

function createWork() {
    newWorkForm.post(route("admin.works.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Succes",
                detail: "Work created successfully",
                life: 3000,
            });
            newWorkForm.reset();
            openNewWorkDrawer.value = false;
        },
        onError: () => {
            const errorMessage = Object.values(newWorkForm.errors)[0];
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}

// ######################################## edit work
const openEditWorkDrawer = ref(false);

const workStatuses = ref(["Ongoing", "Completed", "Cancelled"]);

const editWorkForm = useForm({
    id: null,
    projectId: null,
    description: null,
    price: null,
    startDate: null,
    endDate: null,
    status: null,
});

function showEditWorkDetails(work) {
    editWorkForm.id = work.id;
    editWorkForm.projectId = work.project.id;
    editWorkForm.description = work.description ? work.description : null;
    editWorkForm.price = work.price ? work.price : null;
    editWorkForm.startDate = work.start_date ? work.start_date : null;
    editWorkForm.endDate = work.end_date ? work.end_date : null;
    editWorkForm.status = work.status;
    openEditWorkDrawer.value = true;
}

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
const deleteWork = (workId) => {
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
            router.post(route("admin.works.delete", { work: workId }));
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

        <!-- Create Project Drawer -->
        <Drawer
            v-model:visible="openNewWorkDrawer"
            header="Create New Project"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="createWork"
            >
                <Select
                    v-model="newWorkForm.projectId"
                    name="project_id"
                    :options="projects"
                    optionLabel="title"
                    optionValue="id"
                    placeholder="Project"
                    class="w-full"
                />
                <InputText
                    v-model="newWorkForm.description"
                    name="description"
                    type="text"
                    placeholder="Work Description"
                    autofocus
                    class="w-full"
                />
                <InputNumber
                    v-model="newWorkForm.price"
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
                    v-model="newWorkForm.startDate"
                    name="startDate"
                    placeholder="Start Date"
                    class="w-full"
                />
                <DatePicker
                    v-model="newWorkForm.endDate"
                    name="endDate"
                    placeholder="End Date"
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="newWorkForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="newWorkForm.processing"
                >
                    <span v-if="newWorkForm.processing">processing... </span>
                    <span v-else>Create Work</span>
                </button>
            </form>
        </Drawer>

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

        <!-- Header with title, stats and create button -->
        <div
            class="grid md:grid-cols-3 mb-8 place-items-center gap-4 grid-cols-2"
        >
            <div class="w-full">
                <h1 class="text-3xl font-bold text-gray-800">Works</h1>
                <p class="text-gray-500 mt-1">Manage your work and income</p>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100 w-fit"
            >
                <p class="text-sm text-gray-500">Total Works</p>
                <p class="text-2xl font-semibold text-gray-800 text-center">
                    {{ totalWorks }}
                </p>
            </div>

            <div
                class="flex items-center gap-4 self-stretch sm:self-auto md:justify-end justify-start w-full"
            >
                <button
                    @click="openNewWorkDrawer = true"
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
                </button>
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
                            <h3
                                class="text-lg font-medium text-gray-800 line-clamp-1"
                            >
                                {{ formatCurrency(work.price) }}
                            </h3>
                        </div>
                        <WorkStatus :status="work.status" />
                    </div>

                    <p
                        class="text-sm text-gray-600 line-clamp-2 my-5 border border-slate-200 rounded-md p-1 shadow-sm"
                    >
                        {{ work.description }}
                    </p>

                    <div class="mb-4">
                        <Link
                            :href="route('project', work.project.id)"
                            class="text-sm text-blue-600 hover:text-blue-700 font-medium"
                        >
                            {{ work.project.title }}
                        </Link>
                        <p class="text-xs text-gray-500 mt-1">
                            Client: {{ work.project.client.name }}
                        </p>
                    </div>

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
                        <div
                            class="bg-gray-50 rounded-lg px-3 col-span-2 py-2 text-center"
                        >
                            <p class="text-xs text-gray-500">Total Duration</p>
                            <p
                                class="text-sm font-medium text-gray-800"
                            >
                                {{
                                    totalDuration(
                                        work.start_date,
                                        work.end_date
                                    )
                                }}
                            </p>
                        </div>
                    </div>

                    <!-- Payment History -->
                    <div>payments: TODO</div>

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
                                @click="showEditWorkDetails(work)"
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
                            <button
                                @click="deleteWork(work.id)"
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
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>
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
