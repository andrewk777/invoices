<script setup>
import { computed, onBeforeMount, ref } from 'vue'
import axios from 'axios'

// import ClientsForm from "@/views/pages/clients/ClientsForm.vue";
import ClientsItem from "@/views/pages/clients/ClientsItem.vue";
import LaravelVuePagination from 'laravel-vue-pagination';
import baseService from '@/utils/base-service.js'

const user = ref(baseService.getUserFromLocalStorage());

const loading = ref(false);

const clients = ref([]);
const total = ref(0);
const searchTotal = ref(0);
const dataLoaded = ref(false);
const searchActive = ref(false);
const search_values = ref([]);

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
  <div class="card">

    <div class="row">
      <div class="col-md-6 justify-content-first my-auto">
        <h3 class="card-header">Customers</h3>
      </div>

      <div class="col-md-6 text-right mt-4">
        <router-link
          class="btn btn-info waves-effect waves-light mr-2 btn-sm"
          exact
          :to="{name: 'ClientsCreate'}">
          Add Customer
        </router-link>
      </div>
    </div>

    <div class="table-responsive text-nowrap">
      <table class="table">

        <thead class="table-dark">
        <tr>
          <th>Company</th>
          <th>Main Contact</th>
          <th>Accounts Payable</th>
          <th>Credit Card</th>
          <th>Actions</th>
        </tr>
        </thead>

        <tbody class="table-border-bottom-0">

         <clients-item
            v-for="client in clients.data"
            :key="client.id"
            :client="client"
         >
         </clients-item>

        </tbody>
      </table>
    </div>

    <laravel-vue-pagination
      class="justify-content-center"
      :limit="3"
      :data="clients"
      @pagination-change-page="getClients"
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
