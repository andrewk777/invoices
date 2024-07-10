<script setup>
import { ref } from 'vue'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";
import ClientsUsersForm from "@/views/pages/clients/client-users/ClientsUsersForm.vue";

const props = defineProps({
    users: {
      type: Array,
      required: true,
    },
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

const users = ref(props.users);
const search = ref('');
const token = ref(props.token);
const client = ref(props.client);
const updatedClient = ref(props.updatedClient);

const openFormDialogue = ref(false);
const openModal = () => {
  openFormDialogue.value = true;
};

const closeModal = () => {
  openFormDialogue.value = false;
};

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
]

const deleteItem = itemId => {
  if (!users.value)
    return
  const index = users.value.findIndex(item => item.id === itemId)

  users.value.splice(index, 1)
}

</script>

<template>
  <div>
    <VCardText>
      <h3>Users</h3>
      <VRow>
        <VCol
          cols="6"
          md="6"
        >
<!--          <AppTextField-->
<!--            v-model="search"-->
<!--            placeholder="Search ..."-->
<!--            append-inner-icon="tabler-search"-->
<!--            single-line-->
<!--            hide-details-->
<!--            dense-->
<!--            outlined-->
<!--          />-->
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
          />

        </VCol>
      </VRow>
    </VCardText>

    <!-- 👉 Data Table  -->
    <VDataTable
      :headers="headers"
      :items="users || []"
      :search="search"
      :items-per-page="5"
      class="text-no-wrap"
    >

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

      <!-- Delete -->
      <template #item.access="{ item }">
        <IconBtn>
          <VIcon icon="tabler-accessible" />
        </IconBtn>
      </template>
    </VDataTable>
  </div>
</template>
