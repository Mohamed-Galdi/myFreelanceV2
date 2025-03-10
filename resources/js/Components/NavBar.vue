<script setup>
import { ref, watch, computed, onMounted, onUnmounted } from "vue";
import { Link, usePage, router } from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
});

const currentPath = ref(usePage().url);
const isOpen = ref(false);
const openSubmenu = ref(null);
const windowWidth = ref(window.innerWidth);
const isMobile = computed(() => windowWidth.value < 768); // 768px is md breakpoint

// Update window width on resize
const handleResize = () => {
    windowWidth.value = window.innerWidth;
    if (!isMobile.value) {
        isOpen.value = false;
    }
};

onMounted(() => {
    window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});

const closeMenu = () => {
    isOpen.value = false;
    openSubmenu.value = null;
};

const updateCurrentPath = (newPath) => {
    currentPath.value = new URL(newPath, window.location.origin).pathname;
    closeMenu();
};

const isActive = (menuItems) => {
    return menuItems.some((item) =>
        currentPath.value.startsWith(
            new URL(item.to, window.location.origin).pathname
        )
    );
};

const toggleSubmenu = (label) => {
    if (isMobile.value) {
        // On mobile, toggle the selected submenu
        openSubmenu.value = openSubmenu.value === label ? null : label;
    } else {
        // On desktop, open submenu on hover (handled in template)
        openSubmenu.value = label;
    }
};

// Close submenu when clicking outside
const handleClickOutside = (event) => {
    if (!event.target.closest("nav")) {
        closeMenu();
    }
};

onMounted(() => {
    document.addEventListener("click", handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener("click", handleClickOutside);
});

watch(
    () => usePage().url,
    (newUrl) => {
        currentPath.value = newUrl;
    }
);

// Logout function
const logout = () => {
    router.post(route("logout"));
};
</script>
<template>
    <nav class="relative min-w-[300px]">
        <!-- Mobile toggle button -->
        <div class="flex justify-end items-center md:hidden">
            <button
                @click="isOpen = !isOpen"
                class="p-2 rounded hover:bg-slate-100"
                aria-label="Toggle menu"
            >
                <i class="pi pi-bars text-xl"></i>
            </button>
        </div>

        <!-- Navigation menu -->
        <div
            :class="[
                isOpen ? 'block' : 'hidden md:block',
                'md:flex md:space-x-4 md:static md:shadow-none md:w-auto md:border-0 md:bg-transparent items-center',
                'absolute top-full left-0 w-full bg-white border rounded shadow-md z-50',
            ]"
        >
            <template v-for="item in items" :key="item.label">
                <!-- Dropdown menu item -->
                <div class="relative" v-if="item.items">
                    <button
                        class="flex items-center w-full space-x-2 px-4 py-2 rounded hover:bg-slate-100 transition-all duration-300"
                        :class="{
                            'bg-slate-200 hover:bg-slate-300': isActive(
                                item.items
                            ),
                        }"
                        @click="toggleSubmenu(item.label)"
                    >
                        <i :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                        <i class="pi pi-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div
                        v-show="
                            openSubmenu === item.label || (isOpen && isMobile)
                        "
                        class="md:absolute md:left-0 md:mt-2 w-full md:w-48 bg-white border-0 md:border md:rounded md:shadow-lg pl-4 md:pl-0"
                    >
                        <template
                            v-for="subItem in item.items"
                            :key="subItem.label"
                        >
                            <Link
                                :href="subItem.to"
                                class="block px-4 py-2 hover:bg-slate-100 transition-all duration-300"
                                :class="{
                                    'bg-slate-200 hover:bg-slate-300': isActive(
                                        [subItem]
                                    ),
                                }"
                                @click="closeMenu"
                            >
                                <i :class="subItem.icon" class="mr-2"></i
                                >{{ subItem.label }}
                            </Link>
                        </template>
                    </div>
                </div>

                <!-- Regular menu item -->
                <Link
                    v-else
                    :href="item.to"
                    class="block w-full px-4 py-2 rounded hover:bg-slate-100 transition-all duration-300"
                    :class="{
                        'bg-gray-300 hover:bg-gray-300': isActive([item]),
                    }"
                    @click="closeMenu"
                >
                    <div class="flex items-center space-x-2">
                        <i :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                    </div>
                </Link>
            </template>
            <button
                @click="logout"
                class="flex items-center gap-2 mx-3 bg-slate-600 hover:bg-slate-700 text-white rounded-full px-3 py-[0.4rem] text-sm w-[90%] md:w-fit md:my-0 my-3 h-fit"
            >
                Logout
                <i class="pi pi-sign-out"></i>
            </button>
        </div>
    </nav>
</template>
