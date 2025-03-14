<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref, computed } from "vue";
import { Link, router, useForm } from "@inertiajs/vue3";
import Drawer from "primevue/drawer";
import InputText from "primevue/inputtext";
import Select from "primevue/select";
import { useToast } from "primevue/usetoast";
import Toast from "primevue/toast";
import ConfirmDialog from "primevue/confirmdialog";
import { useConfirm } from "primevue/useconfirm";
import InputNumber from "primevue/inputnumber";
import DatePicker from "primevue/datepicker";

defineOptions({
    layout: AppLayout,
});

const props = defineProps({
    payment: Object,
    totalPaid: String,
    remainingAmount: Number,
    relatedPayments: Array,
    works: Array,
});

const isMobile = computed(() => window.innerWidth <= 768);

const toast = useToast();
const confirm = useConfirm();

const payment = props.payment;
const works = props.works;

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

// Calculate payment progress percentage
const paymentProgress = computed(() => {
    const workPrice = props.payment.work.price;
    if (!workPrice) return 0;
    return Math.min(100, Math.round((props.totalPaid / workPrice) * 100));
});

// ######################################## edit payment
const openEditPaymentDrawer = ref(false);

const paymentMethods = ref([
    "Western Union",
    "Bank Transfer",
    "PayPal",
    "Cash",
    "Upwork",
]);

const editPaymentForm = useForm({
    id: payment.id,
    workId: payment.work_id,
    amount: payment.amount,
    paymentDate: payment.payment_date,
    paymentMethod: payment.payment_method,
    note: payment.note,
});

function updatePayment() {
    editPaymentForm.post(
        route("admin.payments.update", { payment: editPaymentForm.id }),
        {
            preserveState: false,
            onSuccess: () => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Payment updated successfully",
                    life: 3000,
                });
                editPaymentForm.reset();
                openEditPaymentDrawer.value = false;
            },
            onError: () => {
                const errorMessage = Object.values(editPaymentForm.errors)[0];
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

// ######################################## delete payment
function deletePayment() {
    confirm.require({
        group: "templating",
        message: "Are you sure you want to delete this payment?",
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
                route("admin.payments.delete", { payment: payment.id })
            );
            setTimeout(() => {
                toast.add({
                    severity: "success",
                    summary: "Succes",
                    detail: "Payment deleted successfully",
                    life: 3000,
                });
            }, 500);
        },
    });
}
</script>

<template>
    <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
        <Toast position="top-center" />
        <!-- Edit Payment Drawer -->
        <Drawer
            v-model:visible="openEditPaymentDrawer"
            header="Edit Payment"
            :style="{ width: isMobile ? '100%' : '50vw' }"
            position="right"
        >
            <form
                action=""
                class="flex flex-col gap-6 py-3"
                @submit.prevent="updatePayment"
            >
                <Select
                    v-model="editPaymentForm.workId"
                    name="workId"
                    :options="works"
                    optionLabel="name"
                    optionValue="id"
                    placeholder="Work"
                    class="w-full"
                />
                <InputNumber
                    v-model="editPaymentForm.amount"
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
                    v-model="editPaymentForm.paymentMethod"
                    name="paymentMethod"
                    :options="paymentMethods"
                    placeholder="Payment Method"
                    class="w-full"
                />
                <DatePicker
                    v-model="editPaymentForm.paymentDate"
                    name="paymentDate"
                    placeholder="Payment Date"
                    class="w-full"
                />
                <InputText
                    v-model="editPaymentForm.note"
                    name="note"
                    type="text"
                    placeholder="Note"
                    class="w-full"
                />
                <button
                    class="w-full"
                    :class="editPaymentForm.processing ? 'btn-disabled' : 'btn'"
                    :disabled="editPaymentForm.processing"
                >
                    <span v-if="editPaymentForm.processing"
                        >processing...
                    </span>
                    <span v-else>Update Payment</span>
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
                        <p>Are you sure you want to delete this payment?</p>
                    </div>
                </div>
            </template>
        </ConfirmDialog>
        <!-- Header with back button and actions -->
        <div class="flex items-center justify-between mb-8">
            <!-- Back button -->
            <Link
                :href="route('payments')"
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
                    @click="openEditPaymentDrawer = true"
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
                    <p class="text-nowrap">Edit Payment</p>
                </button>
                <!-- Delete button -->
                <button
                    @click="deletePayment()"
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
                    <p class="text-nowrap">Delete Payment</p>
                </button>
            </div>
        </div>

        <!-- Main content grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Payment details -->
            <div
                class="bg-white shadow overflow-hidden sm:rounded-lg lg:col-span-2"
            >
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Payment Information
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Details about this payment and associated work.
                    </p>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div
                            class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Amount
                            </dt>
                            <dd
                                class="mt-1 text-sm sm:mt-0 sm:col-span-2 font-medium text-green-600"
                            >
                                {{ formatCurrency(payment.amount) }}
                            </dd>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Payment Date
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ formatDate(payment.payment_date) }}
                            </dd>
                        </div>
                        <div
                            class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Payment Method
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-blue-100 text-blue-800':
                                            payment.payment_method ===
                                            'bank_transfer',
                                        'bg-yellow-100 text-yellow-800':
                                            payment.payment_method === 'paypal',
                                        'bg-green-100 text-green-800':
                                            payment.payment_method === 'cash',
                                        'bg-purple-100 text-purple-800':
                                            payment.payment_method ===
                                            'credit_card',
                                    }"
                                >
                                    {{
                                        payment.payment_method.replace("_", " ")
                                    }}
                                </span>
                            </dd>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Note
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ payment.note || "No notes added" }}
                            </dd>
                        </div>
                    </dl>
                </div>

                <div
                    class="px-4 py-5 sm:px-6 bg-gray-50 border-t border-gray-200"
                >
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Associated Work
                    </h3>
                </div>
                <div class="border-t border-gray-200">
                    <dl>
                        <div
                            class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Work Description
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ payment.work.description }}
                            </dd>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Project
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <Link
                                    :href="
                                        route(
                                            'project',
                                            payment.work.project.id
                                        )
                                    "
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    {{ payment.work.project.title }}
                                </Link>
                            </dd>
                        </div>
                        <div
                            class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Client
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <Link
                                    :href="
                                        route(
                                            'client',
                                            payment.work.project.client.id
                                        )
                                    "
                                    class="text-indigo-600 hover:text-indigo-900"
                                >
                                    {{ payment.work.project.client.name }}
                                </Link>
                            </dd>
                        </div>
                        <div
                            class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Work Status
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                    :class="{
                                        'bg-green-100 text-green-800':
                                            payment.work.project_status ===
                                            'completed',
                                        'bg-yellow-100 text-yellow-800':
                                            payment.work.project_status ===
                                            'in_progress',
                                        'bg-blue-100 text-blue-800':
                                            payment.work.project_status ===
                                            'planned',
                                        'bg-red-100 text-red-800':
                                            payment.work.project_status ===
                                            'canceled',
                                    }"
                                >
                                    {{ payment.work.status.replace("_", " ") }}
                                </span>
                            </dd>
                        </div>

                        <div
                            class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Work Price
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                {{ formatCurrency(payment.work.price) }}
                            </dd>
                        </div>

                        <div
                            class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6"
                        >
                            <dt class="text-sm font-medium text-gray-500">
                                Payment Progress
                            </dt>
                            <dd
                                class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2"
                            >
                                <div class="flex items-center">
                                    <div
                                        class="w-full bg-gray-200 rounded-full h-2.5"
                                    >
                                        <div
                                            class="bg-green-600 h-2.5 rounded-full"
                                            :style="{
                                                width: paymentProgress + '%',
                                            }"
                                        ></div>
                                    </div>
                                    <span
                                        class="ml-2 text-sm font-medium text-gray-700"
                                        >{{ paymentProgress }}%</span
                                    >
                                </div>
                                <div
                                    class="mt-2 flex justify-between text-xs text-gray-500"
                                >
                                    <span
                                        >{{
                                            formatCurrency(totalPaid)
                                        }}
                                        paid</span
                                    >
                                    <span
                                        v-if="remainingAmount > 0"
                                        class="text-red-600"
                                    >
                                        {{ formatCurrency(remainingAmount) }}
                                        remaining
                                    </span>
                                    <span v-else class="text-green-600">
                                        Fully paid
                                    </span>
                                </div>
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- Sidebar: Related payments -->
            <div class="bg-white shadow overflow-hidden sm:rounded-lg">
                <div class="px-4 py-5 sm:px-6 bg-gray-50">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        Related Payments
                    </h3>
                    <p class="mt-1 max-w-2xl text-sm text-gray-500">
                        Other payments for the same work.
                    </p>
                </div>
                <div>
                    <ul class="divide-y divide-gray-200">
                        <li
                            v-if="relatedPayments.length === 0"
                            class="px-4 py-3 text-sm text-gray-500"
                        >
                            No other payments for this work
                        </li>
                        <li
                            v-for="relatedPayment in relatedPayments"
                            :key="relatedPayment.id"
                            class="px-4 py-3"
                        >
                            <Link
                                :href="route('payment', relatedPayment.id)"
                                class="block hover:bg-gray-50"
                            >
                                <div class="flex justify-between">
                                    <span class="font-medium text-gray-900">{{
                                        formatCurrency(relatedPayment.amount)
                                    }}</span>
                                    <span class="text-gray-500">{{
                                        formatDate(relatedPayment.payment_date)
                                    }}</span>
                                </div>
                                <div
                                    class="mt-1 text-xs text-gray-500 flex items-center"
                                >
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
                                        :class="{
                                            'bg-blue-100 text-blue-800':
                                                relatedPayment.payment_method ===
                                                'bank_transfer',
                                            'bg-yellow-100 text-yellow-800':
                                                relatedPayment.payment_method ===
                                                'paypal',
                                            'bg-green-100 text-green-800':
                                                relatedPayment.payment_method ===
                                                'cash',
                                            'bg-purple-100 text-purple-800':
                                                relatedPayment.payment_method ===
                                                'credit_card',
                                        }"
                                    >
                                        {{
                                            relatedPayment.payment_method.replace(
                                                "_",
                                                " "
                                            )
                                        }}
                                    </span>
                                </div>
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
