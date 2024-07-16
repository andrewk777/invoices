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
  system_access: props.clientUser?.system_access === 1,
});

const closeAllDialogues = () => {
  isDialogVisible.value = false;
  emit('close-edit-dialogue');
}

const editUser = async () => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

    await axios.put(`/api/clients/${client.value.hash}/user/${props.clientUser.hash}/update`, form, {
      headers: {
        'Accept': 'application/json',
        "Authorization": "Bearer " + token.value,
      }
    }).then((response) => {
      if (response.data.success) {
        clientUser.value = response.data.client_user;
        emit('edit-client-user', response.data.client_user);
        emit('close-modal'); // Emit event to close the form dialog
        submitted.value = true;
        isDialogVisible.value = false;
      }
    }).catch((error) => {
      baseService.handleObjectErrors(error, errors);

    });

  loading.value = false;
}

onMounted(() => {

});
</script>

<template>

  <VDialog
    v-model="isDialogVisible"
    max-width="600"
  >
    <!-- Dialog Activator -->
    <template #activator="{ props }">

      <v-list-item-title v-bind="props">
        <a href="javascript:void(0);" class="m-2">
          Edit
        </a>
      </v-list-item-title>

<!--      <IconBtn>-->
<!--        <VIcon-->
<!--          v-bind="props"-->
<!--          icon="tabler-edit"-->
<!--        />-->
<!--      </IconBtn>-->
    </template>

    <!-- Dialog close btn -->
    <DialogCloseBtn @click="isDialogVisible = !isDialogVisible" />

    <!-- Dialog Content -->
    <VCard :title="'Edit User'">
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

<!--            <VAlert-->
<!--              class="text-center"-->
<!--              v-if="submitted"-->
<!--              type="success"-->
<!--            >-->
<!--              {{ 'User updated successfully'}}-->
<!--            </VAlert>-->
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
          @click="isDialogVisible = false"
        >
          Close
        </VBtn>
        <VBtn
          @click="editUser"
          :loading="loading"
        >
          {{ 'Update' }}
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

</template>
