<script setup>
import {computed, reactive, onBeforeMount, ref, watch} from 'vue'
import InvoicesItem from "@/views/pages/invoices/InvoicesListRow.vue";
import LaravelVuePagination from 'laravel-vue-pagination';
import baseService from '@/utils/base-service.js'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

import apiClientAuto from '@/utils/apiCLientAuto.js';
import handleErrors from "@/utils/handleErrors.js";
import config from "@/utils/config.js";
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";

const token = computed(() => baseService.getTokenFromLocalStorage());
const loading = ref(false);

const invoices = ref([]);
const total = ref(0);
const dataLoaded = ref(false);

const searchActive = ref(false);
const searchTotal = ref(0);
// const search = ref('');
const searchKeys = ref([
  'search',
  'date',
  'unpaid',
  'na',
]);

const search_values = ref([]);

const formSearch = reactive({
  search: '',
  date: '',
  unpaid: false,
  na: false,
});

const getInvoices = async (page = 1) => {
  searchActive.value = false;
  dataLoaded.value = false;

  try{
    const response = await apiClientAuto.get('/invoices?page=' + page);
    if(response.data.success === true){
      invoices.value = response.data.invoices;
      total.value = response.data.total;
    }
    dataLoaded.value = true;

  } catch (error) {
    if(config.APP_ENV === 'local'){
      console.error('Error getting invoices:', error);
    }
  }

}

const searchInvoices = async () => {

  if(config.APP_ENV === 'local'){
    console.log('Search invoices:', formSearch);
  }

  if (formSearch.search.trim() === '' && formSearch.date === '' && formSearch.unpaid === false && formSearch.na === false) {
    Object.keys(formSearch).forEach((key) => {
      localStorage.removeItem('invoice-search-' + key);
    });
    await getInvoices();
    return;
  }

  // Store search result to be loaded on page refresh
  //localStorage.setItem('invoice-search', formSearch.search);

  Object.keys(formSearch).forEach((key) => {
    if (
      (typeof formSearch[key] === 'string' && formSearch[key].trim() !== '') ||
      (typeof formSearch[key] === 'boolean' && formSearch[key] === true)
    ) {
      localStorage.setItem('invoice-search-' + key, formSearch[key]);
    }
  });

  dataLoaded.value = false;

    try{
      const response = await apiClientAuto.post('/invoices/search', formSearch);
      if(response.data.success === true){
        invoices.value = response.data.invoices;
        searchTotal.value = response.data.total;
      }
      dataLoaded.value = true;

    } catch (error) {

      if(config.APP_ENV === 'local'){
        console.error('Error searching invoices:', error);
      }

    }
}

const headers = [
  {
    title: 'Invoice #',
    key: 'invoice_num',
  },
  {
    title: 'From Company',
    key: 'company.name',
  },
  {
    title: 'Status',
    key: 'status',
  },
  {
    title: 'Customer',
    key: 'client.company_name',
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

watch(formSearch, (newValue) => {
  if (newValue.search === null) {
    localStorage.removeItem('invoice-search-search');
    formSearch.search = '';
  }
  searchInvoices();
});

onBeforeMount(() => {
  let searchFound = 0;
  searchKeys.value.forEach((key) => {
    const value = localStorage.getItem('invoice-search-' + key);
    if (value) {
      formSearch[key] = value;
      searchFound++;
    }
  });

  if (searchFound > 0) {
    searchInvoices();
  } else {
    getInvoices();
  }

});
</script>

<template>

  <div class="card">

    <VRow>
      <VCol col="6" class="justify-content-first my-auto">
        <h1 class="card-header">Invoices</h1>
      </VCol>

      <VCol col="6" class="text-right mt-4">
        <router-link
          class="btn btn-info waves-effect waves-light"
          exact
          :to="{name: 'InvoicesCreate'}">
          <VBtn
            variant="flat"
            color="primary"
          >
            Create Invoice
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
        <VBtn @click="!formSearch.search ? getInvoices() : searchInvoices()" variant="flat" color="primary" class="mr-2 p-0" >
            <VIcon color="white" size="25" icon="tabler-rotate-clockwise" title="reload" />
        </VBtn>
        <AppTextField
          v-model="formSearch.search"
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

      <VCol
        cols="12"
        md="3"
      >
        <AppDateTimePicker
          @change="searchInvoices"
          class="mb-4"
          v-model="formSearch.date"
          placeholder="Select date range"
          :config="{ mode: 'range' }"
        />
      </VCol>

      <VCol
        cols="12"
        md="3"
      >
        <VSwitch
          @change="searchInvoices"
          v-model="formSearch.unpaid"
          label="Unpaid"
          true-value="Only Unpaid"
          false-value="All"
          class="d-inline-block mr-4"
        />

        <VSwitch
          @change="searchInvoices"
          v-model="formSearch.na"
          label="NA"
          true-value="Show"
          false-value="Hide"
          class="d-inline-block"
        />
      </VCol>
    </VRow>

    <VDataTable
      :headers="headers"
      :items="invoices"
      :items-per-page="25"
      :loading="!dataLoaded"
    >
      <template v-slot:item="{ item: invoice }">
        <InvoicesItem :invoice="invoice" />
      </template>
    </VDataTable>

    <div v-if="formSearch.search && searchTotal > 0" class="text-center mt-4">
      <p>Showing {{ invoices.length }} of {{ searchTotal }} search results.</p>
    </div>

    <div v-if="!formSearch.search && total > 0" class="text-center mt-4">
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
