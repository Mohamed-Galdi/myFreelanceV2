<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import { debounce } from "lodash";
import { ref, watch, computed } from "vue";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";

const props = defineProps({
    clients: Object,
    totalClients: Number,
});

defineOptions({
    layout: AppLayout,
});

const toast = useToast();
const confirm = useConfirm();

function showClientDetails(client) {
    router.get(route("client", { client: client.id }));
}

const isMobile = computed(() => window.innerWidth <= 768);

const sources = ref([
    { name: "Upwork", value: "upwork" },
    { name: "Recommendation", value: "recommendation" },
    { name: "Baaeed", value: "baaeed" },
    { name: "Other", value: "other" },
]);

// ######################################## search clients
const search = ref(props.searchTerm);
const debouncedSearch = ref(props.searchTerm);
watch(
    search,
    debounce((query) => {
        // Update the table
        router.get(
            route("clients"),
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
// ######################################## create client
const openNewClientDrawer = ref(false);

const newClientForm = useForm({
    name: "",
    contact: "",
    source: "",
});

function createClient() {
    newClientForm.post(route("admin.clients.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Succes",
                detail: "Client created successfully",
                life: 3000,
            });
            newClientForm.reset();
            openNewClientDrawer.value = false;
        },
        onError: () => {
            const errorMessage = Object.values(newClientForm.errors)[0];
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}

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

// ######################################## delete client
const deleteClient = (clientId) => {
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
            router.post(route("admin.clients.delete", { client: clientId }));
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
};
</script>

<template>
    <div
        class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 min-h-[calc(100vh-100px)]"
    >
        <Toast position="top-center" />
        <!-- Create Client Drawer -->
        <Drawer
            v-model:visible="openNewClientDrawer"
            header="Create New Client"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="createClient"
            >
                <InputText
                    v-model="newClientForm.name"
                    name="name"
                    type="text"
                    placeholder="Client Name"
                    autofocus
                    class="w-full"
                />
                <InputText
                    v-model="newClientForm.contact"
                    name="contact"
                    type="text"
                    placeholder="Client Contact"
                    autofocus
                    class="w-full"
                />
                <Select
                    v-model="newClientForm.source"
                    name="source"
                    :options="sources"
                    optionLabel="name"
                    optionValue="value"
                    placeholder="Client Source"
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="newClientForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="newClientForm.processing"
                >
                    <span v-if="newClientForm.processing">processing... </span>
                    <span v-else>Create Client</span>
                </button>
            </form>
        </Drawer>

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

        <!-- Header with title, stats and create button -->
        <div
            class="grid md:grid-cols-3 mb-8 place-items-center gap-4 grid-cols-2"
        >
            <div class="w-full">
                <h1 class="text-3xl font-bold text-gray-800">Clients</h1>
                <p class="text-gray-500 mt-1">
                    Manage your client relationships
                </p>
            </div>

            <div
                class="bg-white rounded-xl shadow-sm px-5 py-3 border border-gray-100 w-fit"
            >
                <p class="text-sm text-gray-500">Total Clients</p>
                <p class="text-2xl font-semibold text-gray-800 text-center">
                    {{ totalClients }}
                </p>
            </div>

            <div
                class="flex items-center gap-4 self-stretch sm:self-auto md:justify-end justify-start w-full"
            >
                <button
                    @click="openNewClientDrawer = true"
                    class="inline-flex items-center justify-center px-4 py-2 bg-rose-500 text-white rounded-lg shadow-sm hover:bg-rose-600 transition duration-150"
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
                    New Client
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
                    placeholder="Search clients"
                    class="w-[20vw]"
                />
            </div>
        </div>

        <!-- Empty state -->
        <div
            v-if="clients.data.length === 0"
            class="bg-rose-50 rounded-xl p-12 text-center shadow-sm border border-rose-100"
        >
            <div
                class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-rose-100"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-12 w-12 text-rose-600"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        stroke-width="2"
                        d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"
                    />
                </svg>
            </div>
            <h3 class="mt-6 text-xl font-medium text-gray-900">
                No clients found
            </h3>
            <p class="mt-2 text-gray-500">
                Get started by adding your first client to track projects and
                revenue.
            </p>
            <div class="mt-6">
                <Link
                    href="#"
                    class="px-4 py-2 bg-rose-500 text-white rounded-lg shadow-sm hover:bg-rose-600 transition duration-150"
                >
                    Add Your First Client
                </Link>
            </div>
        </div>

        <!-- Clients grid -->
        <div
            v-else
            class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3"
        >
            <div
                v-for="client in clients.data"
                :key="client.id"
                class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100 hover:shadow-md transition duration-200"
            >
                <div class="p-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                {{ client.name }}
                            </h3>
                            <p
                                v-if="client.contact"
                                class="text-sm text-gray-500 mt-1"
                            >
                                {{ client.contact }}
                            </p>
                            <p
                                v-if="client.source"
                                class="text-sm text-rose-600 mt-1"
                            >
                                Source: {{ client.source }}
                            </p>
                        </div>
                        <div
                            class="h-12 w-12 flex items-center justify-center rounded-full bg-rose-50 text-rose-600"
                        >
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-6 w-6"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"
                                />
                            </svg>
                        </div>
                    </div>

                    <div class="mt-6 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Projects</span>
                            <span class="text-lg font-medium text-gray-800">{{
                                client.projects_count || 0
                            }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500"
                                >Total Revenue</span
                            >
                            <span class="text-lg font-medium text-emerald-600"
                                >${{
                                    Number(
                                        client.total_revenue || 0
                                    ).toLocaleString()
                                }}</span
                            >
                        </div>
                    </div>

                    <div
                        class="mt-5 pt-5 border-t border-gray-100 flex justify-between items-center"
                    >
                        <button
                            @click="showClientDetails(client)"
                            class="text-sm text-rose-600 hover:text-rose-700 font-medium"
                        >
                            View Details
                        </button>
                        <div class="flex space-x-3">
                            <button
                                @click="showEditClientDetails(client)"
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
                                @click="deleteClient(client.id)"
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

        <nav v-if="clients.data.length > 0" class="mt-10 flex justify-center">
            <div class="flex items-center space-x-1 text-sm">
                <template v-for="(link, index) in clients.links" :key="index">
                    <Link
                        v-if="link.url"
                        :preserve-scroll="true"
                        :href="link.url"
                        v-html="link.label"
                        class="px-4 py-2 rounded-md transition-colors"
                        :class="{
                            'bg-rose-500 text-white font-medium': link.active,
                            'bg-white text-gray-700 hover:bg-rose-50 hover:text-rose-600':
                                !link.active,
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === clients.links.length - 1,
                        }"
                    />
                    <p
                        v-else
                        v-html="link.label"
                        class="px-4 py-2 text-gray-500 bg-slate-200 rounded-md"
                        :class="{
                            'rounded-l-lg': index === 0,
                            'rounded-r-lg': index === clients.links.length - 1,
                        }"
                    />
                </template>
            </div>
        </nav>
    </div>
</template>
