<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { DataTable, Column, Button } from "primevue";
import { ref, computed } from "vue";
import Chart from "primevue/chart";
import { Link, router, useForm } from "@inertiajs/vue3";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";


defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    payments: Object,
    totalAmount: Number,
    totalPending: Number,
    lastPayment: Object,
    projectsData: Array,
    clientsData: Array,
    works: Array,
});

const isMobile = computed(() => window.innerWidth <= 768);

const toast = useToast();

function formatCurrency(amount) {
    return new Intl.NumberFormat("en-US", {
        style: "currency",
        currency: "USD",
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString("en-US", {
        year: "numeric",
        month: "short",
        day: "numeric",
    });
}

// Initialize filters object properly for PrimeVue DataTable
const filters = ref({
    global: { value: null, matchMode: "contains" },
});

// Chart data for projects
const projectChartData = computed(() => {
    return {
        labels: props.projectsData?.map((p) => p.title) || [],
        datasets: [
            {
                data: props.projectsData?.map((p) => p.total) || [],
                backgroundColor: [
                    "#42A5F5",
                    "#66BB6A",
                    "#FFA726",
                    "#26C6DA",
                    "#7E57C2",
                    "#EC407A",
                    "#AB47BC",
                    "#7E57C2",
                    "#5C6BC0",
                    "#29B6F6",
                ],
            },
        ],
    };
});

// Chart data for clients
const clientChartData = computed(() => {
    return {
        labels: props.clientsData?.map((c) => c.name) || [],
        datasets: [
            {
                data: props.clientsData?.map((c) => c.total) || [],
                backgroundColor: [
                    "#FFA726",
                    "#66BB6A",
                    "#42A5F5",
                    "#EC407A",
                    "#AB47BC",
                    "#7E57C2",
                    "#5C6BC0",
                    "#29B6F6",
                    "#26C6DA",
                    "#26A69A",
                ],
            },
        ],
    };
});

const chartOptions = {
    plugins: {
        legend: {
            position: "bottom",
            labels: {
                boxWidth: 12,
            },
        },
    },
    cutout: "60%",
};

// ######################################## create payment
const openNewPaymentDrawer = ref(false);

const paymentMethods = ref([
    "Western Union",
    "Bank Transfer",
    "PayPal",
    "Cash",
    "Upwork",
]);

const works = props.works;

const newPaymentForm = useForm({
    workId: "",
    amount: "",
    paymentDate: "",
    paymentMethod: "",
    note: "",
});

function createPayment() {
    newPaymentForm.post(route("admin.payments.store"), {
        onSuccess: () => {
            toast.add({
                severity: "success",
                summary: "Succes",
                detail: "Payment created successfully",
                life: 3000,
            });
            newPaymentForm.reset();
            openNewPaymentDrawer.value = false;
        },
        onError: () => {
            const errorMessage = Object.values(newPaymentForm.errors)[0];
            toast.add({
                severity: "error",
                summary: "Erreur",
                detail: errorMessage,
                life: 3000,
            });
        },
    });
}

// ######################################## go to payment
function goToPayment(payment) {
    router.get(route("payment", { payment: payment.data.id }));
}


</script>

<template>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <Toast position="top-center" />

        <!-- Create Payment Drawer -->
        <Drawer
            v-model:visible="openNewPaymentDrawer"
            header="Create New Payment"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="createPayment"
            >
                <Select
                    v-model="newPaymentForm.workId"
                    name="work_id"
                    :options="works"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Work"
                    class="w-full"
                />
                 <InputNumber
                    v-model="newPaymentForm.amount"
                    inputId="currency-us"
                    mode="currency"
                    currency="USD"
                    locale="en-US"
                    fluid
                    name="amount"
                    placeholder="Amount"
                    class="w-full"
                />
                <Select
                    v-model="newPaymentForm.paymentMethod"
                    name="paymentMethod"
                    :options="paymentMethods"
                    placeholder="Payment Method"
                    class="w-full"
                />
                
                <DatePicker
                    v-model="newPaymentForm.paymentDate"
                    name="paymentDate"
                    placeholder="Payment Date"
                    class="w-full"
                />
                <InputText
                    v-model="newPaymentForm.note"
                    name="note"
                    type="text"
                    placeholder="Note"
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="newPaymentForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="newPaymentForm.processing"
                >
                    <span v-if="newPaymentForm.processing">processing... </span>
                    <span v-else>Create Payment</span>
                </button>
            </form>
        </Drawer>

        <!-- Header section -->
        <div class="mb-8">
           <div class="flex items-center justify-between mb-8">
               <div class="w-full">
                    <h1 class="text-3xl font-bold text-gray-900">Payments</h1>
                    <p class="mt-2 text-gray-600">
                        Track and manage all your client payments
                    </p>
               </div>
                <div
                    class="flex items-center gap-4 self-stretch sm:self-auto md:justify-end justify-start w-full"
                >
                    <button
                        @click="openNewPaymentDrawer = true"
                        class="inline-flex items-center justify-center px-4 py-2 bg-green-800 text-white rounded-lg shadow-sm hover:bg-green-900 transition duration-150"
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
                        New Payment
                    </button>
                </div>
           </div>
        </div>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Last Payment
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{
                            lastPayment
                                ? formatCurrency(lastPayment.amount)
                                : "$0.00"
                        }}
                    </dd>
                    <p class="mt-1 text-sm text-gray-500">
                        {{
                            lastPayment
                                ? formatDate(lastPayment.payment_date)
                                : "No payments yet"
                        }}
                    </p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Payments (Amount)
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{
                            totalAmount ? formatCurrency(totalAmount) : "$0.00"
                        }}
                    </dd>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Total Payments (Count)
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{ payments.total }}
                    </dd>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        Pending Payments
                    </dt>
                    <dd class="mt-1 text-3xl font-semibold text-gray-900">
                        {{
                            totalPending
                                ? formatCurrency(totalPending)
                                : "$0.00"
                        }}
                    </dd>
                </div>
            </div>
        </div>

        <!-- Charts section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Projects chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Payments by Project
                </h2>
                <Chart
                    v-if="projectsData && projectsData.length"
                    type="doughnut"
                    :data="projectChartData"
                    :options="chartOptions"
                    class="h-64"
                />
                <p v-else class="text-gray-500 text-center py-12">
                    No project payment data available
                </p>
            </div>

            <!-- Clients chart -->
            <div class="bg-white p-6 rounded-lg shadow">
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    Payments by Client
                </h2>
                <Chart
                    v-if="clientsData && clientsData.length"
                    type="doughnut"
                    :data="clientChartData"
                    :options="chartOptions"
                    class="h-64"
                />
                <p v-else class="text-gray-500 text-center py-12">
                    No client payment data available
                </p>
            </div>
        </div>

        <!-- Payments table -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <DataTable
                :value="payments.data"
                :paginator="false"
                :rows="payments.per_page"
                :totalRecords="payments.total"
                :rowsPerPageOptions="[10, 25, 50]"
                :filters="filters"
                filterDisplay="menu"
                responsiveLayout="stack"
                class="p-datatable-sm"
                @rowSelect="goToPayment($event)"
                selectionMode="single"
            >
                <Column field="id" header="ID" sortable>
                    <template #body="{ data }">
                        <span class="font-medium">#{{ data.id }}</span>
                    </template>
                </Column>

                <Column field="amount" header="Amount" sortable>
                    <template #body="{ data }">
                        <span class="text-green-600 font-medium">{{
                            formatCurrency(data.amount)
                        }}</span>
                    </template>
                </Column>

                <Column field="payment_date" header="Date" sortable>
                    <template #body="{ data }">
                        {{ formatDate(data.payment_date) }}
                    </template>
                </Column>

                <Column field="payment_method" header="Method" sortable>
                    <template #body="{ data }">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                            :class="{
                                'bg-blue-100 text-blue-800':
                                    data.payment_method === 'bank_transfer',
                                'bg-yellow-100 text-yellow-800':
                                    data.payment_method === 'paypal',
                                'bg-green-100 text-green-800':
                                    data.payment_method === 'cash',
                                'bg-purple-100 text-purple-800':
                                    data.payment_method === 'credit_card',
                            }"
                        >
                            {{ data.payment_method.replace("_", " ") }}
                        </span>
                    </template>
                </Column>

                <Column field="work.project.client.name" header="Client">
                    <template #body="{ data }">
                        {{ data.work?.project?.client?.name || "N/A" }}
                    </template>
                </Column>

                <Column field="work.project.title" header="Project">
                    <template #body="{ data }">
                        {{ data.work?.project?.title || "N/A" }}
                    </template>
                </Column>

                <Column field="work.description" header="Work Description">
                    <template #body="{ data }">
                        <span class="line-clamp-1">{{
                            data.work?.description || "N/A"
                        }}</span>
                    </template>
                </Column>

            </DataTable>

            <!-- Custom pagination -->
            <div
                class="flex items-center justify-between px-4 py-3 bg-white border-t border-gray-200 sm:px-6"
            >
                <div
                    class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between"
                >
                    <div>
                        <p class="text-sm text-gray-700">
                            Showing
                            <span class="font-medium">{{
                                payments.from || 0
                            }}</span>
                            to
                            <span class="font-medium">{{
                                payments.to || 0
                            }}</span>
                            of
                            <span class="font-medium">{{
                                payments.total
                            }}</span>
                            results
                        </p>
                    </div>
                    <div>
                        <nav
                            class="isolate inline-flex -space-x-px rounded-md shadow-sm"
                            aria-label="Pagination"
                        >
                            <Link
                                v-for="link in payments.links"
                                :key="link.label"
                                :href="link.url"
                                :class="[
                                    link.active
                                        ? 'bg-indigo-50 border-indigo-500 text-indigo-600'
                                        : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50',
                                    'relative inline-flex items-center px-4 py-2 text-sm font-medium border',
                                ]"
                                v-html="link.label"
                            >
                            </Link>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
