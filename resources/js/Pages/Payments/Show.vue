<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    payment: Object,
});

defineOptions({
    layout: AppLayout,
});
</script>

<template>
    <p>Payment</p>
</template>