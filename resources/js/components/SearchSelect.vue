<template>
  <div class="search-select">
    <input
      type="text"
      class="form-control mb-3"
      v-model="search"
      placeholder="Search..."
    />
    <select class="form-select" v-model="selected">
      <option v-for="option in filteredOptions" :key="option.value" :value="option.value">
        {{ option.name }}
      </option>
    </select>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
  options: {
    type: Array,
    required: true
  }
});

const search = ref('');
const selected = ref(null);

const filteredOptions = computed(() => {
  if (!search.value) {
    return props.options;
  }
  return props.options.filter(option =>
    option.text.toLowerCase().includes(search.value.toLowerCase())
  );
});
</script>

<style scoped>
.search-select {
  width: 100%;
  max-width: 400px;
  margin: auto;
}

.form-select {
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
  border-radius: 0.25rem;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select:hover {
  border-color: #80bdff;
}

.form-select:focus {
  border-color: #80bdff;
  box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}
</style>
