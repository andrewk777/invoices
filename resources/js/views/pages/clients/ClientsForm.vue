<script setup>
import {ref, watch, onBeforeMount, computed, onMounted} from 'vue';
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
  company_name: hash.value ? client.company_name : '',
  company_email: hash.value ? client.company_email : '',
  company_phone: hash.value ? client.company_phone : '',
  company_address: hash.value ? client.company_address : '',
  main_contact_first_name: hash.value ? client.main_contact_first_name : '',
  main_contact_last_name: hash.value ? client.main_contact_last_name : '',
  main_contact_phone: hash.value ? client.main_contact_phone : '',
  main_contact_email: hash.value ? client.main_contact_email : '',
  ap_first_name: hash.value ? client.ap_first_name : '',
  ap_last_name: hash.value ? client.ap_last_name : '',
  ap_phone: hash.value ? client.ap_phone : '',
  ap_email: hash.value ? client.ap_email : '',
  notes: hash.value ? client.notes : '',
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
  await axios.get('/api/client/show/'+hash, {
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
    }

  }).catch((error) => {
    console.log(error);
  });
  loading.value = false;
}

onBeforeMount(() => {
  if(hash.value){
    getClient(hash.value);
  }
});
</script>

<template>
  <div class="container-xxl flex-grow-1 container-p-y">

    <div class="row">

      <div class="col-12 justify-content-first mb-3">
        <h3 class="card-header">Customers</h3>
      </div>

      <div class="col-12">

        <div class="card">

          <h5 class="card-header">
            {{ hash ? 'Edit Customer' : 'Add Customer' }}
          </h5>

          <div class="row p-3">

            <div class="card-body col-md-6">
              <div class="form-group">
                <label for="defaultFormControlInput" class="form-label">Company Name</label>
                <input v-model="client.company_name" type="text" class="form-control">
                <span v-if="errors.company_name" class="text-danger text-center"></span>
              </div>
            </div>

            <div class="card-body col-md-6">
              <div class="form-group">
                <label for="defaultFormControlInput" class="form-label">Full Address</label>
                <input v-model="client.company_address" type="text" class="form-control">
                <span v-if="errors.company_address" class="text-danger text-center"></span>
              </div>
            </div>

            <div class="card-body col-md-6">
              <div>
                <label for="defaultFormControlInput" class="form-label">Name</label>
                <input type="text" class="form-control" id="defaultFormControlInput" placeholder="John Doe" aria-describedby="defaultFormControlHelp">
                <div id="defaultFormControlHelp" class="form-text">We'll never share your details with anyone else.</div>
              </div>
            </div>

          </div>

        </div>
      </div>

      <div class="col-md-6">
        <div class="card">
          <h5 class="card-header">Float label</h5>
          <div class="card-body">
            <div class="form-floating">
              <input type="text" class="form-control" id="floatingInput" placeholder="John Doe" aria-describedby="floatingInputHelp">
              <label for="floatingInput">Name</label>
              <div id="floatingInputHelp" class="form-text">We'll never share your details with anyone else.</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Form controls -->
      <div class="col-md-6">
        <div class="card">
          <h5 class="card-header">Form Controls</h5>
          <div class="card-body">
            <div class="mb-4">
              <label for="exampleFormControlInput1" class="form-label">Email address</label>
              <input type="email" class="form-control" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>
            <div class="mb-4">
              <label for="exampleFormControlReadOnlyInput1" class="form-label">Read only</label>
              <input class="form-control" type="text" id="exampleFormControlReadOnlyInput1" placeholder="Readonly input here..." readonly="">
            </div>
            <div class="mb-4">
              <label for="exampleFormControlReadOnlyInputPlain1" class="form-label">Read plain</label>
              <input type="text" readonly="" class="form-control-plaintext" id="exampleFormControlReadOnlyInputPlain1" value="email@example.com">
            </div>
            <div class="mb-4">
              <label for="exampleFormControlSelect1" class="form-label">Example select</label>
              <select class="form-select" id="exampleFormControlSelect1" aria-label="Default select example">
                <option selected="">Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div class="mb-4">
              <label for="exampleDataList" class="form-label">Datalist example</label>
              <input class="form-control" list="datalistOptions" id="exampleDataList" placeholder="Type to search...">
              <datalist id="datalistOptions">
                <option value="San Francisco"></option>
                <option value="New York"></option>
                <option value="Seattle"></option>
                <option value="Los Angeles"></option>
                <option value="Chicago"></option>
              </datalist>
            </div>
            <div class="mb-4">
              <label for="exampleFormControlSelect2" class="form-label">Example multiple select</label>
              <select multiple="" class="form-select" id="exampleFormControlSelect2" aria-label="Multiple select example">
                <option selected="">Open this select menu</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div>
              <label for="exampleFormControlTextarea1" class="form-label">Example textarea</label>
              <textarea class="form-control" id="exampleFormControlTextarea1" rows="3"></textarea>
            </div>
          </div>
        </div>
      </div>

      <!-- Input Sizing -->
      <div class="col-md-6">
        <div class="card">
          <h5 class="card-header">Input Sizing &amp; Shape</h5>
          <div class="card-body">
            <small class="text-light fw-medium">Input text</small>

            <div class="mt-2 mb-4">
              <label for="largeInput" class="form-label">Large input</label>
              <input id="largeInput" class="form-control form-control-lg" type="text" placeholder=".form-control-lg">
            </div>
            <div class="mb-4">
              <label for="defaultInput" class="form-label">Default input</label>
              <input id="defaultInput" class="form-control" type="text" placeholder="Default input">
            </div>
            <div>
              <label for="smallInput" class="form-label">Small input</label>
              <input id="smallInput" class="form-control form-control-sm" type="text" placeholder=".form-control-sm">
            </div>
          </div>
          <hr class="m-0">
          <div class="card-body">
            <small class="text-light fw-medium">Input select</small>
            <div class="mt-2 mb-4">
              <label for="largeSelect" class="form-label">Large select</label>
              <select id="largeSelect" class="form-select form-select-lg">
                <option>Large select</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div class="mb-4">
              <label for="defaultSelect" class="form-label">Default select</label>
              <select id="defaultSelect" class="form-select">
                <option>Default select</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
            <div>
              <label for="smallSelect" class="form-label">Small select</label>
              <select id="smallSelect" class="form-select form-select-sm">
                <option>Small select</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
              </select>
            </div>
          </div>
          <hr class="m-0">
          <div class="card-body">
            <small class="text-light fw-medium">Input Shape</small>
            <div class="mt-2">
              <label for="roundedInput" class="form-label">Rounded input</label>
              <input id="roundedInput" class="form-control rounded-pill" type="text" placeholder="Default input">
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>
</template>

<style scoped lang="scss">

</style>
