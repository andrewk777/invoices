<script setup>
import { ref, computed } from 'vue'
import ClientsUsersForm from "@/views/pages/clients/client-users/ClientsUsersForm.vue";
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
};

const addClientUser = clientUser => {
  clientUsers.value.push(clientUser);
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
          />
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
      <!-- Table row templates -->
      <template #item.name="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.name }}</span>
          </div>
        </div>
      </template>

      <template #item.email="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.email }}</span>
          </div>
        </div>
      </template>

      <template #item.last_login="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">{{ item.last_login }}</span>
          </div>
        </div>
      </template>

      <!-- Status -->
      <template #item.system_access="{ item }">
        <div class="d-flex align-center">
          <div class="d-flex flex-column ms-3">
            <span class="d-block font-weight-medium text-truncate text-high-emphasis">
              {{ item.system_access ? 'Yes' : 'No' }}
            </span>
          </div>
        </div>
      </template>

      <!-- Access -->
      <template #item.access="{ item }">
        <IconBtn>
          <VIcon
            v-if="!loading && item.system_access"
            icon="tabler-accessible"
            @click="clientUserAccess(item.hash)"
          />

          <VIcon
            v-else-if="!loading && !item.system_access"
            icon="tabler-accessible-off"
            @click="clientUserAccess(item.hash)"
          />

          <VProgressCircular
            v-else
            indeterminate
            color="primary"
            size="24"
          />
        </IconBtn>
      </template>

      <!-- Edit -->
      <template #item.edit="{ item }">

        <ClientsUsersForm
          :editDialogueVisible="editDialogueVisible"
          :client="client"
          :token="token"
          :updatedClient="updatedClient"
          :clientUser="item"
          @close-modal="closeModal"
          @close-edit-dialogue="editDialogueVisible = false"
          @edit-client-user="updateClientUser"
        />

      </template>
    </VDataTable>
  </div>
</template>
