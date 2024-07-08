<script setup>
import { computed, onBeforeMount, ref } from 'vue'
import axios from 'axios'

import baseService from '@/utils/base-service.js'
import ClientsListRow from "@/views/pages/clients/ClientsListRow.vue";
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

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

// const search = ref('')

onBeforeMount(() => {
  getClients();
});
</script>

<template>

  <VRow class="mb-2">
    <VCol cols="6">
      <h3 class="card-header">Clients</h3>
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

<!--  <VCardText>-->
<!--    <VRow>-->
<!--      <VCol-->
<!--        cols="12"-->
<!--        offset-md="8"-->
<!--        md="4"-->
<!--      >-->
<!--        <AppTextField-->
<!--          v-model="search"-->
<!--          placeholder="Search ..."-->
<!--          append-inner-icon="tabler-search"-->
<!--          single-line-->
<!--          hide-details-->
<!--          dense-->
<!--          outlined-->
<!--        />-->
<!--      </VCol>-->
<!--    </VRow>-->
<!--  </VCardText>-->

  <VDataTable
    :headers="headers"
    :items="clients"
    :items-per-page="25"
  >
    <template v-slot:item="{ item: client }">
      <ClientsListRow :client="client" />
    </template>
  </VDataTable>

</template>

<style scoped>

</style>
