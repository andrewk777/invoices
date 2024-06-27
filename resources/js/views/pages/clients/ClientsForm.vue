<script setup>
import {ref, watch, reactive, computed, onMounted, onBeforeMount} from 'vue';
import axios from "axios";

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

  await axios.post('/api/clients/store', formData, {
    headers: {
      'content-type': 'multipart/form-data',
      'Accept' : 'application/json',
      "Authorization" : "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success){
      submitted.value = true;
      resetForm();
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

const updateClient = async (hash) => {
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

  await axios.post('/api/clients/update/'+hash, formData, {
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

const getClient = async (hash) => {
  // Get token from local storage
  await axios.get('/api/clients/show/'+hash, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
    params: {
      hash: hash.value
    }
  }).then((response) => {
    if (response.data.success) {
      client.value = response.data.client;
      populateForm(client.value);

      if(import.meta.env.VITE_APP_ENV === 'local'){
        console.log("Client Show", client.value);
      }
    }
  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

onMounted(async () => {
  if(hash.value){
    await getClient(hash.value);
  }else{
    resetForm();
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
  emit('close');
};

defineExpose({
  openModal,
});
</script>

<template>
  <div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

      <div class="col-md-6 justify-content-first mb-3">
        <h3 class="card-header">Customers</h3>
      </div>

      <div class="col-md-6 text-right mb-3">
        <router-link
          v-if="hash"
          class="btn btn-info waves-effect waves-light mr-2 btn-sm"
          exact
          :to="{name: 'ClientsCreate'}">
          Add Customer
        </router-link>
        <router-link
          class="btn btn-info waves-effect waves-light btn-sm"
          exact
          :to="{name: 'ClientsView'}">
          All Customers
        </router-link>
      </div>

      <div class="col-12">
        <div class="card">

          <h5 class="card-header">
            {{ hash ? 'Edit Customer' : 'Add Customer' }}
          </h5>

          <form>
            <div class="row p-3">

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
                  <input v-model="form.main_contact_first_name" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_first_name" class="text-danger text-center">
                    {{ errors.main_contact_first_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Last Name</label>
                  <input v-model="form.main_contact_last_name" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_last_name" class="text-danger text-center">
                    {{ errors.main_contact_last_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Email</label>
                  <input v-model="form.main_contact_email" type="text" class="form-control"
                  >
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
                  <input v-model="form.ap_last_name" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_last_name" class="text-danger text-center">
                    {{ errors.ap_last_name[0] }}
                  </span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Email</label>
                  <input v-model="form.ap_email" type="text" class="form-control"
                  >
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
                  <label for="defaultFormControlInput" class="form-label">Notes</label>
                  <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
                  <span v-if="errors.notes" class="text-danger text-center">
                    {{ errors.notes[0] }}
                  </span>
                </div>
              </div>

              <div v-if="hash" class="card-body col-md-6">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Credit Cards</label><br>

                  <button
                    @click.prevent="isModalVisible = true"
                    type="button"
                          class="btn btn-primary waves-effect waves-light btn-sm">
                    Add Credit Card
                  </button>

                  <transition name="modal">
                    <div v-if="isModalVisible" class="modal">
                      <div class="modal-overlay" @click="closeModal"></div>
                        <div class="modal-content">
                          <div class="modal-body">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            <div class="text-center mb-6">
                              <h4 class="mb-2">Add New Card</h4>
                              <p>Add new card to complete payment</p>
                            </div>
                            <form id="addNewCCForm" class="row g-6 fv-plugins-bootstrap5 fv-plugins-framework" onsubmit="return false" novalidate="novalidate">
                              <div class="col-12 fv-plugins-icon-container">
                                <label class="form-label w-100" for="modalAddCard">Card Number</label>
                                <div class="input-group input-group-merge has-validation">
                                  <input id="modalAddCard" name="modalAddCard" class="form-control credit-card-mask" type="text" placeholder="1356 3215 6548 7898" aria-describedby="modalAddCard2">
                                  <span class="input-group-text cursor-pointer p-1" id="modalAddCard2"><span class="card-type"></span></span>
                                </div><div class="fv-plugins-message-container fv-plugins-message-container--enabled invalid-feedback"></div>
                              </div>
                              <div class="col-12 col-md-6">
                                <label class="form-label" for="modalAddCardName">Name</label>
                                <input type="text" id="modalAddCardName" class="form-control" placeholder="John Doe">
                              </div>
                              <div class="col-6 col-md-3">
                                <label class="form-label" for="modalAddCardExpiryDate">Exp. Date</label>
                                <input type="text" id="modalAddCardExpiryDate" class="form-control expiry-date-mask" placeholder="MM/YY">
                              </div>
                              <div class="col-6 col-md-3">
                                <label class="form-label" for="modalAddCardCvv">CVV Code</label>
                                <div class="input-group input-group-merge">
                                  <input type="text" id="modalAddCardCvv" class="form-control cvv-code-mask" maxlength="3" placeholder="654">
                                  <span class="input-group-text cursor-pointer ps-0" id="modalAddCardCvv2"><i class="text-muted ti ti-help" data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Card Verification Value" data-bs-original-title="Card Verification Value"></i></span>
                                </div>
                              </div>
                              <div class="col-12">
                                <div class="form-check form-switch">
                                  <input type="checkbox" class="form-check-input" id="futureAddress">
                                  <label for="futureAddress" class="switch-label">Save card for future billing?</label>
                                </div>
                              </div>
                              <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary me-3 waves-effect waves-light">Submit</button>
                                <button type="reset" class="btn btn-label-secondary btn-reset waves-effect" data-bs-dismiss="modal" aria-label="Close">Cancel</button>
                              </div>
                              <input type="hidden"></form>
                          </div>
                        </div>
                        <button @click="closeModal">Close</button>
                    </div>
                  </transition>


                  <select v-if="client.credit_cards?.length > 0" class="form-control">
                    <option value=""></option>
                    <option v-for="card in client.credit_cards" :key="card.id">
                      {{ card.cc_provider }}
                    </option>
                  </select>
                </div>
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
