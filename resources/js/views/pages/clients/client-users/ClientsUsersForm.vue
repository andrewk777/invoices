<script setup>
import { ref, reactive, defineEmits, defineProps} from 'vue'
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";
import axios from "axios";
import baseService from "@/utils/base-service.js";

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
  clientUser: {
    type: Object,
    default: null,
  },
});

const emit = defineEmits([
  'add-client-user',
  'close-edit-dialogue',
]);

const isDialogVisible = ref(false);
const errors = ref({});
const submitted = ref(false);
const loading = ref(false);
const client = ref(props.client);
const token = ref(props.token);
const clientUser = ref(props.clientUser);

const form = reactive({
  name: props.clientUser?.name || '',
  email: props.clientUser?.email || '',
  password: '',
  system_access: props.clientUser?.system_access || false,
});

const closeAllDialogues = () => {
  isDialogVisible.value = false;
  emit('close-edit-dialogue');
}

const addUser = async () => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

  if (props.clientUser) {
    await axios.put(`/api/clients/${client.value.hash}/user/${props.clientUser.hash}/update`, form, {
      headers: {
        'Accept': 'application/json',
        "Authorization": "Bearer " + token.value,
      }
    }).then((response) => {
      if (response.data.success) {
        clientUser.value = response.data.client_user;
        emit('edit-client-user', response.data.client_user);
        submitted.value = true;
        isDialogVisible.value = false;
      }
    }).catch((error) => {
      baseService.handleObjectErrors(error, errors);
      console.log(error);
    });

  } else {
    await axios.post(`/api/clients/${client.value.hash}/user/store`, form, {
      headers: {
        'Accept': 'application/json',
        "Authorization": "Bearer " + token.value,
      }
    }).then((response) => {
      if (response.data.success){
        clientUser.value = response.data.client_user;
        emit('add-client-user', clientUser.value);
        submitted.value = true;
        isDialogVisible.value = false;
      }
    }).catch((error) => {
      baseService.handleObjectErrors(error, errors);
      console.log(error);
    });
  }

  loading.value = false;
}

onMounted(() => {
  if (props.clientUser) {
    console.log('Edit user', props.clientUser);
  }

  if (props.client) {
    console.log('Client', client.value);
  }
});
</script>

<template>

  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog Activator -->
    <template #activator="{ props }">

      <IconBtn v-if="clientUser">
        <VIcon
          v-bind="props"
          icon="tabler-edit"
        />
      </IconBtn>

      <VBtn
        v-else
        v-bind="props"
        size="small"
      >
        Add User
      </VBtn>
    </template>

    <!-- Dialog close btn -->
    <DialogCloseBtn @click="closeAllDialogues"/>

    <!-- Dialog Content -->
    <VCard :title="clientUser ? 'Edit User' : 'User Profile'">
      <VCardText>
        <VRow>

          <VCol cols="12" md="12">
            <VAlert
              class="text-center"
              v-if="Object.keys(errors).length > 0"
              type="error"
            >
              <p v-for="(error, index) in errors" :key="index">
                {{ error[0] }}
              </p>
            </VAlert>

            <VAlert
              class="text-center"
              v-if="submitted"
              type="success"
            >
              {{ clientUser ? 'User updated successfully' : 'User added successfully' }}
            </VAlert>
          </VCol>

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
            <span class="text-error">
              {{ errors.name ? errors.name[0] : '' }}
            </span>
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
            <span class="text-error">
              {{ errors.email ? errors.email[0] : '' }}
            </span>
          </VCol>

          <VCol
            cols="12"
            md="6"
          >
            <AppTextField
              v-model="form.password"
              label="Password"
              type="password"
            />
            <span class="text-error">
              {{ errors.password ? errors.password[0] : '' }}
            </span>
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
          @click="closeAllDialogues"
        >
          Close
        </VBtn>
        <VBtn
          @click="addUser"
          :loading="loading"
        >
          {{ clientUser ? 'Update' : 'Save' }}
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

</template>
