<script setup>
import {computed, reactive, onBeforeMount, ref, watch} from 'vue'
import InvoicesItem from "@/views/pages/invoices/InvoicesListRow.vue";
import baseService from '@/utils/base-service.js'
import AppTextField from "@core/components/app-form-elements/AppTextField.vue";

import apiClientAuto from '@/utils/apiCLientAuto.js';
import config from "@/utils/config.js";
import AppDateTimePicker from "@core/components/app-form-elements/AppDateTimePicker.vue";

const user = computed(() => baseService.getUserFromLocalStorage());
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

const form = reactive({
  search: '',
  date: '',
  unpaid: false,
  na: false,
});

const filterDate = (string) => {

  if(config.APP_ENV === 'local'){
    console.log('Filter Date:', string);
  }

  if (string === 'today') {
    const today = new Date().toISOString().split('T')[0];
    form.date = `${today} to ${today}`;
    console.log('Filter Today:', form.date);

  } else if (string === 'yesterday') {
    const yesterday = new Date(Date.now() - 86400000).toISOString().split('T')[0];
    form.date = `${yesterday} to ${yesterday}`;
    console.log('Filter Yesterday:', form.date);

  } else if (string === 'this week') {
    const date = new Date();
    const firstDay = new Date(date.setDate(date.getDate() - date.getDay())).toISOString().split('T')[0];
    const lastDay = new Date(date.setDate(date.getDate() - date.getDay() + 6)).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;
    console.log('Filter This Week:', form.date);

  } else if (string === 'last week') {
    const date = new Date();
    const firstDay = new Date(date.setDate(date.getDate() - date.getDay() - 7)).toISOString().split('T')[0];
    const lastDay = new Date(date.setDate(date.getDate() - date.getDay() + 6)).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;

  } else if (string === 'this month') {
    const date = new Date();
    const firstDay = new Date(date.getFullYear(), date.getMonth(), 1).toISOString().split('T')[0];
    const lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;

  } else if (string === 'last month') {
    const date = new Date();
    const firstDay = new Date(date.getFullYear(), date.getMonth() - 1, 1).toISOString().split('T')[0];
    const lastDay = new Date(date.getFullYear(), date.getMonth(), 0).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;

  } else if (string === 'this year') {
    const date = new Date();
    const firstDay = new Date(date.getFullYear(), 0, 1).toISOString().split('T')[0];
    const lastDay = new Date(date.getFullYear(), 11, 31).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;

  } else if (string === 'last year') {
    const date = new Date();
    const firstDay = new Date(date.getFullYear() - 1, 0, 1).toISOString().split('T')[0];
    const lastDay = new Date(date.getFullYear() - 1, 11, 31).toISOString().split('T')[0];
    form.date = `${firstDay} to ${lastDay}`;
  }
};

const getInvoices = async (page = 1) => {
  searchActive.value = false;
  dataLoaded.value = false;

  try{
    const response = await apiClientAuto.get('/invoices?page=' + page);
    if(response.data?.success === true){
      invoices.value = response.data?.invoices;
      total.value = response.data?.total;
    }
    dataLoaded.value = true;

  } catch (error) {
    if(config.APP_ENV === 'local'){
      console.error('Error getting invoices:', error);
    }
  }

}

const clearAllFiltersAndSearch = async () => {
  searchActive.value = false;
  form.search = '';
  form.date = '';
  form.unpaid = false;
  form.na = false;

  Object.keys(form).forEach((key) => {
    localStorage.removeItem('invoice-search-' + key);
  });

  // No need to load invoices because the watcher already does that
  //await getInvoices();
}

const searchInvoices = async () => {

  if(config.APP_ENV === 'local'){
    console.log('Search invoices:', form);
  }

  if (form.search === '' && form.date === '' && form.unpaid === false && form.na === false) {
    Object.keys(form).forEach((key) => {
      localStorage.removeItem('invoice-search-' + key);
    });
    await getInvoices();
    return;
  }

  // Store search result to be loaded on page refresh
  //localStorage.setItem('invoice-search', form.search);

  Object.keys(form).forEach((key) => {
    if (
      (typeof form[key] === 'string' && form[key].trim() !== '') ||
      (typeof form[key] === 'boolean' && form[key] === true)
    ) {
      localStorage.setItem('invoice-search-' + key, form[key]);
    }
  });

  dataLoaded.value = false;

    try{
      const response = await apiClientAuto.post('/invoices/search', form);
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

watch(() => form.search, (newValue) => {
  if (newValue === null) {
    localStorage.removeItem('invoice-search-search');
    form.search = '';
    searchInvoices();
  }
});

onBeforeMount(() => {
  let searchFound = 0;
  searchKeys.value.forEach((key) => {
    const value = localStorage.getItem('invoice-search-' + key);
    if (value && value !== '') {
      form[key] = value;
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

    <VRow v-if="user.role === 'admin'">
      <VCol
        cols="12"
        md="4"
        class="mb-4 d-flex"
      >
        <VBtn
          @click="!form.search ? getInvoices() : searchInvoices()"
          variant="flat" color="primary" class=" p-0"
        >
          <VIcon color="white" size="25" icon="tabler-rotate-clockwise" title="reload" />
        </VBtn>
        <AppTextField
          v-model="form.search"
          @input="searchInvoices"
          placeholder="Search ..."
          append-inner-icon="tabler-search"
          single-line
          hide-details
          dense
          outlined
          clearable
        />
      </VCol>

      <VCol md="3" class="d-flex">
        <AppDateTimePicker
          @change="searchInvoices"
          class="mb-4"
          v-model="form.date"
          placeholder="Select date range"
          :config="{ mode: 'range' }"
          clearable
          style="min-width: 220px;"
        />
        <v-menu>
            <template v-slot:activator="{ props }">
               
            <VBtn vvariant="flat" color="primary" class="mr-2 p-0" v-bind="props" >
                <VIcon color="white" size="25" icon="tabler-dots-vertical"/>
            </VBtn>
            </template>

            <v-list class="border-label-info">
                <v-list-item class="dd-item" @click="filterDate('today')"> Today </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('yesterday')"> Yesterday </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('this week')"> This Week </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('last week')"> Last Week </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('this month')"> This Month </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('last month')"> Last Month </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('this year')"> This Year </v-list-item>
                <v-list-item class="dd-item" @click="filterDate('last year')"> Last Year </v-list-item>
            </v-list>

        </v-menu>
      </VCol>

      <VCol
        md="5"
        cols="12"
      >
        <VSwitch
          @change="searchInvoices"
          v-model="form.unpaid"
          label="Unpaid"
          true-value="Only Unpaid"
          false-value="All"
          class="d-inline-block mr-4"
        />

        <VSwitch
          @change="searchInvoices"
          v-model="form.na"
          label="NA"
          true-value="Show"
          false-value="Hide"
          class="d-inline-block"
        />

        <VBtn
          @click="clearAllFiltersAndSearch"
          variant="flat" color="primary"
          class="p-0 ml-4 mb-5"
        >
            <VIcon color="white" size="25" icon="tabler-eraser" title="reload" />
        </VBtn>
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

    <div v-if="form.search && searchTotal > 0" class="text-center mt-4">
      <p>Showing {{ invoices.length }} of {{ searchTotal }} search results.</p>
    </div>

    <div v-if="!form.search && total > 0" class="text-center mt-4">
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
.dd-item
{
    cursor: pointer;
    padding: 10px;
    &:hover
    {
        background-color: #f1f1f1;
    }
}
</style>
