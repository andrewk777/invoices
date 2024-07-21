<script setup>
import {ref, watch, reactive, computed, onMounted} from 'vue';
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";
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

const client = ref({});
const updatedClient = ref({});
watch(
  () => client.value,
  (updatedClient) => {
    updatedClient.value = updatedClient;
  }
);

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

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
    if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key]);
    }
  });

  try {
    const response = await apiClientAuto.post('/clients/store', form);

    if (response.data.success) {

      hash.value = response.data.client.hash;
      client.value = response.data.client;

      submitted.value = true;

      // resetForm();
      if (action === 'close') {
        window.location.href = '/clients';
      }

    }

  } catch (error) {

    if ([401, 402, 422].includes(error.response.status)) {
      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

  }

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

    if (form[key] !== null && form[key] !== '') {
      formData.append(key, form[key]);
    }
  });

  try {
    const response = await apiClientAuto.post('/clients/update/' + hash, formData);
    if (response.data.success) {
      submitted.value = true;
      if (action === 'close') {
        window.location.href = '/clients';
      }
    }

  } catch (error) {

    if (error.response && [401, 402, 422].includes(error.response.status)){
      if (Object.keys(error.response?.data?.errors).length > 0) {
        errors.value = error.response?.data?.errors;
      }

      if (error.response?.data?.server_error) {
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

  }
  loading.value = false;
}

const getClient = async (hash) => {

  try {
    const response = await apiClientAuto.get('/clients/show/' + hash);

    if (response.data.success) {
      client.value = response.data.client;
      populateForm(client.value);
      creditCards.value = client.value.credit_cards;
    }

  } catch (error) {
    handleErrors(error, errors.value);
  }
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
const emit = defineEmits([
  'close-cc-dialogue',
  'assign-default-cc',
]);

const openModal = () => {
  ccDialogueVisible.value = true;
};

const closeModal = () => {
  ccDialogueVisible.value = false;
};

const assignDefaultCreditCard = (creditCard) => {
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
      <h1 class="card-header">Client: {{ form.company_name }}</h1>
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
        save & close
      </VBtn>

      <router-link
        class="btn btn-info waves-effect waves-light mr-2"
        exact
        :to="{name: 'ClientsView'}"
      >
        <VBtn variant="flat" :size="'small'" :loading="loading">  List All </VBtn>
      </router-link>



      <VBtn
        :size="'small'"
        @click.prevent="$router.go(-1)"
        type="submit"
        color="error"
        class="mr-2 btn-info waves-effect waves-light"
      >
        {{ 'close' }}
      </VBtn>
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
        <v-card class="px-4 py-4 mb-4 v-card--outlined border-success">
            <h3 class="mb-4">Company Info</h3>
            <VRow>                
                <!-- 👉 Company Name -->
                <VCol cols="12" md="4">
                    <AppTextField
                    v-model="form.company_name"
                    label="Company Name"
                    :error-messages="errors.company_name"
                    />
                </VCol>
                <!-- 👉 Company Address -->
                <VCol cols="12" md="8">
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
            </VRow>
        </v-card>
        <v-card class="px-4 py-4 mb-4">
            <h3 class="mb-4">Main Contact</h3>
            <VRow>
                <!-- 👉 Main Contact First Name -->
                <VCol cols="12" md="3">
                    <AppTextField
                    v-model="form.main_contact_first_name"
                    label="Main Contact First Name"
                    :error-messages="errors.main_contact_first_name"
                    />
                </VCol>

                <!-- 👉 Main Contact Last Name -->
                <VCol cols="12" md="3">
                    <AppTextField
                    v-model="form.main_contact_last_name"
                    label="Main Contact Last Name"
                    :error-messages="errors.main_contact_last_name"
                    />
                </VCol>

                <!-- 👉 Main Contact Email -->
                <VCol cols="12" md="3">
                    <AppTextField
                    v-model="form.main_contact_email"
                    label="Main Contact Email"
                    :error-messages="errors.main_contact_email"
                    />
                </VCol>

                <!-- 👉 Main Contact Phone -->
                <VCol cols="12" md="3">
                    <AppTextField
                    v-model="form.main_contact_phone"
                    label="Main Contact Phone"
                    :error-messages="errors.main_contact_phone"
                    />
                </VCol>
            </VRow>
        </v-card>
        <v-card class="px-4 py-4 mb-4">
            <h3 class="mb-4">Accounts Payable</h3>
            <VRow>
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
            </VRow>
        </v-card>


        <VRow class="p-2">
          


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
                    :updatedClient="updatedClient"
                    :token="token"
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

  <VRow v-if="hash && client">
    <VCol cols="12">
      <ClientsUsersIndex
        v-if="client && Object.keys(client).length > 0"
        :hash="hash"
        :users="client.users"
        :client="client"
        :updatedClient="updatedClient"
        :token="token"
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
