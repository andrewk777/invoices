<script setup>
import { computed, onBeforeMount, ref, watch } from 'vue'
import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";

import baseService from '@/utils/base-service.js'
import ClientsListRow from "@/views/pages/clients/ClientsListRow.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);

const clients = ref([]);
const total = ref(0);
const dataLoaded = ref(false);

const searchTotal = ref(0);
const search = ref('');
const savedSearch = computed(() => localStorage.getItem('client-search'));

const headers = [
  {
    title: 'Company Name',
    key: 'company_name',
  },
  {
    title: 'First Name',
    key: 'main_contact_first_name',
  },
  {
    title: 'Last Name',
    key: 'main_contact_last_name',
  },

  {
    title: 'Full Address',
    key: 'company_address',
  },

  {
    title: 'Email',
    key: 'company_email',
  },

  {
    title: 'Credit Card',
    sortable: false,
    key: 'startDate',
  },
  {
    title: 'Actions',
    sortable: false,
  },
];

const getClients = async (page = 1) => {
  dataLoaded.value = false;

  try {
    const response = await apiClientAuto.get('/clients?page=' + page);

    if(response.data.success === true){
      clients.value = response.data.clients;
      total.value = response.data.total;
    }
    dataLoaded.value = true;

  } catch (error) {
    handleErrors(error);
  }

}

const searchClients = async () => {

  if (search.value.trim() === '') {
    localStorage.removeItem('client-search');
    await getClients();
    return;
  }

  // Store search result to be loaded on page refresh
  localStorage.setItem('client-search', search.value);
  dataLoaded.value = false;

  try {
    const response = await apiClientAuto.get('/clients/search?query=' + search.value);

    if (response.data.success === true) {
      clients.value = response.data.clients;
      searchTotal.value = response.data.total;
    }

    dataLoaded.value = true;
  } catch (error) {
    handleErrors(error);
  }
}

watch(search, (newValue) => {
  if (newValue === null) {
    search.value = '';
  }
  searchClients();
});

onBeforeMount(() => {
  if(savedSearch.value) {
    search.value = savedSearch.value;
    searchClients();
  }else{
    getClients();
  }
});
</script>

<template>

  <VRow>
      <VCol col="6" class="justify-content-first my-auto">
        <h1 class="card-header">Clients</h1>
      </VCol>

      <VCol col="6" class="text-right mt-4">
        <router-link
        class="btn btn-info waves-effect waves-light mr-2 btn-sm"
        exact
        :to="{name: 'ClientsCreate'}"
      >
        <VBtn
          variant="flat"
          color="primary"
        >
          Create Client
        </VBtn>
      </router-link>
      </VCol>
    </VRow>

  <VRow>
    <VCol
      cols="12"
      md="4"
      class="mb-4 d-flex"
    >
    <VBtn @click="!search ? getClients() : searchClients()" variant="flat" color="primary" class="p-0" >
            <VIcon color="white" size="25" icon="tabler-rotate-clockwise" title="reload" />
        </VBtn>
      <AppTextField
        v-model="search"
        @keyup.enter="searchClients"
        placeholder="Search ..."
        append-inner-icon="tabler-search"
        single-line
        hide-details
        dense
        outlined
        clearable
      />
    </VCol>
  </VRow>

  <VDataTable
    :headers="headers"
    :items="clients"
    :items-per-page="25"
    :loading="!dataLoaded"
  >
    <template v-slot:item="{ item: client }">
      <ClientsListRow :client="client" />
    </template>
  </VDataTable>

  <div v-if="search && searchTotal > 0" class="text-center mt-4">
    <p>Showing {{ clients.length }} of {{ searchTotal }} search results.</p>
  </div>

  <div v-if="!search && total > 0" class="text-center mt-4">
    <p>Showing {{ clients.length }} of {{ total }} clients.</p>
  </div>
</template>

<style scoped>
/* Add any necessary styles */
</style>
