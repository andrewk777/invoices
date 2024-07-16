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
});

const emit = defineEmits([
  'add-client-user',
]);

const isDialogVisible = ref(false);
const errors = ref({});
const submitted = ref(false);
const loading = ref(false);
const client = ref(props.client);
const token = ref(props.token);

const form = reactive({
  name: '',
  email: '',
  password: '',
  system_access: false,
});

const addUser = async () => {
  // Delete all errors
  Object.keys(errors.value).forEach(function (key) {
    delete errors.value[key];
  });

  submitted.value = false;
  loading.value = true;

    await axios.post(`/api/clients/${client.value.hash}/user/store`, form, {
      headers: {
        'Accept': 'application/json',
        "Authorization": "Bearer " + token.value,
      }
    }).then((response) => {
      if (response.data.success){

        // clear form fields
        Object.keys(form).forEach(function (key) {
          if(key === 'system_access'){
            form[key] = false;
          }else{
            form[key] = '';
          }
        });

        emit('add-client-user', response.data.client_user);
        submitted.value = true;
        isDialogVisible.value = false;
      }
    }).catch((error) => {
      baseService.handleObjectErrors(error, errors);

    });

  // submitted.value = false;
  // // Delete all errors
  // Object.keys(errors.value).forEach(function (key) {
  //   delete errors.value[key];
  // });
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
    <VCard :title="'Add User'">
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
<!--              {{ 'User added successfully' }}-->
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
          @click="addUser"
          :loading="loading"
        >
          {{'Save'}}
        </VBtn>
      </VCardText>
    </VCard>
  </VDialog>

</template>
