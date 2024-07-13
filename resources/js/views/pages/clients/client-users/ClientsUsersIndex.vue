<script setup>
import { ref, computed, onBeforeMount } from 'vue'
import ClientsUsersForm from "@/views/pages/clients/client-users/ClientsUsersForm.vue";
import ClientsUsersRowItem from "@/views/pages/clients/client-users/ClientsUsersRowItem.vue";
import axios from "axios";
import DialogCloseBtn from "@core/components/DialogCloseBtn.vue";

const props = defineProps({
  users: {
    type: Array,
    required: true,
  },
  client: {
    type: Object,
    required: true,
  },
  updatedClient: {
    type: Object,
    required: true,
  },
});

const client = ref(props.client);
const clientUsers = ref(props.users);
const clientUser = ref(null);

const search = ref('');
const token = computed(() => {
  return baseService.getTokenFromLocalStorage();
})

const updatedClient = ref(props.updatedClient);
const submitted = ref(false);
const loading = ref(false);
const errors = ref({});
const responseMessage = ref('');

const openFormDialogue = ref(false);
const openModal = () => {
  openFormDialogue.value = true;
};

const closeModal = () => {
  openFormDialogue.value = false;
  editDialogueVisible.value = false; // Set editDialogueVisible to false
};

const addClientUser = clientUser => {
  clientUsers.value.push(clientUser);
  closeModal();
  editDialogueVisible.value = false;
};

const updateClientUser = (clientUser) => {
  const index = clientUsers.value.findIndex((user) => user.id === clientUser.id);
  clientUsers.value[index] = clientUser;
};

const clientUserAccess = async (hash) => {
  submitted.value = false;
  loading.value = true;

  await axios.get(`/api/clients/user/${hash}/access`, {
    headers: {
      'Accept': 'application/json',
      "Authorization": "Bearer " + token.value,
    }
  }).then((response) => {
    if (response.data.success) {
      clientUsers.value = response.data.client_users;
      responseMessage.value = response.data.access;
      submitted.value = true;
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

const editDialogueVisible = ref(false);

// headers
const headers = [
  {
    title: 'NAME',
    key: 'name',
  },
  {
    title: 'EMAIL',
    key: 'email',
  },
  {
    title: 'LAST LOGIN',
    key: 'last_login',
  },
  {
    title: 'ACCESS',
    key: 'system_access',
  },
  {
    title: 'ACTION',
    key: 'access',
    sortable: false,
  },
  {
    title: 'ACTION',
    key: 'edit',
    sortable: false,
  },
]

onBeforeMount(() => {
  console.log("Client in ClientsUsersIndex", client.value);
});

</script>

<template>
  <div>
    <VCardText>
      <h3>Users</h3>
      <VRow>
        <VCol
          cols="12"
          md="12"
        >
          <VAlert
            v-if="submitted && responseMessage"
            type="success"
            dismissible
            class="text-center"
          >
            {{ responseMessage }}
          </VAlert>
        </VCol>

        <VCol
          cols="6"
          md="6"
        >
          <!-- Search input field -->
        </VCol>

        <VCol
          cols="6"
          md="6"
          class="text-right"
        >
          <ClientsUsersForm
            :client="client"
            :token="token"
            :updatedClient="updatedClient"
            @close-modal="closeModal"
            @add-client-user="addClientUser"
          >
            <template #activator="{ props }">
              <VBtn
                v-bind="props"
                size="small"
              >
                Add User
              </VBtn>
            </template>
          </ClientsUsersForm>
        </VCol>
      </VRow>
    </VCardText>

    <!-- Data Table -->
    <VDataTable
      :headers="headers"
      :items="clientUsers || []"
      :search="search"
      :items-per-page="5"
      class="text-no-wrap"
    >
      <template #item="{ item }">
        <ClientsUsersRowItem
          :item="item"
          :token="token"
          :client="client"
          :updatedClient="updatedClient"
          @client-user-access="clientUserAccess"
          @update-client-user="updateClientUser"
        />
      </template>
    </VDataTable>
  </div>
</template>
