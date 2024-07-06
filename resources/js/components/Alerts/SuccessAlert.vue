<script lang="ts" setup>
import { ref } from 'vue'

const isAlertVisible = ref(false)
</script>

<template>
  <VAlert
    v-model="isAlertVisible"
    closable
    close-label="Close Alert"
    color="success"
    class="text-center my-4"
  >
    <slot></slot>
  </VAlert>

  <!-- Button -->
  <div class="text-center" v-if="isAlertVisible">
    <VBtn
      @click="isAlertVisible = true"
    >
    </VBtn>
  </div>
</template>
