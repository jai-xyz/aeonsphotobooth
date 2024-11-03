<script setup>
import { ref, watch } from 'vue';
import { usePage } from "@inertiajs/vue3";

const toasts = ref([]);

const addToast = (message, isSuccess) => {
    const toast = {
        id: Date.now(),
        message,
        isSuccess,
        timer: null,
        toastClass: isSuccess
            ? 'bg-green-100 text-green-500 dark:text-gray-400 dark:bg-gray-800'
            : 'bg-red-100 text-red-500 dark:text-red-400 dark:bg-red-800',
        iconClass: isSuccess
            ? 'bg-green-100 text-green-500 dark:bg-green-800 dark:text-green-200'
            : 'bg-red-100 text-red-500 dark:bg-red-800 dark:text-red-200'
    };
    toasts.value.push(toast);
    startTimer(toast.id);
};

const startTimer = (id) => {
    const toast = toasts.value.find(t => t.id === id);
    if (toast) {
        toast.timer = setTimeout(() => {
            removeToast(id);
        }, 3000);
    }
};

const clearTimer = (id) => {
    const toast = toasts.value.find(t => t.id === id);
    if (toast && toast.timer) {
        clearTimeout(toast.timer);
    }
};

const removeToast = (id) => {
    toasts.value = toasts.value.filter(t => t.id !== id);
};

const page = usePage();

watch(
    () => page.props.flash,
    (newFlash) => {
        if (newFlash.success || newFlash.error) {
            const message = newFlash.success || newFlash.error;
            const isSuccess = !!newFlash.success;
            addToast(message, isSuccess);
        }
    },
    { immediate: true }
);
</script>

<template>
    <div id="toast" class="fixed top-5 right-5 z-50 space-y-4">
        <transition-group name="toast" tag="div" class="toast-wrapper">
            <div
                v-for="toast in toasts"
                :key="toast.id"
                :class="toast.toastClass"
                class="toast flex items-center w-full max-w-xs p-4 space-x-4 rounded-lg shadow"
                role="alert"
                @before-enter="clearTimer(toast.id)"
                @after-enter="startTimer(toast.id)"
                @before-leave="clearTimer(toast.id)"
            >
                <div
                    :class="toast.iconClass"
                    class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 rounded-lg"
                >
                    <svg
                        class="w-7 h-7"
                        aria-hidden="true"
                        xmlns="http://www.w3.org/2000/svg"
                        fill="currentColor"
                        viewBox="0 0 20 20"
                    >
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                            v-if="toast.isSuccess"
                        />
                        <path
                            d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"
                            v-if="!toast.isSuccess"
                        />
                    </svg>
                </div>
                <div class="ms-3 text-sm font-normal">{{ toast.message }}</div>
            </div>
        </transition-group>
    </div>
</template>