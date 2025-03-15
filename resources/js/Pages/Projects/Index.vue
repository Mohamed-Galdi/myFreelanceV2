<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { debounce } from "lodash";
import { useConfirm } from "primevue/useconfirm";
import { ref, watch, computed } from "vue";
import FileUpload from "@/Components/FileUpload.vue";
import MultiSelect from "primevue/multiselect";

const props = defineProps({
    projects: Object,
    totalProjects: Number,
    clients: Array,
});

defineOptions({
    layout: AppLayout,
});

const toast = useToast();
const confirm = useConfirm();

// Format currency
const formatCurrency = (value) => {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(value || 0);
};

function showProjectDetails(project) {
    router.get(route("project", { project: project.id }));
}

const isMobile = computed(() => window.innerWidth <= 768);

const clients = props.clients;

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

// ######################################## projects search
const search = ref(props.searchTerm);
const debouncedSearch = ref(props.searchTerm);
watch(
    search,
    debounce((query) => {
        // Update the table
        router.get(
            route("projects"),
            {
                search: query,
            },
            {
                preserveState: true,
                preserveScroll: true,
            }
        );
        // Update the debounced search value
        debouncedSearch.value = query;
    }, 300)
);

// ######################################## create project
const openNewProjectDrawer = ref(false);

const newProjectForm = useForm({
    clientId: "",
    title: "",
    description: "",
    logo: "",
    image: "",
    tech_stack: "",
    github_repo: "",
    live_link: "",
});

function createProject() {
    newProjectForm.logo = logoTempFile.value;
    newProjectForm.image = imageTempFile.value;
    newProjectForm.post(route("admin.projects.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Succes",
                detail: "Project created successfully",
                life: 3000,
            });
            newProjectForm.reset();
            openNewProjectDrawer.value = false;
        },
        onError: () => {
            const errorMessage = Object.values(newProjectForm.errors)[0];
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}

// ######################################## edit project
const openEditProjectDrawer = ref(false);

const editProjectForm = useForm({
    id: "",
    clientId: "",
    logo: "",
    image: "",
    title: "",
    description: null,
    tech_stack: null,
    github_repo: null,
    live_link: null,
});

const projectToUpdateLogo = ref(null);
const projectToUpdateImage = ref(null);

function showEditProjectDetails(project) {
    editProjectForm.id = project.id;
    editProjectForm.clientId = project.client.id;
    projectToUpdateLogo.value = project.logo ? project.logo : null;
    projectToUpdateImage.value = project.image ? project.image : null;
    editProjectForm.title = project.title;
    editProjectForm.description = project.description
        ? project.description
        : null;
    editProjectForm.tech_stack = project.tech_stack ? project.tech_stack : null;
    editProjectForm.github_repo = project.github_repo
        ? project.github_repo
        : null;
    editProjectForm.live_link = project.live_link ? project.live_link : null;
    openEditProjectDrawer.value = true;
}

function updateProject() {
    editProjectForm.logo = logoTempFile.value;
    editProjectForm.image = imageTempFile.value;
    editProjectForm.post(
        route("admin.projects.update", { project: editProjectForm.id }),
        {
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
const deleteProject = (projectId) => {
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
            router.post(route("admin.projects.delete", { project: projectId }));
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

        <!-- Create Project Drawer -->
        <Drawer
            v-model:visible="openNewProjectDrawer"
            header="Create New Project"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="createProject"
            >
                <Select
                    v-model="newProjectForm.clientId"
                    name="clientId"
                    :options="clients"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Project Client"
                    class="w-full"
                />
                <InputText
                    v-model="newProjectForm.title"
                    name="title"
                    type="text"
                    placeholder="Project Title"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="newProjectForm.description"
                    name="description"
                    type="text"
                    placeholder="Project Description"
                    autofocus
                    class="w-full"
                />

                <div>
                    <p class="ms-1 text-sm text-slate-600">Project Logo</p>

                    <FileUpload
                        @file-uploaded="handleLogoFileUploaded"
                        @file-reverted="handleLogoFileReverted"
                    />
                </div>
                <div>
                    <p class="ms-1 text-sm text-slate-600">Project Image</p>
                    <FileUpload
                        @file-uploaded="handleImageFileUploaded"
                        @file-reverted="handleImageFileReverted"
                    />
                </div>
                <MultiSelect
                    v-model="newProjectForm.tech_stack"
                    :options="techStacks"
                    optionLabel="label"
                    optionValue="value"
                    placeholder="Tech Stack"
                    class="w-full"
                />
                <InputText
                    v-model="newProjectForm.github_repo"
                    name="github_repo"
                    type="text"
                    placeholder="Project Github Repo"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="newProjectForm.live_link"
                    name="live_link"
                    type="text"
                    placeholder="Project Live Link"
                    autofocus
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="newProjectForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="newProjectForm.processing"
                >
                    <span v-if="newProjectForm.processing">processing... </span>
                    <span v-else>Create Project</span>
                </button>
            </form>
        </Drawer>

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
                        :initial-file="projectToUpdateLogo"
                        @file-uploaded="handleLogoFileUploaded"
                        @file-reverted="handleLogoFileReverted"
                    />
                </div>
                <div>
                    <p class="ms-1 text-sm text-slate-600">Project Image</p>
                    <FileUpload
                        :initial-file="projectToUpdateImage"
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

        <!-- Header with title, stats and create button -->
        <div
            class="grid md:grid-cols-3 mb-8 place-items-center gap-4 grid-cols-2"
        >
            <div class="w-full">
                <h1 class="text-3xl font-bold text-gray-800">Projects</h1>
                <p class="text-gray-500 mt-1">Manage client's projects</p>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100 w-fit"
            >
                <p class="text-sm text-gray-500">Total Projects</p>
                <p class="text-2xl font-semibold text-gray-800 text-center">
                    {{ totalProjects }}
                </p>
            </div>

            <div
                class="flex items-center gap-4 self-stretch sm:self-auto md:justify-end justify-start w-full"
            >
                <button
                    @click="openNewProjectDrawer = true"
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
                </button>
            </div>
        </div>

        <!-- Search bar -->
        <div class="mb-8">
            <div class="relative max-w-md">
                <InputText
                    v-model="search"
                    name="search"
                    type="text"
                    placeholder="Search projects"
                    class="w-[20vw]"
                />
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
                class="bg-slate-100 rounded-xl shadow-sm overflow-hidden border border-slate-200 hover:shadow-md transition duration-200"
            >
                <div v-if="project.image" class="h-40 shrink-0 overflow-hidden">
                    <img
                        :src="project.image"
                        :alt="project.title"
                        class="project-image w-full h-[200px] rounded-lg object-cover object-top"
                    />
                </div>
                <div
                    v-else
                    class="h-40 bg-white items-center justify-center flex flex-col"
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

                <div class="p-5">
                    <div class="flex items-start justify-between mb-2">
                        <div class="flex items-center gap-2 h-10">
                            <div v-if="project.logo" class="">
                                <img
                                    :src="project.logo"
                                    :alt="project.title"
                                    class="w-10 h-10 rounded-full border-slate-300 border-2 bg-white"
                                />
                            </div>
                            <h3
                                class="text-lg font-medium text-gray-800 line-clamp-1"
                            >
                                {{ project.title }}
                            </h3>
                        </div>
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

                    <div class="mb-1">
                        Client:
                        <Link
                            :href="route('client', project.client.id)"
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
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white text-gray-800 border border-blue-900"
                        >
                            {{ tech }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div class="bg-white rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">Works</p>
                            <p class="text-base font-medium text-gray-800">
                                {{ project.total_works }}
                            </p>
                        </div>
                        <div class="bg-white rounded-lg px-3 py-2">
                            <p class="text-xs text-gray-500">Revenue</p>
                            <p class="text-base font-medium text-emerald-600">
                                {{ formatCurrency(project.total_payments) }}
                            </p>
                        </div>
                    </div>

                    <div
                        class="flex items-center justify-between pt-4 border-t border-slate-300"
                    >
                        <button
                            @click="showProjectDetails(project)"
                            class="text-sm text-amber-600 hover:text-amber-700 font-medium"
                        >
                            View Details
                        </button>
                        <div class="flex space-x-3">
                            <button
                                @click="showEditProjectDetails(project)"
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
                                @click="deleteProject(project.id)"
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
                            'bg-amber-100 text-amber-700 hover:bg-amber-50 hover:text-amber-600':
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
