<script setup>
import { ref, computed, onBeforeMount } from 'vue'
import ClientsUsersForm from "@/views/pages/clients/client-users/ClientsUsersForm.vue";
import ClientsUsersRowItem from "@/views/pages/clients/client-users/ClientsUsersRowItem.vue";
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";
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

  try {
    const response = await apiClientAuto.get(`/clients/user/${hash}/access`);

      if (response.data.success) {
        clientUsers.value = response.data.client_users;
        responseMessage.value = response.data.access;
        submitted.value = true;
      }

  } catch (error) {
    handleErrors(error, errors.value);
  }
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
    key: 'edit',
    sortable: false,
  },
]

</script>

<template>
  <div>
    <VCardText>
        <VRow>
            <VCol col="6" class="justify-content-first my-auto">
                <h3 class="card-header pt-3">Users</h3>
            </VCol>

            <VCol col="6" class="text-right">
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

      <VRow v-if="submitted && responseMessage">
        <VCol
          cols="12"
          md="12"
        >
          <VAlert
            type="success"
            dismissible
            class="text-center"
          >
            {{ responseMessage }}
          </VAlert>
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
