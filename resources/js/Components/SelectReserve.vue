<script setup>
import { ref, computed } from 'vue';


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

const isOpen = ref(false);
const selectedValue = ref(props.modelValue);

const selectedOption = computed(() => {
  return props.options.find(option => option.value === selectedValue.value);
});

const toggleDropdown = () => {
  isOpen.value = !isOpen.value;
};

const selectOption = (option) => {
  selectedValue.value = option.value;
  emit('update:modelValue', option.value);
  isOpen.value = false;
};
</script>

<template>
  <div class="dropdown border-gray-300">
    <button type="button" @click="toggleDropdown" class="dropdown-button w-full flex flex-column justify-between button-bordered" >
      <span class="flex flex-column justify-center items-center" v-if="selectedOption">  {{ selectedOption.value }}
        <img :src="selectedOption.image" alt="" class="option-image-selected w-full" />
      
      </span>
      <span class="text-gray-500" v-else>Select Background Type</span>

        <font-awesome-icon class="pr-2 text-gray-500 size-5" icon="fa-solid fa-caret-down"/>
    </button>
    <div v-if="isOpen" class="dropdown-menu">
      <div
        v-for="(option, index) in options"
        :key="index"
        @click="selectOption(option)"
        class="dropdown-item"
      >
         {{ option.value }}
        <img :src="option.image" alt="" class="option-image w-full" />
     
      </div>
    </div>
  </div>
</template>


<style scoped>

.dropdown {
  position: relative;
  display: inline-block;
  width: 100%;
}

.dropdown-button {
  width: 100%;
  padding: 11px;
  border-radius: 8px;
  background-color: #fff;
  cursor: pointer;
  text-align: left;
}

.dropdown-menu {
  position: absolute;
  width: 100%;
  top: 100%;
  left: 0;
  right: 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  background-color: #fff;
  z-index: 1000;
}

.dropdown-item {
  padding: 10px;
  width: 100%;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.dropdown-item:hover {
  background-color: #f0f0f0;
}

.option-image {
  padding-left:10px;
  height: 50px;
  margin-right: 8px;
  display: block;
}

.option-image-selected {
  padding-left:10px;
  height: 20px;
  margin-right: 8px;
  display: block;
}

</style>