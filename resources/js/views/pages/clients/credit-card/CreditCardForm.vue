<script setup>
import { defineProps, reactive, ref } from 'vue';
import axios from "axios";

const props = defineProps({
  client: {
    type: Object,
    required: true,
  },
});

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const form = reactive({
  client_id: props.client.id,
  cc_last_4_digits: '',
  cc_exp_month: '',
  cc_exp_year: '',
});

const submitCreditCard = async () => {
  // Delete all errors
  Object.keys(errors.value).forEach(function(key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  const formData = new FormData();
  // iterate and add form data
  Object.keys(form).forEach(function(key) {
    console.log(key); // key
    if(form[key] !== null && form[key] !== ''){
      formData.append(key, form[key]);
    }
  });

  await axios.post('/api/clients/credit-cards/store', formData, {
    headers: {
      'content-type': 'multipart/form-data',
      'Accept' : 'application/json',
      "Authorization" : "Bearer " + token.value,
    }
  }).then((response) => {

    if (response.data.success){
      submitted.value = true;
    }

  }).catch((error) => {
    if([401, 402, 422].includes(error.response.status)){
      console.log(error.response);

      if(Object.keys(error.response?.data?.errors).length > 0){
        errors.value = error.response?.data?.errors;
        if(import.meta.env.VITE_APP_ENV === 'local'){
          console.log("Validation errors", errors.value);
        }
      }

      if(error.response?.data?.server_error){
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
  loading.value = false;
}

</script>

<template>

  <VForm @submit.prevent="submitCreditCard" class="g-6" novalidate>
    <VRow>
      <VCol cols="12" class="text-center">
        <VAlert v-if="submitted" class="text-success font-weight-bold">Card added successfully.</VAlert>
        <VAlert v-if="errors.server_error" class="text-danger">{{ errors.server_error }}</VAlert>
      </VCol>

      <VCol md="4">
        <VTextField
          v-model="form.cc_last_4_digits"
          label="Last 4 Digits"
          type="number"
          maxlength="4"
          placeholder="0000"
          :error-messages="errors.cc_last_4_digits"
          class="credit-card-mask"
        />
      </VCol>

      <VCol md="4">
        <VTextField
          v-model="form.cc_exp_month"
          label="Expiry Month"
          type="number"
          maxlength="2"
          placeholder="00"
          :error-messages="errors.cc_exp_month"
        />
      </VCol>

      <VCol md="4">
        <VTextField
          v-model="form.cc_exp_year"
          label="Expiry Year"
          type="number"
          maxlength="4"
          class="expiry-date-mask"
          :error-messages="errors.cc_exp_year"
        />
      </VCol>
    </VRow>

    <VCardText class="d-flex justify-end flex-wrap gap-3">
      <VBtn
        variant="tonal"
        color="secondary"
        @click="$emit('close-cc-dialogue')"
      >
        Close
      </VBtn>
      <VBtn v-if="!loading" type="submit">
        Save
      </VBtn>
      <VBtn disabled v-else>
        <VProgressCircular indeterminate color="primary" size="24" />
      </VBtn>
    </VCardText>

  </VForm>



</template>

<style scoped lang="scss">

</style>
