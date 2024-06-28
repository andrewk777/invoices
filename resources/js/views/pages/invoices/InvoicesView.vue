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

onBeforeMount(() => {
  getInvoices();
});
</script>

<template>
  <div class="card">

    <h5 class="card-header">Invoices</h5>

    <div class="table-responsive text-nowrap">
      <table class="table">

        <thead class="table-dark">
        <tr>
          <th>Invoice #</th>
          <th>From Company</th>
          <th>Status</th>
          <th>Customer</th>
          <th>Invoice Date</th>
          <th>Payment Due</th>
          <th>Total</th>
          <th>Currency</th>
          <th>Outstanding</th>
          <th>Actions</th>
        </tr>
        </thead>

        <tbody class="table-border-bottom-0">

        <invoices-item
          v-for="invoice in invoices.data"
          :key="invoice.id"
          :invoice="invoice"
        >
        </invoices-item>

        </tbody>
      </table>
    </div>

    <laravel-vue-pagination
      class="justify-content-center"
      :limit="3"
      :data="invoices"
      @pagination-change-page="getInvoices"
    >
      <template #prev-nav>
        <span>&lt; Previous</span>
      </template>
      <template #next-nav>
        <span>Next &gt;</span>
      </template>
    </laravel-vue-pagination>

  </div>


</template>

<style scoped lang="scss">

</style>
