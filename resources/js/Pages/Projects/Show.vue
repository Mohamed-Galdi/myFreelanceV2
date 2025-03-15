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
import FileUpload from "@/Components/FileUpload.vue";
import MultiSelect from "primevue/multiselect";
import WorkStatus from "@/Components/WorkStatus.vue";

const props = defineProps({
    project: Object,
    clients: Array,
});

defineOptions({
    layout: AppLayout,
});

const isMobile = computed(() => window.innerWidth <= 768);

const clients = props.clients;

const project = props.project;

const techStacks = [
    { label: "React", value: "react" },
    { label: "Vue", value: "vue" },
    { label: "Laravel", value: "laravel" },
    { label: "PHP", value: "php" },
    { label: "JavaScript", value: "javascript" },
    { label: "Tailwind CSS", value: "tailwindcss" },
];

// ######################################## file upload
// logo
const logoTempFile = ref(null);
function handleLogoFileUploaded(fileFolder) {
    logoTempFile.value = fileFolder;
}

function handleLogoFileReverted() {
    logoTempFile.value = null;
}

// image
const imageTempFile = ref(null);
function handleImageFileUploaded(fileFolder) {
    imageTempFile.value = fileFolder;
}

function handleImageFileReverted() {
    imageTempFile.value = null;
}

const toast = useToast();
const confirm = useConfirm();

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

// ######################################## edit project
const openEditProjectDrawer = ref(false);

const editProjectForm = useForm({
    id: project.id,
    clientId: project.client_id,
    logo: null,
    image: null,
    title: project.title,
    description: project.description ? project.description : null,
    tech_stack: project.tech_stack ? project.tech_stack : null,
    github_repo: project.github_repo ? project.github_repo : null,
    live_link: project.live_link ? project.live_link : null,
});

const projectLogo = project.logo ?? null;
const projectImage = project.image ?? null;

function updateProject() {
    editProjectForm.logo = logoTempFile.value;
    editProjectForm.image = imageTempFile.value;
    editProjectForm.post(
        route("admin.projects.update", { project: editProjectForm.id }),
        {
            preserveState: false,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Project updated successfully",
                    life: 3000,
                });
                editProjectForm.reset();
                openEditProjectDrawer.value = false;
            },
            onError: () => {
                const errorMessage = Object.values(editProjectForm.errors)[0];
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

// ######################################## delete project
const deleteProject = () => {
    confirm.require({
        group: "templating",
        message: "Are you sure you want to delete this project?",
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
            router.post(
                route("admin.projects.delete", { project: project.id })
            );
            setTimeout(() => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Project deleted successfully",
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
        <!-- Edit Project Drawer -->
        <Drawer
            v-model:visible="openEditProjectDrawer"
            header="Edit Project"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="updateProject"
            >
                <Select
                    v-model="editProjectForm.clientId"
                    name="clientId"
                    :options="clients"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Project Client"
                    class="w-full"
                />
                <InputText
                    v-model="editProjectForm.title"
                    name="title"
                    type="text"
                    placeholder="Project Title"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="editProjectForm.description"
                    name="description"
                    type="text"
                    placeholder="Project Description"
                    autofocus
                    class="w-full"
                />

                <div>
                    <p class="ms-1 text-sm text-slate-600">Project Logo</p>

                    <FileUpload
                        :initial-file="project.logo ? `/${project.logo}` : null"
                        @file-uploaded="handleLogoFileUploaded"
                        @file-reverted="handleLogoFileReverted"
                    />
                </div>
                <div>
                    <p class="ms-1 text-sm text-slate-600">Project Image</p>
                    <FileUpload
                        :initial-file="
                            project.image ? `/${project.image}` : null
                        "
                        @file-uploaded="handleImageFileUploaded"
                        @file-reverted="handleImageFileReverted"
                    />
                </div>
                <MultiSelect
                    v-model="editProjectForm.tech_stack"
                    :options="techStacks"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Tech Stack"
                    class="w-full"
                />
                <InputText
                    v-model="editProjectForm.github_repo"
                    name="github_repo"
                    type="text"
                    placeholder="Project Github Repo"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="editProjectForm.live_link"
                    name="live_link"
                    type="text"
                    placeholder="Project Live Link"
                    autofocus
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="editProjectForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="editProjectForm.processing"
                >
                    <span v-if="editProjectForm.processing"
                        >processing...
                    </span>
                    <span v-else>Update Project</span>
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
                        <p>Are you sure you want to delete this project?</p>
                    </div>
                </div>
            </template>
        </ConfirmDialog>

        <!-- top buttons -->
        <div class="flex items-center justify-between mb-8">
            <!-- Back button -->
            <Link
                :href="route('projects')"
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
                    @click="openEditProjectDrawer = true"
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
                    @click="deleteProject()"
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Project info card -->
            <div
                class="lg:col-span-2 bg-slate-50 rounded-xl shadow-sm overflow-hidden border border-slate-200"
            >
                <!-- Image -->
                <div v-if="project.image" class="shrink-0 overflow-hidden">
                    <img
                        :src="`/${project.image}`"
                        :alt="project.title"
                        class="project-image w-full h-[250px] object-cover object-top"
                    />
                </div>
                <div
                    v-else
                    class="h-[250px] bg-white items-center justify-center flex flex-col"
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
                    <small class="text-sm text-slate-400">No image</small>
                </div>

                <div class="p-6 space-y-6">
                   <div class="flex justify-between">
                        <!-- Logo and title -->
                        <div class="flex items-center gap-4">
                            <img
                                v-if="project.logo"
                                :src="`/${project.logo}`"
                                :alt="project.title"
                                class="w-10 h-10 rounded-full border-slate-300 border-2 bg-white"
                            />
                            <h2 class="text-xl font-semibold text-gray-800">
                                {{ project.title }}
                            </h2>
                        </div>
                        <!-- Links -->
                        <div class="flex items-center gap-2">
                            <a
                                v-if="project.github_repo"
                                :href="project.github_repo"
                                target="_blank"
                                class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition duration-150"
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
                                class="p-2 rounded-full text-gray-500 hover:text-gray-700 hover:bg-gray-50 transition duration-150"
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
                    <!-- Client info -->
                    <div class="">
                        <Link
                            :href="route('client', project.client.id)"
                            class="text-lg text-amber-600 hover:text-amber-700 font-medium flex items-center"
                        >
                            <h3 class="text-sm font-medium text-gray-500 me-1">
                                Client:
                            </h3>
                            <span>{{ project.client.name }}</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 ml-1"
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
                        </Link>
                    </div>

                    <!-- Description -->
                    <div class="">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">
                            Description
                        </h3>
                        <p class="text-gray-700 whitespace-pre-line">
                            {{
                                project.description || "No description provided"
                            }}
                        </p>
                    </div>

                    <!-- Tech stack -->
                    <div class="">
                        <h3 class="text-sm font-medium text-gray-500 mb-2">
                            Tech Stack
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="(tech, index) in project.tech_stack"
                                :key="index"
                                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-white text-gray-800 border border-blue-900"
                            >
                                {{ tech }}
                            </span>
                            <span
                                v-if="
                                    !project.tech_stack ||
                                    project.tech_stack.length === 0
                                "
                                class="text-gray-500 italic"
                                >No tech stack specified</span
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats sidebar -->
            <div class="space-y-8">
                <!-- Revenue card -->
                <div
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div class="p-6 bg-teal-50 border-b border-teal-100">
                        <h2 class="text-xl font-semibold text-teal-800">
                            Project Revenue
                        </h2>
                    </div>
                    <div class="p-6">
                        <div class="text-center">
                            <p class="text-3xl font-bold text-emerald-600 mb-2">
                                {{ formatCurrency(project.total_revenue) }}
                            </p>
                            <p class="text-gray-500">Total Revenue</p>
                        </div>
                    </div>
                </div>

                <!-- Works list -->
                <div
                    class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100"
                >
                    <div class="p-6 border-b border-gray-100 bg-blue-100">
                        <h2 class="text-xl font-semibold text-gray-800">
                            Work Items
                        </h2>
                    </div>

                    <div
                        v-if="project.works.length === 0"
                        class="p-6 text-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-12 w-12 mx-auto text-gray-400"
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
                        <p class="mt-4 text-gray-600">
                            No work items found for this project
                        </p>
                    </div>

                    <div v-else class="divide-y divide-gray-100">
                        <div
                            v-for="work in project.works"
                            :key="work.id"
                            class="p-6 hover:bg-gray-50 transition duration-150"
                        >
                            <div class="flex items-start justify-between">
                                <Link :href="route('work', work.id)" class="text-blue-700 mb-3">
                                    {{ work.description }}
                                </Link>
                                <WorkStatus :status="work.status" />
                            </div>
                            <p class="text-lg font-medium text-gray-800">
                                {{ formatCurrency(work.price) }}
                            </p>

                            <div
                                class="flex items-center gap-4 text-sm text-gray-500"
                            >
                                <span
                                    v-if="work.start_date || work.end_date"
                                    class="flex items-center"
                                >
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        class="h-4 w-4 mr-1"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
                                        />
                                    </svg>
                                    {{ formatDate(work.start_date) }} -
                                    {{ formatDate(work.end_date) }} (
                                    {{
                                        totalDuration(
                                            work.start_date,
                                            work.end_date
                                        )
                                    }}
                                    )
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
