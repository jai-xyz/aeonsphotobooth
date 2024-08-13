<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: String,
    required: true,
});

const input = ref(null);

const filterNumericInput = (event) => {
    event.target.value = event.target.value.replace(/[^0-9]/g, '');
    model.value = event.target.value;
};

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
          class="input input-bordered border-gray-300 input-info shadow-sm"
        v-model="model"
        ref="input"
        @input="filterNumericInput"
    />
</template>