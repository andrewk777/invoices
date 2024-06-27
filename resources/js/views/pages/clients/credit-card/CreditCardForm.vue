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
  <div class="modal-content">
    <div class="modal-body">

      <div class="text-center mb-6">
        <h4 class="mb-2">Add New Card</h4>
      </div>

      <form
        @submit.prevent="submitCreditCard"
        class="row g-6 fv-plugins-bootstrap5 fv-plugins-framework"
        novalidate="novalidate">

        <div class="col-12 text-center text-info">
          <p v-if="submitted" class="text-success font-weight-bold">Card added successfully.</p>
          <p v-if="errors.server_error" class="text-danger">{{ errors.server_error }}</p>
        </div>

        <div class="col-md-4 fv-plugins-icon-container">
          <label
            class="form-label w-100"
            for="modalAddCard">
            Last 4 Digits</label>
          <div class="input-group input-group-merge has-validation">
            <input v-model="form.cc_last_4_digits"
                   class="form-control credit-card-mask"
                   type="number"
                   maxlength="4"
                   placeholder="0000"
                   >
          </div>
          <small v-if="errors.cc_last_4_digits" class="text-danger ">
            {{ errors.cc_last_4_digits[0] }}
          </small>
        </div>

        <div class="col-md-4">
          <label class="form-label" for="modalAddCardName">Expiry Month</label>
          <input
            v-model="form.cc_exp_month"
            type="number"
            maxlength="2"
            class="form-control"
            placeholder="00"
          >
          <small v-if="errors.cc_exp_month" class="text-danger">
            {{ errors.cc_exp_month[0] }}
          </small>
        </div>

        <div class="col-md-4">
          <label class="form-label" for="modalAddCardExpiryDate">Expiry Year</label>
          <input
            v-model="form.cc_exp_year"
            type="number"
            maxlength="4"
            class="form-control expiry-date-mask"
          >
          <small v-if="errors.cc_exp_year" class="text-danger">
            {{ errors.cc_exp_year[0] }}
          </small>
        </div>

        <div class="col-12 text-center mt-2">
          <button v-if="!loading" type="submit" class="btn btn-primary me-3 waves-effect waves-light">Submit</button>
          <button v-else type="submit" disabled class="btn btn-primary me-3 waves-effect waves-light">
            <i class="fa fa-circle-notch fa-spin"></i>
          </button>
          <button
            @click.prevent="$emit('emit-close-modal')"
            type="reset"
            class="btn btn-dark btn-reset waves-effect" data-bs-dismiss="modal" aria-label="Close">Close</button>
        </div>

      </form>
    </div>
  </div>
</template>

<style scoped lang="scss">

</style>
