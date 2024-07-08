<script setup>
import { computed, onBeforeMount, ref } from 'vue'
import axios from 'axios'

import InvoicesItem from "@/views/pages/invoices/InvoicesItem.vue";
import LaravelVuePagination from 'laravel-vue-pagination';
import baseService from '@/utils/base-service.js'

const token = computed(() => baseService.getTokenFromLocalStorage());

const loading = ref(false);

const invoices = ref([]);
const total = ref(0);
const searchTotal = ref(0);
const dataLoaded = ref(false);
const searchActive = ref(false);
const search_values = ref([]);

const getInvoices = (page = 1) => {
  searchActive.value = false;
  dataLoaded.value = false;
  axios.get('/api/invoices?page=' + page, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      invoices.value = response.data.invoices;
      total.value = response.data.total;
    }
    dataLoaded.value = true;

    if(import.meta.env.VITE_APP_ENV === 'local'){
      console.log(invoices.value);
    }

  }).catch((error) => {
    console.log(error);
  });
}

const headers = [
  {
    title: 'Invoice #',
    key: 'invoice_num',
  },
  {
    title: 'From Company',
    key: 'invoice.company.name',
  },
  {
    title: 'Status',
    key: 'status',
  },
  {
    title: 'Customer',
    key: 'invoice.client.company_name',
  },
  {
    title: 'Invoice Date',
    key: 'invoice_date',
  },
  {
    title: 'Payment Due',
    key: 'invoice_due',
  },
  {
    title: 'Total',
    key: 'total',
  },
  {
    title: 'Currency',
    key: 'currency',
  },
  {
    title: 'Outstanding',
    key: 'balance',
  },
  {
    title: 'Actions',
    key: 'actions',
    sortable: false,
  },
]

onBeforeMount(() => {
  getInvoices();
});
</script>

<template>

  <div class="card">

    <VRow>
      <VCol col="6" class="justify-content-first my-auto">
        <h3 class="card-header">Invoices</h3>
      </VCol>

      <VCol col="6" class="text-right mt-4">
        <router-link
          class="btn btn-info waves-effect waves-light mr-2 btn-sm"
          exact
          :to="{name: 'InvoicesCreate'}">
          <VBtn variant="flat">
            Create Invoice
          </VBtn>
        </router-link>
      </VCol>
    </VRow>

    <VDataTable
      :headers="headers"
      :items="invoices"
      :items-per-page="25"
    >
      <template v-slot:item="{ item: invoice }">
        <InvoicesItem :invoice="invoice" />
      </template>
    </VDataTable>

<!--    <laravel-vue-pagination-->
<!--      class="justify-content-center"-->
<!--      :limit="3"-->
<!--      :data="invoices"-->
<!--      @pagination-change-page="getInvoices"-->
<!--    >-->
<!--      <template #prev-nav>-->
<!--        <span>&lt; Previous</span>-->
<!--      </template>-->
<!--      <template #next-nav>-->
<!--        <span>Next &gt;</span>-->
<!--      </template>-->
<!--    </laravel-vue-pagination>-->

  </div>


</template>

<style scoped lang="scss">

</style>
