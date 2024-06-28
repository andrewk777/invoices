<script setup>
import {ref, watch, reactive, computed, onMounted} from 'vue';
import axios from "axios";
import {router} from "@/plugins/1.router/index.js";

// For routing with params
const hash = ref('');
const route = useRoute();
hash.value = route.params.hash;
watch(
  () => route.params.hash,
  () => {
    hash.value = route.params.hash;
    if (hash.value) {
      getInvoice(hash.value);
    }
  },
);

const invoice = ref({});
const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const form = reactive({
  'company_id': '',
  'client_id': '',
  'invoice_num': '',
  'invoice_type': '',
  'status': '',
  'currency': '',
  'invoice_date': '',
  'invoice_due': '',
  'na': null,
  'can_pay_with_cc': null,
  'sub_total': '',
  'tax': '',
  'total': '',
  'total_paid': '',
  'balance': '',
});

const populateForm = (invoiceData) => {
  for (const key in form) {
    if (invoiceData.hasOwnProperty(key)) {
      form[key] = invoiceData[key] || '';
    }
  }
};

const resetForm = () => {
  for (const key in form) {
    form[key] = '';
  }
};

const submitInvoice = async () => {
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

  await axios.post('/api/invoices/store', formData, {
    headers: {
      'content-type': 'multipart/form-data',
      'Accept' : 'application/json',
      "Authorization" : "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success){
      submitted.value = true;
      router.push({ name: 'InvoicesView', params: { hash: response.data.invoice.hash } });
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

const updateInvoice = async (hash) => {
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

  await axios.post('/api/invoices/update/'+hash, formData, {
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
    if(error.response && [401, 402, 422].includes(error.response.status)){
      console.log(error.response);

      if(Object.keys(error.response?.data?.errors).length > 0){
        errors.value = error.response?.data?.errors;
      }

      if(error.response?.data?.server_error){
        errors.value.server_error = 'Server error. Please try again later or contact your admin.';
      }
    }

    console.log(error);
  });
  loading.value = false;
}

const getInvoice = async (hash) => {
  // Get token from local storage
  await axios.get('/api/invoices/show/'+hash, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
    params: {
      hash: hash.value
    }
  }).then((response) => {
    if (response.data.success) {
      invoice.value = response.data.invoice;
      populateForm(invoice.value);

      if(import.meta.env.VITE_APP_ENV === 'local'){
        console.log("Invoice Show", invoice.value);
      }
    }
  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

onMounted(async () => {
  if(hash.value){
    await getInvoice(hash.value);
  }
});

// Modal Popup
const emit = defineEmits(['close']);

const isModalVisible = ref(false);

const openModal = () => {
  isModalVisible.value = true;
};

const closeModal = () => {
  isModalVisible.value = false;
};

</script>

<template>
  <div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

      <div class="col-md-6 justify-content-first mb-3">
        <h3 class="card-header">Invoices</h3>
      </div>

      <div class="col-md-6 text-right mb-3">
        <router-link
          v-if="hash"
          class="btn btn-info waves-effect waves-light mr-2 btn-sm"
          exact
          :to="{name: 'InvoicesCreate'}">
          Add Invoice
        </router-link>
        <router-link
          class="btn btn-info waves-effect waves-light btn-sm"
          exact
          :to="{name: 'InvoicesView'}">
          All Invoices
        </router-link>
      </div>

      <div class="col-12">
        <div class="card">

          <h5 class="card-header">
            {{ hash ? 'Edit Customer' : 'Add Customer' }}
          </h5>

          <form>
            <div class="row p-2">

              <div class="card-body col-md-6">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Company Name</label>
                  <input v-model="form.company_name" type="text" class="form-control">
                  <span v-if="errors.company_name" class="text-danger text-center">
                    {{ errors.company_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-6">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Company Address</label>
                  <input v-model="form.company_address" type="text" class="form-control">
                  <span v-if="errors.company_address" class="text-danger text-center">
                    {{ errors.company_address[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">First Name</label>
                  <input v-model="form.main_contact_first_name" type="text" class="form-control">
                  <span v-if="errors.main_contact_first_name" class="text-danger text-center">
                    {{ errors.main_contact_first_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Last Name</label>
                  <input v-model="form.main_contact_last_name" type="text" class="form-control">
                  <span v-if="errors.main_contact_last_name" class="text-danger text-center">
                    {{ errors.main_contact_last_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Email</label>
                  <input v-model="form.main_contact_email" type="text" class="form-control">
                  <span v-if="errors.main_contact_email" class="text-danger text-center">
                    {{ errors.main_contact_email[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Phone</label>
                  <input v-model="form.main_contact_phone" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_phone" class="text-danger text-center">
                    {{ errors.main_contact_phone[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP First Name</label>
                  <input v-model="form.ap_first_name" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_first_name" class="text-danger text-center">
                    {{ errors.ap_first_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Last Name</label>
                  <input v-model="form.ap_last_name" type="text" class="form-control">
                  <span v-if="errors.ap_last_name" class="text-danger text-center">
                    {{ errors.ap_last_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Email</label>
                  <input v-model="form.ap_email" type="text" class="form-control">
                  <span v-if="errors.ap_email" class="text-danger text-center">
                    {{ errors.ap_email[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Phone</label>
                  <input v-model="form.ap_phone" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_phone" class="text-danger text-center">
                    {{ errors.ap_phone[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-12">
                <div class="form-group">
                  <label class="form-label">Notes</label>
                  <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
                  <span v-if="errors.notes" class="text-danger text-center">
                    {{ errors.notes[0] }}
                  </span>
                </div>
              </div>

              <div v-if="hash" class="col-md-4">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Credit Cards</label><br>

                  <a href=""
                     @click.prevent="isModalVisible = true" class="">
                    Add Credit Card
                  </a>

                  <transition name="modal">
                    <div v-if="isModalVisible" class="modal">
                      <div class="modal-overlay" @click="closeModal"></div>

                      <credit-card-form
                        :client="client"
                        :token="token.value"
                        @emit-close-modal="closeModal"
                      ></credit-card-form>

                    </div>
                  </transition>
                </div>

                <select v-if="client.credit_cards?.length > 0" class="form-control mt-2">
                  <option value="">Select Credit Card</option>
                  <option v-for="card in client.credit_cards" :key="card.id">
                    {{ card.cc_last_4_digits+' - '+card.cc_type }}
                  </option>
                </select>

              </div>

              <transition name="modal">
                <div v-if="submitted" class="col-12 justify-content-center d-flex">
                  <p class="p-2 bg-success text-white justify-center text-center w-50">
                    {{ hash ? 'Customer Updated' : 'Customer Added' }}
                  </p>
                </div>
              </transition>

              <div class="col-12 justify-content-center d-flex">
                <div class="d-flex">
                  <button
                    @click.prevent="hash ? updateClient(hash) : submitClient()"
                    v-if="!loading"
                    class="btn btn-sm btn-success"
                  >
                    {{ hash ? 'Update Customer' : 'Add Customer' }}
                  </button>
                  <button v-else class="btn btn-sm btn-success" disabled>
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Loading...
                  </button>
                </div>
              </div>

            </div>
          </form>

        </div>
      </div>
    </div>

    <div v-if="hash && client.users" class="row border-top-dashed">
      <div class="card">
        <h5 class="card-header">Users</h5>
        <div class="table-responsive text-nowrap">
          <table class="table">
            <thead class="table-light">
            <tr>
              <th>First Name</th>
              <th>Last Name</th>
              <th>Role</th>
              <th>Last Login</th>
            </tr>
            </thead>
            <tbody class="table-border-bottom-0">

            <tr v-for="user in client.users" :key="user">
              <td>
                <span class="fw-medium">{{ user.first_name }}</span>
              </td>
              <td>
                {{ user.last_name }}
              </td>
              <td>

              </td>
              <td>
                {{ user.role }}
              </td>
              <td>
                {{ user.last_login }}
              </td>
            </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>
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
