<script setup>
import {ref, watch, reactive, computed, onMounted} from 'vue';
import axios from "axios";
import CreditCardForm from "@/views/pages/clients/credit-card/CreditCardForm.vue";

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
const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});
const ccDialogueVisible = ref(false);

const form = reactive({
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

const submitClient = async () => {
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
      submitted.value = true;
      resetForm();
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

const updateClient = async (hash) => {
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
const emit = defineEmits(['close-cc-dialogue']);

const openModal = () => {
  ccDialogueVisible.value = true;
};

const closeModal = () => {
  ccDialogueVisible.value = false;
};


// const closeModal = () => {
//   isModalVisible.value = false;
//   emit('close');
// };

defineExpose({
  openModal,
});
</script>

<template>
  <!--  <div class="container-xxl flex-grow-1 container-p-y">-->

  <!--    <div class="row">-->

  <!--      <div class="col-md-6 justify-content-first mb-3">-->
  <!--        <h3 class="card-header">Customers</h3>-->
  <!--      </div>-->

  <!--      <div class="col-md-6 text-right mb-3">-->
  <!--        <router-link-->
  <!--          v-if="hash"-->
  <!--          class="btn btn-info waves-effect waves-light mr-2 btn-sm"-->
  <!--          exact-->
  <!--          :to="{name: 'ClientsCreate'}">-->
  <!--          Add Customer-->
  <!--        </router-link>-->
  <!--        <router-link-->
  <!--          class="btn btn-info waves-effect waves-light btn-sm"-->
  <!--          exact-->
  <!--          :to="{name: 'ClientsView'}">-->
  <!--          All Customers-->
  <!--        </router-link>-->
  <!--      </div>-->

  <!--      <div class="col-12">-->
  <!--        <div class="card">-->

  <!--          <h5 class="card-header">-->
  <!--            {{ hash ? 'Edit Customer' : 'Add Customer' }}-->
  <!--          </h5>-->

  <!--          <form>-->
  <!--            <div class="row p-2">-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Company Name</label>-->
  <!--                  <input v-model="form.company_name" type="text" class="form-control">-->
  <!--                  <span v-if="errors.company_name" class="text-danger text-center">-->
  <!--                    {{ errors.company_name[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Company Address</label>-->
  <!--                  <input v-model="form.company_address" type="text" class="form-control">-->
  <!--                  <span v-if="errors.company_address" class="text-danger text-center">-->
  <!--                    {{ errors.company_address[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Company Email</label>-->
  <!--                  <input v-model="form.company_email" type="text" class="form-control">-->
  <!--                  <span v-if="errors.company_email" class="text-danger text-center">-->
  <!--                    {{ errors.company_email[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Company Phone No.</label>-->
  <!--                  <input v-model="form.company_phone" type="text" class="form-control">-->
  <!--                  <span v-if="errors.company_phone" class="text-danger text-center">-->
  <!--                    {{ errors.company_phone[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">First Name</label>-->
  <!--                  <input v-model="form.main_contact_first_name" type="text" class="form-control">-->
  <!--                  <span v-if="errors.main_contact_first_name" class="text-danger text-center">-->
  <!--                    {{ errors.main_contact_first_name[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Last Name</label>-->
  <!--                  <input v-model="form.main_contact_last_name" type="text" class="form-control">-->
  <!--                  <span v-if="errors.main_contact_last_name" class="text-danger text-center">-->
  <!--                    {{ errors.main_contact_last_name[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Email</label>-->
  <!--                  <input v-model="form.main_contact_email" type="text" class="form-control">-->
  <!--                  <span v-if="errors.main_contact_email" class="text-danger text-center">-->
  <!--                    {{ errors.main_contact_email[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Phone</label>-->
  <!--                  <input v-model="form.main_contact_phone" type="text" class="form-control"-->
  <!--                  >-->
  <!--                  <span v-if="errors.main_contact_phone" class="text-danger text-center">-->
  <!--                    {{ errors.main_contact_phone[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">AP First Name</label>-->
  <!--                  <input v-model="form.ap_first_name" type="text" class="form-control"-->
  <!--                  >-->
  <!--                  <span v-if="errors.ap_first_name" class="text-danger text-center">-->
  <!--                    {{ errors.ap_first_name[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">AP Last Name</label>-->
  <!--                  <input v-model="form.ap_last_name" type="text" class="form-control">-->
  <!--                  <span v-if="errors.ap_last_name" class="text-danger text-center">-->
  <!--                    {{ errors.ap_last_name[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">AP Email</label>-->
  <!--                  <input v-model="form.ap_email" type="text" class="form-control">-->
  <!--                  <span v-if="errors.ap_email" class="text-danger text-center">-->
  <!--                    {{ errors.ap_email[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-md-3">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">AP Phone</label>-->
  <!--                  <input v-model="form.ap_phone" type="text" class="form-control"-->
  <!--                  >-->
  <!--                  <span v-if="errors.ap_phone" class="text-danger text-center">-->
  <!--                    {{ errors.ap_phone[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div class="card-body col-12">-->
  <!--                <div class="form-group">-->
  <!--                  <label class="form-label">Notes</label>-->
  <!--                  <textarea v-model="form.notes" class="form-control" rows="3"></textarea>-->
  <!--                  <span v-if="errors.notes" class="text-danger text-center">-->
  <!--                    {{ errors.notes[0] }}-->
  <!--                  </span>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--              <div v-if="hash" class="col-md-4">-->
  <!--                <div class="form-group">-->
  <!--                  <label for="defaultFormControlInput" class="form-label">Credit Cards</label><br>-->

  <!--                  <a href=""-->
  <!--                    @click.prevent="isModalVisible = true" class="">-->
  <!--                    Add Credit Card-->
  <!--                  </a>-->

  <!--                  <transition name="modal">-->
  <!--                    <div v-if="isModalVisible" class="modal">-->
  <!--                      <div class="modal-overlay" @click="closeModal"></div>-->

  <!--                        <credit-card-form-->
  <!--                          :client="client"-->
  <!--                          :token="token.value"-->
  <!--                          @emit-close-modal="closeModal"-->
  <!--                        ></credit-card-form>-->

  <!--                    </div>-->
  <!--                  </transition>-->
  <!--                </div>-->

  <!--                <select v-if="client.credit_cards?.length > 0" class="form-control mt-2">-->
  <!--                  <option value="">Select Credit Card</option>-->
  <!--                  <option v-for="card in client.credit_cards" :key="card.id">-->
  <!--                    {{ card.cc_last_4_digits+' - '+card.cc_type }}-->
  <!--                  </option>-->
  <!--                </select>-->

  <!--              </div>-->

  <!--              <transition name="modal">-->
  <!--                <div v-if="submitted" class="col-12 justify-content-center d-flex">-->
  <!--                  <p class="p-2 bg-success text-white justify-center text-center w-50">-->
  <!--                    {{ hash ? 'Customer Updated' : 'Customer Added' }}-->
  <!--                  </p>-->
  <!--                </div>-->
  <!--              </transition>-->

  <!--              <div class="col-12 justify-content-center d-flex">-->
  <!--                <div class="d-flex">-->
  <!--                  <button-->
  <!--                    @click.prevent="hash ? updateClient(hash) : submitClient()"-->
  <!--                    v-if="!loading"-->
  <!--                    class="btn btn-sm btn-success"-->
  <!--                  >-->
  <!--                    {{ hash ? 'Update Customer' : 'Add Customer' }}-->
  <!--                  </button>-->
  <!--                  <button v-else class="btn btn-sm btn-success" disabled>-->
  <!--                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>-->
  <!--                    Loading...-->
  <!--                  </button>-->
  <!--                </div>-->
  <!--              </div>-->

  <!--            </div>-->
  <!--          </form>-->

  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->

  <!--    <div v-if="hash && client.users" class="row border-top-dashed">-->
  <!--      <div class="card">-->
  <!--        <h5 class="card-header">Users</h5>-->
  <!--        <div class="table-responsive text-nowrap">-->
  <!--          <table class="table">-->
  <!--            <thead class="table-light">-->
  <!--              <tr>-->
  <!--                <th>First Name</th>-->
  <!--                <th>Last Name</th>-->
  <!--                <th>Role</th>-->
  <!--                <th>Last Login</th>-->
  <!--              </tr>-->
  <!--            </thead>-->
  <!--            <tbody class="table-border-bottom-0">-->

  <!--            <tr v-for="user in client.users" :key="user">-->
  <!--              <td>-->
  <!--                <span class="fw-medium">{{ user.first_name }}</span>-->
  <!--              </td>-->
  <!--              <td>-->
  <!--                {{ user.last_name }}-->
  <!--              </td>-->
  <!--              <td>-->

  <!--              </td>-->
  <!--              <td>-->
  <!--                {{ user.role }}-->
  <!--              </td>-->
  <!--              <td>-->
  <!--                {{ user.last_login }}-->
  <!--              </td>-->
  <!--            </tr>-->

  <!--            </tbody>-->
  <!--          </table>-->
  <!--        </div>-->
  <!--      </div>-->
  <!--    </div>-->

  <!--  </div>-->

  <VRow class="mb-2">
    <VCol cols="6">
      <h3 class="card-header">Customers</h3>
    </VCol>

    <VCol cols="6" class="text-right">
      <router-link
        class="btn btn-info waves-effect waves-light mr-2 btn-sm"
        exact
        :to="{name: 'ClientsCreate'}"
      >
        <VBtn variant="flat">
          Create Customer
        </VBtn>
      </router-link>
    </VCol>
  </VRow>

  <VRow>
    <VCol col="12">

      <VForm @submit.prevent="hash ? updateClient(hash) : submitClient()">
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
                  />
                </VCardText>

              </VCard>
            </VDialog>

            <VSelect
              v-if="client.credit_cards?.length > 0"
              v-model="selectedCreditCard"
              :items="client.credit_cards"
              item-title="cc_last_4_digits"
              item-value="id"
              label="Select Credit Card"
              class="mt-2"
            />
          </VCol>

          <!-- 👉 Success Message -->
          <VCol cols="12" v-if="submitted" class="justify-center">
            <VAlert type="success" class="text-center" closable>
              {{ hash ? 'Customer Updated' : 'Customer Added' }}
            </VAlert>
          </VCol>

          <!-- 👉 Submit Button -->
          <VCol cols="12" class="d-flex justify-center">
            <VBtn type="submit" color="success" :loading="loading">
              {{ hash ? 'Update Customer' : 'Add Customer' }}
            </VBtn>
          </VCol>

        </VRow>
      </VForm>

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
