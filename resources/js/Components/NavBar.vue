<script setup>
import { ref, onMounted, watch } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
    items: Array,
});

const currentPath = ref(usePage().url);

const updateCurrentPath = (newPath) => {
    currentPath.value = new URL(newPath, window.location.origin).pathname;
};

const isActive = (menuItems) => {
    return menuItems.some((item) =>
        currentPath.value.startsWith(
            new URL(item.to, window.location.origin).pathname
        )
    );
};

watch(
    () => usePage().url,
    (newUrl) => {
        currentPath.value = newUrl;
    }
);
</script>
<template>
    <nav>
        <div class="flex space-x-4">
            <template v-for="item in items" :key="item.label">
                <div class="relative group" v-if="item.items">
                    <button
                        class="flex items-center space-x-2 px-4 py-2 rounded hover:bg-slate-100 transition-colors duration-200"
                        :class="{
                            'bg-slate-200 hover:bg-slate-300 transition-colors duration-200':
                                isActive(item.items),
                        }"
                    >
                        <i :class="item.icon"></i>
                        <span>{{ item.label }}</span>
                    </button>
                    <div
                        class="absolute left-0 mt-2 w-48 bg-white border rounded shadow-lg hidden group-hover:block"
                    >
                        <Link
                            v-for="subItem in item.items"
                            :key="subItem.label"
                            :href="subItem.to"
                            class="block px-4 py-2 hover:bg-slate-100 transition-colors duration-200"
                            :class="{
                                'bg-slate-200 hover:bg-slate-300 transition-colors duration-200':
                                    isActive([subItem]),
                            }"
                            @click="updateCurrentPath(subItem.to)"
                        >
                            <i :class="subItem.icon" class="mr-2"></i
                            >{{ subItem.label }}
                        </Link>
                    </div>
                </div>
                <Link
                    v-else
                    :href="item.to"
                    class="flex items-center space-x-2 px-4 py-2 rounded hover:bg-slate-100 transition-colors duration-200"
                    :class="{
                        'bg-slate-200 hover:bg-slate-300 transition-colors duration-200':
                            isActive([item]),
                    }"
                    @click="updateCurrentPath(item.to)"
                >
                    <i :class="item.icon"></i>
                    <span>{{ item.label }}</span>
                </Link>
            </template>
        </div>
    </nav>
</template>

<style scoped>
nav div:hover > div {
    display: block;
}
</style>
