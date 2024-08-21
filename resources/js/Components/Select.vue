<script setup>

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  name: {
    type: String,
    required: true
  },
  options: {
    type: Array,
    required: true,
    default: () => []
  },
  modelValue: {
    type: [String, Number],
    required: false,
    default: ''
  }
});

const emit = defineEmits(['update:modelValue']);
const handleChange = (event) => {
  emit('update:modelValue', event.target.value);
};
</script>

<template>
    <select class="select select-info border-gray-300" 
            :id="id"
            :name="name"
            :value="modelValue"
            @change="handleBackdropTypeChange">
    <option disabled selected>Select Background Type</option>
          <option v-for="(option, index) in options" :key="index">
           {{ option.value }}  
          </option>
    
    </select>
</template>
