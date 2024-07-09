<script setup>
import {ref, watch, reactive, computed, onMounted} from 'vue';
import axios from "axios";
import CreditCardForm from "@/views/pages/clients/credit-card/CreditCardForm.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import DialogCloseBtn
  from "../../../../../vuexy-vue-laravel-version/typescript-version/full-version/resources/ts/@core/components/DialogCloseBtn.vue";
import ClientsUsersIndex from "@/views/pages/clients/client-users/ClientsUsersIndex.vue";

// For routing with params
const hash = ref('');
const route = useRoute();
hash.value = route.params.hash;
watch(
  () => route.params.hash,
  () => {
    hash.value = route.params.hash;
    if (hash.value) {
      getClient(hash.value);
    } else {
      resetForm();
    }
  },
);

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const client = ref({});
const creditCards = ref([]);

const ccDialogueVisible = ref(false);

const form = reactive({
  default_credit_card_id: '',
  company_name: '',
  company_email: '',
  company_phone: '',
  company_address: '',
  main_contact_first_name: '',
  main_contact_last_name: '',
  main_contact_phone: '',
  main_contact_email: '',
  ap_first_name: '',
  ap_last_name: '',
  ap_phone: '',
  ap_email: '',
  notes: '',
});

const populateForm = (clientData) => {
  for (const key in form) {
    if (clientData.hasOwnProperty(key)) {
      form[key] = clientData[key] || '';
    }
  }
};

const resetForm = () => {
  for (const key in form) {
    form[key] = '';
  }
};

const submitClient = async (action = null) => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  const formData = new FormData();
  // iterate and add form data
  Object.keys(form).forEach(function (key) {
    console.log(key); // key
    if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key]);
    }
  });

  await axios.post('/api/clients/store', formData, {
    headers: {
      'content-type': 'multipart/form-data',
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success) {
      hash.value = response.data.client.hash;
      submitted.value = true;
      // resetForm();
      if (action === 'close') {
        window.location.href = '/clients';
      }
    }
  }).catch((error) => {
    if ([401, 402, 422].includes(error.response.status)) {
      console.log(error.response);

      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
        if (import.meta.env.VITE_APP_ENV === 'local') {
          console.log("Validation errors", errors.value);
        }
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
  loading.value = false;
}

const updateClient = async (hash, action = null) => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  const formData = new FormData();
  // iterate and add form data
  Object.keys(form).forEach(function (key) {
    console.log(key); // key
    if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key]);
    }
  });

  await axios.post('/api/clients/update/' + hash, formData, {
    headers: {
      'content-type': 'multipart/form-data',
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success) {
      submitted.value = true;
      if (action === 'close') {
        window.location.href = '/clients';
      }
    }
  }).catch((error) => {
    if (error.response && [401, 402, 422].includes(error.response.status)) {
      console.log(error.response);

      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
  loading.value = false;
}

const getClient = async (hash) => {
  // Get token from local storage
  await axios.get('/api/clients/show/' + hash, {
    headers: {
      "Authorization": "Bearer " + token.value,
      'Accept': 'application/json',
    },
    params: {
      hash: hash.value
    }
  }).then((response) => {
    if (response.data.success) {
      client.value = response.data.client;
      populateForm(client.value);
      creditCards.value = client.value.credit_cards;

      if (import.meta.env.VITE_APP_ENV === 'local') {
        console.log("Client Show", client.value);
      }
    }
  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

onMounted(async () => {
  if (hash.value) {
    await getClient(hash.value);
  } else {
    resetForm();
  }
});

// Modal Popup
const emit = defineEmits(['close-cc-dialogue', 'assign-default-cc']);

const openModal = () => {
  ccDialogueVisible.value = true;
};

const closeModal = () => {
  ccDialogueVisible.value = false;
};

const assignDefaultCreditCard = (creditCard) => {
  //creditCards.value.push(creditCard);
  getClient(hash.value);
  closeModal();
};

defineExpose({
  openModal,
});
</script>

<template>
  <VRow class="mb-2">
    <VCol cols="4">
      <h3 class="card-header">Clients</h3>
    </VCol>

    <VCol cols="8" class="text-right flex">

      <VBtn
        :size="'small'"
        @click.prevent="hash ? updateClient(hash) : submitClient()"
        type="submit"
        color="success"
        :loading="loading"
        class="mr-2"
      >
        {{ 'save' }}
      </VBtn>

      <VBtn
        :size="'small'"
        @click.prevent="hash ? updateClient(hash, 'close') : submitClient('close')"
        type="submit"
        color="success"
        :loading="loading"
        class="mr-2 btn-info waves-effect waves-light"
      >
        {{ 'save and close' }}
      </VBtn>

      <router-link
        class="btn btn-info waves-effect waves-light"
        exact
        :to="{name: 'ClientsView'}"
      >
        <VBtn variant="flat" :size="'small'">
          Clients List
        </VBtn>
      </router-link>
    </VCol>

    <VCol cols="12">
      <VAlert
        v-if="Object.keys(errors).length > 0"
        class="text-center mb-2"
        type="error"
      >
        <p v-for="(error, index) in Object.keys(errors)" :key="index" class="mb-0">
          {{ errors[error][0] }}
        </p>
      </VAlert>
    </VCol>

    <!-- 👉 Success Message -->
    <VCol cols="12" v-if="submitted" class="justify-center">
      <VAlert type="success" class="text-center" closable>
        {{ hash ? 'Client Updated' : 'Client Created' }}
      </VAlert>
    </VCol>

  </VRow>

  <VRow>
    <VCol col="12">

      <VForm>
        <VRow class="p-2">
          <!-- 👉 Company Name -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.company_name"
              label="Company Name"
              :error-messages="errors.company_name"
            />
          </VCol>

          <!-- 👉 Company Address -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.company_address"
              label="Company Address"
              :error-messages="errors.company_address"
            />
          </VCol>

          <!-- 👉 Company Email -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.company_email"
              label="Company Email"
              :error-messages="errors.company_email"
            />
          </VCol>

          <!-- 👉 Company Phone No. -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.company_phone"
              label="Company Phone No."
              :error-messages="errors.company_phone"
            />
          </VCol>

          <!-- 👉 First Name -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.main_contact_first_name"
              label="First Name"
              :error-messages="errors.main_contact_first_name"
            />
          </VCol>

          <!-- 👉 Last Name -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.main_contact_last_name"
              label="Last Name"
              :error-messages="errors.main_contact_last_name"
            />
          </VCol>

          <!-- 👉 Email -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.main_contact_email"
              label="Email"
              :error-messages="errors.main_contact_email"
            />
          </VCol>

          <!-- 👉 Phone -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.main_contact_phone"
              label="Phone"
              :error-messages="errors.main_contact_phone"
            />
          </VCol>

          <!-- 👉 AP First Name -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.ap_first_name"
              label="AP First Name"
              :error-messages="errors.ap_first_name"
            />
          </VCol>

          <!-- 👉 AP Last Name -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.ap_last_name"
              label="AP Last Name"
              :error-messages="errors.ap_last_name"
            />
          </VCol>

          <!-- 👉 AP Email -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.ap_email"
              label="AP Email"
              :error-messages="errors.ap_email"
            />
          </VCol>

          <!-- 👉 AP Phone -->
          <VCol cols="12" md="3">
            <AppTextField
              v-model="form.ap_phone"
              label="AP Phone"
              :error-messages="errors.ap_phone"
            />
          </VCol>

          <!-- 👉 Notes -->
          <VCol cols="12">
            <VTextarea
              v-model="form.notes"
              label="Notes"
              rows="3"
              :error-messages="errors.notes"
            />
          </VCol>

          <!-- 👉 Credit Cards -->
          <VCol cols="12" md="4" class="d-block" v-if="hash">
            <VDialog
              v-model="ccDialogueVisible"
              max-width="600"
            >
              <!-- Dialog Activator -->
              <template #activator="{ props }">
                <VBtn v-bind="props">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="icon icon-tabler icon-tabler-credit-card-filled"
                    width="30" height="30"
                    viewBox="0 0 24 24"
                    stroke-width="1.5"
                    stroke="#2c3e50"
                    fill="none"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                  >
                    <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
                    <path d="M22 10v6a4 4 0 0 1 -4 4h-12a4 4 0 0 1 -4 -4v-6h20zm-14.99 4h-.01a1 1 0 1 0 .01 2a1 1 0 0 0 0 -2zm5.99 0h-2a1 1 0 0 0 0 2h2a1 1 0 0 0 0 -2zm5 -10a4 4 0 0 1 4 4h-20a4 4 0 0 1 4 -4h12z" stroke-width="0" fill="currentColor" />
                  </svg>
                  Add Credit Card
                </VBtn>
              </template>

              <!-- Dialog close btn -->
              <DialogCloseBtn @click="ccDialogueVisible = !ccDialogueVisible" />

              <!-- Dialog Content -->
              <VCard title="Add New Card">
                <VCardText>
                  <CreditCardForm
                    :client="client"
                    :token="token.value"
                    @close-cc-dialogue="closeModal"
                    @assign-default-cc="assignDefaultCreditCard"
                  />
                </VCardText>

              </VCard>
            </VDialog>

            <VSelect
              v-if="creditCards?.length > 0"
              v-model="form.default_credit_card_id"
              :items="creditCards"
              item-title="cc_last_4_digits"
              item-value="id"
              label="Select Credit Card"
              class="mt-2"
            />
          </VCol>

        </VRow>
      </VForm>

    </VCol>
  </VRow>

  <VRow v-if="hash && client.users?.length > 0">
    <VCol cols="12">
      <ClientsUsersIndex
        :users="client.users"
      />
    </VCol>
  </VRow>

</template>

<style scoped lang="scss">

.modal {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 9999;
}

.modal-overlay {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal-content {
  background-color: white;
  padding: 20px;
  border-radius: 4px;
  max-width: 500px;
  z-index: 10000;
}

/* Enter and leave animations */
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

/* Content transition */
.modal-content {
  transition: transform 0.3s ease;
}

.modal-enter-from .modal-content,
.modal-leave-to .modal-content {
  transform: scale(0.8);
}

</style>
