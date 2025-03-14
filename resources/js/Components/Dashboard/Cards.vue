<script setup>
defineProps({
    summary: Object,
});
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

        <!-- Clients -->
        <div class="summary-card rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl bg-gradient-to-br from-rose-50 to-white p-6 border border-rose-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-rose-900">Clients</h3>
                <div class="bg-rose-100 p-3 rounded-xl shadow-inner">
                    <i class="pi pi-users text-rose-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-4xl font-extrabold text-rose-700">
                {{ summary.clientCount }}
            </div>
        </div>

         <!-- Projects (Works) -->
        <div class="summary-card rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl bg-gradient-to-br from-amber-50 to-white p-6 border border-amber-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-amber-900">Projects <span class="text-base">(Works)</span></h3>
                <div class="bg-amber-100 p-3 rounded-xl shadow-inner">
                    <i class="pi pi-briefcase text-amber-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-4xl font-extrabold text-amber-700">
                {{ summary.projectCount }} <span class="text-2xl">({{ summary.workCount }})</span>
            </div>
        </div>

        <!-- Monthly Average -->
        <div class="summary-card rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl bg-gradient-to-br from-sky-50 to-white p-6 border border-sky-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-sky-900">Pending Amount</h3>
                <div class="bg-sky-100 p-3 rounded-xl shadow-inner">
                    <i class="pi pi-clock text-sky-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 text-4xl font-extrabold text-sky-700">
                ${{
                    summary.moneyPending.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    })
                }}
            </div>
        </div>

        <!-- Total Revenue -->
        <div class="summary-card rounded-2xl shadow-md transition-all duration-300 hover:shadow-xl bg-gradient-to-br from-emerald-50 to-white p-6 border border-emerald-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-emerald-900">Revenue</h3>
                <div class="bg-emerald-100 p-3 rounded-xl shadow-inner">
                    <i class="pi pi-dollar text-emerald-600 text-xl"></i>
                </div>
            </div>
            <p class="mt-4 text-4xl font-extrabold text-emerald-700 flex flex-col">
                ${{
                    summary.moneyReceived.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    })
                }}
                <span class="text-xl">(Total: ${{
                    summary.totalEarnings.toLocaleString(undefined, {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    })
                }})</span>
            </p>
        </div>

    </div>
</template>
