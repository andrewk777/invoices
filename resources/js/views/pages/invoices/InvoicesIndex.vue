<script setup>
import {computed, onBeforeMount, ref, watch} from 'vue'
import axios from 'axios'

import InvoicesItem from "@/views/pages/invoices/InvoicesListRow.vue";
import LaravelVuePagination from 'laravel-vue-pagination';
import baseService from '@/utils/base-service.js'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);

const invoices = ref([]);
const total = ref(0);
const dataLoaded = ref(false);

const searchActive = ref(false);
const searchTotal = ref(0);
const search = ref('');
const savedSearch = computed(() => localStorage.getItem('client-search'));
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

const searchInvoices = () => {
  if (search.value.trim() === '') {
    localStorage.removeItem('invoice-search');
    getInvoices();
    return;
  }

  // Store search result to be loaded on page refresh
  localStorage.setItem('invoice-search', search.value);

  dataLoaded.value = false;
  axios.get('/api/invoices/search?query=' + search.value, {
    headers: {
      "Authorization" : "Bearer " + token.value,
      'Accept' : 'application/json',
    },
  }).then((response) => {
    if(response.data.success === true){
      invoices.value = response.data.invoices;
      searchTotal.value = response.data.total;
    }
    dataLoaded.value = true;
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

watch(search, () => {
  searchInvoices();
});

onBeforeMount(() => {
  if(savedSearch.value) {
    search.value = savedSearch.value;
    searchInvoices();
  }else{
    getInvoices();
  }
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
          class="btn btn-info waves-effect waves-light"
          exact
          :to="{name: 'InvoicesCreate'}">
          <VBtn
            variant="flat"
            :size="'small'"
            color="primary"
          >
            Create Invoice
          </VBtn>
        </router-link>
      </VCol>
    </VRow>

    <VRow>
      <VCol
        cols="4"
        md="4"
        class="mb-4"
      >
        <AppTextField
          v-model="search"
          @keyup.enter="searchInvoices"
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
      :items="invoices"
      :items-per-page="25"
    >
      <template v-slot:item="{ item: invoice }">
        <InvoicesItem :invoice="invoice" />
      </template>
    </VDataTable>

    <div v-if="search && searchTotal > 0" class="text-center mt-4">
      <p>Showing {{ invoices.length }} of {{ searchTotal }} search results.</p>
    </div>

    <div v-if="!search && total > 0" class="text-center mt-4">
      <p>Showing {{ invoices.length }} of {{ total }} invoices.</p>
    </div>

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
