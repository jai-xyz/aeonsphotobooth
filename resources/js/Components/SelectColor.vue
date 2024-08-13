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

</script>   



<template>
    <select class="select select-info border-gray-300">
    <option disabled selected>Select Background Color</option>
          <option v-for="(option, index) in options" :key="index" class="flex flex-col rounded-xl ">
           {{ option.value }}  
          </option>
    
    </select>
</template>
