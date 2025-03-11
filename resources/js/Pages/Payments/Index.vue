<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link, router } from "@inertiajs/vue3";

const props = defineProps({
    payments: Object,
    totalPayments: Number,
});

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <p>Payments</p>
</template>