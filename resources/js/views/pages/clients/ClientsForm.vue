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
  },
);

const client = ref({});
const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);
const submitted = ref(false);
const errors = ref({});

const form = reactive({
  company_name: 'None',
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

watch(client, (newClient) => {
  if (hash.value && newClient) {
    form.company_name = newClient.company_name || '';
    form.company_email = newClient.company_email || '';
    form.company_phone = newClient.company_phone || '';
    form.company_address = newClient.company_address || '';
    form.main_contact_first_name = newClient.main_contact_first_name || '';
    form.main_contact_last_name = newClient.main_contact_last_name || '';
    form.main_contact_phone = newClient.main_contact_phone || '';
    form.main_contact_email = newClient.main_contact_email || '';
    form.ap_first_name = newClient.ap_first_name || '';
    form.ap_last_name = newClient.ap_last_name || '';
    form.ap_phone = newClient.ap_phone || '';
    form.ap_email = newClient.ap_email || '';
    form.notes = newClient.notes || '';
  }
});

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

      // Empty form fields
      Object.keys(form).forEach(function(key) {

        if(import.meta.env.VITE_APP_ENV === 'local'){
          console.log(key); // key
          console.log(form[key]); // value
        }

          form[key] = '';
      });
    }

  }).catch((error) => {
    if([401, 402, 422].includes(error.response.status)){
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

      if(import.meta.env.VITE_APP_ENV === 'local'){
        console.log("Client SHow", client.value);
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
                  <span v-if="errors.company_name" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-6">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Company Address</label>
                  <input v-model="form.company_address" type="text" class="form-control">
                  <span v-if="errors.company_address" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">First Name</label>
                  <input v-model="form.main_contact_first_name" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_first_name" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Last Name</label>
                  <input v-model="form.main_contact_last_name" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_last_name" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Email</label>
                  <input v-model="form.main_contact_email" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_email" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Phone</label>
                  <input v-model="form.main_contact_phone" type="text" class="form-control"
                  >
                  <span v-if="errors.main_contact_phone" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP First Name</label>
                  <input v-model="form.ap_first_name" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_first_name" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Last Name</label>
                  <input v-model="form.ap_last_name" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_last_name" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Email</label>
                  <input v-model="form.ap_email" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_email" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-md-3">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">AP Phone</label>
                  <input v-model="form.ap_phone" type="text" class="form-control"
                  >
                  <span v-if="errors.ap_phone" class="text-danger text-center"></span>
                </div>
              </div>

              <div class="card-body col-12">
                <div class="form-group">
                  <label for="defaultFormControlInput" class="form-label">Notes</label>
                  <textarea v-model="form.notes" class="form-control" rows="3"></textarea>
                  <span v-if="errors.notes" class="text-danger text-center"></span>
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
                        <h2>Title</h2>
                        <slot></slot>
                        <button @click="closeModal">Close</button>
                      </div>
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
