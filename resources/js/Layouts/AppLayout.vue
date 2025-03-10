<script setup>
import { ref } from "vue";
import Menubar from "primevue/menubar";
import NavBar from "@/Components/NavBar.vue";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

const items = ref([
    {
        label: "Dashboard",
        icon: "pi pi-th-large",
        to: route("dashboard"),
    },
    {
        label: "Clients",
        icon: "pi pi-users",
        to: route("clients"),
    },
    {
        label: "Projects",
        icon: "pi pi-briefcase",
        to: route("projects"),
    },
    {
        label: "Works",
        icon: "pi pi-calendar",
        to: route("works"),
    },
    {
        label: "Settings",
        icon: "pi pi-cog",
        to: route("settings"),
    },
]);

</script>

<template>
    <div class="min-h-screen">
        <!-- Top Navigation -->
        <div class="w-full bg-gray-50 border-b border-gray-200">
            <div class="flex justify-between max-w-7xl mx-auto px-2 py-3">
                <!-- Logo and Brand -->
                <div class="flex items-center">
                    <img
                        src="../../../public/assets/logo.png"
                        alt="Logo"
                        class="h-8 mr-3 w-14 object-cover"
                    />
                </div>
    
                <!-- Navigation Menu -->
                <div class="flex items-center">
                    <NavBar :items="items" />
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <main class="">
            <slot></slot>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-1">
            <div class="mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-sm text-gray-500">
                    © {{ new Date().getFullYear() }} {{ appName }}. All rights
                    reserved.
                </p>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Menubar styling for top navigation */
:deep(.p-menubar) {
    padding: 0;
    background: transparent;
    border: none;
}

:deep(.p-menuitem-link) {
    padding: 0.75rem 1rem;
}

:deep(.p-menuitem-link:hover) {
    background-color: #f3f4f6;
}

:deep(.p-menuitem-active > .p-menuitem-link) {
    color: #4f46e5;
}
</style>
