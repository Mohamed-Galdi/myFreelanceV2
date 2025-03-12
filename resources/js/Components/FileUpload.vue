<script setup>
import { ref, onMounted, watch } from "vue";
import vueFilePond from "vue-filepond";
import FilePondPluginFileValidateType from "filepond-plugin-file-validate-type";
import FilePondPluginFileValidateSize from 'filepond-plugin-file-validate-size';
import FilePondPluginImagePreview from 'filepond-plugin-image-preview';
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css';
import "filepond/dist/filepond.min.css";
import { router, usePage } from "@inertiajs/vue3";
import { setOptions } from "vue-filepond";


// Define component props
const props = defineProps({
    initialFile: {
        type: [String, Object], // Can be a file URL or an object
        default: null,
    },
    allowedFileTypes: {
        type: Array,
        default: () => [ "image/jpeg", "image/png", "image/gif", "image/svg+xml"], 
    },
    allowMultiple: {
        type: Boolean,
        default: false,
    },
    maxFiles: {
        type: Number,
        default: 1,
    },
    maxFileSize: {
        type: Number,
        default: 10 * 1024 * 1024, // 10 MB
    },
    locale: {
        type: Object,
    },
});

// Reactive variables
const $page = usePage();
const files = ref([]);

// Initialize FilePond component with the required plugin
const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginFileValidateSize, FilePondPluginImagePreview);

// Set FilePond locale dynamically
setOptions(props.locale);

// Handle initial file (useful for update scenarios)
onMounted(() => {
    if (props.initialFile) {
        files.value = [
            {
                source: props.initialFile,
                options: { type: "local" },
            },
        ];
    }
});

// Define emitted events for parent component communication
const emit = defineEmits(["fileUploaded", "fileReverted"]);

// Handle file upload response
function handleFileUpload(response) {
    files.value.push(response); // Store uploaded file reference
    emit("fileUploaded", response); // Notify parent component
    return response;
}

// Handle file revert (removal)
function handleFileRevert(uniqueId, load) {
    emit("fileReverted", uniqueId);
    router.post(route("file.revert", { id: uniqueId })); // Send revert request
    load(); // Notify FilePond that the revert was successful
}

// FilePond server configuration
const serverOptions = {
    process: {
        url: route("file.upload"),
        method: "POST",
        onload: handleFileUpload,
    },
    revert: handleFileRevert,
    headers: {
        "X-CSRF-TOKEN": $page.props.csrf_token,
    },
    load: (source, load, error, progress, abort, headers) => {
        var myRequest = new Request(source);
        fetch(myRequest).then(function (response) {
            response.blob().then(function (myBlob) {
                load(myBlob);
            });
        });
    },
};
</script>

<template>
    <div>
        <FilePond
            v-model="files"
            :server="serverOptions"
            :allow-multiple="allowMultiple"
            :accepted-file-types="allowedFileTypes"
            :max-files="maxFiles"
            :max-file-size="maxFileSize"
            credits="false"
            :locale="locale"
            :files="files"
        />
    </div>
</template>
