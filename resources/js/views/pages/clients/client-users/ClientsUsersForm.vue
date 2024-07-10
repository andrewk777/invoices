<script setup>
import { ref, reactive } from 'vue'
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";
import axios from "axios";

const props = defineProps({
  client: {
    type: Object,
    required: true,
  },
  token: {
    type: String,
    required: true,
  },
  updatedClient: {
    type: Object,
    required: true,
  },
});

const isDialogVisible = ref(false);
const errors = ref({});
const submitted = ref(false);
const loading = ref(false);
const hash = ref('');
const client = ref(props.client);
const token = ref(props.token);
const clientUser = ref({});


const form = reactive({
  name: '',
  email: '',
  password: '',
  system_access: false,
});

const addUser = async (action = null) => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  await axios.post('/api/clients/'+client.value.hash+'/user/store', form, {
    headers: {
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {

    if (response.data.success) {
        clientUser.value = response.data.user;
        submitted.value = true;
        isDialogVisible.value = false;
    }

  }).catch((error) => {
    if (error.response){
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

</script>

<template>
  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog Activator -->
    <template #activator="{ props }">
      <VBtn
        v-bind="props"
        size="small"
      >
        Add User
      </VBtn>
    </template>

    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

    <!-- Dialog Content -->
    <VCard title="User Profile">
      <VCardText>
        <VRow>

          <VCol
            cols="12"
            sm="6"
            md="6"
          >
            <AppTextField
              v-model="form.name"
              label="Name"
              placeholder="Name"
            />
          </VCol>

          <VCol
            cols="12"
            sm="6"
            md="6"
          >
            <AppTextField
              v-model="form.email"
              label="Email"
              persistent-hint
              placeholder="Email"
              type="email"
            />
          </VCol>

          <VCol
            cols="12"
            md="6"
          >
            <AppTextField
              v-model="form.password"
              label="Password"
              placeholder="johndoe@email.com"
            />
          </VCol>

          <VCol
            cols="12"
            md="6"
          >
            <VCheckbox
              v-model="form.system_access"
              label="System Access"
            />
          </VCol>

        </VRow>
      </VCardText>

      <VCardText class="d-flex justify-end flex-wrap gap-3">
        <VBtn
          variant="tonal"
          color="secondary"
          @click="isDialogVisible = false"
        >
          Close
        </VBtn>
        <VBtn
          @click="addUser"
          :loading="loading"
        >
          Save
        </VBtn>
      </VCardText>
    </VCard>

  </VDialog>
</template>
