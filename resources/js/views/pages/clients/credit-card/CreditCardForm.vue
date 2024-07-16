<script setup>
import {defineProps, reactive, ref, defineEmits, watch, onMounted} from 'vue';
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";

const props = defineProps({
  client: {
    type: Object,
    required: true,
  },
  updatedClient: {
    type: Object,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
});

watch(() => props.updatedClient, (updatedClient) => {
  client.value = updatedClient;
});

const emit = defineEmits(['close-cc-dialogue', 'assign-default-cc']);

const client = ref(props.client);
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const form = reactive({
  client_id: client.value.id,
  cc_number: '',
  cc_exp_month: '',
  cc_exp_year: '',
});

const maskMonthAndYear = (event) => {
  let cc_exp = event.target.value;

  // Remove any non-digit characters
  cc_exp = cc_exp.replace(/\D/g, '');

  // Add '/' after the first 2 digits
  if (cc_exp.length > 2) {
    cc_exp = cc_exp.slice(0, 2) + '/' + cc_exp.slice(2);
  }

  // Limit the input to 5 characters (MM/YY)
  cc_exp = cc_exp.slice(0, 5);

  // Update the form.cc_exp field with the masked value
  form.cc_exp = cc_exp;

  // Split the masked value and assign to the respective fields
  const cc_exp_array = cc_exp.split('/');
  form.cc_exp_month = cc_exp_array[0];
  form.cc_exp_year = cc_exp_array[1];
}

const maxLength = (event, max) => {
  let value = event.target.value.replace(/\s/g, ''); // Remove existing spaces

  if (value.length > max) {
    value = value.slice(0, max);
  }

  // Add a space after every 4 digits
  event.target.value = value.replace(/(\d{4})/g, '$1 ').trim();
}

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

    if(form[key] !== null && form[key] !== ''){
      formData.append(key, form[key]);
    }
  });

  try {
    const response = await apiClientAuto.post('/clients/credit-cards/store', formData);

    if (response.data.success){
      submitted.value = true;
      emit('assign-default-cc', response.data.credit_card.id);
    }

  } catch (error) {
    handleErrors(error, errors.value);
  }

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

      <VCol md="8">
        <VTextField
          @keydown="maxLength($event, 19)"
          v-model="form.cc_number"
          label=""
          type="text"
          maxlength="19"
          placeholder="0000 0000 0000 0000"
          class="credit-card-mask"
        />
        <p v-if="errors.cc_number" class="text-warning">
          {{ errors.cc_number[0] }}
        </p>
      </VCol>

      <VCol md="4">
        <VTextField
          @input="maskMonthAndYear"
          v-model="form.cc_exp"
          label="Expiry"
          type="text"
          maxlength="5"
          placeholder="MM/YY"
          :error-messages="errors.cc_exp"
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
