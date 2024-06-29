<script setup>
import { computed, onBeforeMount, ref } from 'vue'
import axios from 'axios'

import ClientsItem from "@/views/pages/clients/ClientsItem.vue";
import baseService from '@/utils/base-service.js'

const user = ref(baseService.getUserFromLocalStorage());

const loading = ref(false);

const clients = ref([]);
const total = ref(0);
const searchTotal = ref(0);
const dataLoaded = ref(false);
const searchActive = ref(false);
const search_values = ref([]);

const headers = [
  {
    title: 'Company',
    sortable: false,
    key: 'id',
  },
  {
    title: 'Main Contact',
    key: 'fullName',
  },
  {
    title: 'Accounts Payable',
    key: 'email',
  },
  {
    title: 'Credit Card',
    key: 'startDate',
  },
  {
    title: 'Actions',
    key: 'experience',
  },
]

const getClients = (page = 1) => {
  searchActive.value = false;
  dataLoaded.value = false;
  axios.get('/api/clients?page=' + page, {
    headers: {
      "Authorization" : "Bearer " + user.value.token,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      clients.value = response.data.clients;
      total.value = response.data.total;
    }
    dataLoaded.value = true;
    console.log(clients.value);
  }).catch((error) => {
    console.log(error);
  });
}

onBeforeMount(() => {
  getClients();
});
</script>

<template>

  <VRow class="mb-2">
    <VCol cols="6">
      <h3 class="card-header">Customers</h3>
    </VCol>

    <VCol cols="6" class="text-right mt-4">
      <router-link
        class="btn btn-info waves-effect waves-light mr-2 btn-sm"
        exact
        :to="{name: 'ClientsCreate'}"
      >
        <VBtn variant="flat">
          Create Customer
        </VBtn>
      </router-link>
    </VCol>
  </VRow>

  <VDataTable
    :headers="headers"
    :items="clients"
    :items-per-page="25"
  >
    <template v-slot:item="{ item: client }">
      <ClientsItem :client="client" />
    </template>
  </VDataTable>

</template>

<style scoped>

</style>
